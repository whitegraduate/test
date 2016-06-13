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



/**
 * 描述：前台维修服务
 * Class MaintainController
 * @package Home\Controller
 */
class RepairController extends WapController {

//    protected function _initialize(){
//        //设置城市服务类型 1:维修 2:保养 3:充电 4:租车 5:二手车
//        $this->assign('serid',1);//当前服务类型为1
//    }
    public function __construct() {
        parent::__construct();
    }

    /**
     * 维修首页
     * @author: MC <zhouyibin@seersee.com>
     */
    public function index(){
        $openid = $this->gOpenId;
        $this->assign('openid', $openid);//绿源软件部插入

        $mFans = M('Fans');
        $fans = $mFans->where(array('openid' => $openid))->find();
        
        $this->assign('fans', $fans);
    	$mOrder = M('RepairOrder');
    	if(IS_POST)
    	{
    		$orderid = neworderid('WX', $this->gOpenId);
    		$repair_type = $_POST['repair_type'];
    		$order['shopid'] = $_POST['shopid'];
    		$order['problem'] = $_POST['problem'];
    		$order['uid'] = 0;
    		$order['openid'] = $this->gOpenId;
    		$order['transid'] = $orderid;
    		if(3 == $repair_type)
    		{
    			$order['latitude'] = $_POST['latitude'];
    			$order['longitude'] = $_POST['longitude'];
    			$order['location'] = $_POST['location'];
    			$order['status'] = 4;
    		}
    		else
    		$order['status'] = 0;//0:已下单 1：已确认开始 2：已还车 3：已取消 4:待抢单
    		$order['create_time'] = $_POST['nowtime'];
    		$order['mobile'] = $_POST['mobile'];
    		$apoint_time = $_POST['apoint_time'];
    		$order['apoint_time'] = strtotime($apoint_time);
    		$order['order_type'] = $repair_type;//1:预约 2：到店 3：一键
            if($mOrder->where(array("create_time"=>$order['create_time'],"openid"=>$this->gOpenId))->find()){
                $this->error("请勿重复提交订单！");
            }
            $oid = $mOrder->add($order);
            parent::sendMsg2Shop($order['shopid'],'您有新的维修订单',$_POST['problem'],date('Y-m-d H:i:s',$order['create_time']),'未计算',true,'请登录门店后台进行处理');
            if(3 == $repair_type)
                $this->success("提交成功!",U('Fans/order_repair_details',array('oid'=>$oid)),false,false);
            else
                $this->success("提交成功!",U('Fans/order_repair_details',array('oid'=>$oid)),false,true);
    	}
    	else
    	{
	    	$lstShop = get_shops('WX');
	    	$this->assign('lstShop',$lstShop);
	    	//是否有待抢单状态的订单
	    	$orderUngrab = $mOrder->where(array('openid'=>$this->gOpenId,'status'=>4))->order('id desc')->find();
	    	if($orderUngrab)
	    	{
	    		$this->assign('grab',$orderUngrab);
	    		$this->redirect("Fans/order_repair_details",array("oid"=>$orderUngrab['id']));
	    	}
            $this->assign('nowtime',NOW_TIME);
	    	$this->wap_title = '我要维修';
	        $this->display();
    	}
    }

    /**
    * 预约维修
    * @date: 2015-7-19
    * @author: BillyZ
    * @return:
    */
    public function apointment()
    {
    	if(IS_POST)
    	{
    		$mOrder = M('RepairOrder');

    		$orderid = neworderid('WX', $this->gOpenId);
    		$order['shopid'] = $_POST['shopid'];
    		$order['problem'] = $_POST['problem'];
    		$order['uid'] = 0;
    		$order['openid'] = $this->gOpenId;
    		$order['transid'] = $orderid;
    		$order['status'] = 0;//0:已下单 1：已确认开始 2：已还车 3：已取消
    		$order['create_time'] = NOW_TIME;

    		$apoint_time = $_POST['apoint_time'];
    		log::write('apoint_time='.$apoint_time);
    		$order['apoint_time'] = strtotime($apoint_time);

    		log::write('apoint_time2='.$order['apoint_time']);
    		$order['order_type'] = 1;//1:预约 2：到店
    		$mOrder->add($order);

    		$this->success("预约成功!",'',false,true);
    	}
    	else
    	{
    		$lstShop = M('Shop')->select();
    		$this->assign('lstShop',$lstShop);

	    	$this->wap_title = '预约维修';
	    	$this->display();
    	}
    }

    /**
     * 到店维修
     * @date: 2015-7-19
     * @author: BillyZ
     * @return:
     */
    public function inshop()
    {
    	if(IS_POST)
    	{
    		$mOrder = M('RepairOrder');

    		$orderid = neworderid('WX', $this->gOpenId);
    		$order['shopid'] = $_POST['shopid'];
    		$order['problem'] = $_POST['problem'];
    		$order['uid'] = 0;
    		$order['openid'] = $this->gOpenId;
    		$order['transid'] = $orderid;
    		$order['status'] = 0;//0:已下单 1：已确认开始 2：已还车 3：已取消
    		$order['create_time'] = NOW_TIME;
    		//$order['apoint_time'] =  TODO
    		$order['order_type'] = 2;//1:预约 2：到店
    		$mOrder->add($order);

    		$this->success("下单成功!",'',false,true);
    	}
    	else
    	{
    		$lstShop = M('Shop')->select();
    		$this->assign('lstShop',$lstShop);

    		$this->wap_title = '到店维修';
    		$this->display();
    	}
    }

    /**
    * 维修下单
    * @date: 2015-7-19
    * @author: BillyZ
    * @return:
    */
    public function order()
    {
    	if(IS_POST)
    	{
    		$this->success("下单成功!",'',false,true);
    	}
    	else
    	{
    		$this->assign('product',$product);

            $lstShop = get_shops('WX');
    		$this->assign('lstShop',$lstShop);

    		$this->assign('jsapi_sign_package',$this->gWechatObj->getSignPackage());
    		$this->wap_title = '维修下单';
    		$this->display();
    	}
    }

    /**
     * 维修订单支付
     * @date: 2015-7-18
     * @author: BillyZ
     * @return:
     */
    public function pay()
    {
        $orderid = $_GET['oid'];
        $mOrder = M('RepairOrder');
        $order = $mOrder->where(array('id'=>$orderid))->find();
        $order['shop'] = get_shop_id($order['shopid']);

        $unpay_status = 1;
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

        //MC 20150819
        //代付冲突处理 开始
        $requestid = newrequestid('WX',$order['openid']);
        //添加请求记录、避免支付途中订单号被篡改问题
        add_payrequest($order['transid'],$requestid);
        if(!empty($df))
        {
            $order['is_df'] = 1;
            $mOrder->save($order);
        }
        else
        {
            if(1 == $order['is_df'])
            {
                $order['is_df'] = 0;
                $mOrder->save($order);
            }
        }

        $this->assign('rid', $requestid);
        $this->assign('order',$order);
        $this->wap_title = "订单支付";
        $this->assign('jsapi_sign_package',$this->gWechatObj->getSignPackage());
        $this->display();
    }

    /**
     * 微信统一支付下单(ajax调用)
     * @author: Nice <hupeipei@seersee.com>
     */
    public function prepay() {
        //todo 价格用查询调用，明文传有安全隐患，有空改  Ketory
        $transid = $_POST['rsid'];
        $price = $_POST['oprice'] * 100;
    	$body = '维修';
    	$data = parent::prepay($body, $transid, $price);
    	echo $data;
    }

    /**
     * 支付成功后调用
     * @author: Nice <hupeipei@seersee.com>
     */
    public function afterpay() {
    }

    /**
    * 确认订单（价格为0订单的处理）
    * @date: 2015-8-16
    * @author: BillyZ
    * @return:
    */
    public function confirm()
    {
    	$orderid = I('oid');
    	$mOrder = M('RepairOrder');
    	$order = $mOrder->where(array('id'=>$orderid))->find();
    	$data['flag'] = 0;
    	if($order)
    	{
    		if('1' == $order['status'] && 0 == $order['pay_price'])
    		{
    			$order['status'] = 2;
    			$order['pay_time'] = NOW_TIME;
    			$mOrder->save($order);


    			$data['flag'] = 1;
    			$data['msg'] = "确认成功";
    		}
    		else
    			$data['msg'] = "状态不正确";
    	}
    	else
    		$data['msg'] = "信息不存在";

    	$this->ajaxReturn($data);
    }
}