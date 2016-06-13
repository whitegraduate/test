<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;
use User\Api\UserApi as UserApi;

/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class IndexController extends AdminController {

    /**
     * 后台首页
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    public function index(){
        echo C("DB_PREFIX");
        $this->meta_title = '管理首页';
        $this->display();
    }
    
    /**
    * 投诉管理列表
    * @date: 2015-10-16
    * @author: BillyZ
    * @return:
    */
    public function complain()
    {
    	$status = $_GET['status'];
    	
    	$startDate = I('start_date', date('Y-m-d', strtotime('-10 day')));
    	$endDate = I('end_date', date('Y-m-d', time()));
    	$where['create_time'] = array(array('egt', strtotime($startDate)), array('elt', strtotime($endDate) + 3600 * 24));
    	 
    	if(isset($status))
    	{
    		if($status != -1)
    			$where['status'] = $status;
    	}
    	else
    		$status = -1;
    	 
    	if(0 < $this->gShopid)
    	{
    		$where['shopid'] = $this->gShopid;
    	}
    	
    	$this->assign('status',$status);
    	
    	$list = $this->lists('OrderComplain', $where, 'id desc');
    	$this->assign('list',$list);
    	$this->display();
    }

}
