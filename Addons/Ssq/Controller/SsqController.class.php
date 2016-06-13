<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Addons\Ssq\Controller;
use Home\Controller\AddonsController; 

class SsqController extends AddonsController{

	//实现的province-city-area钩子方法
	public function get_area(){
		$cityid = I('get.cityid');
		$arealist = M('CityArea')->where(array('cityid'=>$cityid))->select();
		$this->ajaxReturn($arealist,'JSON');
	}

}
