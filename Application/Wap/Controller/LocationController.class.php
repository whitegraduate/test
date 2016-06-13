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
 * 描述：位置处理服务
 * Class LocationController
 * @package Home\Controller
 */
class LocationController extends WapController {

    public function index(){
        Log::weixin_info('loc_openid:'.$this->gOpenId);
        $userdata['openid']=$this->gOpenId;
        $data['city'] =  $_POST['city'];
        $data['province'] =  $_POST['province'];
        $data['status'] =  1;
        $cityServer = M("CityServer")->alias("a")->join("Left join gc_city b on a.cityid = b.id")->where($data)->find();
        $userCity = M("FansCity")->where($userdata)->getField("city");
        if($cityServer){
            if($userCity){
                if($cityServer['cityid']!=$userCity){
                    $this->ServerCityChange($data);
                }
            }
            else{
                $this->ServerCityLink($data);
            }
        }
        else{
            if(!$userCity){
                $this->moreCityLink();
            }
        }
    }

    /**
     * 有服务城市下改变城市
     * @param $user
     * @param $city
     */
    Public function ServerCityChange($city){
        $this->assign("message","尊敬的用户，您所在的城市已改变，是否将所在城市切换至".$city['city']."?");
        $this->display("ServerCityChange");
    }

    /**
     * 有服务城市下关联城市
     */
    Public function ServerCityLink($city){
        $this->assign("message","尊敬的用户，您所在城市是否为".$city['city']."?");
        $this->display("ServerCityLink");

    }

    /**
     * 手动城市关联
     */
    Public function moreCityLink(){
        $citys = M("City")->select();
        $this->assign("citys",$citys);
        $this->assign("message","尊敬的用户，请您选择您所在的城市");
        $this->display("moreCityLink");

    }

    /**
     * 设置城市
     */
    public function setCity(){
        $userdata['openid']=$this->gOpenId;
        $citydata['city'] =  $_POST['city'];
        $citydata['province'] =  $_POST['province'];
        $cityid = M("City")->where($citydata)->getField("id");
        if($cityid){
            if(M("FansCity")->where($userdata)->find()){
                if(M("FansCity")->where($userdata)->setField("city",$cityid)){
                    $this->success("城市修改成功");
                }
                else{
                    $this->error("城市修改失败");
                }
            }
            else{
                if(M("FansCity")->where($userdata)->add(array("openid"=>$userdata['openid'],"city"=>$cityid))){
                    $this->success("城市设定成功");
                }
                else{
                    $this->error("城市设定失败");
                }
            }
        }
        else{
            $this->error("尚未对该城市开通服务");
        }
    }


    /**
     * 通过id设置城市
     */
    public function setCityid(){
        $userdata['openid']=$this->gOpenId;
        $citydata['id'] =  $_POST['cityid'];
        $cityid = M("City")->where($citydata)->getField("id");
        if($cityid){
            if(M("FansCity")->where($userdata)->find()){
                if(M("FansCity")->where($userdata)->setField("city",$cityid)){
                    $this->success("城市修改成功");
                }
                else{
                    $this->error("城市修改失败");
                }
            }
            else{
                if(M("FansCity")->where($userdata)->add(array("openid"=>$userdata['openid'],"city"=>$cityid))){
                    $this->success("城市设定成功");
                }
                else{
                    $this->error("城市设定失败");
                }
            }
        }
        else{
            $this->error("尚未对该城市开通服务");
        }
    }

    /**
     * 通过id设置城市
     */
    public function getmycity(){
        $openid=$this->gOpenId;
        $cityid = M('FansCity')->where(array('openid'=>$openid))->getField('city');
        $cityinfo =M('City')->where(array('id'=>$cityid))->find();
        $this->ajaxReturn($cityinfo,'JSON');
    }
}

