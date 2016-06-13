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
 * 后台积分控制器
 * @author MC <zhouyibin@seersee.com>
 */
class SechandController extends AdminController
{

    /**
     * 后台积分首页
     * @return none
     */
    public function index()
    {
        $map = array();
        if (isset($_GET['group'])) {
            $map['group'] = I('group', 0);
        }
        if (isset($_GET['name'])) {
            $map['name'] = array('like', '%' . (string)I('name') . '%');
        }

        $list = $this->lists('Sechand', $map, 'create_time desc');
        $this->assign('group_id', I('get.group', 0));
        $this->assign('list', $list);
        $this->meta_title = '二手车管理';
        $this->display();
    }

    public function tongguo()
    {
        $id = I('id');
        $mSechand = M('Sechand');
        $sechand = $mSechand->where(array('id' => $id))->find();
        if ($sechand) {
            $sechand['status'] = 2;
            $mSechand->save($sechand);
            //增加消息模板推送 @20150808 MC
            $data = array(
                "first" => array("value" => "您的二手车信息已经审核通过，请将您的手机保持畅通！", "color" => "#173177"),
                "keyword1" => array("value" => get_fansname_openid($sechand['openid']), "color" => "#173177"),
                "keyword2" => array("value" => '二手车', "color" => "#173177"),
                "keyword3" => array("value" => date('Y-m-d H:i', NOW_TIME), "color" => "#173177"),
                "remark" => array("value" => "点击查看详情", "color" => "#173177")
            );

            parent::sendTempMsg($sechand['openid'], 'zlIicRmlIJ5UWqoBGFFFxw95bL6_5PTCVOViS5V5qHg', C('site_url').'/wap.php?s=/Sechand/detail/id/' . $id . '.html', $data);
        }

        $this->success('审核成功');
    }

    public function butongguo()
    {
        if (IS_POST) {
            $id = I('id');
            $remark=I('remark');
            $mSechand = M('Sechand');
            $sechand = $mSechand->where(array('id' => $id))->find();
            if ($sechand) {
                $sechand['status'] = 3;
                $sechand['remark'] = $remark;
                $mSechand->save($sechand);
                //增加消息模板推送 @20150808 MC
                $data = array(
                    "first" => array("value" => "您的二手车信息审核结果：", "color" => "#173177"),
                    "keyword1" => array("value" => '未通过', "color" => "#173177"),
                    "keyword2" => array("value" => $remark, "color" => "#173177"),
                    "remark" => array("value" => "请按照要求重新上传", "color" => "#173177")
                );

                parent::sendTempMsg($sechand['openid'], 'SmQoyFPZNCNu4qjleqFBACFutk2Tjaue_O9MU-copX4', '', $data);
            }

            $this->success('操作成功！',U('index'));
        } else {
            $id = I('id');
            $info = M('Sechand')->where(array('id' => $id))->find();
            $this->assign('info',$info);
            $this->display();
        }

    }

    /**
     * 积分记录
     * @return none
     */
    public function log()
    {

        $title = trim(I('get.title'));
        $map['pid'] = $pid;
        if ($title)
            $map['title'] = array('like', "%{$title}%");
        $list = M("ScoreLog")->where($map)->field(true)->order('id asc')->select();
        //int_to_string($list,array('hide'=>array(1=>'是',0=>'否'),'is_dev'=>array(1=>'是',0=>'否')));

        $this->assign('list', $list);
        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);

        $this->meta_title = '积分记录列表';
        $this->display();
    }


    /**
     * 二手车删除
     * @date: 2015-8-8
     * @author: BillyZ
     * @return:
     */
    public function del()
    {
        $id = array_unique((array)I('id', 0));

        if (empty($id)) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id));

        if (M('Sechand')->where($map)->delete()) {

            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
}