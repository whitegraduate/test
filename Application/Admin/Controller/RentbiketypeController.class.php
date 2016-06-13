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
class RentbiketypeController extends AdminController
{

    /**
     * 区域管理
     */
    public function index()
    {
        $cityid = I('get.cityid');
        $RentbikeTypelist = M('RentbikeType')->select();
        //$RentbikeTypelist = get_provice_city_area($RentbikeTypelist);
        $this->assign('RentbikeTypelist', $RentbikeTypelist);
        $this->display();
    }

    /**
     * 增加区域
     */
    public function add()
    {
        if (IS_POST) {
            $data = M('RentbikeType')->create();
            if ($data) {
               $res = M('RentbikeType')->add();
                if($res)
                {
                    $this->success('新增成功',U('index'));
                }
            } else {
                $this->error('新增失败');
            }
        } else {
            $this->display('edit');
        }
    }

    /**
     * 增加区域
     */
    public function edit($id=0)
    {
        $rentbikeinfo = M('RentbikeType')->where(array('id'=>$id))->find();
        if (IS_POST) {
            $mRentbikeType = M('RentbikeType');
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
            $this->display('edit');
        }
    }
}