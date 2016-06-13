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


/**
 * 描述：首页控制器
 * Class IndexController
 * @package Home\Controller
 */
class GreenApiController extends Controller {

	/**
	 *
	 */
	public function coupon_api()
	{
		$openid= I('openid');
		$count = I('count',1);
		$cid= I('cid');
		$timespan = I('timespan',time());
		$code= I('code');

		$result = array();
		$result["result"]=0;
		//$realcode = think_encrypt($timespan,"repair_api");
		$realcode = $this->encrypt($timespan,"repair_api");
		if($realcode==$code)
		{
			if($openid)
			{
				$uid = M('Fans')->where(array('openid'=>$openid))->getField("id");
				$Coupon_info = M('Coupon')->where(array('id'=>$cid))->find();

				$mFansCoupon=M('FansCoupon');
				$FansCoupon_info['uid']=$uid;
				$FansCoupon_info['cid'] = $cid;
				$FansCoupon_info['cname'] = $Coupon_info['name'];
				$FansCoupon_info['total_times']=1;
				$FansCoupon_info['remain_times']=1;
				$FansCoupon_info['status']=1;
				$FansCoupon_info['service_type']=1;
				$FansCoupon_info['code']="Api";
				$FansCoupon_info['face']=$Coupon_info['face'];


				for($i=0;$i<$count;$i++)
				{
					$mFansCoupon->add($FansCoupon_info);
				}
				$result["result"]=1;
			}
		}

		$this->ajaxReturn($result,'JSON');
	}

	/**
	 *
	 */
	public function coupon_action_api()
	{
		$openid= I('openid');
		$count = I('count',1);
		$action= I('action');
		$timespan = I('timespan',time());
		$code= I('code');

		$result = array();
		$result["result"]=0;
		//$realcode = think_encrypt($timespan,"repair_api");
		$realcode = $this->encrypt($timespan,"repair_api");
		if($realcode==$code)
		{
			if($openid)
			{
				$uid = M('Fans')->where(array('openid'=>$openid))->getField("id");
				if($uid>0)
				{
					for($i=0;$i<$count;$i++)
					{
						giveCoupon($uid,$action);
						$result["result"]=1;
					}
				}

			}
		}

		$this->ajaxReturn($result,'JSON');
	}

	private function encrypt($timespan,$key)
	{
		$realcode = md5(strval($timespan).$key);
		return $realcode;
	}
}