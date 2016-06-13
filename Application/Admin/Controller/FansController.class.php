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
 * 后台粉丝控制器
 * @author MC <zhouyibin@seersee.com>
 */
class FansController extends AdminController {

    /**
     * 后台粉丝首页
     * @return none
     */
    public function index(){
        $map = array();
        $condition['mobile'] = I('mobile');
        $condition['idcard_status'] = I('idcard_status', -1);
        $condition['nickname'] = I("nickname");
        $condition['openid'] = I("openid");
        if (!empty($condition['mobile'])) {
            $map['mobile'] = $condition['mobile'];
        }

        if(!empty($condition['nickname'])) {
            $map['nickname'] = array("LIKE","%".$condition['nickname']."%");
        }
        if(!empty($condition['openid'])) {
            $map['openid'] = $condition['openid'];
        }

        if ($condition['idcard_status'] != -1) {
            $map['idcard_status'] = $condition['idcard_status'];
        }
            $request=I('get.');
        if(!empty($request['create_time_start'])){
            $map['create_time']=array(array('egt',strtotime($request['create_time_start'])),array('elt',strtotime($request['create_time_end'])),'and');
        }
       $list = $this->lists_join('Fans', $map,'id desc',true,"left join (select c.brandid,d.id,c.uid,d.name,c.bike,count(uid) as count from fg_fans_bike as c right join fg_brand as d on c.brandid=d.id group by uid ) b on a.id=b.uid ","b.name as bikename ,b.bike as biketype,b.count as bikecount");
        $this->assign('list',$list);
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->assign('create_time_start',$request['create_time_start']);
        $this->assign('create_time_end',$request['create_time_end']);
        $this->assign('condition', $condition);
        $this->meta_title = '会员列表';
        $this->display();

    }
   public function FansIndexPost(){
       $uid=I('get.uid');
       $condition['uid']=$uid;
       $mbike = M('fans_bike');
       $bikeinfo=$this->lists_join('fans',$condition,'id desc',true,"left join gc_fans_bike as b on a.id=b.uid left join gc_brand as c on b.brandid=c.id","c.name as bikename,b.bike as biketype,b.buy_time as buytime");
       $this->ajaxReturn($bikeinfo);
   }

    /**
     * 会员订单列表
     * @author: Nice <hupeipei@seersee.com>
     */
    public function orders() {
        $uid = I('uid');
        $fans = M('Fans')->where(array('id' => $uid))->find();
        if (empty($fans)) {
            $this->error('会员信息不存在');
        }
        $this->assign('fans', $fans);

        $orderType = I('ordertype', 'MaintainOrder');
        if (!empty($orderType)) {
            $map['openid'] = $fans['openid'];
            $list = $this->lists($orderType, $map);
            foreach ($list as $k => $v) {
                $statusDicName = 'ORDER_STATUS_' . strtoupper(substr($orderType, 0, -5));
                $list[$k]['status_desc'] = get_dic_desc($statusDicName, $v['status']);
                if (!isset($v['price'])) {
                    $list[$k]['price'] = 1;
                }
            }
            $this->assign('list', $list);
            $this->assign('ordertype', $orderType);
            $this->meta_title = '会员订单';
            $this->display();
        } else {
            $this->error('非法链接');
        }
    }

    /**
     * 会员积分明细
     * @author: Nice <hupeipei@seersee.com>
     */
    public function scores() {
        $uid = I('uid');
        $fans = M('Fans')->where(array('id' => $uid))->find();
        if (empty($fans)) {
            $this->error('会员信息不存在');
        }
        $this->assign('fans', $fans);
        $map['uid'] = $uid;
        $list = $this->lists('ScoreLog', $map, 'createtime desc');
        $this->assign('list', $list);
        $this->meta_title = '会员积分明细';
        $this->display();
    }
    
    /**
     * 会员等级
     * @return none
     */
    public function grade(){
    	$map = array();
    	$list       =   M("FansGrade")->where($map)->field(true)->order('id asc')->select();
    	$this->assign('list',$list);
    	// 记录当前列表页的cookie
    	Cookie('__forward__',$_SERVER['REQUEST_URI']);
    
    	$this->meta_title = '会员等级列表';
    	$this->display();
    }

    /**
    * 会员等级添加
    * @date: 2015-7-8
    * @author: BillyZ
    * @return:
    */
    public function grade_add(){
        if(IS_POST){
            $mGrade = M('FansGrade');
            $data = $mGrade->create();
            if($data){
                $id = $mGrade->add();
                if($id){
                    $this->success('新增成功', Cookie('__forward__'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($mGrade->getError());
            }
        } else {
            $this->assign('info',array('pid'=>I('pid')));
            $grades = M('FansGrade')->field(true)->select();
            $this->assign('Grades', $grades);
            $this->meta_title = '新增会员等级';
            $this->display('grade_edit');
        }
    }

    /**
    * 会员等级编辑
    * @date: 2015-7-8
    * @author: BillyZ
    * @return:
    */
    public function grade_edit($id = 0){
        if(IS_POST){
            $mGrade = M('FansGrade');
            $data = $mGrade->create();
            if($data){
                if($mGrade->save()!== false){
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($mGrade->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('FansGrade')->field(true)->find($id);
            $this->assign('info', $info);
            $this->meta_title = '编辑会员等级';
            $this->display();
        }
    }

    /**
    * 会员等级删除
    * @date: 2015-7-8
    * @author: BillyZ
    * @return:
    */
    public function grade_del(){
        $id = array_unique((array)I('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('FansGrade')->where($map)->delete()){
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }


    /**
    * 会员特权列表
    * @date: 2015-7-8
    * @author: BillyZ
    * @return:
    */
    public function rights(){
    
    	$title      =   trim(I('get.title'));
        $map = array();
    	if($title)
    		$map['title'] = array('like',"%{$title}%");
    	$list       =   M("FansRights")->where($map)->field(true)->order('id asc')->select();
    	//int_to_string($list,array('hide'=>array(1=>'是',0=>'否'),'is_dev'=>array(1=>'是',0=>'否')));
    
    	$this->assign('list',$list);
    	// 记录当前列表页的cookie
    	Cookie('__forward__',$_SERVER['REQUEST_URI']);
    
    	$this->meta_title = '会员等级列表';
    	$this->display();
    }
    
    /**
     * 会员特权添加
     * @date: 2015-7-8
     * @author: BillyZ
     * @return:
     */
    public function rights_add(){
    	if(IS_POST){
    		$mRights = M('FansRights');
    		$data = $mRights->create();
    		if($data){
    			$id = $mRights->add();
    			if($id){
    				$this->success('新增成功', Cookie('__forward__'));
    			} else {
    				$this->error('新增失败');
    			}
    		} else {
    			$this->error($mRights->getError());
    		}
    	} else {
    		$arrGradeRightList = C('GRADE_RIGHT_LIST');
    		$this->assign('grlist',$arrGradeRightList);
    		//var_dump($arrGradeRightList);
    		
    		$this->assign('info',array('pid'=>I('pid')));  
    		$this->meta_title = '新增会员特权';
    		$this->display('rights_edit');
    	}
    }
    
    /**
     * 会员特权编辑
     * @date: 2015-7-8
     * @author: BillyZ
     * @return:
     */
    public function rights_edit($id = 0){
    	if(IS_POST){
    		$mRights = M('FansRights');
    		$data = $mRights->create();
    		if($data){
    			if($mRights->save()!== false){
    				$this->success('更新成功', Cookie('__forward__'));
    			} else {
    				$this->error('更新失败');
    			}
    		} else {
    			$this->error($mRights->getError());
    		}
    	} else {
    		$info = array();
    		/* 获取数据 */
    		$info = M('FansRights')->field(true)->find($id);
    		$this->assign('info', $info);
    		$this->meta_title = '编辑会员特权';
    		$this->display();
    	}
    }
    
    /**
     * 会员特权删除
     * @date: 2015-7-8
     * @author: BillyZ
     * @return:
     */
    public function rights_del(){
    	$id = array_unique((array)I('id',0));
    
    	if ( empty($id) ) {
    		$this->error('请选择要操作的数据!');
    	}
    
    	$map = array('id' => array('in', $id) );
    	if(M('FansRights')->where($map)->delete()){
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
    	
    	$uid = I('uid');
    	
    	$fans = M('Fans')->where(array('id'=>$uid))->find();
    	if($fans)
    	{
    		$this->assign('fans',$fans);
    	}
    	
    	$map['uid'] = $uid;
    	$list       =   M("FansCoupon")->where($map)->field(true)->order('id asc')->select();
    
    	$this->assign('list',$list);
    	// 记录当前列表页的cookie
    	Cookie('__forward__',$_SERVER['REQUEST_URI']);
    
    	$this->meta_title = '会员优惠券列表';
    	$this->display();
    }
    
    /**
     * 优惠券发放
     * @date: 2015-7-8
     * @author: BillyZ
     * @return:
     */
    public function add_fans_coupon()
    {
    	$uid = I('uid');
    	$fans = M('Fans')->where(array('id'=>$uid))->find();
    	if(IS_POST)
    	{
    		$num = $_POST['num'];
    		$mFansCoupon = M('FansCoupon');
    		if(0 < $num) {
                give_coupon_one($uid, $_POST['cid'],$num);
    		} 
    		else if(0 == $num) {
    			giveCoupon($uid, 1);
    		}
    		 $this->success('发放成功');
    	}
    	else
    	{
	    	if($fans)
	    	{
	    		$this->assign('fans',$fans);
	    	}
	    	$lstCoupon = M('Coupon')->select();
	    	$this->assign('lstCoupon',$lstCoupon);
	    	
	    	$this->display();
    	}
    }

    public function toogleHide($id,$value = 1){
        $this->editRow('Menu', array('hide'=>$value), array('id'=>$id));
    }

    /**
     * 实名认证操作
     * @author: Nice <hupeipei@seersee.com>
     */
    public function authIdCard() {
        $id = I('id');
        $status = I('status');
        $mFans = M('Fans');
        $fans = $mFans->where(array('id' => $id))->find();
        if (empty($fans)) {
            $this->error('会员信息不存在');
        }
        $data['id'] = $id;
        $data['idcard_status'] = $status;
        $val = $mFans->save($data);
        if (false !== $val) {
        	flush_fans($fans['openid']);
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }

    public function fans_export(){
        $request=I('get.');
        $map['create_time']=array(array('egt',strtotime($request['create_time_start'])),array('elt',strtotime($request['create_time_end'])),'and');
        $list=M('Fans')->where($map)->select();
        $template = "<td width='150' align='center' height='30' style='border:1px solid'>{val}</td>";
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=".$request['create_time_start']."_".$request['create_time_end']."会员信息表.xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        //输出内容如下：
        echo "<table style='border:1em solid'>";
        echo "<tr>";
        echo str_replace('{val}', 'ID', $template);
        echo str_replace('{val}', '昵称', $template);
        echo str_replace('{val}', '姓名', $template);
        echo str_replace('{val}', '手机号', $template);
        echo str_replace('{val}', '所在位置', $template);
        echo str_replace('{val}', '注册时间', $template);
        echo str_replace('{val}', '是否绑定微信', $template);
        echo str_replace('{val}', '实名认证', $template);
        // echo str_replace('{val}', '车辆数', $template);
        echo "</tr>";
        $pay_type = '本人支付';
        for($i=0;$i<count($list);$i++)
        {  
            echo "<tr>";
            echo str_replace('{val}', $list[$i]['id'], $template);
            echo str_replace('{val}', $list[$i]['nickname'], $template); 
            echo str_replace('{val}', $list[$i]['realname'], $template); 
            echo str_replace('{val}', $list[$i]['mobile'], $template); 
            echo str_replace('{val}', $list[$i]['address'], $template); 
            echo str_replace('{val}', date('Y-m-d H:i',$list[$i]['subscribe_time']), $template); 
            echo str_replace('{val}', get_bool_desc($list[$i]['wechat_binded']), $template); 
            if($list[$i]['idcard_status']==0)$idcard_status='未认证';
            if($list[$i]['idcard_status']==1)$idcard_status='已认证';
            if($list[$i]['idcard_status']==2)$idcard_status='审核中';
            echo str_replace('{val}', $idcard_status, $template);
            // echo str_replace('{val}', $list[$i]['bikecount'], $template);

        }
        echo "</tr></table>";
        
    }
}