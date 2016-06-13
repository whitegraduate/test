<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie <yangweijiester@gmail.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;

use OT\Wechat\Wechat;
use Think\Log;

/**
 * 后台维修控制器
 * @author MC <zhouybin@seersee.com>
 */
class RepairCityController extends AdminController
{

	/**
	 * 后台维修配件管理
	 * @return none
	 */
	public function index()
	{
//		$pid = I('get.pid', 0);
//		if ($pid) {
//			$data = M('RepairPartsCity')->where("id={$pid}")->field(true)->find();
//			$this->assign('data', $data);
//		}
//		$title = trim(I('get.title'));
//		$map['pid'] = $pid;
//		if ($title)
//			$map['name'] = array('like', "%{$title}%");
		$map=array();
		$list = M("RepairPartsCity")->join('gc_repair_parts on gc_repair_parts.id = gc_repair_parts_city.partid')->where($map)->field("gc_repair_parts.name,gc_repair_parts_city.*")->order('areaid')->select();

		$list = get_provice_city_area($list);
		$this->assign('list', $list);

		// 记录当前列表页的cookie
		Cookie('__forward__', $_SERVER['REQUEST_URI']);

		$this->meta_title = '维修配件列表';
		$this->display();
	}

	/**
	 * 维修配件添加
	 * @date: 2015-7-1
	 * @author: BillyZ
	 * @return:
	 */
	public function add()
	{
		if (IS_POST) {
			$mParts = M('RepairPartsCity');
			$data = $mParts->create();
			if ($data) {
				$id = $mParts->add();
				if ($id) {
					$this->success('新增成功', Cookie('__forward__'));
				} else {
					$this->error('新增失败');
				}
			} else {
				$this->error($mParts->getError());
			}
		} else {
			$this->meta_title = '新增区域配件';
			$this->display('edit');
		}
	}

	/**
	 * 维修配件编辑
	 * @date: 2015-7-1
	 * @author: BillyZ
	 * @return:
	 */
	public function edit($id = 0)
	{
		if (IS_POST) {
			$mParts = M('RepairPartsCity');
			$data = $mParts->create();
			if ($data) {
				if ($mParts->save() !== false) {
					$this->success('更新成功', Cookie('__forward__'));
				} else {
					$this->error('更新失败');
				}
			} else {
				$this->error($mParts->getError());
			}
		} else {
			$info =M("RepairPartsCity")->join('gc_repair_parts on gc_repair_parts.id = gc_repair_parts_city.partid')->where(array('gc_repair_parts_city.id'=>I('id')))->field("gc_repair_parts.name,gc_repair_parts_city.*")->order('areaid')->find();
			$this->assign('info', $info);
			$this->meta_title = '编辑区域配件';
			$this->display();
		}
	}

	/**
	 * 删除维修配件
	 * @date: 2015-7-1
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
		if (M('RepairParts')->where($map)->delete()) {

			$this->success('删除成功');
		} else {
			$this->error('删除失败！');
		}
	}
}

