<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.seersee.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: MC <zhouyibin@seersee.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use OT\Wechat\Wechat;
use Think\Log;
/**
 * 后台渠道控制器
 * @author MC <zhouyibin@seersee.com>
 */
class CanalController extends AdminController {

    /**
     * 后台渠道首页
     * @return none
     */
    public function index(){
       
        $list       =   M("Canal")->field(true)->where(array('code'=>'YX'))->order('id asc')->select();
        if($list) {
            
            $this->assign('list',$list);
        }
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);

        $this->meta_title = '渠道列表';
        $this->display();
    }

    /**
    * 渠道添加
    * @date: 2015-7-1
    * @author: BillyZ
    * @return:
    */
    public function add(){
        if(IS_POST){
            $mCanal = M('Canal');
            $data = $mCanal->create();
            if($data){
                $id = $mCanal->add();
                if($id){
                	$qimg = $this->generate_qrcode($id);
                	
                	$mCanal->where(array('id'=>$id))->setField('qimg',$qimg);
                	
                    $this->success('新增成功', Cookie('__forward__'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($mCanal->getError());
            }
        } else {  
            $this->meta_title = '新增渠道';
            $this->display('edit');
        }
    }

    /**
    * 渠道编辑
    * @date: 2015-7-1
    * @author: BillyZ
    * @return:
    */
    public function edit($id = 0){
        if(IS_POST){
            $mCanal = M('Canal');
            $data = $mCanal->create();
            if($data){
                if($mCanal->save()!== false){
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($mCanal->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('Canal')->field(true)->find($id);
            $this->assign('info', $info);
            $this->meta_title = '编辑渠道';
            $this->display();
        }
    }

    /**
     * 删除渠道
     * @author MC <yangweijiester@gmail.com>
     */
    public function del(){
        $id = array_unique((array)I('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('Canal')->where($map)->delete()){
            
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    } 
    
    /**
    * 生成二维码
    * @date: 2015-7-14
    * @author: BillyZ
    * @return:
    */
    private function generate_qrcode($canalid)
    {
	    $objWechat = new Wechat(C('WEIXIN_CONFIG'));
	    $jsonQrcode = $objWechat->getQRCode($canalid,1);    	
	
	    $qimg =  'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$jsonQrcode['ticket'];
	    return $qimg;
    }
}