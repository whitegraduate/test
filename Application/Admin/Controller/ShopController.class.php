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
use Think\Log;
/**
 * 门店控制器
 * @author MC <zhouyibin@seersee.com>
 */
class ShopController extends AdminController {

    /**
     * 后台门店首页
     * @return none
     */
    public function index(){
        $title      =   trim(I('get.title'));
    	if($title)
    		$map['name'] = array('like',"%{$title}%");
    	$map['shop_type'] = 1;
    	$list       =   M("Shop")->where($map)->field(true)->order('id asc')->select();

		foreach($list as $key=> $value)
		{
			$cityinfo = M('City')->where(array('id'=>$value['cityid']))->find();
			$list[$key]['cityname']= $cityinfo['province'].$cityinfo['city'];
			$areainfo = M('CityArea')->where(array('id'=>$value['areaid']))->find();
			$list[$key]['areaname']= $areainfo['areaname'];
		}
    	$this->assign('list',$list);
    	// 记录当前列表页的cookie
    	Cookie('__forward__',$_SERVER['REQUEST_URI']);
    
    	$this->meta_title = '门店列表';
    	$this->display();
    }

    /**
     * 新增门店
     * @author MC <zhouyibin@seersee.com>
     */
    public function add(){
        if(IS_POST){
            $mShop = M('Shop');
            $data = $mShop->create();
            if($data){
                $id = $mShop->add();
                if($id){
                    $this->success('新增成功', Cookie('__forward__'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($mShop->getError());
            }
        } else {
        	$lstCarrier = M('Carrier')->select();
        	$this->assign('lstCarrier',$lstCarrier);
            $this->meta_title = '新增门店';
            $this->display('edit');
        }
    }

    /**
     * 编辑配置
     * @author MC <zhouyibin@seersee.com>
     */
    public function edit($id = 0){
        if(IS_POST){
            $mShop = M('Shop');
            $data = $mShop->create();
            if($data){
                if($mShop->save()!== false){
                    
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($mShop->getError());
            }
        } else {
            $info = array();
            $lstCarrier = M('Carrier')->select();
            $this->assign('lstCarrier',$lstCarrier);
            
            /* 获取数据 */
            $info = M('Shop')->field(true)->find($id);  
            $this->assign('info', $info);
            $this->meta_title = '编辑门店';
            $this->display();
        }
    }

    /**
     * 删除门店
     * @author MC <zhouyibin@seersee.com>
     */
    public function del(){
        $id = array_unique((array)I('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('Shop')->where($map)->delete()){  
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }


    /**
     * 编辑门店账号
     * @author MC <zhouyibin@seersee.com>
     */
    public function shop_user($id = 0,$uid = 0,$username = '', $password = '', $repassword = '', $email = '',$stype = 1){
    	
    	if(IS_POST){
    		if(0 == $uid)
    		{
    			//新增提交
    			/* 检测密码 */
    			if($password != $repassword){
    				$this->error('密码和重复密码不一致！');
    			}

    			log::write('1,');
    			/* 调用注册接口注册用户 */
    			$User   =   new UserApi;
    			log::write('2,');
    			$uid    =   $User->register($username, $password, $email);
    			log::write('3,');
    			if(0 < $uid){ //注册成功
    				//更新Shop的uid
    			log::write('4,');
    				$shop = M('Shop')->where(array('id'=>$id))->find();
    				if($shop)
    				{
    			log::write('5,');
    					$shop['uid'] = $uid;
    					M('Shop')->save($shop);
    				}

    				log::write('6,');
    				//3 门店
    				$user = array('uid' => $uid, 'nickname' => $username, 'status' => 1, 'user_type' => 3);
    				if(!M('Member')->add($user)){
    					$this->error('用户添加失败！');
    				} else {
    					//访问授权
    					 
    					if(1 == $stype)
    						$gid = 3;//门店用户组 
    					else 
    						$gid = 5;//服务商用户组
    					
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
    				log::write('注册失败');
    				log::write('show.error='.$this->showRegError($uid));
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
    
    		$this->meta_title = '编辑门店';
    		$this->display();
    	}
    }

    /**
     * 后台服务商首页
     * @return none
     */
    public function servicer(){
    	$title      =   trim(I('get.title'));
    	if($title)
    		$map['name'] = array('like',"%{$title}%");
    	$map['shop_type'] = 2;
    	$list       =   M("Shop")->where($map)->field(true)->order('id asc')->select();
    
    	$this->assign('list',$list);
    	// 记录当前列表页的cookie
    	Cookie('__forward__',$_SERVER['REQUEST_URI']);
    
    	$this->meta_title = '服务商列表';
    	$this->display();
    }
    
    /**
     * 新增服务商
     * @author MC <zhouyibin@seersee.com>
     */
    public function servicer_add(){
    	if(IS_POST){
    		$mShop = M('Shop');
    		$data = $mShop->create();
    		if($data){
    			$id = $mShop->add();
    			if($id){
    				$this->success('新增成功', Cookie('__forward__'));
    			} else {
    				$this->error('新增失败');
    			}
    		} else {
    			$this->error($mShop->getError());
    		}
    	} else {
    		$lstCarrier = M('Carrier')->select();
    		$this->assign('lstCarrier',$lstCarrier);
    		$this->meta_title = '新增服务商';
    		$this->display('servicer_edit');
    	}
    }
    
    /**
     * 编辑服务商
     * @author MC <zhouyibin@seersee.com>
     */
    public function servicer_edit($id = 0){
        if(IS_POST){
            $mShop = M('Shop');
            $data = $mShop->create();
            if($data){
                if($mShop->save()!== false){
                    
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($mShop->getError());
            }
        } else {
            $info = array();
            $lstCarrier = M('Carrier')->select();
            $this->assign('lstCarrier',$lstCarrier);
            
            /* 获取数据 */
            $info = M('Shop')->field(true)->find($id);  
            $this->assign('info', $info);
            $this->meta_title = '编辑服务商';
            $this->display();
        }
    }
    
    /**
     * 切换门店状态
     * @author MC <zhouyibin@seersee.com>
     */
    public function toogleStatus($id,$value = 1){
        $this->editRow('Shop', array('status'=>$value), array('id'=>$id));
    }
    

    /**
     * 结算
     * @return none
     */
    public function settle(){
    	$list       =   M("SettleLog")->where($map)->field(true)->order('id desc')->select();
    
    	$this->assign('list',$list);
    	// 记录当前列表页的cookie
    	Cookie('__forward__',$_SERVER['REQUEST_URI']);
    
    	$this->meta_title = '门店结算';
    	$this->display();
    }


    /**
     * 门店打款
     * @author MC <zhouyibin@seersee.com>
     */
    public function pay(){
    	if(IS_POST){ 
    		$mSettleLog = D('SettleLog');
    		
    		$log = $mSettleLog->where(array('shopid'=>$_POST['shopid'],array('create_time'=>array('gt',strtotime(date('Y-m-d'))))))->find();

    		if($log)
    			$this->error('该门店今日已打款');
    		else
    		{
	            $data = $mSettleLog->create();
	  
	    		if($data){
	    			$id = $mSettleLog->add();
	    			if($id){
						$tradeno = $data['tradeno'];
						$openid = $data['openid'];
						$amount = 100 * $data['money_total'];
						$desc = $data['memo'];
						$ip = get_client_ip();

	    				$result = parent::pay2shop($tradeno, $openid, $amount, $desc, $ip);
	    				if('SUCCESS' == $result['return_code'])
	    				{
	    					if('SUCCESS' == $result['result_code'])
	    					{
	    						//成功改变结算状态
	    						$mSettleLog->where(array('id'=>$id))->setField('status',2);
	    						$this->success('新增成功', Cookie('__forward__'));
	    					}
	    					else
	    					{
	    						$this->error('付款失败，原因是：'.$result['err_code_des']);
	    					}
	    				} 
	    				else 
	    				{
	    					$this->error('提交信息失败,'.$result['return_msg']);
	    				}
	    				
	    			} else {
	    				$this->error('新增失败');
	    			}
	    		} else {
	    			$this->error($mSettleLog->getError());
	    		}
    		}
    	} else {
    		$lstShop = M('Shop')->select();
    		$this->assign('lstShop',$lstShop);
    		$this->meta_title = '门店打款';
    		$this->display();
    	}
    }
    
    /**
    * 结算再次提交
    * @date: 2015-11-10
    * @author: BillyZ
    * @return:
    */
    public function pay_again()
    {
    	$mSettleLog = D('SettleLog');
    	$id = I('id');
    	$log = $mSettleLog->where(array('id'=>$id))->find();
    	
    	if($log)
    	{
    		$result = parent::pay2shop($log['tradeno'], $log['openid'], $log['money_total'], $log['memo'], get_client_ip());
    		 
    		if('SUCCESS' == $result['return_code'])
    		{
    			if('SUCCESS' == $result['result_code'])
    			{
    				//成功改变结算状态
	    			$mSettleLog->where(array('id'=>$id))->setField('status',2);
    				$this->success('打款成功', Cookie('__forward__'));
    			}
    			else
    			{
    				$this->error('付款失败，原因是：'.$result['err_code_des']);
    			}
    		}
    		else
    		{
    			$this->error('提交信息失败'.$result['return_msg']);
    		}
    	}
    	else 
    	{
    		$this->error('提交信息失败,原因是：结算信息不存在'.$id);
     	}
    }
}