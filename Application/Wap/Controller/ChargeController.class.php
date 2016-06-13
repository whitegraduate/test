<?php
// +----------------------------------------------------------------------
// | 视界无限 - SEERSEE
// +----------------------------------------------------------------------
// | Copyright (c) 2015
// +----------------------------------------------------------------------
// | Author: Nice <hupeipei@seersee.com>
// +----------------------------------------------------------------------

namespace Wap\Controller;
use Think\Controller;
use Think\Log;
use User\Api\UserApi;
use OT\Wechat\SDKRuntimeException;
use OT\Wechat\UnifiedOrder;
use OT\Wechat\PayConfig;
use OT\Wechat\JsApi;
use OT\Wechat\Wechat;


/**
 * 描述：前台充电服务
 * Class ChargeController
 * @package Home\Controller
 */
class ChargeController extends WapController { 

    public function __construct() {
        parent::__construct(); 
    }


    /**
     * 查看某个桩各个插口的状态
     * @date: 2015-7-16
     * @author: ZhouWC
     * @param string $mid 充电桩的id
     * @return mixed|void
     */
    public function show($mid) {

        $this->wap_title = "充电口查询";
        $this->assign('mid',$mid);
        $charger = M('Charger')->where(array('deviceid'=>$mid))->find();
        $this->assign('charger',$charger);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://121.41.112.29/check.php?mid=m$mid");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $result = curl_exec($ch);
        $data = json_decode($result,true);

        if($data) {
            foreach($data['state'] as $key=>$value) {
                $data['state'][$key]['qimg'] = M('Outlet')->where(array('chargerid'=>$charger['id'],'code'=>$key))->getField('qimg');
            }
        } else {
            $outletList = M('Outlet')->where(array('chargerid'=>$charger['id']))->field(array("qimg","code"))->order('code')->select();
            foreach($outletList as $key=>$outlet) {
                $data['state'][$key+1]['qimg'] = $outlet['qimg'];
                $data['state'][$key+1]['id'] = '01';
            }
        }
        $middle = count($data['state'])>5?6:count($data['state'])+1;
        $this->assign('middle',$middle);
        $this->assign('data',$data);

        $this->display();
    }

    /**
     * 查看所有桩的位置
     * @date: 2015-7-16
     * @author: ZhouWC
     */
    public function index() {
        $data = M("charger")->select();
        foreach($data as $key=>$charger) {
            $data[$key]['count'] = count(M('Outlet')->where(array('chargerid'=>$charger['id']))->select());
        }
        $this->assign("data",$data);
        $this->wap_title = '充电桩';
        $this->display();
    }

    /**
     * 充电下单
     * @author: MC <zhouyibin@seersee.com>
     */
    public function order(){ 
    	$outletid = $_GET['oid'];
    	$mOrder = M('ChargeOrder');
    	$mFansCoupon = M('FansCoupon');


    	if(IS_POST)
    	{
    		//仅仅下单 20150805
    		//1.添加订单
    		$outletid = $_POST['outletid'];
    		$charger = $_POST['charger'];
    		$orderid = neworderid('CD', $this->gOpenId);
    		$order['outletid'] = $outletid;
            $city_charge_price = get_charge_price($outletid);
    		$order['uid'] = 0;
    		$order['openid'] = $this->gOpenId;
    		$order['transid'] = $orderid;
    		$order['create_time'] = $_POST['nowtime'];
    		$order['charger'] = $charger;
    		$order['price'] = $city_charge_price;
    		$order['couponid'] = $_POST['couponid'];
    		//$pay_price = C('CHARGE_PRICE');
            $pay_price =$city_charge_price;
    		$order['status'] = 0;// 0:待支付 1:充电中 2:充电完成  3:已取消
    		$fans_coupon = $mFansCoupon->where(array('id'=>$order['couponid']))->find();
    		$needPay = true;
    		if(0 < $order['couponid']) if($fans_coupon) if($fans_coupon['status'] == 1) $pay_price = 0;
    		if($pay_price <= 0) $needPay = false;
    		$order['pay_price'] = $pay_price;
            $order_use = M("ChargeOrder")->where(array("outletid"=>$outletid,"status"=>1))->find();
            if(!empty($order_use)) {
                if( $order_use['openid']== $this->gOpenId)
                    redirect(U("Fans/order_charge_details/oid/".$order_use["id"]));
                else
                    $this->success('此充电口其他用户正在使用，请重新选择','','',true);
                return;
            }
            if($mOrder->where(array("openid"=>$this->gOpenId,"create_time"=>$_POST['nowtime']))->find()){
                $this->error('请勿重复提交订单');
            }
    		if($needPay)
    		{
    			$oid = $mOrder->add($order);
    			redirect('/wxpay/wap.php?s=/Charge/pay/oid/'.$oid.'.html');
    		}
    		else
    		{
    			$oid = $mOrder->add($order);
    			$this->afterpay_charge($orderid);
    			//2.优惠券使用
    			coupon_used($order['couponid']);
    			$this->success('支付成功',U("Fans/order_charge_details",array('oid'=>$oid)),false,true);
    		}
    	}
    	else 
    	{
	    	$outlet = M('Outlet')->where(array('id'=>$outletid))->find();
            if($outlet)$charger = M('Charger')->where(array('id'=>$outlet['chargerid']))->find();
	        $order = M("ChargeOrder")->where(array("outletid"=>$outletid,"status"=>1))->find();
	        if(!empty($order)) {
                if( $order['openid']== $this->gOpenId)
                    redirect(U("Fans/order_charge_details?oid=".$order["id"]));
                else
	                $this->success('此充电口其他用户正在使用，请重新选择','','',true);
	            return;
	        }
	    	//优惠券
	    	$fans = M('Fans')->where(array('openid'=>$this->gOpenId))->find();
	    	$where['uid'] = $fans['id'];
	    	$where['service_type'] = 3;
	    	$where['status'] = 1;
	    	$where['end_time'] = array('gt',NOW_TIME);
	    	$coupons = M("FansCoupon")->where($where)->find();
	    	$coupons_count = M("FansCoupon")->where($where)->count();
	    	$coupons['face'] = M('Coupon')->where(array('id'=>$coupons['cid']))->getField('face');
	    	$this->assign('coupons',$coupons);
	    	$this->assign('nowtime',NOW_TIME);
	    	$this->assign('coupons_count',$coupons_count);
            $this->assign('outlet',$outlet);
            $this->assign('charger',$charger);
	    	$this->assign('jsapi_sign_package',$this->gWechatObj->getSignPackage());
            $this->assign('charge_city_price',get_charge_price($outletid));
	    	$this->wap_title = "准备充电";
	        $this->display();
    	}
    }

    /**
     * 异常订单处理
     * @author: ZhouWC
     */
    public function checkOrderError() {
        $nowTime = time();
        //充电超过10个小时
        $orderList = M("ChargeOrder")->where(array("status"=>1,"pay_time" => array("LT",$nowTime-10*60*60)))->select();
        $data = array('status'=>2,'end_time'=>time());
        foreach($orderList as $order) {
            $order->save($data);
            M("ChargeOrder")->where(array("oid"=>$order['id']))->save(array("status"=>1));
            $this->sendMsgHandle(2,$order);
        }
        //意外脱落超过15分钟没确认
        //gc_charge_status status 0代表数据有效  1代表数据过期
        $statusList = M('ChargeStatus')->where(array("report"=>9,"status"=>0,"time"=>array("LT",$nowTime-15*60)))->select();

        foreach($statusList as $status) {
            $chargeOrder = M('ChargeOrder')->where(array("status"=>1,"id"=>$status['oid']))->find();
            $chargeOrder->save($data);
            M("ChargeStatus")->where(array("oid"=>$status['oid']))->setField('status',1);
            $this->sendMsgHandle(2,$chargeOrder);
        }
    }

    /**
     * 确定是否本人拔掉插头
     * @author: Ketory <chenzhuozhou@seersee.com>
     */
    public function makesure(){
        $chargeStatus = M('ChargeStatus')->where(array("report"=>"9","oid"=>$_GET['oid'],'status'=>0,'time'=>array('GT',time()-15*60)))->find();
        $chargeOrder = M("ChargeOrder")->where(array("id"=>$_GET['oid'],'status'=>2,"openid"=>$this->gOpenId))->find();
        $openId = $this->gOpenId;

        if($openId != $chargeOrder['openid'])
        {
            $this->success('非本人订单',U('Fans/order_repair'),false,true);exit;
        }

        //消息是否有效
        if( empty($chargeStatus) ) {
            $this->redirect("Fans/order_charge");
        }
        if(IS_POST){
            if($_POST["make"]==0){
                //将充电状态表的9号状态设为失效
                M('ChargeStatus')->where(array("oid"=>$_GET['oid'],"report"=>"9"))->setField("status",1);
                    $this->success("感谢您的支持，欢迎再次使用！",true);
                }
                else{
                //将订单重新设为充电中
                M("ChargeOrder")->where(array("id"=>$_GET['oid']))->setField("status",1);
                    $outlet = M('Outlet')->where(array('id'=>$chargeOrder['outletid']))->find();
                    $charger = M('Charger')->where(array('id'=>$outlet['chargerid']))->find();
                    $url = 'http://121.41.112.29/advice.php?m='.$charger['deviceid'].'&p='.$outlet['code'].'&s=1';
                    log::write('再次通电url='.$url);
                    http_get($url);
                    $this->success("通电命令处理中，成功后将立刻通知您",true);
                }
            }
        $this->wap_title = "异常断开";
        $this->display();


    }
    
    /**
     * 微信统一支付下单(ajax调用)
     * @author: Nice <hupeipei@seersee.com>
     */
    public function prepay() {
        $transid = $_POST['rsid'];
        $price = $_POST['oprice'] * 100;
    	$body = '充电';
    	$data = parent::prepay($body, $transid, $price);
    	echo $data;
    }

    /**
     * 订单支付
     * @date: 2015-7-18
     * @author: BillyZ
     * @return:
     */
    public function pay()
    {
    	$orderid = $_GET['oid'];
    	$mOrder = M('ChargeOrder');
    	$order = $mOrder->where(array('id'=>$orderid))->find();
    	$outlet = M('Outlet')->where(array('id'=>$order['outletid']))->find(); //查询充电口名
    	if($outlet)
    	{

    		$charger = M('Charger')->where(array('id'=>$outlet['chargerid']))->find();
            $this->assign('outlet',$outlet);
    		$this->assign('charger',$charger);
    	} //查询充电桩名

        $unpay_status = 0;
        if (empty($order)) {
            $data['code'] = 0;
            $data['msg'] = '订单不存在！';
            die(json_encode($data));
        }
        else
        {
            if($unpay_status != $order['status'])
            {
                $data['code'] = 0;
                $data['msg'] = '订单状态不正确！';
                die(json_encode($data));
            }
        }
        $requestid = newrequestid('CD',$order['openid']);
        add_payrequest($order['transid'],$requestid);
        if(!empty($df))
        {
            $order['is_df'] = 1;
            $mOrder->save($order);

            $msg = '订单代付发起';
        }
        else
        {
            if(1 == $order['is_df'])
            {
                $order['is_df'] = 0;
                $mOrder->save($order);
            }
        }

    	$this->assign('rid',$requestid);
    	$this->assign('order',$order);
    	$this->wap_title = "订单支付";
    	$this->assign('jsapi_sign_package',$this->gWechatObj->getSignPackage());
        $this->assign('charge_city_price',get_charge_price($order['outletid']));
    	$this->display();
    }
    
    /**
    * 消息发送
    * @date: 2015-7-15
    * @author: BillyZ
    */
    public function sendmsg()
    {
    	$msgs = 'm='.$_GET['m'].',p='.$_GET['p'].',s='.$_GET['s'];
    	Log::write($msgs);
        $m = $_GET['m'];
        $p = $_GET['p'];
        $s = $_GET['s'];

    	//1.找到充电口当前订单
        $chargerid = M("Charger")->where(array("deviceid"=>$m))->getField("id");
        $outletid = M("Outlet")->where(array("chargerid"=>$chargerid,"code"=>$p))->getField("id");
        $order = M("ChargeOrder")->where(array("outletid"=>$outletid,"status"=>1))->find();

        log::write('chargeid='.$chargerid);
        log::write('$outletid='.$outletid);
        log::write('$order.id='.$order['id']);

        if(empty($order))
        die(json_decode(array("status"=>0,"msg"=>"没有发现此充电口相关的订单")));

         //2.给当前订单的用户发送消息
        $this->sendMsgHandle($s,$order);

        //3把状态添加到chargeStatus中
        M('ChargeStatus')->data(array("oid"=>$order['id'],"report"=>$s,"time"=>time(),"status"=>0))->add();

        //4.改变订单状态
        if( $s == '0' || $s == '2' || $s == "9" ) {
            $order['status'] = 2;
            $order['end_time'] = time();
            M("ChargeOrder")->save($order);
                log::write('自动结束用户订单,订单号:'.$order['id']);
            }

        }

    /**
     * 给当前订单的用户发送消息
     * @param $s 消息类型
     * @param $order 订单
     * @author: ZhouWC
     */
    private function sendMsgHandle($s,$order) {
        switch($s) {
            case "0":$val = array("url"=>C('site_url')."/wap.php?s=/Fans/order_charge.html",
                "template_id"=>"tDjn76XyWSlwBKiKkC97EQcnUvobeehRKESnZOJIUv4",
                "first"=>"亲爱的用户，您的项目状态发生变更",
                "keyword1"=>"充电桩充电",
                "keyword2"=>"充电桩已断电",
                "remark"=>"检测到充电口已断电",
                "touser"=>$order['openid']);break;

            case "1":$val = array("url"=>C('site_url')."/wap.php?s=/Fans/order_charge.html",
                "template_id"=>"PBqG7DRWobXhoH44sDuiwOZNSx12nHDZacioC8Prc8Q",
                "first"=>"充电口已通电，充电成功后系统将通过消息再次通知您",
                "keyword1"=>"充电桩充电",
                "keyword2"=>date('Y年n月d日h:i'),
                "remark"=>"充电成功后系统将通过消息再次通知您",
                "touser"=>$order['openid']);break;

            case "2":$val = array("url"=>C('site_url')."/wap.php?s=/Fans/order_charge.html",
                "template_id"=>"tDjn76XyWSlwBKiKkC97EQcnUvobeehRKESnZOJIUv4",
                "first"=>"亲爱的用户，您的项目状态发生变更",
                "keyword1"=>"充电桩充电",
                "keyword2"=>"充电已完成",
                "remark"=>"您的电动车已经充电完成，为了避免长时间充电对电池的损坏，请及时取走电动车！",
                "touser"=>$order['openid']);break;

            case "9":$val = array("url"=>C('site_url')."/wap.php/Charge/makesure/oid/".$order['id'],
                "template_id"=>"tDjn76XyWSlwBKiKkC97EQcnUvobeehRKESnZOJIUv4",
                "first"=>"充电口异常断开","firstColor"=>"#DF1818",
                "keyword1"=>"充电状态",
                "keyword2"=>"充电口异常脱落",
                "remark"=>"可能原因是:充电器松动或者他人恶意拔掉充电线，点击进入重新通电，无处理15分钟后释放本次充电订单","remarkColor"=>"#DF1818",
                "touser"=>$order['openid']);break;

             //TODO 故障时url和touser需在后期确定
            case "5":$val = array("url"=>C('site_url')."/wap.php/Charge/makesure/oid/".$order['id'],
                "template_id"=>"tDjn76XyWSlwBKiKkC97EQcnUvobeehRKESnZOJIUv4",
                "first"=>"充电口出现异常",
                "keyword1"=>"充电口ID:"+$order['outletid'],
                "keyword2"=>"继电器故障",
                "remark"=>"请及时拍出相关人员进行维修",
                "touser"=>"o3g0Gs5Ueri5czuTHNceerHcNr_o");break;

            case "6":$val = array("url"=>C('site_url')."/wap.php/Charge/makesure/oid/".$order['id'],
                "template_id"=>"tDjn76XyWSlwBKiKkC97EQcnUvobeehRKESnZOJIUv4",
                "first"=>"充电口出现异常",
                "keyword1"=>"充电口ID:"+$order['outletid'],
                "keyword2"=>"互感器故障",
                "remark"=>"请及时拍出相关人员进行维修",
                "touser"=>"o3g0Gs5Ueri5czuTHNceerHcNr_o");break;

            case "7":$val = array("url"=>C('site_url')."/wap.php/Charge/makesure/oid/".$order['id'],
                "template_id"=>"tDjn76XyWSlwBKiKkC97EQcnUvobeehRKESnZOJIUv4",
                "first"=>"充电口出现异常",
                "keyword1"=>"充电口ID:"+$order['outletid'],
                "keyword2"=>"漏电",
                "remark"=>"请及时拍出相关人员进行维修",
                "touser"=>"o3g0Gs5Ueri5czuTHNceerHcNr_o");break;
        }
        $msg = $this->getMeg($val);
        if(!is_null($val['url'])) $this->gWechatObj->sendTemplateMessage($msg);
    }

    /*
     * 消息模板
     * @author: ZhouWC
     * @param 各个参数的数组
     * @return: 模板消息的数组
     */
    private function getMeg($val) {
        $msg = array(
            "touser"=>$val['touser'],
            "template_id"=>$val['template_id'],
            "url"=>$val['url'],
            "topcolor"=>"#FF0000",
            "data"=>array(
                "first"=>array(
                    "value"=>$val['first'],
                    "color"=>"#173177"     //参数颜色
                ),
                "keyword1"=>array(
                    "value"=>$val['keyword1'],
                    "color"=>"#173177"
                ),
                "keyword2"=>array(
                    "value"=>$val['keyword2'],
                    "color"=>"#173177"
                ),
                "remark"=>array(
                    "value"=>$val['remark'],
                    "color"=>"#173177"
                )
            )
        );
        if($val['firstColor'])$msg['data']['first']['color']=$val['firstColor'];
        if($val['keyword1Color'])$msg['data']['keyword1']['color']=$val['keyword1Color'];
        if($val['keyword2Color'])$msg['data']['keyword2']['color']=$val['keyword2Color'];
        if($val['remarkColor'])$msg['data']['remark']['color']=$val['remarkColor'];
        return $msg;
    }

}