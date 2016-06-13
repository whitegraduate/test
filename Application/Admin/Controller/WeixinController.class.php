<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015 Seersee rights reserved.
// +----------------------------------------------------------------------
// | Author: MC <zhouyibin@seersee.com>  
// +----------------------------------------------------------------------

namespace Admin\Controller;

use OT\Wechat\Wechat;

use Think\Log;


/**
 * 后台微信控制器
 * @author MC <zhouyibin@seersee.com>
 */
class WeixinController extends AdminController {

	public function _initialize()
	{
		parent::_initialize();
		$this->siteUrl = "http://www.green.com/";
	}
	
    /**
     * 后台微信首页
     * @return none
     */
    public function index(){
        //redirect(U('Config/group',array('id'=>5)));
        $this->appid = C('WX_APPID');
        $this->appsecret = C('WX_APPSECRET');
        $this->display();
    }
    
    /**
    * 关注回复
    * @date: 2015-7-1
    * @author: BillyZ
    * @return:
    */
    public function areply()
    {
    	$this->display();
    }
     
    /**
    * 自定义菜单管理
    * @date: 2015-6-30
    * @author: BillyZ
    * @return:
    */
    public function diymenu(){
    	$pid  = I('get.pid',0);
    	if($pid){
    		$data = M('Diymenu')->where("id={$pid}")->field(true)->find();
    		$this->assign('data',$data);
    	}
    	$title      =   trim(I('get.title'));
    	$type       =   C('CONFIG_GROUP_LIST');
    	$all_menu   =   M('Diymenu')->getField('id,title');
    	$map['pid'] =   $pid;
    	if($title)
    		$map['title'] = array('like',"%{$title}%");
    	$list       =   M("Diymenu")->where($map)->field(true)->order('sort asc,id asc')->select();
    	int_to_string($list,array('is_show'=>array(1=>'显示',0=>'隐藏')));
    	if($list) {
    		foreach($list as &$key){
    			if($key['pid']){
    				$key['up_title'] = $all_menu[$key['pid']];
    			}
    		} 
    		foreach($list as $k=>$v){
    			 
    			$c=M('Diymenu')->where(array('pid'=>$v['id']))->order('sort asc')->select();
    			$list[$k]['class']=$c;
    		}
    		
    		$this->assign('list',$list);
    	}
    	// 记录当前列表页的cookie
    	Cookie('__forward__',$_SERVER['REQUEST_URI']);
    
    	$this->meta_title = '菜单列表';
    	$this->display();
    }

    /**
     * 新增菜单
	 * @author MC <zhouyibin@seersee.com>
     */
    public function diymenu_add(){
        if(IS_POST){
            $Menu = M('Diymenu');
            $data = $Menu->create();
            if($data){
                $id = $Menu->add();
                if($id){
                   
                    //记录行为
                    //action_log('update_menu', 'Menu', $id, UID);
                    $this->success('新增成功', Cookie('__forward__'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($Menu->getError());
            }
        } else {
            $this->assign('info',array('pid'=>I('pid')));
            $menus = M('Diymenu')->field(true)->where(array('pid'=>0))->select();
            
            //$menus = D('Common/Tree')->toFormatTree($menus);
            $menus = array_merge(array(0=>array('id'=>0,'title'=>'顶级菜单')), $menus);
            $this->assign('Menus', $menus);
            $this->meta_title = '新增菜单';
            $this->display('diymenu_edit');
        }
    }

    /**
     * 编辑菜单
	 * @author MC <zhouyibin@seersee.com>
     */
    public function diymenu_edit($id = 0){
        if(IS_POST){
            $Menu = D('Diymenu');
            $data = $Menu->create();
            if($data){
                if($Menu->save()!== false){
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($Menu->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('Diymenu')->field(true)->find($id);
            $menus = M('Diymenu')->field(true)->where(array('pid'=>0))->select();
            //$menus = D('Common/Tree')->toFormatTree($menus);

            $menus = array_merge(array(0=>array('id'=>0,'title'=>'顶级菜单')), $menus);
            $this->assign('Menus', $menus);
            if(false === $info){
                $this->error('获取后台菜单信息错误');
            }
            $this->assign('info', $info);
            $this->meta_title = '编辑后台菜单';
            $this->display();
        }
    }

    /**
     * 删除后台菜单
     * @author yangweijie <yangweijiester@gmail.com>
     */
    public function diymenu_del(){
        $id = array_unique((array)I('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('Diymenu')->where($map)->delete()){
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    public function toogleHide($id,$value = 1){
        $this->editRow('Diymenu', array('is_show'=>$value), array('id'=>$id));
    }
    
    /**
    * 生成自定义菜单
    * @date: 2015-7-2
    * @author: BillyZ
    * @return:
    */
    public function generate_menu(){
    	if(IS_POST){
    		$data = '{"button":[';
    
    		$class=M('Diymenu')->where(array('pid'=>0))->limit(3)->order('sort')->select();//dump($class);
    		$kcount=M('Diymenu')->where(array('pid'=>0))->limit(3)->count();
    		$k=1;
    			
    		foreach($class as $key=>$vo){
    			//主菜单
    			$data.='{"name":"'.$vo['title'].'",';
    			$c=M('Diymenu')->where(array('pid'=>$vo['id'],'is_show'=>1))->limit(5)->order('sort')->select();
    			$count=M('Diymenu')->where(array('pid'=>$vo['id'],'is_show'=>1))->limit(5)->order('sort')->count();
    			//子菜单
    			$vo['url']=str_replace(array('{siteUrl}','&amp;','&wecha_id={wechat_id}'),array($this->siteUrl,'&','&diymenu=1'),$vo['url']);
    			if($c!=false){
    				$data.='"sub_button":[';
    			}else{
    				if(1 == $vo['menutype']){
    					$data.='"type":"click","key":"'.$vo['keyword'].'"';
    				}else if(2 == $vo['menutype']){
    					$data.='"type":"view","url":"'.$vo['url'].'"';
    				}else if(3 == $vo['menutype']){
    					$data.='"type":"'.$this->_get_sys('send',$vo['sys']).'","key":"'.$vo['sys'].'"';
    				}
    			}
    
    			$i=1;
    			foreach($c as $voo){ 
    				$voo['url']=str_replace(array('{siteUrl}','&amp;','&wecha_id={wechat_id}'),array($this->siteUrl,'&','&diymenu=1'),$voo['url']);
    				if($i==$count){
    					if(1 == $voo['menutype']){
    						$data.='{"type":"click","name":"'.$voo['title'].'","key":"'.$voo['keyword'].'"}';
    					}else if(2 == $voo['menutype']){
    						$data.='{"type":"view","name":"'.$voo['title'].'","url":"'.$voo['url'].'"}';
    					}else if(3 == $voo['menutype']){
    						$data.='{"type":"'.$this->_get_sys('send',$voo['sys']).'","name":"'.$voo['title'].'","key":"'.$voo['sys'].'"}';
    					}
    				}else{
    					if(1 == $voo['menutype']){
    						$data.='{"type":"click","name":"'.$voo['title'].'","key":"'.$voo['keyword'].'"},';
    					}else if(2 == $voo['menutype']){
    						$data.='{"type":"view","name":"'.$voo['title'].'","url":"'.$voo['url'].'"},';
    					}else if(3 == $voo['menutype']){
    						$data.='{"type":"'.$this->_get_sys('send',$voo['sys']).'","name":"'.$voo['title'].'","key":"'.$voo['sys'].'"},';
    					}
    				}
    				$i++;
    			}
    			if($c!=false){
    				$data.=']';
    			}
    
    			if($k==$kcount){
    				$data.='}';
    			}else{
    				$data.='},';
    			}
    			$k++;
    		}
    		$data.=']}';
    		
    		Log::write('data='.$data);
    		
    		$postdata = json_decode($data,true);
    		 
    		$weixinConfig = C('WEIXIN_CONFIG');
    		$this->weObj = new Wechat($weixinConfig);
    		$result = $this->weObj->createMenu($postdata);
    		$this->success($this->weObj->errMsg.'['. $this->weObj->errCode.']');
    
    		if(true){
    			
    			$this->error('操作失败');
    		}else{
    			$this->success('操作成功');
    		}
    	}else{
    		$this->error('非法操作');
    	}
    }
    
    /**
     * 图文回复列表
     * @date: 2015-7-2
     * @author: BillyZ
     * @return:
     */
    public function imgreply(){
    	$pid  = I('get.pid',0);
    	if($pid){
    		$data = M('Imgreply')->where("id={$pid}")->field(true)->find();
    		$this->assign('data',$data);
    	}
    	$title      =   trim(I('get.title'));
    	$type       =   C('CONFIG_GROUP_LIST');
    	$all_menu   =   M('Imgreply')->getField('id,title');
    	$map['pid'] =   $pid;
    	if($title)
    		$map['title'] = array('like',"%{$title}%");
    	$list       =   M("Imgreply")->where($map)->field(true)->order('sort asc,id asc')->select();
    	int_to_string($list,array('hide'=>array(1=>'是',0=>'否'),'is_dev'=>array(1=>'是',0=>'否')));
    	if($list) {
    		foreach($list as &$key){
    			if($key['pid']){
    				$key['up_title'] = $all_menu[$key['pid']];
    			}
    		}
    		foreach($list as $k=>$v){
    
    			$c=M('Imgreply')->where(array('pid'=>$v['id']))->order('sort desc')->select();
    			$list[$k]['class']=$c;
    		}
    
    		$this->assign('list',$list);
    	}
    	// 记录当前列表页的cookie
    	Cookie('__forward__',$_SERVER['REQUEST_URI']);
    
    	$this->meta_title = '图文回复列表';
    	$this->display();
    }
    
    /**
     * 新增菜单
     * @author MC <zhouyibin@seersee.com>
     */
    public function imgreply_add(){
    	if(IS_POST){
    		$Menu = M('Imgreply');
    		$data = $Menu->create();
    		if($data){
    			$id = $Menu->add();
    			if($id){
    				 
    				//记录行为
    				//action_log('update_menu', 'Menu', $id, UID);
    				$this->success('新增成功', Cookie('__forward__'));
    			} else {
    				$this->error('新增失败');
    			}
    		} else {
    			$this->error($Menu->getError());
    		}
    	} else {
    		$this->assign('info',array('pid'=>I('pid')));
    		$menus = M('Imgreply')->field(true)->where(array('pid'=>0))->select();
    
    		//$menus = D('Common/Tree')->toFormatTree($menus);
    		$menus = array_merge(array(0=>array('id'=>0,'title'=>'顶级菜单')), $menus);
    		$this->assign('Menus', $menus);
    		$this->meta_title = '新增图文回复';
    		$this->display('imgreply_edit');
    	}
    }
    
    private function _get_sys($type='',$key=''){
    	$wxsys 	= array(
    			'扫码带提示',
    			'扫码推事件',
    			'系统拍照发图',
    			'拍照或者相册发图',
    			'微信相册发图',
    			'发送位置',
    	);
    
    	if($type == 'send'){
    		$wxsys 	= array(
    				'扫码带提示'=>'scancode_waitmsg',
    				'扫码推事件'=>'scancode_push',
    				'系统拍照发图'=>'pic_sysphoto',
    				'拍照或者相册发图'=>'pic_photo_or_album',
    				'微信相册发图'=>'pic_weixin',
    				'发送位置'=>'location_select',
    		);
    		return $wxsys[$key];
    	}
    	return $wxsys;
    }

    /**
     * 微信关键词回复列表
     * @author: Nice <hupeipei@seersee.com>
     */
    public function indexReply() {
        $keyword = I('keyword');
        $map = array();
        if (!empty($keyword)) {
            $map['keyword'] = array('like', '%' . $keyword . '%');
        }
        $list = $this->lists('Reply', $map, 'sort desc');
        $this->assign('list', $list);
        $this->meta_title = '微信关键词回复';
        $this->display();
    }

    /**
     * 新增微信文本回复
     * @author: Nice <hupeipei@seersee.com>
     */
    public function addTextReply() {
        if (IS_POST) {
            $mReply = M('Reply');
            $data = I('post.');
            $data['reply_type'] = 1;
            $data['create_time'] = NOW_TIME;
            $val = $mReply->add($data);
            if (0 < $val) {
                $this->success('新增成功', U('indexReply'));
            } else {
                $this->error('新增失败');
            }
        } else {
            $this->meta_title = '文本回复';
            $this->display('textReply');
        }
    }

    /**
     * 编辑微信文本回复
     * @author: Nice <hupeipei@seersee.com>
     */
    public function editTextReply() {
        $mReply = M('Reply');
        if (IS_POST) {
            $data = I('post.');
            $val = $mReply->save($data);
            if (false !== $val) {
                $this->success('编辑成功', U('indexReply'));
            } else {
                $this->error('编辑失败');
            }
        } else {
            $id = I('id');
            $reply = $mReply->where(array('id' => $id))->find();
            $this->assign('info', $reply);
            $this->meta_title = '文本回复';
            $this->display('textReply');
        }
    }

    /**
     * 新增微信图文回复
     * @author: Nice <hupeipei@seersee.com>
     */
    public function addImgReply() {
        if (IS_POST) {
            $mReply = M('Reply');
            $data = I('post.');
            $data['reply_type'] = 2;
            $data['create_time'] = NOW_TIME;
            $val = $mReply->add($data);
            if (0 < $val) {
                $this->success('新增成功', U('indexReply'));
            } else {
                $this->error('新增失败');
            }
        } else {
            $this->meta_title = '图文回复';
            $this->display('imgReply');
        }
    }

    /**
     * 编辑微信图文回复
     * @author: Nice <hupeipei@seersee.com>
     */
    public function editImgReply() {
        $mReply = M('Reply');
        if (IS_POST) {
            $data = I('post.');
            $val = $mReply->save($data);
            if (false !== $val) {
                $this->success('编辑成功', U('indexReply'));
            } else {
                $this->error('编辑失败');
            }
        } else {
            $id = I('id');
            $reply = $mReply->where(array('id' => $id))->find();
            $this->assign('info', $reply);
            $this->meta_title = '图文回复';
            $this->display('imgReply');
        }
    }

    /**
     * 删除微信回复
     * @author: Nice <hupeipei@seersee.com>
     */
    public function delReply() {
        $mReply = M('Reply');
        $val = $mReply->where(array('id' => I('id')))->delete();
        if (false !== $val) {
            $this->success('操作成功', U('indexReply'));
        } else {
            $this->error('操作失败');
        }
    }
}