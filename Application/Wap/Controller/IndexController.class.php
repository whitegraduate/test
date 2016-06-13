<?php
// +----------------------------------------------------------------------
// | 视界无限 - SEERSEE
// +----------------------------------------------------------------------
// | Copyright (c) 2015
// +----------------------------------------------------------------------
// | Author: Nice <hupeipei@seersee.com>
// +----------------------------------------------------------------------

namespace Wap\Controller;
use Think\Controller;
use Think\Log;


/**
 * 描述：首页控制器
 * Class IndexController
 * @package Home\Controller
 */
class IndexController extends Controller {
 
	public function index()
	{
		$is_index = $_SERVER["PATH_INFO"]=="Index/index"?1:0;
		$citylist = M('City')->select();
		$this->assign('citylist',$citylist);

		$str = '<div class="citychange"><p>尊敬的用户，请您选择您所在的城市</p>';
		foreach($citylist as $key=>$v)
		{
			$str.='<a class="cityid" id="'.$v['id'].'" onclick="setthiscity('.$v['id'].')">'.$v["city"].'</a>';
		}
		$str.='</div>';
		$this->assign('allcity',$str);
		$this->assign('is_index',$is_index);
		$this->display();
	}
	
    /**
    * 一键导航
    * @date: 2015-8-4
    * @author: BillyZ
    * @return:
    */
    public function route()
    {
    	$code = I('code');
    	$id = I('id');
    	$destination;
    	switch ($code)
    	{
    		case "MD":
    			$shop = M('Shop')->where(array('id'=>$id))->find();
    			$destination['latitude'] = $shop['latitude'];
    			$destination['longitude'] = $shop['longitude'];
    			break;
    		case "CD":
    			$charger = M('Charger')->where(array('id'=>$id))->find();
    			$destination['latitude'] = $charger['latitude'];
    			$destination['longitude'] = $charger['longitude'];
    			break;
    		default:
    			break;
    	}
    
    	$this->assign('des',$destination);
		$this->wap_title = "导航";
    	$this->display();
    }
    
    /**
    * 积分商城
    * @date: 2015-8-4
    * @author: BillyZ
    * @return:
    */
    public function exchange()
    {
    	$where['status'] = 1;
    	$where['valid_time'] = array('gt',time());
    	$where['num'] = array('gt',0);
		$gifts = M('ScoreGift')->where($where)->order('create_time desc')->select();
		$this->assign('gifts',$gifts);
    	$this->wap_title = '积分商城';
    	$this->display();
    }
    
    public function qrcode()
    {
    	vendor("phpqrcode");
		$oid= I('oid');
		$code = I('code');
		switch($code)
		{
			case "BY":
				$url = C('site_url').'/wxpay/wap.php?s=/Maintain/pay/df/1/oid/'.$oid.'.html';
				break;
			case "CD":
				$url = C('site_url').'/wxpay/wap.php?s=/Charge/pay/df/1/oid/'.$oid.'.html';
				break;
			case "ZC":
				$url = C('site_url').'/wxpay/wap.php?s=/Rent/pay/df/1/oid/'.$oid.'.html';
				break;
			case "WX":
				$url = C('site_url').'/wxpay/wap.php?s=/Repair/pay/df/1/oid/'.$oid.'.html';
				break;
			case "XC":
				$url = C('site_url').'/wxpay/wap.php?s=/Mall/pay/df/1/oid/'.$oid.'.html';
				break;
			default:
				break;
		}
    	
		if($url)
    		return \QRcode::png($url);
		else 
			return '';
    }

}