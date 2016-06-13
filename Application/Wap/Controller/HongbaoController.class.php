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
use OT\Wechat\Wechat;
use User\Api\UserApi;
use OT\Wechat\SDKRuntimeException;
use OT\Wechat\UnifiedOrder;
use OT\Wechat\PayConfig;
use OT\Wechat\JsApi;

/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class HongbaoController extends CommonController {

    protected $gWechatObj; //微信接口
    protected $gOpenId; //用户openid
    protected $gUnionId; //用户unionid
    protected $uid; //用户ID

    /* 空操作，用于输出404页面 */

    public function _empty() {
        $this->redirect('Index/404');
    }

    /**
     * 红包首页(跳转中间页)
     * @author: JasonZ
     */
    public function index() {
        $openid = $this->gOpenId;
        $this->assign('openid', $openid);
        $this->assign('nowtime', NOW_TIME);
        $this->wap_title = '我要红包';
        $this->assign('jsapi_sign_package', $this->gWechatObj->getSignPackage());
        $this->display();
    }

    /**
     * 红包首页(跳转后页)
     * @author: JasonZ
     */
    public function index0() {
        $openid = $this->gOpenId;
        $this->assign('openid', $openid);
        $this->assign('nowtime', NOW_TIME);
        $this->wap_title = '我要红包';
        $this->assign('jsapi_sign_package', $this->gWechatObj->getSignPackage());
        $this->display();
    }


//

    /**
     * 红包分享说明页
     * @author: JasonZ <zhouyibin@seersee.com>
     */
    public function share() {
        $openid = $this->gOpenId;
        $this->assign('openid', $openid);
        $this->assign('nowtime', NOW_TIME);
        $this->wap_title = '分享红包';
        $this->assign('jsapi_sign_package', $this->gWechatObj->getSignPackage());
        $this->display();
    }

    /**
     * 初始化
     * @author: Nice <hupeipei@seersee.com>
     */
    protected function _initialize() {
        parent::_initialize();
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config); //添加配置

        if (!C('WEB_SITE_CLOSE')) {
            $this->error('站点已经关闭，请稍后访问~');
        }

        /* if (!is_weixin_agent()) {
          echo '请在微信中打开页面。';
          exit;
          } */

        if (!$this->gWechatObj) {
            $this->gWechatObj = $this->weObj;
            Log::weixin_info('复制了Wechat对象');
        }

        $callBack = get_current_url();
        Log::weixin_info('callback=' . $callBack);
        $this->gOpenId = session_get_openid();
        $this->gUnionId = session_get_unionid();

        ////test
        // $this->gOpenId = "wxb1d1a6ed32ae47f8";
        //$this->gUnionId = "111111";
        if (empty($this->gOpenId)) {
            Log::weixin_info('empty openid');
            $result = $this->getOpenId();
            if (!isset($_GET['from'])) {
                if (!$result) {
                    Log::weixin_info('!result');
                    // 跳转网页基础授权，获取openid和unionid
                    $url = $this->gWechatObj->getOauthRedirect($callBack, 'base', 'snsapi_base');
                    header('Location: ' . $url);
                    exit;
                } else {
                    Log::weixin_info('result');
                    $mFans = M('Fans');
                    $wechat = $mFans->where(array('openid' => $this->gOpenId))->find();
                    if (empty($wechat)) {
                        $wechat['nickname'] = '';
                        $wechat['openid'] = $this->gOpenId;
                        $wechat['unionid'] = $this->gUnionId;
                        $wechat['sex'] = 0;
                        $wechat['wecha_binded'] = 0;
                        $wechat['create_time'] = NOW_TIME;
                        //$mFans->add($wechat);
                    }
                    session('wechat', $wechat);
                }
            } else {
                if (isset($_GET['fromoid'])) {
                    $this->gOpenId = $_GET['fromoid'];
                    $wechat = M('Fans')->where(array('openid' => $this->gOpenId))->find();
                    if ($wechat) {
                        session('wechat', $wechat);
                    }
                }
                Log::weixin_info('logininfo');
            }
        } else {
            Log::weixin_info('not empty openid');
        }
    }

    /*     * *************************************************************************************************
     *                                      内部私有方法定义
     * *********************************************************************************************** */

    /**
     * 获取粉丝openid，优先级：缓存 -> 网页授权
     * @return bool
     * @author: Nice <hupeipei@seersee.com>
     */
    private function getOpenId() {
        $code = I('code');
        if (!empty($code)) {
            $json = $this->gWechatObj->getOauthAccessToken();
            if ($json) {
                $this->gOpenId = $json["openid"];
                $this->gUnionId = $json["unionid"];
                Log::weixin_info('网页基础授权 OpenId===' . $this->gOpenId . '; UnionId===' . $this->gUnionId);
                return true;
            } else {
                Log::weixin_info('网页基础授权 JSON为空');
            }
        } else {
            Log::weixin_info('网页基础授权 code为空，转重定向');
        }
        return false;
    }

    /**
     * 检测用户是否关注
     * @author: JasonZ
     * 2016-3-3
     */
    public function subscribe() {
        $wechat = session('wechat');
        if (empty($wechat['subscribe'])) {
            //未记录关注，先获取用户信息
            $openId = $this->gOpenId;
            $userInfo = $this->gWechatObj->getUserInfo($openId);
            if (empty($userInfo) || empty($userInfo['subscribe'])) {
                $data['msg'] = 'http://mp.weixin.qq.com/s?__biz=MzA3MjYxMjQ1Mw==&mid=402580261&idx=1&sn=176da9f35c5dd5b7558a1e3658d30bb1&scene=0&previewkey=pBj0vQDeh5rTXI%2FbyrTP48NS9bJajjJKzz%2F0By7ITJA%3D#wechat_redirect';
                $data['flag'] = -1;
                $this->ajaxReturn($data);
            } else {
                //如果已关注,则进入页面该页面就注册为会员或者更新会员信息.
                $this->createWechatFans();
                $data['openid'] = $openId;
                // $data['msg'] = '已关注';
                $data['flag'] = 0;
                $this->ajaxReturn($data);
            }
        } else {
            $this->createWechatFans();
            $data['openid'] = $openId;
            // $data['msg'] = '已关注';
            $data['flag'] = 0;
            $this->ajaxReturn($data);
        }
    }

    /**
     * 领取红包逻辑
     * @author: 小童 JasonZ
     * 2016.3.3
     */
    public function redPaperSave($PaperId) {
        //领取用户
        //$openId='oZVgkt7ftdNAFe89eLeF_22NdmqM';
        $openId = $this->gOpenId;
        //红包ID
        //$redpaperSendId =  '1';
        $redpaperSendId = $PaperId;
        //红包信息
        $redpaperSend = M('RedpaperSend')->where(array('ids' => $redpaperSendId))->find();

        if (empty($redpaperSend)) {
            $data['msg'] = '红包不存在！';
            $data['flag'] = -1;
            $this->ajaxReturn($data);
        }
        //1.判断是否过期
        if ($redpaperSend['sendtime'] + $redpaperSend['days'] * 24 * 3600 < NOW_TIME) {
            $data['msg'] = '亲！该红包已经过期！';
            $data['flag'] = -1;
            $this->ajaxReturn($data);
        }
        //2.判断是否领完
        $map['ids'] = $redpaperSendId;
        $redpaperReceiveAll = M('RedpaperReceive')->where($map)->field(true)->order('ids asc')->select();
        if (count($redpaperReceiveAll) >= $redpaperSend['qty']) {
            $data['msg'] = '该红包已经领完！';
            $data['flag'] = -1;
            $this->ajaxReturn($data);
        }
        //3.判断是否领过红包
        $map['openid'] = $openId;
        $redpaperReceive = M('RedpaperReceive')->where($map)->find();
        if (!empty($redpaperReceive)) {
            $data['msg'] = '亲！您已经领取过该红包！';
            $data['flag'] = -1;
            $this->ajaxReturn($data);
        }
        //领取人信息
        $userInfo = $this->gWechatObj->getUserInfo($openId);

        //4.插入优惠券数据
        //uid 是收包人的ID,$mFans是收包人的信息
        $mFans = M('Fans')->where(array('openid' => $openId))->find();
        $uid = $mFans['id'];
        if ($uid === null) {
            $data['msg'] = '请先成为粉丝'; //小童把粉丝插入函数写好后直接调用.
            $data['flag'] = -1;
            $this->ajaxReturn($data);
        }

        //4.插入优惠券数据
        $mFansCoupon = M('FansCoupon');
        $fans = get_obj_fans($uid);
        $coupon = get_obj_coupon($redpaperSend['couponid']);
        $fans_coupon['uid'] = $uid;
        $fans_coupon['cid'] = $redpaperSend['couponid'];
        $fans_coupon['cname'] = $coupon['name'];
        $fans_coupon['total_times'] = 1;
        $fans_coupon['remain_times'] = 1;
        $fans_coupon['create_time'] = NOW_TIME;
        $fans_coupon['end_time'] = $fans_coupon['create_time'] + $coupon['days'] * 24 * 3600;
        $fans_coupon['status'] = 1;
        $fans_coupon['service_type'] = $coupon['service_type'];
        $fans_coupon['code'] = new_coupon_id($coupon['service_type'], $fans['openid']);
        $fans_coupon['face'] = $coupon['face'];
        if ($mFansCoupon->add($fans_coupon) === false) {
            $data['msg'] = '插入优惠券失败,请检查逻辑';
            $data['flag'] = -1;
            $this->ajaxReturn($data);
        }

        //5.插入收红包数据
        $mRedpaper = M('RedpaperReceive');
        $mdata['ids'] = $redpaperSendId;
        $mdata['openid'] = $openId;
        $mdata['receivetime'] = NOW_TIME;
        $mdata['code'] = $fans_coupon['code'];
        if ($mRedpaper->add($mdata) === false) {
            $data['msg'] = '插入收红包数据,请检查逻辑';
            $data['flag'] = -1;
            $this->ajaxReturn($data);
        }
    }

    /**
     * 领取红包流程
     * @author: 小童 JasonZ 
     * 2016-3-2
     */
    public function getRedPaper($PaperId) {
        $wechat = session('wechat');

        $openId = $this->gOpenId;
        $userInfo = $this->gWechatObj->getUserInfo($openId);

        $this->redPaperSave($PaperId);

        $map2['ids'] = $PaperId;
        $mlist = $this->lists_join('RedpaperReceive', $map2, 'ids desc', true, "inner join gc_fans_coupon b on a.code = b.code ", "b.cname,b.end_time,b.face");
        $msginfo = '恭喜您获得：' . $mlist[0]['cname'] . ',面值：' . $mlist[0]['face'] . '(元),到期日期：' . date('Y-m-d', $mlist[0]['end_time']);

        $wechat['openid'] = $openId;
        $wechat['nickname'] = $userInfo['nickname'];
        $wechat['headimgurl'] = $userInfo['headimgurl'];
        $wechat['sex'] = $userInfo['sex'];
        $wechat['subscribe'] = $userInfo['subscribe'];
        $wechat['subscribe_time'] = $userInfo['subscribe_time'];
        $wechat['address'] = $userInfo['province'] . $userInfo['city'];
        $wechat['unionid'] = $userInfo['unionid'];
        $wechat['is_auth'] = 1;

        $data['openid'] = $wechat['openid'];
        //$data['msg'] = '尊敬的' . $wechat['nickname'] . '您已领取' . '</br>' . $mlist[0]['nickname'] . '</br>' . '赏的红包!';
        $data['msg'] = $msginfo;
        $data['face'] = $mlist[0]['face'];
        $data['end_time'] = date('Y-m-d', $mlist[0]['end_time']);
        $data['flag'] = 0;
        $this->ajaxReturn($data);
    }

    /**
     * 获取发红包人信息
     * @author: JasonZ 
     * 2016-3-10
     */
    public function getsendname($PaperId) {
        $map2['ids'] = $PaperId;
        $mlist = $this->lists_join('RedpaperSend', $map2, 'ids desc', true, "inner join gc_fans b on a.openid = b.openid ", "b.nickname");
        $data['nickname'] = $mlist[0]['nickname'];
        $this->ajaxReturn($data);
    }

    /**
     * 获取该红包被收取的历史信息
     * @author: JasonZ 
     * 2016-3-11
     */
    public function getReceiveData($PaperId) {
        $map2['ids'] = $PaperId;
        $mlist = $this->lists_join('RedpaperReceive', $map2, 'idr asc', true, "inner join gc_fans_coupon b on a.code = b.code inner join gc_fans c on c.openid=a.openid ", "c.headimgurl,CAST(b.face AS SIGNED) face,case when LENGTH(c.nickname)>9 then concat(left(c.nickname, 3),'..') else c.nickname end nickname");
        $data['nickname'] = '';
        $data['face'] = '';
        $data['headimgurl'] = '';
        $json = '{"data":[';
        for ($i = 0; $i < count($mlist); $i++) {
            $json = $json . '{"nickname":"' . $mlist[$i]['nickname'] . '","face":"'
                    . $mlist[$i]['face'] . '","headimgurl":"'
                    . $mlist[$i]['headimgurl'] . '"},';
        }
        $json = substr($json, 0, strlen($json) - 1);
        $json = $json . ']}';
        $this->ajaxReturn($json);
    }

    /**
     * 描述：获取微信用户信息，创建Fans
     * @author JasonZ
     * 2016-3-4
     */
    private function createWechatFans() {
        $openId = $this->gOpenId;
        $sceneId = 258;
        $userinfo = $this->weObj->getUserInfo($openId); //UnionID机制拉取用户信息(为判断是否已经关注平台)
        Log::getWeixinInfo("抓取用户信息：" . json_encode($userinfo) . "   TIME:" . date('Y-m-d', NOW_TIME), "", "", C('LOG_PATH') . "Fans" . date('y_m_d') . ".log");
        if ($userinfo['subscribe'] == 0) {
            Log::getWeixinInfo("抓取用户信息失败：" . json_encode($userinfo) . "   TIME:" . date('Y-m-d', NOW_TIME), "", "", C('LOG_PATH') . "Fans" . date('y_m_d') . ".log");
            return false;
        }

        $fansset['openid'] = $openId;
        $fansset['nickname'] = $userinfo['nickname'];
        $fansset['headimgurl'] = $userinfo['headimgurl'];
        $fansset['sex'] = $userinfo['sex'];
        $fansset['subscribe'] = 1;
        $fansset['subscribe_time'] = $userinfo['subscribe_time'];
        $fansset['address'] = $userinfo['country'] . $userinfo['province'] . $userinfo['city'];
        $fansset['unionid'] = $userinfo['unionid'];
        $fans = M('Fans')->where(array('openid' => $openId))->find();
        if ($fans) {
            if (M('Fans')->where(array('openid' => $openId))->save($fansset)) {
                Log::getWeixinInfo("Save Fans success!" . $fans['nickname'] . "--" . $openId);
            } else {
                Log::getWeixinInfo("Save Fans fail!" . $fans['nickname'] . "--" . $openId);
            }
        } else {
            $fansset['create_time'] = NOW_TIME;
            if ($sceneId) {
                $fansset['canalid'] = $sceneId;
                $fansset['intro'] = '通过场景id=' . $sceneId . '关注';
            }
            $uid = M('Fans')->add($fansset);
            if ($uid) {
                giveCoupon($uid, 1);
                Log::getWeixinInfo("Add Fans success!" . $fans['nickname'] . "--" . $openId);
            } else {
                Log::getWeixinInfo("Add Fans fail!" . $fans['nickname'] . "--" . $openId);
            }
        }
    }

    /**
     * 是否领取过红包
     * @author: JasonZ 
     * 2016-3-14
     */
    public function isGetPaper($PaperId) {
        $map2['ids'] = $PaperId;
        $map2['openid'] = $this->gOpenId;
        $redpaperReceiveAll = M('RedpaperReceive')->where($map2)->field(true)->order('ids asc')->select();

        if (count($redpaperReceiveAll) == 0) {
            $data['msg'] = '没领过！';
            $data['flag'] = 0;
            $this->ajaxReturn($data);
        } else {
            $data['msg'] = '领过了！';
            $data['flag'] = 1;

            $map['ids'] = $PaperId;
            $mlist = $this->lists_join('RedpaperReceive', $map, 'ids desc', true, "inner join gc_fans_coupon b on a.code = b.code ", "b.cname,b.end_time,b.face");
            $data['face'] = $mlist[0]['face'];
            $data['end_time'] = date('Y-m-d', $mlist[0]['end_time']);
            $this->ajaxReturn($data);
        }
    }

    /**
     * 根据订单ID获取红包ID
     * @author: JasonZ 
     * 2016-3-21
     */
    public function getPaidByOid($oid) {
        $mFans = M('RedpaperSend')->where(array('orderid' => $oid))->find();
        $data['ids'] = $mFans['ids'];
        $this->ajaxReturn($data);
    }

}
