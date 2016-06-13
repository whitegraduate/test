<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace Admin\Controller;

use User\Api\UserApi;
/**
 * 红包
 * @author MC <zhouyibin@seersee.com>
 */
class RedPaperController extends AdminController {

    /**
     * 红包策略日志
     * @return none
     */
    public function redpaperlog(){
        $title      =   trim(I('get.title'));
    	if($title)
    		$map['name'] = array('like',"%{$title}%");
    
    	 $list       =   M("RedpaperLog")->where($map)->field(true)->order('logid asc')->select();
          $list = $this->lists_join('RedpaperLog', $map,'logid asc',true,"left join gc_member b on a.logby=b.uid ","b.nickname as logname");
        
    	$this->assign('list',$list);
    	// 记录当前列表页的cookie
    	Cookie('__forward__',$_SERVER['REQUEST_URI']);
    
    	$this->meta_title = '红包策略日志列表';
    	$this->display();
    }
    
    
    /**
     * 收红包记录
     * @return none
     */
    public function redpapersdetail(){
        if($_GET['ids']){
            $where = array("a.ids"=>$_GET['ids']);
        }
        else{
            $where=" 1=1 ";
        }
        $list = $this->lists_join('RedpaperReceive', $where,'idr desc',true,"inner join gc_redpaper_send b on a.ids=b.ids inner join gc_fans_coupon c on a.code = c.code inner join gc_fans d on a.openid=d.openid ","b.couponid as couponid,d.nickname as nickname,c.create_time,c.used_time,c.end_time");
    	$this->assign('list',$list);
    	// 记录当前列表页的cookie
    	Cookie('__forward__',$_SERVER['REQUEST_URI']);
    
    	$this->meta_title = '红包记录列表';
    	$this->display();
    }
    
    
    
    /**
     * 红包记录
     * @return none
     */
    public function redpapers(){
        if($_GET['nickname']){
            $where = array("nickname"=>$_GET['nickname']);
        }
        else{
            $where=" 1=1 ";
        }
        //$list = $this->lists('RedpaperSend', $where, 'ids asc');
               $list = $this->lists_join('RedpaperSend', $where,'ids desc',true,"left join (select c.ids,count(c.ids) as recqty from gc_redpaper_send as c inner join gc_redpaper_receive as d on c.ids=d.ids group by c.ids ) b on a.ids=b.ids left join gc_fans e on a.openid=e.openid ","b.recqty as recqty,e.nickname as nickname");
    	$this->assign('list',$list);
    	// 记录当前列表页的cookie
    	Cookie('__forward__',$_SERVER['REQUEST_URI']);
    
    	$this->meta_title = '红包记录列表';
    	$this->display();
    }
    
        /**
     * 红包配置
     * @return none
     */
    public function redpaper(){
    	$title      =   trim(I('get.title'));
    	if($title)
    		$map['name'] = array('like',"%{$title}%");
    
    	 $list       =   M("Redpaper")->where($map)->field(true)->order('id asc')->select();
        
    	$this->assign('list',$list);
    	// 记录当前列表页的cookie
    	Cookie('__forward__',$_SERVER['REQUEST_URI']);
    
    	$this->meta_title = '红包列表';
    	$this->display();
    }
    
     /**
     * 新增红包配置
     * @author MC <zhouyibin@seersee.com>
     */
    public function redpaper_add(){
        $UID = defined('UID');
        if(IS_POST){
            $mRedpaper = M('Redpaper');
            $data = $mRedpaper->create();
            if($data){
                $IsExists = M('Redpaper') ->where(array('service_type' => $data['service_type']))->find();
                if(!empty($IsExists))
                {
                    $this->error('该服务类型已经存在！');
                }
                $data['createdtime'] = NOW_TIME;
                $data['createdby'] = $UID;
                $data['status'] = 1;//1、是可用。2、是禁用
                $id = $mRedpaper->add($data);
                if($id){
                    $this->success('新增成功', Cookie('__forward__'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($mRedpaper->getError());
            }
        } else {
        	$lstRedpaper = M('Redpaper')->select();
        	$this->assign('list',$lstRedpaper);
                
                   $lstCoupon = M('Coupon')->select();
	    	$this->assign('lstCoupon',$lstCoupon);
                
            $this->meta_title = '新增红包';
            $this->display('redpaperedit');
        }
    }
    
    /**
     * 编辑红包配置
     * @author MC <zhouyibin@seersee.com>
     */
    public function redpaperedit($id = 0){
        $UID = defined('UID');
        if(IS_POST){
            $mRedpaper = M('Redpaper');
            $dd = M('Redpaper') ->where(array('id' => $id))->find();
            $data = $mRedpaper->create();
            if($data){
                $map['id'] = array('notlike',"%{$id}%");              
                $map['service_type'] = $data['service_type'];
                $IsExists = M('Redpaper') ->where($map)->find();
                if(!empty($IsExists))
                {
                    $this->error('该服务类型已经存在！');
                }
                //插入日志表
                $RedpaperLog = M('RedpaperLog');
                $mRedpaperLog['id'] = $dd['id'];
                $mRedpaperLog['name'] = $dd['name'];
                $mRedpaperLog['service_type'] = $dd['service_type'];
                $mRedpaperLog['couponid'] = $dd['couponid'];
                $mRedpaperLog['qty'] = $dd['qty'];
                $mRedpaperLog['days'] = $dd['days'];
                $mRedpaperLog['createdtime'] = $dd['createdtime'];
                $mRedpaperLog['createdby'] = $dd['createdby'];
                $mRedpaperLog['modifiedtime'] = $dd['modifiedtime'];
                $mRedpaperLog['modifiedby'] = $dd['modifiedby'];
                $mRedpaperLog['status'] = $dd['status'];
                $mRedpaperLog['logtime'] = NOW_TIME;
                $mRedpaperLog['logby'] = $UID;
                $RedpaperLog->add($mRedpaperLog);

                $data['modifiedtime'] = NOW_TIME;
                $data['modifiedby'] = $UID;
                if($mRedpaper->save($data)!== false){  
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($mRedpaper->getError());
            }
        } else {
            $info = array();
            $lstCoupon = M('Coupon')->select();
	    	$this->assign('lstCoupon',$lstCoupon);
            /* 获取数据 */
            $info = M('Redpaper')->field(true)->find($id);  
            $this->assign('info', $info);
            $this->meta_title = '编辑红包策略';
            $this->display();
        }
    }
     /**
     * 删除红包配置
     * @author MC <zhouyibin@seersee.com>
     */
    public function redpaper_del(){
        $id = array_unique((array)I('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('Redpaper')->where($map)->delete()){  
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
}
