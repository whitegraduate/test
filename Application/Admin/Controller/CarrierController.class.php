<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie <yangweijiester@gmail.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use User\Api\UserApi;

/**
 * 运营商控制器
 * @author MC <zhouyibin@seersee.com>
 */
class CarrierController extends AdminController {

    /**
     * 后台运营商首页
     * @return none
     */
    public function index(){
        $title      =   trim(I('get.title'));
    	$map['pid'] =   $pid;
    	if($title)
    		$map['title'] = array('like',"%{$title}%");
    	$list       =   M("Carrier")->where($map)->field(true)->order('id asc')->select();
    
    	$this->assign('list',$list);
    	// 记录当前列表页的cookie
    	Cookie('__forward__',$_SERVER['REQUEST_URI']);

    	$this->meta_title = '运营商列表';
    	$this->display();
    }

    /**
     * 新增运营商
     * @author MC <zhouyibin@seersee.com>
     */
    public function add(){
        if(IS_POST){
            $mCarrier = M('Carrier');
            $data = $mCarrier->create();
            if($data){
                $id = $mCarrier->add();
                if($id){
                    $this->success('新增成功', Cookie('__forward__'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($mCarrier->getError());
            }
        } else {
			$citylist = M('City')->order('province')->select();
			$this->assign('citylist',$citylist);
            $this->meta_title = '新增运营商';
            $this->display('edit');
        }
    }

    /**
     * 编辑运营商
     * @author MC <zhouyibin@seersee.com>
     */
    public function edit($id = 0){
        if(IS_POST){
            $mCarrier = M('Carrier');
            $data = $mCarrier->create();
            if($data){
                if($mCarrier->save()!== false){  
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($mCarrier->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('Carrier')->field(true)->find($id);  
            $this->assign('info', $info);
			$citylist = M('City')->order('province')->select();
			$this->assign('citylist',$citylist);
            $this->meta_title = '编辑运营商';
            $this->display();
        }
    }

    /**
     * 删除运营商
     * @author yangweijie <yangweijiester@gmail.com>
     */
    public function del(){
        $id = array_unique((array)I('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('Carrier')->where($map)->delete()){
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
    


    /**
     * 编辑运营商账号
     * @author MC <zhouyibin@seersee.com>
     */
    public function carrier_user($id = 0,$uid = 0,$username = '', $password = '', $repassword = '', $email = ''){
    	
    	if(IS_POST){
    		if(0 == $uid)
    		{
    			//新增提交
	    		/* 检测密码 */
	    		if($password != $repassword){
	    			$this->error('密码和重复密码不一致！');
	    		}
	    		
	    		/* 调用注册接口注册用户 */
	    		$User   =   new UserApi;
	    		$uid    =   $User->register($username, $password, $email);
	    		if(0 < $uid){ //注册成功
	    			//更新Carrier的uid
	    			$carrier = M('Carrier')->where(array('id'=>$id))->find();
	    			if($carrier)
	    			{
	    				$carrier['uid'] = $uid;
	    				M('Carrier')->save($carrier);
	    			}
	    			
	    			//2 运营商
	    			$user = array('uid' => $uid, 'nickname' => $username, 'status' => 1, 'user_type' => 2);
	    			if(!M('Member')->add($user)){
	    				$this->error('用户添加失败！');
	    			} else {
	    				//访问授权
	    			
	    				$gid = 4;//运营商用户组
	    					
	    				$AuthGroup = D('AuthGroup');
	    				if(is_numeric($uid)){
	    					if ( is_administrator($uid) ) {
	    						//$this->error('该用户为超级管理员');
	    					}
	    				}
	    					
	    				if( $gid && !$AuthGroup->checkGroupId($gid)){
	    					//$this->error($AuthGroup->error);
	    				}
	    				if ( $AuthGroup->addToGroup($uid,$gid) ){
	    					$this->success('用户添加成功！',U('index'));
	    				}else{
	    					$this->error($AuthGroup->getError());
	    				}
	    					
	    				//$this->success('用户添加成功！',U('index'));
	    			}
	    		} else { //注册失败，显示错误信息
	    			$this->error($this->showRegError($uid));
	    		}
    		}
    		else 
    		{
    			//编辑提交
    		}
    	} else {
    		if(0 == $uid)
    		{
    			//echo '新增';
    		}
    		else
    		{
    			$User   =   new UserApi();
    			$user = $User->info($uid,false);
    			if($user)
    			{
    				//编辑
    				$this->assign('info', $user);
    			}
    		}
    		
    		$this->meta_title = '编辑运营商';
    		$this->display();
    	}
    }

    public function toogleHide($id,$value = 1){
        $this->editRow('Carrier', array('hide'=>$value), array('id'=>$id));
    }
}