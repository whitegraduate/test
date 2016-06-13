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
use User\Api\UserApi;


/**
 * 描述：前台充电服务
 * Class ChargeController
 * @package Home\Controller
 */
class SechandController extends WapController {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 二手车首页
     * @author: MC <zhouyibin@seersee.com>
     */
    public function index(){
        $openid = $this->gOpenId;
        $mFans = M('Fans');
        $fans = $mFans->where(array(
            'openid' => $openid
        ))->find();
        $this->assign('fans', $fans);

        if(IS_POST)
        {
            $data = I('post.');
            Log::write('sechandpost'.json_encode($data));
            $data['pic'] = I('imagesid');
            $data['create_time']=time();
            $data['status']=1;
            $data['openid'] = $this->gOpenId;

            $mediaId = I('imagesid');
            $picPath = './Uploads/file/';
            $picName = $mediaId . '.jpg';
            $picture = $this->gWechatObj->getMedia($mediaId);
            saveFile($picPath . $picName, $picture);
            $data['pic'] = substr($picPath, 1) . $picName;

            $res = M('Sechand')->add($data);
            if($res)
            {
                $this->ajaxReturn('<script type="text/javascript">galert("工作人员将尽快为您审核二手车信息,审核结果将会以微信消息通知");window.location.href="' . U('index') . '";</script>', 'EVAL');
            }
            else
            {
                $this->ajaxReturn('<script type="text/javascript">galert("操作失败！");window.location.href="' . U('index') . '";</script>', 'EVAL');
            }
        }
        else
        {   
            $Brandlist = M('Brand')->where(array('is_famous'=>100))->select();

            $this->assign('package',$this->gWechatObj->getSignPackage());
            $this->assign('brandlist',$Brandlist);
            $this->assign('price',$price);
            $this->wap_title = "二手车首页";
            $this->display();
        }
    }



    public function test()
    {
        $this->assign('package',$this->gWechatObj->getSignPackage());
        $this->display();
    }

    /**
     * 充电下单
     * @author: MC <zhouyibin@seersee.com>
     */
    public function indexlist(){
        $para = I('get.');
        $price = $para['price'];
        $buydate = $para['buy_date'];
        $border = $para['border'];
        $page = $para['page'];

        switch($border)
        {
            case '从低到高':
                $bikeorder = "price_customer asc";
                break;
            case '从高到低':
                $bikeorder = "price_customer desc";
                break;
            default:
            	$bikeorder = "create_time desc";
            	break;
        }
        switch($price)
        {
            case '500以下':
                $map['price_customer']  = array('lt',500);
                break;
            case '500~1000':
                $map['price_customer'] = array(array('egt',500),array('lt',1000));
                break;
            case '1000~1500':
                $map['price_customer'] = array(array('egt',1000),array('lt',1500));
                break;
            case '1500以上':
                $map['price_customer']  = array('gt',1500);
                break;
        }
        if(!empty($buydate) && $buydate != "全部")
        {
            $map['buy_date'] = $buydate;
        }

        $map['status']= array('in','2,4');
        
        //$list = M('Sechand')->where($map)->order($bikeorder)->select();
        
        $list = $this->lists('Sechand', $map, $bikeorder, true, $page);
        if($list['datas'])
        {
        	for($i=0;$i<count($list['datas']);$i++)
        	{
        		$list['datas'][$i]['statusname'] = get_sechand_status($list['datas'][$i]['status']);
        	}
        }
        $this->ajaxreturn($list);
    }

    public function iwantsell()
    {
        if(IS_POST)
        {
            $data = I('post.');
            $data['pic'] = I('icon');
            $data['create_time'] = time();
            $data['status'] = 1;
            $data['is_changedbattery'] = I('sport');
            $data['openid'] = $this->gOpenId;//增加Openid

            $res = M('Sechand')->add($data);
            if($res)
            {
                $this->ajaxReturn('<script type="text/javascript">alert("工作人员将尽快为您审核二手车信息\\n审核结果将会以微信消息通知");window.location.href="' . U('index') . '";</script>', 'EVAL');
            }
            else
            {
                $this->ajaxReturn('<script type="text/javascript">alert("操作失败！");window.location.href="' . U('index') . '";</script>', 'EVAL');
            }
        }
        else
        {
            $Brandlist = M('Brand')->select();
            $this->assign('brandlist',$Brandlist);
            $this->wap_title = "我想卖";
            $this->display();
        }
    }

    public function imgupload()
    {
        $mid = I('medid');
        $imgs = array();
        $asstoken = $this->gWechatObj->checkAuth();
        foreach ($mid as $v){
            $url = "http://file.api.weixin.qq.com/cgi-bin/media/get?access_token={$asstoken}&media_id={$v}";
            log::write('图片地址：'.$url);
            $img = file_get_contents($url);
            $rand = rand(100, 999);
            $pics = date("YmdHis") . $rand ;//上传路径
            $pic_path = "uploads/file/". $pics.'.jpg';
            file_put_contents($pic_path,$img);
            $pic_path = C('site_url').'/'.$pic_path;
            log::write('保存地址：'.$pic_path);
            array_push($imgs, $pic_path);
        }
        $data['images'] = json_encode($imgs);
    }

    public function iwantbuy()
    {

    }

    public function detail()
    {
        $id=I('id');
        $info = M('Sechand')->where(array('id'=>$id))->find();
        $this->assign('info',$info);

        $this->wap_title = "二手车详情";
        $this->display();
    }
}