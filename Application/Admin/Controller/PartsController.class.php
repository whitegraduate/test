<?php
// +----------------------------------------------------------------------
// | 视界无限 - SEERSEE
// +----------------------------------------------------------------------
// | Copyright (c) 2015
// +----------------------------------------------------------------------
// | Author: badboy <zhaoyunfeng@seersee.com>
// +----------------------------------------------------------------------

namespace Admin\Controller;

/**
 * 附件控制器
 * @author badboy <zhaoyunfeng@seersee.com>
 */
class PartsController extends AdminController
{
    /**
     * 附件管理
     * @author badboy <zhaoyunfeng@seersee.com>
     */
    public function parts()
    {
        /* 查询条件初始化 */
        $map = array();
        $map['type'] = 2;
        $list = $this->lists('Mall', $map, 'sort desc');
        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $list=get_provice_city_area($list);
        $this->assign('list', $list);
        $this->meta_title = '附件管理';
        $this->display();
    }

    /**
     * 新增附件
     * @author badboy <zhaoyunfeng@seersee.com>
     */
    public function add()
    {
        if (IS_POST) {
            $data = I('post.');
            $data['create_time']=time();
            $data['pic']=$data['icon'];
            $data['type']=2;
            $colors = array_unique((array)$data['color']);
            $data['color'] = arr2str($colors);
            $res = M('Mall')->add($data);
            if ($res) {
                $this->success('新增成功', U('parts'));
            } else {
                $this->error('新增失败');
            }
        } else {
            $this->meta_title = '新增附件';
            $colorlist = M('Color')->select();
            $this->assign('colorlist', $colorlist);
            $this->assign('info', null);
            $this->display('edit');
        }
    }

    /**
     * 编辑附件
     * @author badboy <zhaoyunfeng@seersee.com>
     */
    public function edit($id = 0)
    {
        if (IS_POST) {
            if($_POST["act"]=="attr"){
                $attr_data['mallid']=$_GET['id'];
                $attr_data['attrname']=$_POST['an'];
                $attr_data['attrinfo']=$_POST['av'];
                $attr_data['attr']=json_encode(explode(",",$_POST['av']));
                $attr_res = M('MallAttr')->add($attr_data);
                if($attr_res){
                    $back['status']=1;
                }
                else{
                    $back['status']=0;
                }
                echo json_encode($back);
            }
            elseif($_POST["act"]=="del"){
                if(M('MallAttr')->where(array("id"=>$_POST["did"]))->delete()){
                    $back['status']=1;
                }
                else{
                    $back['status']=0;
                }
                echo json_encode($back);
            }
            else{
                $data = I('post.');
                $data['pic']=$data['icon'];
                $colors = array_unique((array)$data['color']);
                $data['color'] = arr2str($colors);
                $res = M('Mall')->save($data);
                if ($res) {
                    //记录行为
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            }

        } else {
            $info = array();
            /* 获取数据 */
            $info = M('Mall')->where(array('id'=>$id))->find();
            $attr = M("MallAttr")->where(array('mallid'=>$id))->select();
            $this->assign('info', $info);
            $colorlist = M('Color')->select();
            $this->assign('colorlist', $colorlist);
            $this->assign('attr', $attr);
            $this->meta_title = '编辑';
            $this->display();
        }
    }
}