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
class AreaController extends AdminController {

	/**
	 * 区域管理
	 */
    public function index(){
		$cityid = I('get.cityid');
		$citylist =M('City')->select();
		$this->assign('citylist',$citylist);
		$map=array();
		if(!empty($cityid)&&$cityid>0)
		{
			$map['cityid']=$cityid;
		}
		$arealist = M('CityArea')->join('gc_city on gc_city.id = gc_city_area.cityid')->where($map)->field('gc_city.*,gc_city_area.areaname,gc_city_area.id as cid')->order('city')->select();
		$this->assign('arealist',$arealist);
		$this->display();
    }

	/**
	 * 增加区域
	 */
	public function add()
	{
		if(IS_POST)
		{
			$data = I('post.');
			$info['cityid']=$data['cityid'];
			$info['areaname']=$data['areaname'];
			$res = M('CityArea')->add($info);
			if($res)
			{
				$this->success('新增成功', U('index',array('cityid'=>$info['cityid'])));
			}else {
				$this->error('新增失败');
			}
		}
		else
		{
			$citylist =M('City')->select();
			$this->assign('citylist',$citylist);
			$this->display();
		}
	}

	/**
	 * 增加区域
	 */
	public function edit($id =0)
	{
		if(IS_POST)
		{
			$data = M('CityArea')->create();
			if($data)
			{
				$res =  M('CityArea')->save();
				if($res)
				{
					$this->success('编辑成功', U('index',array('cityid'=>1)));
				}else {
					$this->error('编辑失败');
				}
			}
		}
		else
		{
			$areainfo =M('CityArea')->where(array('id'=>$id))->find();
			$cityname =M('City')->where(array('id'=>$areainfo['cityid']))->getField('city');
			$areainfo['cityname']=$cityname;
			$this->assign('areainfo',$areainfo);
			$this->display();
		}
	}
}