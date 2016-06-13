<?php
// +----------------------------------------------------------------------
// | 视界无限 - SEERSEE
// +----------------------------------------------------------------------
// | Copyright (c) 2015
// +----------------------------------------------------------------------
// | Author: MC <zhouyibin@seersee.com>
// +----------------------------------------------------------------------

namespace Wap\Controller;
use Think\Controller;
use Think\Log;
use OT\Wechat\Wechat;

/**
 * 描述：Job控制器
 * Class JobController
 * @package Home\Controller
 */
class JobController extends Controller {
 
	public function index()
	{
		log::job_info('job执行begin@'.date('Y-m-d H:i:s',NOW_TIME));
		//echo 'job@'.date('Y-m-d H:i:s',NOW_TIME);
		$this->do_error_charge_order();
		$this->do_expire_coupon();
		$this->maintain_remind();
		log::job_info('job执行end@'.date('Y-m-d H:i:s',NOW_TIME));
	}
	
	/**
	* 取消未支付订单
	* @date: 2015-8-21
	* @author: BillyZ
	* @return:
	*/
	private function cancel_unpay_order()
	{
		
	}
	
	/**
	* 未抢单超时订单处理
	* @date: 2015-8-21
	* @author: BillyZ
	* @return:
	*/
	private function do_ungrab_repair_order()
	{
		
	}
	
	/**
	* 充电异常订单处理
	* @date: 2015-8-21
	* @author: BillyZ
	* @return:
	*/
	private function do_error_charge_order()
	{
		$time1 = NOW_TIME;
		log::job_info('do_error_charge_order执行begin@'.date('Y-m-d H:i:s',$time1));
		$hours_finish = 10;
		$minutes_unconfirm = 15;
		$nowTime = time();
		$mOrder = M("ChargeOrder");
		$mStatus = M("ChargeStatus");
		//充电超过10个小时
		$orderList = $mOrder->where(array("status"=>1,"pay_time" => array("LT",$nowTime-$hours_finish*60*60)))->select();
		
		$data = array('status'=>2,'end_time'=>time());
		foreach($orderList as $order) {
			$mOrder->where(array('id'=>$order['id']))->save($data);
			$mStatus->where(array("oid"=>$order['id'],'report'=>9))->save(array("status"=>1));
			//$this->sendMsgHandle(2,$order);
		}
		//意外脱落超过15分钟没确认
		//gc_charge_status status 0代表数据有效  1代表数据过期
		$statusList = $mStatus->where(array("report"=>9,"status"=>0,"time"=>array("LT",$nowTime-$minutes_unconfirm*60)))->select();
		
		foreach($statusList as $status) {
			$mOrder->where(array("status"=>1,"id"=>$status['oid']))->save($data);
			$mStatus->where(array("oid"=>$status['oid'],'report'=>9))->setField('status',1);
			//$this->sendMsgHandle(2,$chargeOrder);
		}
		
		//TODO 增加处理日志
		$time2 = NOW_TIME;
		log::job_info('do_error_charge_order执行end@'.date('Y-m-d H:i:s',$time2).',总时长：'.($time2 - $time1).',处理超时充电订单：'.count($orderList).'个，处理超时确认订单：'.count($statusList).'个');
	}

	/**
	 * 处理过期优惠券
	 */
	private function do_expire_coupon()
{
		$mFanscoupon = M('FansCoupon');
		$coupons = $mFanscoupon->where(array('end_time'=>array('lt',NOW_TIME),'status'=>1))->limit(1000)->select();

		log::job_info('do_expire_coupon,过期优惠券数量：'.count($coupons));
		foreach($coupons as $cp)
		{
			$mFanscoupon->where(array('id'=>$cp['id']))->setField('status',3);
		}
	}

	/**
	 * 保养提醒订单自动推送
	 */
	private function maintain_remind()
	{
		//1.找出所有需要发的提醒订单
		$mRemindlog = M('RemindLog');
		$where['remindtime'] = array('lt',NOW_TIME);
		$where['status'] = 0;
		$logs = $mRemindlog->where($where)->select();
		foreach($logs as $log)
		{
			//2.改变log状态
			$log['status'] = 1;
			$log['sendtime'] = NOW_TIME;
			$mRemindlog->save($log);

			//3.给订单的买家发送消息推送
			if(empty($log['openid']))
				$openid = "o3g0Gs5Ueri5czuTHNceerHcNr_o";
			else
				$openid = $log['openid'];

			//会员姓名
			$fans = get_obj_fans_openid($openid);
			$fansname = $fans['nickname'];
			//上次保养时间
			$end_time = M('MaintainOrder')->field('end_time')->where(array('openid'=>$openid,'status'=>2, 'order_type'=>1))->order('end_time desc')->find();
			//到期时间
			$expiretime = $log['expiretime'];

			$msg = array(
				"touser"=>$openid,
				"template_id"=>"yBBT8Fp8zVf8sE9U5kByYm8EUsUO0nf931PxVgsTJBU",
				"url"=>C('site_url')."/wap.php?s=/Maintain/remind/oid/".$log['oid'].'.html',
				"topcolor"=>"#FF0000",
				"data"=>array(
					"first"=>array(
						"value"=>"您好，您的电动车部分部件即将过保",
						"color"=>"#173177"
					),
					"keyword1"=>array(
						"value"=>$fansname,
						"color"=>"#173177"
					),
					"keyword2"=>array(
						"value"=>$end_time,
						"color"=>"#173177"
					),
					"keyword3"=>array(
						"value"=> date('Y-m-d',$expiretime),
						"color"=>"#173177"

					),
					"remark"=>array(
						"value"=>"建议您在近期到滴啊滴做免费检测！以便在保修范围享受保修实惠！",
						"color"=>"#173177"
					)
				)
			);

			$this->gWechatObj = new Wechat(C('WEIXIN_CONFIG'));
			$this->gWechatObj->sendTemplateMessage($msg);
		}
	}
}