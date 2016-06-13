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
 * 描述：门店
 * Class ShopController
 * @package Home\Controller
 */
class ShopController extends WapController { 

    public function __construct() {
        parent::__construct(); 
    }

    /**
    * 查看所有门店
    * @author: BillyZ
    * @return:
    */
    public function index() {
        $lstShop =get_shops('ALL');
        $this->assign("lstShop",$lstShop);
        $this->wap_title = '网点';
        $this->display();
    }
    
    /**
    * 门店
    * @author: BillyZ
    * @return:
    */
    public function shop()
    {
    	$shopid = I('sid');
    	$mShop = M('Shop');
    	$shop = $mShop->field(true)->find($shopid);
    	$this->assign('shop',$shop);
    	$this->wap_title = $shop['name'];
    	$this->display();
    }
    
    /**
    * 门店微信号绑定
    * @date: 2015-8-26
    * @author: BillyZ
    * @return:
    */
    public function bind()
    {
    	$wechat = session ( 'wechat' );
	    $shop = M('Shop')->where(array('id'=>I('sid')))->find();
    	if (IS_POST) {
			
			$umember = M('UcenterMember')->where(array('id'=>$shop['uid']))->find();
			
			$key = '|e:c9g[%mSVjlb?IF^p/XW+!k#@noTtE(GD;2qyu';
			if($umember)
			{
				//验证密码
				if(md5(sha1($_POST['password']) . $key) === $umember['password'])
				{
					//写入门店
					$shop['openid'] = $wechat['openid'];
					$shop['wechatname'] = $wechat['nickname'];
					M('Shop')->save($shop);
					$this->success('绑定成功');
				}
				else 
				{
					$this->success('密码错误','',false,false);
				}	
			}
    	} else {
	    	$this->assign('shop',$shop);
	        $this->wap_title = '门店绑定';
    		$this->assign ( 'wechat', $wechat );
    		$this->display ();
    	}
    }
    
    /**
    * 单个门店（服务商）求助
    * @date: 2015-9-8
    * @author: BillyZ
    * @return:
    */
    public function help()
    {
    	$shopid = I('sid');
    	$problem = I('problem');
    	$mobile = I('mobile');
    	
    	$mOrder = M('RepairOrder');
    	$orderid = neworderid('WX', $this->gOpenId);
    	$repair_type = 1;
    	$order['shopid'] = $shopid;
    	$order['problem'] = $problem;
    	$order['uid'] = 0;
    	$order['openid'] = $this->gOpenId;
    	$order['transid'] = $orderid;
    	$order['status'] = 0;//0:已下单 1：已确认开始 2：已还车 3：已取消 4:待抢单
    	$order['create_time'] = NOW_TIME;
    	$order['mobile'] = $mobile;
    	
    	$order['order_type'] = $repair_type;//1:预约 2：到店 3：一键
    	$oid = $mOrder->add($order);
    	
    	parent::sendMsg2Shop($shopid,'您有新的维修订单',$problem,date('Y-m-d H:i:s',$order['create_time']),'未计算',true,'请登录后台进行处理');
    	
    	$return['flag'] = 1;
    	
    	$this->ajaxReturn($return);
    }
}