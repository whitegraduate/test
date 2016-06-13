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
class RentbiketypeareaController extends AdminController
{

    /**
     * 区域管理
     */
    public function index()
    {
        $cityid = I('get.cityid');
        $RentbikeTypeArealist = M('RentbikeTypeArea')->select();
        $RentbikeTypeArealist = get_provice_city_area($RentbikeTypeArealist);
        foreach($RentbikeTypeArealist as $key=>$v)
        {
            $typeinfo = M('RentbikeType')->where(array('id'=>$v['rentbiketypeid']))->find();
            $RentbikeTypeArealist[$key]['typename']=$typeinfo['typename'];
        }
        $this->assign('RentbikeTypeArealist', $RentbikeTypeArealist);
        $this->display();
    }

    /**
     * 增加区域
     */
    public function add()
    {
        if (IS_POST) {
            $data = M('RentbikeTypeArea')->create();
            if ($data) {
               $res = M('RentbikeTypeArea')->add();
                if($res)
                {
                    $this->success('新增成功',U('index'));
                }
            } else {
                $this->error('新增失败');
            }
        } else {
            $RentbikeTypelist = M('RentbikeType')->select();
            $this->assign('RentbikeTypelist', $RentbikeTypelist);
            $this->display();
        }
    }

    /**
     * 增加区域
     */
    public function edit($id=0)
    {
        $rentbikeinfo = M('RentbikeTypeArea')->where(array('id'=>$id))->find();
        if (IS_POST) {
            $mRentbikeType = M('RentbikeTypeArea');
            $data =$mRentbikeType->create();
            if ($data) {
                $res =$mRentbikeType->save();
                if($res)
                {
                    $this->success('更新成功',U('index'));
                }
            } else {
                $this->error('更新失败');
            }
        } else {
            $this->assign('rentbikeinfo',$rentbikeinfo);
            $RentbikeTypelist = M('RentbikeType')->select();
            $this->assign('RentbikeTypelist', $RentbikeTypelist);
            $this->display('edit');
        }
    }

    public function change_status($id=0)
    {
        if($id){
            $val=0;
            $info = M('RentbikeTypeArea')->where(array('id'=>$id))->find();
            if($info['status']==1)
            {
                $val=0;
            }
            else
            {
                $val=1;
            }
            $info['status']=$val;
            M('RentbikeTypeArea')->save($info);
        }
    }
}