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
 * 基础信息控制器
 * @author MC <zhouyibin@seersee.com>
 */
class BasicController extends AdminController {

  /**
     * 首页
     * @return none
     */
    public function index(){
    
    	$this->meta_title = '列表';
    	$this->display();
    } 
    
    /**
     * 部件首页
     * @return none
     */
    public function maintain_parts(){
    
    	$lstBrand = M('MaintainParts')->select();
    	$this->assign('list',$lstBrand);
    
    	Cookie('__forward__',$_SERVER['REQUEST_URI']);
    	$this->meta_title = '部件列表';
    	$this->display();
    }

    /**
     * 新增部件
     * @author MC <zhouyibin@seersee.com>
     */
    public function maintain_parts_add(){
    	if(IS_POST){
    		$mBrand = M('MaintainParts');
    		$data = $mBrand->create();
    		if($data){
    			$id = $mBrand->add();
    			if($id){
    				$this->success('新增成功', Cookie('__forward__'));
    			} else {
    				$this->error('新增失败');
    			}
    		} else {
    			$this->error($mBrand->getError());
    		}
    	} else {
    		$this->meta_title = '新增部件';
    		$this->display('maintain_parts_edit');
    	}
    }
    
    /**
     * 编辑部件
     * @author MC <zhouyibin@seersee.com>
     */
    public function maintain_parts_edit($id = 0){
    	if(IS_POST){
    		$mBrand = M('MaintainParts');
    		$data = $mBrand->create();
    		if($data){
    			if($mBrand->save()!== false){
    				$this->success('更新成功', Cookie('__forward__'));
    			} else {
    				$this->error('更新失败');
    			}
    		} else {
    			$this->error($mBrand->getError());
    		}
    	} else {
    		$info = array();
    		/* 获取数据 */
    		$info = M('MaintainParts')->field(true)->find($id);
    		$this->assign('info', $info);
    		$this->meta_title = '编辑部件';
    		$this->display();
    	}
    }
    
    /**
     * 删除保养部件
     * @author MC <zhouyibin@seersee.com>
     */
    public function maintain_parts_del(){
    	$id = array_unique((array)I('id',0));
    
    	if ( empty($id) ) {
    		$this->error('请选择要操作的数据!');
    	}
    
    	$map = array('id' => array('in', $id) );
    	if(M('MaintainParts')->where($map)->delete()){
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败！');
    	}
    }

    /**
     * 品牌首页
     * @return none
     */
    public function brand(){

    	$lstBrand = M('Brand')->select();
    	$this->assign('list',$lstBrand);

    	Cookie('__forward__',$_SERVER['REQUEST_URI']);
    	$this->meta_title = '列表';
    	$this->display();
    }

    /**
     * 新增品牌
     * @author MC <zhouyibin@seersee.com>
     */
    public function brand_add(){
        if(IS_POST){
            $mBrand = M('Brand');
            $data = $mBrand->create();
            if($data){
                $id = $mBrand->add();
                if($id){
                    $this->success('新增成功', Cookie('__forward__'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($mBrand->getError());
            }
        } else {
            $this->meta_title = '新增品牌';
            $this->display('brand_edit');
        }
    }

    /**
     * 编辑品牌
     * @author MC <zhouyibin@seersee.com>
     */
    public function brand_edit($id = 0){
        if(IS_POST){
            $mBrand = M('Brand');
            $data = $mBrand->create();
            if($data){
                if($mBrand->save()!== false){  
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($mBrand->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('Brand')->field(true)->find($id);  
            $this->assign('info', $info);
            $this->meta_title = '编辑品牌';
            $this->display();
        }
    }

    /**
     * 删除品牌
     * @author MC <zhouyibin@seersee.com>
     */
    public function brand_del(){
        $id = array_unique((array)I('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('Brand')->where($map)->delete()){
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
    
    /**
     * 后台维修配件类型管理
     * @return none
     */
    public function rparts_type(){
    	$list       =   M("RepairPartsType")->field(true)->order('id asc')->select();
    	 
    	$this->assign('list',$list);
    
    	// 记录当前列表页的cookie
    	Cookie('__forward__',$_SERVER['REQUEST_URI']);
    
    	$this->meta_title = '维修配件类型列表';
    	$this->display();
    }

    /**
     * 根据大类获取维修小类
     * @date: 2015-9-24
     * @author: BillyZ
     * @return:
     */
    public function get_repair_parts_types_by_parent($type)
    {
    	$lstTypes = M('RepairPartsType')->where(array('pid'=>$type))->select();
    	$this->ajaxReturn($lstTypes);
    }

	public function getpart_by_typed($type)
	{
		$lstTypes = M('RepairParts')->where(array('type'=>$type))->select();
		$this->ajaxReturn($lstTypes);
	}
    
    /**
     * 新增配件类型
     * @author MC <zhouyibin@seersee.com>
     */
    public function rparts_type_add(){
    	if(IS_POST){
    		$mType = M('RepairPartsType');
    		$data = $mType->create();
    		if($data){
    			$id = $mType->add();
    			if($id){
    				$this->success('新增成功', Cookie('__forward__'));
    			} else {
    				$this->error('新增失败');
    			}
    		} else {
    			$this->error($mType->getError());
    		}
    	} else {
    		$this->meta_title = '新增部件';
    		$this->display('rparts_type_edit');
    	}
    }
    
    /**
     * 编辑部件
     * @author MC <zhouyibin@seersee.com>
     */
    public function rparts_type_edit($id = 0){
    	if(IS_POST){
    		$mType = M('RepairPartsType');
    		$data = $mType->create();
    		if($data){
    			if($mType->save()!== false){
    				$this->success('更新成功', Cookie('__forward__'));
    			} else {
    				$this->error('更新失败');
    			}
    		} else {
    			$this->error($mType->getError());
    		}
    	} else {
    		$info = array();
    		/* 获取数据 */
    		$info = M('RepairPartsType')->field(true)->find($id);
    		$this->assign('info', $info);
    		$this->meta_title = '编辑部件';
    		$this->display();
    	}
    }
    
    /**
     * 删除配件类型
     * @author MC <zhouyibin@seersee.com>
     */
    public function rparts_type_del(){
    	$id = array_unique((array)I('id',0));
    
    	if ( empty($id) ) {
    		$this->error('请选择要操作的数据!');
    	}
    
    	$map = array('id' => array('in', $id) );
    	if(M('RepairPartsType')->where($map)->delete()){
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败！');
    	}
    }
 
}