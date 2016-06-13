<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: MC <zhouyibin@seersee.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use OT\Wechat\Wechat;
use Think\Log;
/**
 * 支付控制器
 * @author MC <zhouyibin@seersee.com>
 */
class PayController extends AdminController {

    /**
     * 订单退款
     * @date: 2015-10-13
     * @author: BillyZ
     * @return:
     */
    public function refund()
    {
    	$transid = $_GET['transid'];
    	$memo = $_POST['memo'];
    	
    	$model = 'ChargeOrder';
    	$flag = 'CD';
    	$order_type = 3;
    	$valid_status = 1;
    	$cancel_status = 3;
    	
    	if(!empty($transid))
    	{
    		$flag = substr($transid,0,2);
    	}
    	else
    		$this->error('订单信息错误');
    	
    	$model = get_model_by_transid($transid);
    	
    	if(empty($model))
    		$this->error('订单信息错误!');
    		
    	switch($flag)
    	{
    		case "CD":
    			$order_type = 3;
    			$valid_status = 1;
    			break;
    		case "BY":
    			$order_type = 2;
    			$valid_status = 2;
    			break;
    		case "WX":
    			$order_type = 1;
    			$valid_status = 2;
    			break;
    		case "XC":
    			$order_type = 5;
    			$valid_status = 2;
    			break;
    		case "ZC":
    			$order_type = 4;
    			$valid_status = 4;
    			break;
    		default:
    			break;
    	}
    	
    	$order = M($model)->where(array('transid'=>$transid))->find();
    	if($order)
    	{
    		if($valid_status == $order['status'] && 0 < $order['pay_price'] && 0 < $order['pay_time'])
    		{
    			$result = $this->refund_order($order['transid'], $order['pay_price'] * 100, $order['pay_price'] * 100, session('user_auth.username'));
    
    			log::write('code='.$result['return_code']);
    			log::write('msg='.$result['return_msg']);
    			log::write('result_code='.$result['result_code']);
    			log::write('err_code='.$result['err_code']);
    			log::write('err_code_des='.$result['err_code_des']);
    			 
    			if('SUCCESS' == $result['return_code'])
    			{
    				if('SUCCESS' == $result['result_code'])
    				{
    					//已支付状态 才能退款
    					$order['status'] = $cancel_status;//已取消
    					M($model)->save($order);
    					
    					//1维修 2保养 3充电 4租车 5精品
    					logorder(0, $order['id'], $order_type, session('user_auth.username')."操作订单".$order['transid']."退款:".$memo);
    						
    					$this->success('退款成功');
    				}
    				else
    					$this->error('退款失败，原因是：'.$result['err_code_des']);
    			}
    			else
    			{
    				$this->error('请求失败，原因是：'.$result['return_msg']);
    			}
    		}
    		else
    			$this->error('订单状态不正确');
    	}
    	else
    		$this->error('订单不存在');
    }
    
    /**
     * 投诉处理
     * @date: 2015-10-13
     * @author: BillyZ
     * @return:
     */
    public function do_complain()
    {
    	$complainid = $_GET['id'];
    	$memo = $_POST['memo'];
    	$status = $_POST['status'];
    	
    	$mComplain = M('OrderComplain');
    	$complain = $mComplain->where(array('id'=>$complainid))->find();
    	
    	switch ($status)
    	{
    		case "3":
    			//取消订单并退款
    			$result = parent::cancel_order($complain['transid'], $memo, true);
    			break;
    		case "4":
    			//完成投诉，不改变订单状态
    			$result['code'] = 1;
    			break;
    		case "2":
    			//取消订单，不改变订单状态
    			$result = parent::cancel_order($complain['transid'], $memo, false);
    			break;
    	}
    	
    	if(1 == $result['code'])
    	{
	    	$complain['status'] = $status;
	    	$complain['op_memo'] = $memo;
	    	$complain['op_user'] = session('user_auth.username');
	    	$complain['finish_time'] = NOW_TIME;
	    	$mComplain->save($complain);

	    	$this->success('投诉处理成功');
    	}
        else
    		$this->error('处理失败，原因是:'.$result['msg']);
        	
    }
    
}