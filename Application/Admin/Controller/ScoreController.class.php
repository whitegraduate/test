<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie <yangweijiester@gmail.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;

/**
 * 后台积分控制器
 * @author MC <zhouyibin@seersee.com>
 */
class ScoreController extends AdminController {

    /**
     * 后台积分首页
     * @return none
     */
    public function index(){
        
        $id     =   6;
        $type   =   C('CONFIG_GROUP_LIST');
        $list   =   M("Config")->where(array('status'=>1,'group'=>$id))->field('id,name,title,extra,value,remark,type')->order('sort')->select();
        if($list) {
            $this->assign('list',$list);
        }
        $this->assign('id',$id);
        $this->meta_title = $type[$id].'设置';
        $this->display();
    }
    
    /**
     * 后台积分商城
     * @return none
     */
    public function gifts(){
    	$list   =   M("ScoreGift")->order('create_time desc')->select();
    	if($list) {
    		$this->assign('list',$list);
    	}
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
    	$this->meta_title = '积分';
    	$this->display();
    }
    

    /**
     * 商城产品添加
     * @date: 2015-7-1
     * @author: BillyZ
     * @return:
     */
    public function gift_add(){
    	if(IS_POST){
    		$mGift = M('ScoreGift');
    		
    		$data['name'] = $_POST['name'];
    		$data['score'] = $_POST['score'];
    		//$data['gifttype'] = $_POST['gifttype'];
    		$gift['gifttype'] = 2;//目前仅支持优惠券
    		if('2' == $gift['gifttype'])
    			$data['couponid'] = $_POST['couponid'];
    		else 
    			$data['couponid'] = 0;
    		$data['img'] = $_POST['img'];
    		$data['create_time'] = NOW_TIME;
    		$data['valid_time'] = strtotime($_POST['valid_time']);
    		$data['num'] = $_POST['num'];
    		$data['status'] = $_POST['status'];
     		 
    		if($data){
    			$id = $mGift->add($data);
    			if($id){
    				$this->success('新增成功', Cookie('__forward__'));
    			} else {
    				$this->error('新增失败');
    			}
    		} else {
    			$this->error($mCharger->getError());
    		}
    	} else {
    		$coupons = M('Coupon')->select();
    		
    		$startDate = I('start_date', date('Y-m-d', strtotime('+30 day')));
    		
    		$this->assign('start_date',$startDate);
    		$this->assign('coupons',$coupons);
    		$this->meta_title = '新增礼品';
    		$this->display('gift_edit');
    	}
    }

    /**
     * 礼品编辑
     * @date: 2015-7-1
     * @author: BillyZ
     * @return:
     */
    public function gift_edit($id = 0){
    	if(IS_POST){
    		$gift = M('ScoreGift')->where(array('id'=>$id))->find();
    		
    		$gift['name'] = $_POST['name'];
    		$gift['score'] = $_POST['score'];
    		//$gift['gifttype'] = $_POST['gifttype'];
    		$gift['gifttype'] = 2;//目前仅支持优惠券
    		if('2' == $gift['gifttype'])
    			$gift['couponid'] = $_POST['couponid'];
    		else
    			$gift['couponid'] = 0;
    		$gift['img'] = $_POST['img'];
    		$gift['valid_time'] = strtotime($_POST['valid_time']);
    		$gift['num'] = $_POST['num'];
    		$gift['status'] = $_POST['status'];
    		
    		M('ScoreGift')->save($gift);
    		$this->success('更新成功', Cookie('__forward__'));
    	} else {
    		$info = array();
    		/* 获取数据 */
    		$info = M('ScoreGift')->field(true)->find($id);
    		$coupons = M('Coupon')->select();

    		$startDate = date('Y-m-d',$info['valid_time']);
    		$this->assign('start_date',$startDate);
    		$this->assign('coupons',$coupons);
    		$this->assign('info', $info);
    		$this->meta_title = '编辑礼品';
    		$this->display();
    	}
    }

    /**
     * 删除礼品
     * @author MC <zhouyibin@seersee.com>
     */
    public function gift_del(){
    	$id = array_unique((array)I('id',0));
    
    	if ( empty($id) ) {
    		$this->error('请选择要操作的数据!');
    	}
    
    	$map = array('id' => array('in', $id) );
    	if(M('ScoreGift')->where($map)->delete()){
    
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败！');
    	}
    }
    
    
    /**
     * 积分记录
     * @return none
     */
    public function log(){
    
    	$title      =   trim(I('get.title'));
    	$map['pid'] =   $pid;
    	if($title)
    		$map['title'] = array('like',"%{$title}%");
    	// $list       =   M("ScoreLog")->where($map)->field(true)->order('id asc')->select(); 
        $list = $this->lists('ScoreLog',$where,'id desc');
    	foreach($list as $k => $v)
    	{
    		$list[$k]['fansname'] = get_fansname_openid($v['openid']);
    		$list[$k]['log_type'] = get_scorelog_type_id($v['log_type']);
    	}
    	
    
    	$this->assign('list',$list);
    	// 记录当前列表页的cookie
    	Cookie('__forward__',$_SERVER['REQUEST_URI']);
    
    	$this->meta_title = '积分记录列表';
    	$this->display();
    }
}