<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.seersee.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: MC <zhouyibin@seersee.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use User\Api\UserApi;

/**
 * 优惠券控制器
 * @author MC <zhouyibin@seersee.com>
 */
class CouponController extends AdminController {

    /**
     * 后台优惠券首页
     * @return none
     */
    public function index(){
        $title      =   trim(I('get.title'));
    	$map['pid'] =   $pid;
    	if($title)
    		$map['title'] = array('like',"%{$title}%");
    	$list       =   M("Coupon")->where($map)->field(true)->order('id asc')->select();
    
    	$this->assign('list',$list);
    	// 记录当前列表页的cookie
    	Cookie('__forward__',$_SERVER['REQUEST_URI']);
    
    	$this->meta_title = '运营商列表';
    	$this->display();
    }

    /**
     * 新增优惠券
     * @author MC <zhouyibin@seersee.com>
     */
    public function add(){
        if(IS_POST){
            $mCoupon = M('Coupon');
            $data = $mCoupon->create();
            if($data){
                $id = $mCoupon->add();
                if($id){
                    $this->success('新增成功', Cookie('__forward__'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($mCoupon->getError());
            }
        } else {
            $this->meta_title = '新增优惠券';
            $this->display('edit');
        }
    }

    /**
     * 编辑优惠券
     * @author MC <zhouyibin@seersee.com>
     */
    public function edit($id = 0){
        if(IS_POST){
            $mCoupon = M('Coupon');
            $data = $mCoupon->create();
            if($data){
                if($mCoupon->save()!== false){  
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($mCoupon->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('Coupon')->field(true)->find($id);  
            $this->assign('info', $info);
            $this->meta_title = '编辑优惠券';
            $this->display();
        }
    }

    /**
     * 删除优惠券
     * @author MC <zhouyibin@seersee.com>
     */
    public function del(){
        $id = array_unique((array)I('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('Coupon')->where($map)->delete()){
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }


    /**
     * 优惠券列表
     * @date: 2015-7-8
     * @author: BillyZ
     * @return:
     */
    public function coupons(){
		$request=I('get.');
		if($request['create_time_start']) {
			$where[]['create_time']=array('egt',strtotime($request['create_time_start']));
			$create_time_status='下单时间：'.$request['create_time_start'];
		}
		if($request['create_time_end']){
			$where[]['create_time']=array('elt',strtotime($request['create_time_end']));
			$create_time_status.='至'.$request['create_time_end'];
		}
		if($request['used_time_start']) {
			$where[]['used_time']=array('egt',strtotime($request['used_time_start']));
			$used_time_status='使用时间：'.$request['used_time_start'];
		}
		if($request['used_time_end']){
			$where[]['used_time']=array('elt',strtotime($request['used_time_end']));
			$used_time_status.='至'.$request['used_time_end'];
		}

        if($_GET['code']){
            $where[]['code'] = array("eq",$request['code']);
        }
		if($request['canalid']){
			if((int)$request['canalid']>0)
			{
				$where[]['canalid'] = array("eq",$request['canalid']);
			}
		}
		if($request['cid']){
			if((int)$request['cid']>0)
			{
				$where[]['cid'] = array("eq",$request['cid']);
			}
		}
        $list = $this->lists('FansCoupon', $where, 'id asc');
    	$this->assign('list',$list);
    	// 记录当前列表页的cookie
    	Cookie('__forward__',$_SERVER['REQUEST_URI']);

		$listcoupon = M('Coupon')->select();
		$this->assign('listcoupon',$listcoupon);

		$Model = M('FansCoupon');
		$map['canalid'] = array('gt',0);
		$listcanalid = $Model->where($map)->distinct(true)->field('canalid')->select();
		foreach($listcanalid as $key=> $v)
		{
			$cname = M('Canal')->where(array('id'=>$v['canalid']))->getField('name');
			$cname =empty($cname)?"所有":$cname;
			$listcanalid[$key]['cname'] = $cname;
		}
		$this->assign('listcanalid',$listcanalid);

		$this->assign('create_time_end',$request['create_time_end']);
		$this->assign('used_time_end',$request['used_time_end']);
		$this->assign('create_time_start',$request['create_time_start']);
		$this->assign('used_time_start',$request['used_time_start']);
		$this->assign('create_time_status',$create_time_status);
		$this->assign('used_time_status',$used_time_status);
		$this->assign('canalid',$request['canalid']);
		$this->assign('cid',$request['cid']);
    	$this->meta_title = '会员优惠券列表';
    	$this->display();
    }

    /**
     * 优惠券列表
     * @date: 2015-7-8
     * @author: BillyZ
     * @return:
     */
    public function config(){  
    	$list       =   M("CouponConfig")->field(true)->order('id asc')->select();
    
    	$this->assign('list',$list);
    	// 记录当前列表页的cookie
    	Cookie('__forward__',$_SERVER['REQUEST_URI']);
    
    	$this->meta_title = '优惠券策略列表';
    	$this->display();
    }
    

    /**
     * 新增优惠券策略
     * @author MC <zhouyibin@seersee.com>
     */
    public function config_add(){
    	if(IS_POST){
    		$mCoupon = M('CouponConfig');
    		$config['actions'] = $_POST['actions'];
    		$config['couponid'] = $_POST['couponid'];
    		$config['num'] = $_POST['num'];
    		 
    		$config['canals'] = implode(',', $_POST['canals']); 
    		$config['status'] = $_POST['status'];
    		$id = $mCoupon->add($config);
    		if($id){
    			$this->success('新增成功', Cookie('__forward__'));
    		} else {
    			$this->error('新增失败');
    		}
    	} else {
    		$lstCoupon = M('Coupon')->select();
    		$this->assign('lstCoupon',$lstCoupon);
    		
    		$lstCanal = M('Canal')->where(array('code'=>'YX'))->select();
    		$this->assign('lstCanal',$lstCanal);
    		
    		$this->meta_title = '新增优惠券策略';
    		$this->display('config_edit');
    	}
    }

    /**
     * 编辑优惠券策略
     * @author MC <zhouyibin@seersee.com>
     */
    public function config_edit($id = 0){
    	if(IS_POST){
    		$mCoupon = M('CouponConfig');
    		$config = $mCoupon->where(array('id'=>$id))->find();
    		if($config)
    		{
    			$config['actions'] = $_POST['actions'];
    			$config['couponid'] = $_POST['couponid'];
    			$config['num'] = $_POST['num'];
    			 
    			$config['canals'] = implode(',', $_POST['canals']);
    			$config['status'] = $_POST['status'];
    			if($mCoupon->save($config))
    			{
    				$this->success('更新成功', Cookie('__forward__'));
    			}
    			else
    				$this->error('更新失败');
    		}
    		else
    			$this->error('信息错误');
    		
    	} else {
    		$lstCoupon = M('Coupon')->select();
    		$this->assign('lstCoupon',$lstCoupon);
    		
    		$lstCanal = M('Canal')->where(array('code'=>'YX'))->select();
    		$this->assign('lstCanal',$lstCanal);
    		$info = array();
    		/* 获取数据 */
    		$info = M('CouponConfig')->field(true)->find($id);
    		$this->assign('info', $info);
    		$this->meta_title = '编辑优惠券策略';
    		$this->display();
    	}
    }
    
    /**
     * 删除优惠券策略
     * @author MC <zhouyibin@seersee.com>
     */
    public function config_del(){
        $id = array_unique((array)I('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('CouponConfig')->where($map)->delete()){
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
    
    
    /**
     * 优惠券使用
     * @date: 2015-7-18
     * @author: BillyZ
     * @return:
     */
    public function coupon_use($id,$value = 1){
    	$this->editRow('FansCoupon', array('status'=>$value), array('id'=>$id));
    }
    
    
    public function batch_give_temp()
    {
    	/*
    	$where['id'] = array('lt',81);
    	$list = M('Fans')->where($where)->select();
		//echo count($list);
    	//$this->success(count($list));
    	
    	$count = 0;
    	for($i=0;$i<count($list);$i++)
    	{
    		$count ++;
    		$this->giveCoupon($list[$i]['id']);
    	}
    	*/
    	
    	$this->success('当前不可用');
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

	/**
	 *时间组合搜索
	 *author:zsl
	 **/
	function serache_details(){
		$request=I('get.');
		if($request['create_time_start']) {
			$where[]['create_time']=array('egt',strtotime($request['create_time_start']));
			$create_time_status='下单时间：'.$request['create_time_start'];
		}
		if($request['create_time_end']){
			$where[]['create_time']=array('elt',strtotime($request['create_time_end']));
			$create_time_status.='至'.$request['create_time_end'];
		}
		if($request['used_time_start']) {
			$where[]['used_time']=array('egt',strtotime($request['used_time_start']));
			$used_time_status='使用时间：'.$request['used_time_start'];
		}
		if($request['used_time_end']){
			$where[]['used_time']=array('elt',strtotime($request['used_time_end']));
			$used_time_status.='至'.$request['used_time_end'];
		}
		if($_GET['code']){
			$where[]['code'] = array("eq",$request['code']);
		}
		if($request['canalid']){
			if((int)$request['canalid']>0)
			{
				$where[]['canalid'] = array("eq",$request['canalid']);
			}
		}
		if($request['cid']){
			if((int)$request['cid']>0)
			{
				$where[]['cid'] = array("eq",$request['cid']);
			}
		}
		$serache_details['where']=$where;
		$this->assign('create_time_end',$request['create_time_end']);
		$this->assign('used_time_end',$request['used_time_end']);
		$this->assign('create_time_start',$request['create_time_start']);
		$this->assign('used_time_start',$request['used_time_start']);
		$this->assign('create_time_status',$create_time_status);
		$this->assign('used_time_status',$used_time_status);
		return $serache_details;
	}

	/**
	 * 导出订单数据
	 * @date: 2015-8-24
	 * @author: BillyZ
	 * @return:
	 */
	public function export_orders()
	{
		$status = I('status');
		$serache_details=$this->serache_details();
		$where=$serache_details['where'];
		if($status)
		{
			if($status != -1)
				$where['status'] = $status;
		}
		else
			$status = -1;


		$list = M('FansCoupon')->where($where)->order('id desc')->limit(1000)->select();

		$template = "<td width='150' align='center' height='30' style='border:1px solid'>{val}</td>";

		header("Content-type:application/vnd.ms-excel");
		header("Content-Disposition:attachment;filename=".date('Y-m-d',$serache_details['where'][0]['create_time'][1])."_".date('Y-m-d',$serache_details['where'][1]['create_time'][1])."充电订单.xls");
		header("Pragma: no-cache");
		header("Expires: 0");

		//输出内容如下：
		echo "<table style='border:1em solid'>";
		echo "<tr>";
		echo str_replace('{val}', '券号', $template);
		echo str_replace('{val}', '优惠券', $template);
		echo str_replace('{val}', '发放时间', $template);
		echo str_replace('{val}', '使用时间', $template);
		echo str_replace('{val}', '状态', $template);
		echo str_replace('{val}', '类型', $template);
		echo str_replace('{val}', '面值', $template);
		echo str_replace('{val}', '规则', $template);
		echo "</tr>";

		$pay_type = '本人支付';
		for($i=0;$i<count($list);$i++)
		{
			echo "<tr>";
			echo str_replace('{val}', $list[$i]['code'], $template);
			echo str_replace('{val}', $list[$i]['cname'], $template);
			echo str_replace('{val}', date('Y-m-d H:i',$list[$i]['create_time']), $template);
			echo str_replace('{val}', date('Y-m-d H:i',$list[$i]['used_time']), $template);
			echo str_replace('{val}', $list[$i]['status'], $template);

			$type = C('COUPON_TYPE');

			echo str_replace('{val}', $type[$list[$i]['service_type']], $template);
			echo str_replace('{val}', $list[$i]['face'], $template);

			$txt="";
			if($list[$i]['arrive_face']>0&&$list[$i]['reduce_face']>0)
			{
				$txt="满".$list[$i]['arrive_face']."抵扣".$list[$i]['reduce_face'];
			}

			echo str_replace('{val}', $txt, $template);

			echo "</tr>";
		}
		echo "</table>";
	}
}