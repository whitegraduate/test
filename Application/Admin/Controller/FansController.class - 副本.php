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
        if (!empty($condition['mobile'])) {
            $map['mobile'] = $condition['mobile'];
        }

        if(!empty($condition['nickname'])) {
            $map['nickname'] = array("LIKE","%".$condition['nickname']."%");
        }

        if ($condition['idcard_status'] != -1) {
            $map['idcard_status'] = $condition['idcard_status'];
        }

        $list = $this->lists('Fans', $map);

        $this->assign('list',$list);
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->assign('condition', $condition);
        $this->meta_title = '会员列表';
        $this->display();
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
    		if(0 < $num)
    		{
    			give_coupon_one($uid, $_POST['cid']);
    		} 
    		else if(0 == $num)
    		{
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
    
    public function batch_sync_fansinfo()
    {
    	$list = M('Fans')->where(array('nickname'=>''))->select();
    	foreach ($list as $k=>$v)
    	{
    		echo "\$list[$k] => $v.\n";
    		echo "\$list[$k]['id'] => ".$v['id']."\n";
    		$this->sync_fansinfo($v['id']);
    		 
    	}
    }
    
    private function sync_fansinfo($id)
    { 
    	$key = 'access_token_wxb915214f14fdddfd';
        $data = S($key);
        if (isset($data) && !empty($data['access_token']) && $data['expired_time'] > NOW_TIME) {
        	//echo 'access_token='.$data['access_token'];
        }
        else
        	echo 'aaabb';
    	$fans = M('Fans')->where(array('id'=>$id))->find();
    	if($fans)
    	{
    		echo $fans['openid'].',';
    		$userinfo = $this->weObj->getUserInfo($fans['openid']);
    		echo 'userinfo=';
    		if($userinfo)
    		{
    			if('' == emoj($userinfo['nickname']))
    			{
    				echo 'nickname is null';
    			}
    			else
    			{
		    		$fans['nickname'] = $userinfo['nickname'];
		    		M('Fans')->save($fans);
	    			echo $userinfo['nickname'].'成功';
	    			sleep(1);
    			}
    		}
    		else
    			echo 'userinfo is null,';
    		
    		
    		
    	}
    	else
    		echo 'fans is null,';
    }
    
}