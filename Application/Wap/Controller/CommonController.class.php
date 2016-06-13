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
use OT\Wechat\Wechat;
use Think\Log;
use User\Api\UserApi;


/**
 * 描述：微信接口控制器
 * Class WeixinController
 * @package Home\Controller
 */
class CommonController extends Controller {
    protected $weObj; 
    
    protected function _initialize(){
    	if (!$this->weObj) {
    		$this->weObj = new Wechat(C('WEIXIN_CONFIG'));
    		Log::weixin_info('创建了Wechat对象');
    	}
        $config = api('Config/lists');
        C($config); //添加配置
    }

    /**
    * 请求回调处理-充电
    * @date: 2015-7-15
    * @author: BillyZ
    * @return:
    */
    protected function afterpay_charge($orderid)
    {
    	Log::write('afterpay_charge_begin');
    	$order = M('ChargeOrder')->where(array('transid'=>$orderid))->find();
    	//1.修改订单状态
    	if('0' == $order['status'])
    	{
    		$order['status'] = 1;
    		$order['pay_time'] = NOW_TIME;
    		M('ChargeOrder')->save($order);
            if($order['is_df']){
                logorder(0, $orderid, 2, '订单由:'.$order['transid'].'发起代付');
            }
    		//2.发送模板消息通知用户
    		if(empty($order['openid']))
    			$openid = "o3g0Gs5Ueri5czuTHNceerHcNr_o";
    		else
    			$openid = $order['openid'];

            //TODO 价格定死了，有机会要恢复 Ketory
    	    $data = array(
    			    "first"=>array("value"=>"您的订单已经支付成功,请确保您的插头已经连接成功,充电桩稍后将为您充电","color"=>"#173177"),
    				"orderMoneySum"=>array("value"=> round(1,2)."元","color"=>"#173177"),
    				"orderProductName"=>array("value"=>"充电桩充电","color"=>"#173177"),
    		   		"Remark"=>array("value"=>"网络连接中，系统将在1分钟之内为您通电","color"=>"#173177")
    		   	);
    		$tempid = "FcTw7GMzFfXqJufoe8Y6Fyh7jkAOWzKrHaSVQCBVR3M";
    		$url = C('site_url')."/wap.php?s=/Fans/order_charge.html";
    		$this->sendTempMsg($openid,$tempid,$url,$data);
    		//3.通知指定充电口通电
    		//获取充电口
    		$outlet = M('Outlet')->where(array('id'=>$order['outletid']))->find();
    		$charger = M('Charger')->where(array('id'=>$outlet['chargerid']))->find();
    		$url = 'http://121.41.112.29/advice.php?m='.$charger['deviceid'].'&p='.$outlet['code'].'&s=1';
    		log::write('通电url='.$url);
    		http_get($url);
    	}
    }
    

    /**
     * 请求回调处理-租车
     * @date: 2015-7-15
     * @author: BillyZ
     * @return:
     */
    protected function afterpay_rent($orderid)
    {
    	$order = M('RentbikeOrder')->where(array('transid'=>$orderid))->find();
    	//1.修改订单状态
    	if('2' == $order['status'])
    	{
    		$money_shop = C('MONEY_SHOP',null,0.974);
    		$money_carrier = C('MONEY_CARRIER',null,0.01); 

    		$order['money_shop'] = round($order['pay_price'] * $money_shop,2);
    		$order['money_carrier'] = round($order['pay_price'] * $money_carrier,2);
    		$order['money_gc'] = $order['pay_price'] - $order['money_shop'] - $order['money_carrier'];
    		
    		$order['status'] = 4;
    		$order['pay_time'] = NOW_TIME;
    		
    		if(!M('RentbikeOrder')->save($order))
    		{
    			log::weixin_info('订单保存失败@'.$orderid);
    		}

    		//2.发送模板消息通知用户
    		if(empty($order['openid']))
    			$openid = "o3g0Gs5Ueri5czuTHNceerHcNr_o";
    		else
    			$openid = $order['openid'];
    		 
    		$data = array(
    				"first"=>array("value"=>"您的订单已经支付成功","color"=>"#173177"),
    				"orderMoneySum"=>array("value"=>round($order['price'],2),"color"=>"#173177"),
    				"orderProductName"=>array("value"=>'租车',"color"=>"#173177"),
    				"Remark"=>array("value"=>"点击查看详情对本次订单服务进行评价","color"=>"#173177")
    		);
    		$tempid = "FcTw7GMzFfXqJufoe8Y6Fyh7jkAOWzKrHaSVQCBVR3M";
    		$url = C('site_url')."/wap.php?s=/Fans/remark/flag/ZC/oid/".$order['id'].".html";
    		$this->sendTempMsg($openid,$tempid,$url,$data);
    		//3.关闭指定车辆的MOS开关 TODO
    		
    		//$bike = M('Rentbike')->where(array('id'=>$order['bikeid']))->find();
    	}
    	else
    		log::weixin_info('订单状态不正确'.$orderid.',status='.$order['status']);
    }
    

    /**
     * 请求回调处理-保养
     * @date: 2015-7-15
     * @author: BillyZ
     * @return:
     */
    protected function afterpay_maintain($orderid)
    {
    	$mOrder = M('MaintainOrder');
    	$order = $mOrder->where(array('transid'=>$orderid))->find();
    	//1.修改订单状态
    	if('1' == $order['status']) {
			if (2 == $order['order_type'])
			{
				//保养提醒-写提醒队列
				//TODO 订单里写的brandid是bikeid 获取需要继续优化
				$bikeid = $order['brandid'];

				$bike = M('FansBike')->where(array('id'=>$bikeid))->find();
				$brandid = $bike['brandid'];//车辆id
				$buytime = $bike['buy_time'];//车辆购买时间
				//2.这个品牌所有部件的提醒时间
				$list = M('MaintainPartsRemind')->distinct(true)->field('remind_month')->where(array('brandid'=>$brandid))->order('remind_month')->select();

				//提早7天通知
				$forespan = 7;

				for($i=0;$i<count($list);$i++)
				{
					//添加发送日志
					if($buytime <= 0)
						$buytime = NOW_TIME;

					$expiretime = $buytime + $list[$i]['remind_month'] * 30 * 24 * 3600;

					$remindtime = $expiretime - $forespan * 24 * 3600;

					add_remindlog($order['id'],$order['openid'],$remindtime,$expiretime);
				}
			}
			else
			{
				//普通保养-分账
				$money_shop = C('MONEY_SHOP',null,0.974);
				$money_carrier = C('MONEY_CARRIER',null,0.01);

				$order['money_shop'] = round($order['pay_price'] * $money_shop,2);
				$order['money_carrier'] = round($order['pay_price'] * $money_carrier,2);
				$order['money_gc'] = $order['pay_price'] - $order['money_shop'] - $order['money_carrier'];
			}
    		$order['status'] = 2;
    		$order['pay_time'] = NOW_TIME;
    		$mOrder->save($order);
    		
    		//1.5 将对应的券使用
    		if(0 < $order['couponid'])
    		{
    			M('FansCoupon')->where(array('id'=>$order['couponid']))->setField('status',2);
    		}
    		
    		//2.发送模板消息通知会员
    		if(empty($order['openid']))
    			$openid = "o3g0Gs5Ueri5czuTHNceerHcNr_o";
    		else
    			$openid = $order['openid'];
    		 
    		$data = array(
    				"first"=>array("value"=>"您的订单已经支付成功","color"=>"#173177"),
    				"orderMoneySum"=>array("value"=>round($order['pay_price'],2),"color"=>"#173177"),
    				"orderProductName"=>array("value"=>'车辆保养',"color"=>"#173177"),
    				"Remark"=>array("value"=>"点击查看详情","color"=>"#173177")
    		);
    		$tempid = "FcTw7GMzFfXqJufoe8Y6Fyh7jkAOWzKrHaSVQCBVR3M";
    		$url = C('site_url')."/wap.php?s=/Fans/order_maintain_details/oid/".$order['id'].".html";
    		$this->sendTempMsg($openid,$tempid,$url,$data);
    		
    		//3.发送模板消息通知门店
    		$this->sendMsg2Shop($order['shopid'],'您有新的保养订单','车辆保养',date('Y-m-d H:i:s',$order['create_time']),$order['price'],true,'请登录门店后台进行处理');
    	}
    }


    /**
     * 请求回调处理-维修
     * @date: 2015-7-15
     * @author: BillyZ
     * @return:
     */
    protected function afterpay_repair($orderid)
    {
    	$mOrder = M('RepairOrder');
    	$order = $mOrder->where(array('transid'=>$orderid))->find();
    	//1.修改订单状态 1->2
    	if('1' == $order['status'])
    	{
    		$money_shop = C('MONEY_SHOP',null,0.974);
    		$money_carrier = C('MONEY_CARRIER',null,0.01); 

    		$order['money_shop'] = round($order['pay_price'] * $money_shop,2);
    		$order['money_carrier'] = round($order['pay_price'] * $money_carrier,2);
    		$order['money_gc'] = $order['pay_price'] - $order['money_shop'] - $order['money_carrier'];
    		
    		$order['status'] = 2;
    		$order['pay_time'] = NOW_TIME;
    		$mOrder->save($order);
    		
    		//2.发送模板消息通知用户
    		if(empty($order['openid']))
    			$openid = "o3g0Gs5Ueri5czuTHNceerHcNr_o";
    		else
    			$openid = $order['openid'];
    		 
    		$data = array(
    				"first"=>array("value"=>"您的订单已经支付成功","color"=>"#173177"),
    				"orderMoneySum"=>array("value"=>round($order['price'],2),"color"=>"#173177"),
    				"orderProductName"=>array("value"=>$order['problem'].'维修',"color"=>"#173177"),
    				"Remark"=>array("value"=>"点击查看详情对本次服务进行评价","color"=>"#173177")
    		);
    		$tempid = "FcTw7GMzFfXqJufoe8Y6Fyh7jkAOWzKrHaSVQCBVR3M";
    		$url = C('site_url')."/wap.php?s=/Fans/remark/flag/WX/oid/".$order['id'].".html";
    		$this->sendTempMsg($openid,$tempid,$url,$data);
    	}
    }

    /**
     * 请求回调处理-商城
     * @return:
     */
    protected function afterpay_malltain($orderid)
    {
        $mOrder = M('MallOrder');
        $order = $mOrder->where(array('orderid'=>$orderid))->find();
        //1.修改订单状态
        if('0' == $order['status'])
        {
    		$money_shop = C('MONEY_SHOP',null,0.974);
    		$money_carrier = C('MONEY_CARRIER',null,0.01); 

    		$order['money_shop'] = round($order['price'] * $money_shop,2);
    		$order['money_carrier'] = round($order['price'] * $money_carrier,2);
    		$order['money_gc'] = $order['price'] - $order['money_shop'] - $order['money_carrier'];
            $order['status'] = 2;
            $order['pay_time'] = NOW_TIME;

            
            //对存货数量进行减少
            if(!M('Mall')->where(array('id'=>$order['mallid']))->setDec('quantity')){
                log::write('存货数量递减失败');
            }

            //执行动作
            $mall=M('Mall')->where(array('id'=>$order['mallid']))->find();
            switch($mall['doaction']){
                case "add_20_pieces_of_charge_coupon":
                    $uid = M("Fans")->where(array("openid"=>$order['openid']))->getField("id");
                    give_coupon_one($uid,9,20);//加20次充电优惠券
                    logorder(0,$order['id'],5,"20张充电优惠券发放成功");
                    $order['status']=1;
                    break;
            }
            $mOrder->save($order);

            //2.发送模板消息通知用户
            if(empty($order['openid'])) $openid = "o3g0Gs5Ueri5czuTHNceerHcNr_o";
            else $openid = $order['openid'];
             
            $data = array(
            		"first"=>array("value"=>"您的订单已经支付成功","color"=>"#173177"),
            		"orderMoneySum"=>array("value"=>round($order['price'],2),"color"=>"#173177"),
            		"orderProductName"=>array("value"=>$mall['title'],"color"=>"#173177"),
            		"Remark"=>array("value"=>"点击查看详情","color"=>"#173177")
            );
            $tempid = "FcTw7GMzFfXqJufoe8Y6Fyh7jkAOWzKrHaSVQCBVR3M";
            $url = C('site_url')."/wap.php?s=/Fans/order_mall_details/oid/".$order['id'].".html";
            $this->sendTempMsg($openid,$tempid,$url,$data);

            //3.发送模板消息通知门店
            $this->sendMsg2Shop($order['shopid'],'您有新的精品订单',$mall['title'],date('Y-m-d H:i:s',$order['create_time']),$order['price'],true,'请登录门店后台进行处理');
        }
    }

    
    /**
    * 给门店发送订单通知
    * @date: 2015-8-26
    * @author: BillyZ
    * @return:
    */
    protected function sendMsg2Shop($shopid,$title,$product,$createtime,$price,$paid,$remark)
    {
    	log::write('sendMsg2Shop1,');
    	$shop = get_obj_shop($shopid);
    	if($shop)
    	{
    	log::write('sendMsg2Shop2,');
    		if(!empty($shop['openid']))
    		{
    	log::write('sendMsg2Shop3,');
		    	$tempid = "Bp4BusuZ9Nt39i1C9ETKhC6HIZxmA0UCtu-hCtdYtLQ";
		    	$url = C('site_url')."/admin.php";
    			$paid_str = $paid?'已支付':'待支付';
    			$data = array(
    					"first"=>array("value"=>$title,"color"=>"#173177"),
    					"keyword1"=>array("value"=>$shop['name'],"color"=>"#173177"),
    					"keyword2"=>array("value"=>$product,"color"=>"#173177"),
    					"keyword3"=>array("value"=>$createtime,"color"=>"#173177"),
    					"keyword4"=>array("value"=>$price,"color"=>"#173177"),
    					"keyword5"=>array("value"=>$paid_str,"color"=>"#173177"),
    					"remark"=>array("value"=>$remark,"color"=>"#173177")
    			);

    			log::write('sendMsg2Shop4,');
    			$this->sendTempMsg($shop['openid'],$tempid,$url,$data);
    	log::write('sendMsg2Shop5,');
    		}
    	log::write('sendMsg2Shop6,');
    	}
    	log::write('sendMsg2Shop7,');
    }
    
    /***************************************************************************************************
     *                                      内部私有方法定义
     *************************************************************************************************/

    /**
     * 发送模板消息
     * @date: 2015-7-3
     * @author: BillyZ
     * @return:
     */
    private function sendTempMsg($openid,$tempid,$url,$data)
    {
    	$msg = array(
    			"touser"=>$openid,
    			"template_id"=>$tempid,
    			"url"=>$url,
    			"topcolor"=>"#FF0000",
    			"data"=>$data
    	);
    	 
    	$this->gWechatObj = new Wechat(C('WEIXIN_CONFIG'));
    	$this->gWechatObj->sendTemplateMessage($msg);
    } 

    /**
    * 发放优惠券
    * @date: 2015-8-5
    * @author: BillyZ
    * @return:
    */
	private function giveCoupon($uid)
	{
		//1.查询注册需要发放的优惠券
		$lstConfigs = M('CouponConfig')->where(array('actions'=>1,'status'=>1))->select();
	
		if($lstConfigs)
		{
			$mFansCoupon = M('FansCoupon');

			foreach($lstConfigs as $config)
			{
				//2.发放(支持多张)
				for($i=0;$i<$config['num'];$i++)
				{
					$fans = get_obj_fans($uid);
					$coupon = get_obj_coupon($config['couponid']);
					$fans_coupon['uid'] = $uid;
					$fans_coupon['cid'] = $config['couponid'];
					$fans_coupon['cname'] = $coupon['name'];
					$fans_coupon['total_times'] = 1;
					$fans_coupon['remain_times'] = 1;
					$fans_coupon['create_time'] = NOW_TIME; 
					$fans_coupon['end_time'] = $fans_coupon['create_time'] + $coupon['days'] * 24 * 3600;
					$fans_coupon['status'] = 1;
					$fans_coupon['service_type'] = $coupon['service_type'];
					$fans_coupon['code'] = new_coupon_id($coupon['service_type'], $fans['openid']);	
					$mFansCoupon->add($fans_coupon);
				}
			}
		}
	}
        
    protected function lists_join ($model,$where=array(),$order='',$field=true,$join="",$joinField="b.*"){
        $options    =   array();
        $REQUEST    =   (array)I('request.');
        if(is_string($model)){
            $model  =   M($model);
        }

        $OPT        =   new \ReflectionProperty($model,'options');
        $OPT->setAccessible(true);

        $pk         =   $model->getPk();
        if($order===null){
            //order置空
        }else if ( isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']),array('desc','asc')) ) {
            $options['order'] = '`'.$REQUEST['_field'].'` '.$REQUEST['_order'];
        }elseif( $order==='' && empty($options['order']) && !empty($pk) ){
            $options['order'] = $pk.' desc';
        }elseif($order){
            $options['order'] = $order;
        }
        unset($REQUEST['_order'],$REQUEST['_field']);
        if(empty($where)){
            $where  =   array('a.status'=>array('egt',0));
        }
        if( !empty($where)){
            $options['where']   =   $where;
        }
        $options      =   array_merge( (array)$OPT->getValue($model), $options );
        $total        =   $model->alias("a")->where($options['where'])->count();

        if( isset($REQUEST['r']) ){
            $listRows = (int)$REQUEST['r'];
        }else{
            $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        }
        $page = new \Think\Page($total, $listRows, $REQUEST);
        if($total>$listRows){
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $p =$page->show();
        $this->assign('_page', $p? $p: '');
        $this->assign('_total',$total);
        $options['limit'] = $page->firstRow.','.$page->listRows;

        $model->setProperty('options',$options);
        if($join){
            $sqlData = $model->alias("a")->field("a.*,".$joinField)->join($join)->select();
        }
        else{
            $sqlData = $model->alias("a")->field($field)->select();
        }
        return $sqlData;
    }
}