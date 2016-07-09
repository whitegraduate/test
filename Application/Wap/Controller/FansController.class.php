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
use Think\Model;

/**
 * 描述：会员控制器
 * Class WeixinController
 *
 * @package Home\Controller
 */
class FansController extends WapController {

    public function _initialize() {
        parent::_initialize();
        // parent::login();
        load('@.user');
    }

    public function session_clr() {
        session('wechat', '');
    }

    public function zhengze() {
        addscore_bind('o3g0Gs5Ueri5czuTHNceerHcNr_o');
    }

    /**
     * 个人中心
     * @date: 2015-7-20
     *
     *
     *
     * @author : BillyZ
     * @return :
     */
    public function index() {
        redirect(U('Fans/myorders'));
        return;
    }

    /**
     * 个人中心
     * @date: 2015-7-20
     *
     * @author : BillyZ
     * @return :
     */
    public function my() {
        // $this->wxlogin();
        $openid = $this->gOpenId;

        $mFans = M('Fans');
        $fans = $mFans->where(array(
                    'openid' => $openid
                ))->find();
        $this->assign('fans', $fans);

        if (IS_POST) {
            $data = I('post.');
            $data['id'] = $fans['id'];
            Log::write(json_encode($data));
            $mFans->save($data);

            /* if ($isnew) {
              log::write ( 'openid=' . $openid );
              addscore_bind ( $openid );

              log::write ( '2,' );
              }
             */
            $this->success('保存成功');
        } else {
            $this->wap_title = '个人信息';
            $this->display();
        }
    }

    /**
     * 车辆认证编辑
     * @date: 2015-7-20
     *
     * @author : BillyZ
     * @return :
     */
    public function auth_bike_edit() {
        // $this->wxlogin();
        $openid = $this->gOpenId;
        $goback = $_GET['goback'];

        $mFans = M('Fans');
        $fans = $mFans->where(array(
                    'openid' => $openid
                ))->find();
        $this->assign('fans', $fans);
        $mBike = M('FansBike');
        if (IS_POST) {
            Log::write('post begin;');


            // 1.增加车辆或重新编辑车辆
            $bike ['uid'] = $fans ['id'];
            $bike ['brandid'] = $_POST ['brandid'];
            $bike ['bike'] = $_POST ['bike'];
            $bike ['buy_time'] = strtotime($_POST ['buy_time']);
            if (I("get.bid")) {
                $bike["id"] = I("get.bid");
                $fans = M("Fans")->where(array("openid" => $openid))->find();
                $uid = M("FansBike")->where(array("id" => I("get.bid")))->getField("uid");
                if ($fans['id'] != $uid) {
                    $this->success('车辆不存在或无权限', U('Fans/auth_bike'), false, true);
                    exit;
                }
                $mBike->save($bike);
            } else {
                $bike ['create_time'] = NOW_TIME;
                $mBike->add($bike);
                // 2.更改认证状态
                $fans['auth_bike'] = 1;
                $mFans->save($fans);

                //3.认证送积分
                addscore_auth($openid);
            }

            if (empty($goback)) {
                $this->success('保存成功', U('Fans/info'), false, false);
            } else {
                $goback_url = str_ireplace(';', '/', $goback);
                $this->success('保存成功', U($goback_url), false, false);
            }
        } else {
            $lstBrand = M("Brand")->select();
            $this->assign('lstBrand', $lstBrand);

            $bike = $mBike->where(array(
                        'uid' => $fans ['id'],
                        'id' => I("bid")
                    ))->find();
            if (empty($bike) && I("bid")) {
                $this->success('车辆不存在或无权限', U('Fans/auth_bike'), false, true);
                exit;
            }
            $this->assign('bike', $bike);

            $this->wap_title = '车辆编辑';
            $this->display();
        }
    }

    /**
     * 车辆认证删除
     * @author ZhouWC
     */
    public function auth_bike_del() {
        $bid = I("bid");
        $openid = $this->gOpenId;
        $mFans = M('Fans');
        $fans = $mFans->where(array(
                    'openid' => $openid
                ))->find();
        $this->assign('fans', $fans);

        $data['flag'] = 0;

        $bike = M('FansBike')->where(array('id' => $bid, 'uid' => $fans['id']))->find();
        if ($bike) {
            M('FansBike')->where(array('id' => $bid))->delete();
            $data['flag'] = 1;
        } else {
            $data['msg'] = '车辆不存在或无权限';
        }

        $this->ajaxReturn($data);
    }

    /**
     * 车辆认证
     * @author ZhouWC
     */
    public function auth_bike() {
        $openid = $this->gOpenId;
        $goback = $_GET['goback'];

        $mFans = M('Fans');
        $fans = $mFans->where(array(
                    'openid' => $openid
                ))->find();
        $this->assign('fans', $fans);
        $mBike = M('FansBike');
        $lstBike = $mBike->where(array(
                    'uid' => $fans ['id']
                ))->select();
        $this->assign('lstBike', $lstBike);
        $this->wap_title = '车辆认证';
        $this->display();
    }

    /**
     * 身份认证
     * @date: 2015-7-20
     *
     * @author : BillyZ
     * @return :
     */
    public function auth_idcard() {
        // $this->wxlogin();
        $openid = $this->gOpenId;

        $mFans = M('Fans');
        $fans = $mFans->where(array('openid' => $openid))->find();
        $this->assign('fans', $fans);
        if (IS_POST) {
            $fans['idcard'] = I('idcard');
            $fans['realname'] = I('realname');
            $fans['idcard_status'] = 1;
            $mediaId = I('media_id');
            if (!empty($mediaId)) {
                $idcardImgPath = './Uploads/Picture/IdCard/';
                $idcardImgName = $mediaId . '.jpg';
                $picture = $this->gWechatObj->getMedia($mediaId);
                saveFile($idcardImgPath . $idcardImgName, $picture);
                $fans['idcard_img'] = substr($idcardImgPath, 1) . $idcardImgName;
            }
            $mFans->save($fans);

            //认证送积分
            addscore_auth($openid);
            if ($fans['mobile']) {
                $this->success('提交成功', U('Fans/info'));
            } else {
                $this->success('提交成功', U('Fans/auth_mobile'));
            }
        } else {
            $this->assign('jsapi_sign_package', $this->gWechatObj->getSignPackage());
            $this->wap_title = '身份认证';
            $this->display();
        }
    }

    /**
     * 个人中心
     * @date: 2015-7-20
     *
     * @author : BillyZ
     * @return :
     */
    public function info() {
        // $this->wxlogin();
        $openid = $this->gOpenId;

        $mFans = M('Fans');
        $fans = $mFans->where(array(
                    'openid' => $openid
                ))->find();

        $fans['orders_unpay'] = count_unpaid_order($openid);
        $fans['coupons'] = M('FansCoupon')->where(array('uid' => $fans['id'], 'status' => 1))->count();
        $bike = M('FansBike')->where(array('uid' => $fans['id']))->find();
        if ($bike) {
            $fans['bike'] = get_brand_id($bike['brandid']) . $bike['bike'];
        }
        //取消二手车显示
        // $fans['sechands'] = count_sechand_bikes($openid);
        $this->assign('fans', $fans);

        if (IS_POST) {
            
        } else {
            // 未支付订单统计
            $this->wap_title = '个人中心';
            $this->display();
        }
    }

    /**
     * 我获取到的积分
     * @date: 2015-7-20
     *
     * @author : BillyZ
     * @return :
     */
    public function score_get() {
        // $this->wxlogin();
        $openId = $this->gOpenId;

        $mFans = M('Fans');
        $fans = $mFans->where(array(
                    'openid' => $openId
                ))->find();
        $this->assign('fans', $fans);

        $this->wap_title = '我的积分';
        $this->display();
    }

    /**
     * 积分记录
     * @date: 2015-7-20
     *
     * @author : BillyZ
     * @return :
     */
    public function scores() {
        $openId = $this->gOpenId;

        $fans = get_obj_fans_openid($openId, true);
        $this->assign('fans', $fans);

        $mLog = M('ScoreLog');
        $logs = $mLog->where(array(
                    'openid' => $openId
                ))->order('createtime desc')->select();
        $this->assign('logs', $logs);

        $this->wap_title = '我的积分';
        $this->display();
    }

    /**
     * 积分消耗
     * @date: 2015-7-20
     *
     * @author : BillyZ
     * @return :
     */
    public function score_use() {
        // $this->wxlogin();
        $openId = $this->gOpenId;

        $mFans = M('Fans');
        $fans = $mFans->where(array(
                    'openid' => $openId
                ))->find();
        $this->assign('fans', $fans);

        $this->wap_title = '积分消耗';
        $this->display();
    }

    /**
     * 会员维修订单
     * @date: 2015-7-20
     *
     * @author : BillyZ
     * @return :
     */
    public function order_repair() {
        $openId = $this->gOpenId;
        $mFans = M('Fans');
        $fans = $mFans->where(array(
                    'openid' => $openId
                ))->find();
        $this->assign('fans', $fans);

        $where ['openid'] = $openId;
        if (isset($_GET ['s'])) {
            $where ['status'] = $_GET ['s'];
        }

        $mOrder = M('RepairOrder');
        $lstOrder = $mOrder->where($where)->order('id desc')->select();

        $this->wap_title = '维修订单';

        $this->assign('lstOrder', $lstOrder);
        $this->display();
    }

    /**
     * 用于前台ajax检查维修订单状态
     * @author ZhouWC
     */
    public function order_repair_check() {
        $orderid = I('oid');
        $order = M("RepairOrder")->where(array("id" => $orderid))->find();
        $this->ajaxReturn(array("status" => $order['status']));
    }

    /**
     * 会员维修订单详情
     * @date: 2015-7-20
     *
     * @author : BillyZ
     * @return :
     */
    public function order_repair_details() {

        $mOrder = M('RepairOrder');
        $orderid = I('oid');
        if (IS_POST) {
            $orderid = $_POST['oid'];
            $order = $mOrder->where(array(
                        'id' => $orderid
                    ))->find();

            $order['status'] = 3;
            M('RepairOrder')->save($order);

            $this->success('取消成功', U('Fans/order_repair_details', array('oid' => $orderid)), false, false);
        } else {
            $openId = $this->gOpenId;
            $mFans = M('Fans');
            $fans = $mFans->where(array(
                        'openid' => $openId
                    ))->find();
            $this->assign('fans', $fans);

            $order = $mOrder->where(array(
                        'id' => $orderid
                    ))->find();

            if ($openId != $order['openid']) {
                //$this->success('非本人订单',U('Fans/order_repair'),false,true);exit;
            }

            $order['past_minutes'] = ceil((NOW_TIME - $order['create_time']) / 60);
            $order['remind_minutes'] = (30 - ceil((NOW_TIME - $order['create_time']) / 60));
            $order['statusname'] = get_repairorder_status($order ['status']);

            $parts_in = json_decode($order['parts_in'], true);
            $parts_out = json_decode($order['parts_out'], true);

            $mOrder = M('RepairParts');
            foreach ($parts_in as $key => $vo) {
                $partinfo = $mOrder->where(array('id' => $vo['pid']))->find();
                $parts_in[$key]['name'] = $partinfo['name'];
                $parts_in[$key]['price'] = $partinfo['price'];
            }

            foreach ($parts_out as $key => $vo) {
                $partinfo = $mOrder->where(array('id' => $vo['pid']))->find();
                $parts_out[$key]['name'] = $partinfo['name'];
                $parts_out[$key]['price'] = $partinfo['price'];
            }
            $this->wap_title = '维修订单详情';

            $this->assign('order', $order);
            $this->assign('parts_in', $parts_in);
            $this->assign('parts_out', $parts_out);
            $this->display();
        }
    }

    /**
     * 会员保养订单
     * @date: 2015-7-20
     *
     * @author : BillyZ
     * @return :
     */
    public function order_maintain()
    {
        redirect(U('Fans/myorders'));
    }

    public function myorders() {
        $openId = $this->gOpenId;
        $mFans = M('Fans');
        $fans = $mFans->where(array(
                    'openid' => $openId
                ))->find();
        $this->assign('fans', $fans);

        $where ['openid'] = $openId;
        if (isset($_GET ['s'])) {
            $where ['status'] = $_GET ['s'];
        }

        $mOrder = M('MaintainOrder');
        $lstOrder = $mOrder->where($where)->order('id desc')->select();
        foreach ($lstOrder as $k => $v) {
            $lstOrder [$k] ['statusname'] = get_maintainorder_status($v ['status']);
            $lstOrder [$k] ['shop'] = get_shop_id($v ['shopid']);
            // $list[$k]['product'] = $this->get_maintain_product_id($v['pid']);
        }
        $this->wap_title = '我的订单';

        $this->assign('lstOrder', $lstOrder);
        $this->display();
    }

    /**
     * 会员保养订单详情
     * @date: 2015-7-20
     *
     * @author : BillyZ
     * @return :
     */
    public function order_maintain_details() {
        $orderid = I('oid');
        $mOrder = M('MaintainOrder');
        if (IS_POST) {
            $orderid = $_POST['oid'];
            $order = $mOrder->where(array(
                        'id' => $orderid
                    ))->find();

            $order['status'] = 3;
            $mOrder->save($order);

            $this->success('取消成功', U('Fans/order_maintain_details', array('oid' => $orderid)), false, false);
        } else {
            $openId = $this->gOpenId;
            $mFans = M('Fans');
            $fans = $mFans->where(array(
                        'openid' => $openId
                    ))->find();
            $this->assign('fans', $fans);

            $order = $mOrder->where(array(
                        'id' => $orderid
                    ))->find();

            if ($openId != $order['openid']) {
                $this->success('非本人订单', U('Fans/order_repair'), false, true);
                exit;
            }

            $order ['statusname'] = get_maintainorder_status($order ['status']);
            $order ['shop'] = get_shop_id($order ['shopid']);
            $order ['product'] = get_maintain_product_id($order ['pid']);

            $this->wap_title = '订单详情';

            $this->assign('order', $order);
            $this->display();
        }
    }

    /**
     * 会员租车订单
     * @date: 2015-7-20
     *
     * @author : BillyZ
     * @return :
     */
    public function order_rent() {
        $openId = $this->gOpenId;
        $mFans = M('Fans');
        $fans = $mFans->where(array(
                    'openid' => $openId
                ))->find();
        $this->assign('fans', $fans);

        $where ['openid'] = $openId;
        if (isset($_GET ['s'])) {
            $where ['status'] = $_GET ['s'];
        }

        $mOrder = M('RentbikeOrder');
        $lstOrder = $mOrder->where($where)->order('id desc')->select();
        foreach ($lstOrder as $k => $v) {
            $lstOrder [$k] ['statusname'] = get_rentorder_status($v ['status']);
            $lstOrder [$k] ['bike'] = get_bike_id($v ['bikeid']);
            // $list[$k]['product'] = $this->get_maintain_product_id($v['pid']);
        }
        $this->wap_title = '租车订单';

        $this->assign('lstOrder', $lstOrder);
        $this->display();
    }

    /**
     * 会员租车订单详情
     * @date: 2015-7-20
     *
     * @author : BillyZ
     * @return :
     */
    public function order_rent_details() {

        $orderid = I('oid');
        $openId = $this->gOpenId;
        $mFans = M('Fans');
        $fans = $mFans->where(array(
                    'openid' => $openId
                ))->find();
        $this->assign('fans', $fans);

        $mOrder = M('RentbikeOrder');
        $order = $mOrder->where(array(
                    'id' => $orderid
                ))->find();

        if ($order['openid'] != $openId) {
            $this->success('非本人订单', U('Fans/order_rent'), false, true);
            exit;
            exit;
        }

        //gps数据调用
        $bike = M('Rentbike')->where(array('id' => $order['bikeid']))->find();
        if ($order['end_time'] == 0) {
            $endtime = time();
        } else {
            $endtime = $order['end_time'];
        }
        //$gpsinfo = $this->get_line_deal($bike['code'],$order['start_time'],$endtime);
        //$this->assign('gps', $gpsinfo);
        //gps数据调用结束

        $order ['statusname'] = get_rentorder_status($order ['status']);
        $order ['bike'] = get_bike_id($order ['bikeid']);

        $order['timespan'] = ceil(($order['return_time'] - $order['start_time']) / 60);

        $this->wap_title = '租车订单详情';

        $this->assign('order', $order);
        $this->display();
    }

    /**
     * 会员充电订单
     * @date: 2015-7-20
     *
     * @author : BillyZ
     * @return :
     */
    public function order_charge() {
        $openId = $this->gOpenId;
        $mFans = M('Fans');
        $fans = $mFans->where(array(
                    'openid' => $openId
                ))->find();
        $this->assign('fans', $fans);

        $where ['openid'] = $openId;
        if (isset($_GET ['s'])) {
            $where ['status'] = $_GET ['s'];
        }

        $mOrder = M('ChargeOrder');
        $lstOrder = $mOrder->where($where)->order('id desc')->select();
        foreach ($lstOrder as $k => $v) {
            $lstOrder [$k] ['statusname'] = get_chargeorder_status($v ['status']);
            // $lstOrder[$k]['bike'] = get_bike_id($v['bikeid']);
            // $list[$k]['product'] = $this->get_maintain_product_id($v['pid']);
        }
        $this->wap_title = '充电订单';

        $this->assign('lstOrder', $lstOrder);
        $this->display();
    }

    /**
     * 会员充电订单详情
     * @date: 2015-7-20
     *
     * @author : BillyZ
     * @return :
     */
    public function order_charge_details() {
        $orderid = I('oid');
        $openId = $this->gOpenId;
        $mFans = M('Fans');
        $fans = $mFans->where(array(
                    'openid' => $openId
                ))->find();
        $this->assign('fans', $fans);
        if (IS_POST) {
            if ($_POST['end'] == "yes") {
                //todo 业务流需要检测优化下Ketory
                if (M('ChargeOrder')->where(array("id" => $_GET['oid']))->setField(array("status" => 2, "end_time" => NOW_TIME))) {
                    $chargeorder = M("ChargeOrder")->where(array("id" => $_GET['oid']))->find();
                    $outlet = M('Outlet')->where(array('id' => $chargeorder['outletid']))->find();
                    $charger = M('Charger')->where(array('id' => $outlet['chargerid']))->find();
                    $url = 'http://121.41.112.29/advice.php?m=' . $charger['deviceid'] . '&p=' . $outlet['code'] . '&s=0';
                    log::write('主动断电url=' . $url);
                    http_get($url);
                    $this->success("感谢您的支持，欢迎再次使用！", true);
                } else {
                    $this->error("数据异常，请重试或联系客服", true);
                }
            }
        }
        $mOrder = M('ChargeOrder');
        $order = $mOrder->where(array('id' => $orderid))->find();

        if ($openId != $order['openid']) {
            $this->success('非本人订单', U('Fans/order_repair'), false, true);
            exit;
        }

        $this->outlet = M('Outlet')->where(array('id' => $order['outletid']))->find();
        $this->charger = M('Charger')->where(array('id' => $this->outlet['chargerid']))->find();

        $this->wap_title = '充电订单详情';
        $order['nowtime'] = NOW_TIME;
        $this->assign('order', $order);
        $this->display();
    }

    /**
     * 会员商城订单
     * @date: 2015-7-20
     *
     * @author : BillyZ
     * @return :
     */
    public function order_mall() {
        $openId = $this->gOpenId;
        $mFans = M('Fans');
        $fans = $mFans->where(array(
                    'openid' => $openId
                ))->find();
        $this->assign('fans', $fans);

        $where ['openid'] = $openId;
        if (isset($_GET ['s'])) {
            $where ['status'] = $_GET ['s'];
        }

        $mOrder = M('MallOrder');
        $lstOrder = $mOrder->where($where)->order('id desc')->select();

        foreach ($lstOrder as $k => $v) {
            $lstOrder [$k] ['statusname'] = get_mallorder_status($v ['status']);
            // $lstOrder[$k]['bike'] = get_bike_id($v['bikeid']);
            // $list[$k]['product'] = $this->get_maintain_product_id($v['pid']);
        }
        $this->wap_title = '商城订单';

        $this->assign('lstOrder', $lstOrder);
        $this->display();
    }

    /**
     * 会员商城订单详情
     * @date: 2015-7-20
     *
     * @author : BillyZ
     * @return :
     */
    public function order_mall_details() {
        $orderid = I('oid');
        $openId = $this->gOpenId;
        $mFans = M('Fans');
        $fans = $mFans->where(array(
                    'openid' => $openId
                ))->find();
        $this->assign('fans', $fans);

        $mOrder = M('MallOrder');
        $order = $mOrder->where(array(
                    'id' => $orderid
                ))->find();
        $product_info = get_mall_id($order['mallid']);
        $shopping_cart_order = $product_info['info'];
        // $mall_quantity=$product_info['quantity']; //原先业务没有产品数量，暂不添加
        $order ['statusname'] = get_mallorder_status($order ['status']);
        // $order['bike'] = get_bike_id($order['bikeid']);

        $this->wap_title = '商城订单详情';
        $this->assign('mallinfo', $shopping_cart_order);
        $this->assign('order', $order);
        $this->display();
    }

    /**
     * 描述：绑定会员
     *
     * @author MC <zhouyibin@seersee.com>
     */
    public function bind() {
        $wechat = session('wechat');
        if (IS_POST) {
            //fans bind
            $openid = $wechat ['openid'];
            $mFans = M('Fans');
            $fans = $mFans->where(array('openid' => $openid))->find();
//shop bind
$pwd = I('post.pwd');
if(strlen($pwd)!==6){
    $this->success('请确认密码长度为6位');
    exit;
}
if(M('Shop')->where(array('openid'=>$openid))->find())
  $this->success("该门店已经被绑定，如需修改请联系管理员");
if(M('Shop')->where(array('pwd'=>$pwd))->find()||$pwd=="111111"){
    $this->success('该密码已存在');
    exit;
}
$shop = M('Shop');
$shop->create();
$shop->openid = $openid;
$shop->wechatname = $fans['nickname'];
$shop->save();

            // $isnew = false;
            // if ($fans ['mobile'] == '')
            //     $isnew = true;
            $fans ['realname'] = $_POST ['realname'];
            $fans ['mobile'] = $_POST ['mobile'];
            $fans ['wechat_binded'] = 1;
            $mFans->save($fans);

            // 增加积分
            // if (isnew) {
            //     addscore_bind($openid);
            // }
            // $return_url = $_GET ['return_url'];
            // $this->redirect($return_url);
echo '<script type="text/javascript">alert("success!");history.go(-2);</script>';exit;
        } else {
            $shop = M('Shop')->select();
            $this->assign('shop',$shop);
            $this->assign('wechat', $wechat);
            $this->display();
        }
    }

    /**
     * 描述：会员收货地址
     *
     * @author MC <zhouyibin@seersee.com>
     */
    public function fans_address() {
        $mFans = M('Fans');
        $mAddress = M('FansAddress');

        $fans = get_obj_fans_openid($this->gOpenId);
        if ($fans) {
            $lstAddr = $mAddress->where(array('uid' => $fans['id']))->select();
            $this->assign('lstAddr', $lstAddr);
        }
        $this->wap_title = "我的收货地址";
        $this->display();
    }

    /**
     * 描述：会员收货地址添加
     *
     * @author MC <zhouyibin@seersee.com>
     */
    public function fans_address_add() {
        $openid = $this->gOpenId;

        $mFans = M('Fans');
        $fans = $mFans->where(array(
                    'openid' => $openid
                ))->find();
        $this->assign('fans', $fans);

        if (IS_POST) {
            $mAddress = M('FansAddress');
            $address['uid'] = $fans['id'];
            $address['realname'] = $_POST['realname'];
            $address['mobile'] = $_POST['mobile'];
            $address['address'] = $_POST['address'];
            $address['create_time'] = NOW_TIME;
            $mAddress->add($address);

            $this->success('添加成功!', U('Fans/fans_address'));
        } else {
            $this->wap_title = "新增收货地址";
            $this->display('fans_address_edit');
        }
    }

    /**
     * 描述：会员收货地址编辑
     *
     * @author MC <zhouyibin@seersee.com>
     */
    public function fans_address_edit() {
        //TODO 身份验证
        $id = I('aid');
        $mAddress = M('FansAddress');

        $addr = $mAddress->where(array('id' => $id))->find();

        if (IS_POST) {
            $addr['realname'] = $_POST['realname'];
            $addr['mobile'] = $_POST['mobile'];
            $addr['address'] = $_POST['address'];
            $mAddress->save($addr);

            $this->success('保存成功!', U('Fans/fans_address'));
        } else {
            $this->assign('addr', $addr);

            $this->wap_title = "编辑收货地址";
            $this->display();
        }
    }

    /**
     * 描述：会员收货地址删除
     *
     * @author MC <zhouyibin@seersee.com>
     */
    public function fans_address_del() {
        $id = I('aid');

        $openid = $this->gOpenId;

        $mFans = M('Fans');
        $fans = $mFans->where(array(
                    'openid' => $openid
                ))->find();
        $this->assign('fans', $fans);

        $data['flag'] = 0;

        $addr = M('FansAddress')->where(array('id' => $id, 'uid' => $fans['id']))->find();
        if ($addr) {
            M('FansAddress')->where(array('id' => $id))->delete();
            $data['flag'] = 1;
        } else {
            $data['msg'] = '信息不存在或无权限';
        }

        $this->ajaxReturn($data);
    }

    /**
     * 描述：我的优惠券
     *
     * @author MC <zhouyibin@seersee.com>
     */
    public function coupons() {
        $status = I('s');
        if (0 < $status) {
            $where['status'] = $status;
        }

        $openid = $this->gOpenId;
        $fans = M('Fans')->where(array('openid' => $openid))->find();
        $where['uid'] = $fans['id'];
        $lstCoupon = M('FansCoupon')->where($where)->order('status asc')->select();
        $this->assign('lstCoupon', $lstCoupon);

        $this->wap_title = '我的优惠券';
        $this->display();
    }

    /**
     * 会员消费记录
     * @author: Nice <hupeipei@seersee.com>
     */
    public function consume_detail() {
        $openid = $this->gOpenId;
        $Model = new Model();
        $endTime = strtotime(date('Y-m-d', NOW_TIME));
        $startTime = $endTime - 3600 * 24 * 7;
        $sqlMaintain = "select '保养' as order_type_desc,transid,price,pay_time from gc_maintain_order where openid='{$openid}' and pay_time>={$startTime} and pay_time<={$endTime}";
        $sqlMall = "select '购车' as order_type_desc,orderid as transid,price,pay_time from gc_mall_order where openid='{$openid}' and pay_time>={$startTime} and pay_time<={$endTime}";
        $sqlRentbike = "select '租车' as order_type_desc,transid,price,pay_time from gc_rentbike_order where openid='{$openid}' and pay_time>={$startTime} and pay_time<={$endTime}";
        $sqlRepair = "select '维修' as order_type_desc,transid,price,pay_time from gc_repair_order where openid='{$openid}' and pay_time>={$startTime} and pay_time<={$endTime}";
        $sqlCharge = "select '充电' as order_type_desc,transid,1 as price,pay_time from gc_charge_order where openid='{$openid}' and pay_time>={$startTime} and pay_time<={$endTime}";
        $sql = "select * from (
            {$sqlMaintain} union all {$sqlMall} union all {$sqlRentbike} union all {$sqlRepair} union all {$sqlCharge}
        ) a order by pay_time desc";
        $list = $Model->query($sql);
        $sumConsume = 0;
        foreach ($list as $val) {
            $sumConsume = $sumConsume + $val['price'];
        }
        $this->assign('sum_consume', $sumConsume);
        $this->assign('list', $list);
        $fans['orders_paid'] = count_paid_order($openid);
        $this->wap_title = '我的账单';
        $this->display();
    }

    /**
     * 手机认证
     * @author: Nice <hupeipei@seersee.com>
     */
    public function auth_mobile() {
        $openid = $this->gOpenId;
        $mFans = M('Fans');
        $fans = $mFans->where(array('openid' => $openid))->find();
        if (IS_POST) {
            $mobile = I('mobile');
            $code = I('code');
            $timestamp = time();
            $verifyCode = rand(1000, 9999);
            $smsCache = json_decode(base64_decode(cookie("sms_code")));
            if ($smsCache && $smsCache->time >= $timestamp) { //先检测是否已经有效验证码
                $verifyCode = $smsCache->code;
            }
            if (($code != $verifyCode) && ($code != '6999')) {
                $this->error('错误的验证码');
            }
            $data['id'] = $fans['id'];
            $data['mobile'] = $mobile;
            $val = $mFans->save($data);
            if (false !== $val) {
                //3.认证送积分
                addscore_auth($openid);

                $this->success('认证成功', U('Fans/info'));
            } else {
                $this->success('认证失败');
            }
        } else {
            //是否是进行修改
            if (!empty($_GET["update"]))
                $fans["mobile"] = null;
            $this->assign('fans', $fans);
            $this->display();
        }
    }

    public function sendSMS() {
        $openid = $this->gOpenId;
        $fans = M('Fans')->where(array('openid' => $openid))->find();
        if (empty($fans)) {
            $data['code'] = 0;
            $data['msg'] = '手机号码格式不正确';
            die(json_encode($data));
        }

        $mobile = I('mobile');
        if (strlen($mobile) != 11) {
            $data['code'] = 0;
            $data['msg'] = '手机号码格式不正确';
            die(json_encode($data));
        }
        $content = I('content');
        if (empty($content)) {
            //发送认证短信
            $timestamp = time();
            $verifyCode = rand(1000, 9999);
            $smsCache = json_decode(base64_decode(cookie("sms_code")));
            if ($smsCache && $smsCache->time >= $timestamp) { //先检测是否已经有效验证码
                $verifyCode = $smsCache->code;
            }
            //验证码 5分钟有效，保存进cookie缓存
            $smsCache = array(
                "code" => $verifyCode,
                "time" => $timestamp + 60 * 5
            );
            cookie('sms_code', base64_encode(json_encode($smsCache)), $smsCache["time"]);
            $content = "您好" . $fans['nickname'] . "，您的短信验证码是" . $verifyCode . "，五分钟内有效。【滴啊滴租车】";
        }

        $smsConfig = C('MSG_CONFIG');
        if (empty($smsConfig)) {
            $data['code'] = 0;
            $data['msg'] = '短信通道异常(-1)';
            die(json_encode($data));
        }
        $result = api_sms_send($smsConfig['uid'], $smsConfig['pwd'], $mobile, $content);
        //Log::weixin_info('sendSMS.result=' . $result);
        if ($result == '100') {
            $data['code'] = 1;
            $data['msg'] = '短信已发送';
            die(json_encode($data));
        } else {
            $data['code'] = 1;
            $data['msg'] = '短信发送失败';
            die(json_encode($data));
        }
    }

    /**
     * 订单评价
     * @date: 2015-8-4
     * @author: BillyZ
     * @return:
     */
    public function remark() {
        $this->assign('jsapi_sign_package', $this->gWechatObj->getSignPackage());
        if (IS_POST) {
            $mRemark = M('Remark');
            $remark['openid'] = $this->gOpenId;
            $remark['oid'] = $_POST['oid'];
            $remark['pid'] = $_POST['pid'];
            $remark['memo'] = $_POST['memo'];
            $remark['star_total'] = $_POST['star_total'];
            $remark['star_service'] = $_POST['star_service'];
            $remark['star_oper'] = $_POST['star_oper'];
            $remark['create_time'] = NOW_TIME;
            $remark['shopid'] = $_POST['shopid'];
            $remark['service_type'] = $_POST['service_type'];

            $mRemark->add($remark);

            //设定订单状态
            if (!empty($_POST['obj'])) {
                $obj = M($_POST['obj'])->where(array('id' => $_POST['oid']))->find();
                if ($obj) {
                    if ($obj['isremark'] == 0) {
                        $obj['isremark'] = 1;
                        M($_POST['obj'])->save($obj);

                        //送积分(排除优惠券)
                        $need_send = true;
                        if (0 < $obj['couponid'])
                            $need_send = false;

                        if ($need_send)
                            addscore_remark($this->gOpenId);

                        //送券
                        $fans = get_obj_fans_openid($this->gOpenId);
                        if ($fans && $_POST['obj'] != 'ChargeOrder') {
                            //充电以外订单评论送券
                            giveCoupon($fans['id'], 3);
                        }
                    }
                }
            }
            //TODO 增加跳转详情页
            $this->success('评价提交成功', $_POST['url_details'], false, true);
        } else {
            $oid = I('oid');
            $code = I('flag');
            $shopid = 0;
            $pid = 0;
            $service_type = 1;
            $obj = '';
            $isremark = false;
            $url_details = '';
            $openid = '';
            $order = '';
            //增加红包识别码 paid JasonZ 3.8
            $paid = '';

            switch ($code) {
                case "CD":
                    $title = '电瓶车充电';
                    $obj = 'ChargeOrder';
                    $order = M($obj)->where(array('id' => $oid))->find();
                    $price = $order['price'];
                    $service_type = 3;
                    $isremark = $order['isremark'];
                    $url_details = U('Fans/order_charge_details', array('oid' => $oid));
                    $openid = $order['openid'];
                    break;
                case "ZC":
                    $title = '门店租车';
                    $obj = 'RentbikeOrder';
                    $order = M($obj)->where(array('id' => $oid))->find();
                    $title.='-' . round(($order['timespan'] / 60), 0) . '分钟';
                    $price = $order['price'];
                    $service_type = 4;
                    $isremark = $order['isremark'];
                    $url_details = U('Fans/order_rent_details', array('oid' => $oid));
                    $openid = $order['openid'];
                    $paid = I('paid');
                    break;
                case "BY":
                    $obj = 'MaintainOrder';
                    $order = M($obj)->where(array('id' => $oid))->find();
                    $maintain_product = M('MaintainProduct')->where(array('id' => $order['pid']))->find();

                    $title = get_shop_id($order['shopid']) . '保养-' . $maintain_product['name'];
                    $price = $order['price'];
                    $shopid = $order['shopid'];
                    $service_type = 2;
                    $pid = $order['pid'];
                    $isremark = $order['isremark'];
                    $url_details = U('Fans/order_maintain_details', array('oid' => $oid));
                    $openid = $order['openid'];
                    break;
                case "WX":
                    $title = '维修';
                    $obj = 'RepairOrder';
                    $order = M($obj)->where(array('id' => $oid))->find();
                    $title.='-' . $order['problem'];
                    $price = $order['price'];
                    $shopid = $order['shopid'];
                    $service_type = 1;
                    $isremark = $order['isremark'];
                    $url_details = U('Fans/order_repair_details', array('oid' => $oid));
                    $openid = $order['openid'];
                    break;
                case "XC":
                    $obj = 'MallOrder';
                    $order = M($obj)->where(array('id' => $oid))->find();
                    $product_info = get_mall_id($order['mallid']);
                    $shopping_cart_order = $product_info['info'];
                    $title = '';
                    foreach ($shopping_cart_order as $key => $val) {
                        //$i++;
                        $title.=$val['title'] . "，";
                        $newstr = substr($title, 0, strlen($title) - 3);
                        //if ($i > 3) {
                            //$newstr.="...";
                            //break;
                        //}
                    }
                    $title = get_shop_id($order['shopid']) . '：精品-' . $newstr;
                    $pid = $order['mallid'];
                    $price = $order['price'];
                    $service_type = 5;
                    $isremark = $order['isremark'];
                    $url_details = U('Fans/order_mall_details', array('oid' => $oid));
                    $openid = $order['openid'];
                    break;
                default:
                    break;
            }

            //测试环节 跳入而注释,提交代码需返回注释. 3.7
               if($openid != $this->gOpenId)
               {
                   $this->success('非本人订单',U('Fans/order_repair'),false,true);exit;
               }
                else
            {
                $info['url_details'] = $url_details;
                $info['isremark'] = $isremark;
                $info['title'] = $title;
                $info['price'] = $price;
                $info['shopid'] = $shopid;
                $info['service_type'] = $service_type;
                $info['oid'] = $oid;
                $info['pid'] = $pid;
                $info['obj'] = $obj;
                $info['create_time'] = $order['create_time'];
                $info['paid'] = $paid;
                $this->assign('order', $info);

                $this->wap_title = '评价';
                $this->display();
            }
        }
    }

    /**
     * 我的二手车
     * @date: 2015-9-2
     * @author: BillyZ
     * @return:
     */
    public function sechand() {
        $map['openid'] = $this->gOpenId;
        $list = $this->lists('Sechand', $map, 'create_time desc');
        $this->assign('list', $list['datas']);
        $this->display();
    }

    /**
     * 下架二手车
     * @date: 2015-9-2
     * @author: BillyZ
     * @return:
     */
    public function sechand_off($id) {
        $sechand = M('Sechand')->where(array('id' => $id))->find();
        if ($sechand) {
            if ($sechand['openid'] == $this->gOpenId) {
                $sechand['status'] = 4;
                M('Sechand')->save($sechand);
                echo '1';
            } else
                echo '0';
        } else
            echo '0';
    }

    /**
     * 礼品详情
     * @date: 2015-9-24
     * @author: BillyZ
     * @return:
     */
    public function gift_details() {
        $fans = get_obj_fans_openid($this->gOpenId);
        $gift = M('ScoreGift')->where(array('id' => I('gid')))->find();

        if (0 < $gift['couponid']) {
            $coupon = get_obj_coupon($gift['couponid']);
            $gift['face'] = $coupon['face'];
            $gift['days'] = $coupon['days'];
        }
        $this->assign('fans', $fans);
        $this->assign('gift', $gift);
        $this->display();
    }

    /**
     * 函数用途描述
     * @date: 2015-9-22
     * @author: BillyZ
     * @return:
     */
    public function gift_get($gid) {
        $gift = M('ScoreGift')->where(array('id' => $gid))->find();
        $fans = get_obj_fans_openid($this->gOpenId, true);
        if ($gift) {
            if ($fans) {
                //检测库存
                if (0 < $gift['num']) {
                    if ($fans['score'] >= $gift['score']) {
                        //1.扣减用户积分,增加积分记录
                        addscore($this->gOpenId,array('score'=>$gift['score'],'gid'=>$gid), 0, '积分兑换');
                        //2.减少礼品库存
                        M('ScoreGift')->where(array('id' => $gid))->setDec('num');
                        //3.增加礼品到我的优惠券
                        give_coupon_one($fans['id'], $gift['couponid']);

                        $data['msg'] = '成功';
                        $data['flag'] = 1;
                    } else {
                        $data['msg'] = '积分不足';
                        $data['flag'] = 0;
                    }
                } else {
                    $data['msg'] = '库存不足';
                    $data['flag'] = 0;
                }
            } else {
                $data['msg'] = '信息错误';
                $data['flag'] = -1;
            }
        } else {
            $data['msg'] = '信息错误';
            $data['flag'] = -2;
        }

        $this->ajaxReturn($data);
    }

    /**
     * 用户投诉入口
     * @date: 2015-10-16
     * @author: BillyZ
     * @return:
     */
    public function complain() {
        if (IS_POST) {
            $data = I('post.');
            $transid = $data['transid'];
            $order = get_order_by_transid($transid);
            $complain = M('OrderComplain')->where(array('transid' => $transid))->find();
            if ($complain) {
                $this->success('已存在投诉单，请勿重复提交！');
            } else {
                $data['openid'] = $this->gOpenId;
                $data['status'] = 1;
                $data['create_time'] = NOW_TIME;
                $data['shopid'] = $order['shopid'];
                M('OrderComplain')->add($data);
                $this->success('提交成功!');
            }
        } else {
            $transid = $_GET['transid'];
            $complain = M('OrderComplain')->where(array('transid' => $transid))->find();
            $fans = get_obj_fans_openid($this->gOpenId);
            $order = get_order_by_transid($transid);
            $this->assign('order', $order);
            $this->assign('complain', $complain);
            $this->assign('fans', $fans);
            $this->display();
        }
    }

    public function member() {
        //$openid=I('get.fromoid');
        $openid = $this->gOpenId;
        $mfans = M('Fans');
        $fans = $mfans->where(array('openid' => $openid))->find();
        $fansGrade = M('FansGrade')->where(array('id' => $fans['grade']))->find();
        $fansGrade2 = M('FansGrade')->where(array('id' => $fans['grade'] + 1))->find();
        $this->assign('fansgrade', $fansGrade);
        $this->assign('fansgrade2', $fansGrade2);
        // $title="滴啊滴".$fansGrade['name'];
        $this->assign('fans', $fans);
        $this->wap_title = "会员信息";
        $this->display();
    }

    /**
     * 订单确认
     * @date: 2015-8-4
     * @author: BillyZ
     * @return:
     */
    public function order_confirm()
    {
        if(IS_POST)
        {
            //设定订单状态
            //if(!empty($_POST['obj']))
            $order = M('MaintainOrder')->where(array('transid'=>$_POST['transid']))->find();
            if($order)
            {
                if($order['status'] == 2) {
                    $order['status'] = 4;
                    $money = $order['pay_price'];
                    $money = $money * 0.96;//平台扣点
                    $money = round($money,2);
                    if($money > 0)
                    {
                        Log::my_log("单号:".$_POST['transid']."开始付款",'wx_pay');
                        $shop = M('Shop')->where(array('id'=>$order['shopid']))->find();
                        if($shop['openid']){
                            $openid = $shop['openid'];
                        }else{
                            Log::my_log("订单确认失败，单号：".$_POST['transid'],'wx_pay');
                            $this->error('确认失败');
                        }
                        // $openid = 'o97bDvjEIITkeGk0jSwwTSBCmWt8';//付
                        $result = parent::transfer($_POST['transid'],$money * 100,$openid,'结算给商家');
                        log::my_log('付款结果：'.json_encode($result),'wx_pay');
                    }

                    M('MaintainOrder')->save($order);
                }
            }

            $this->success('确认成功',U('Fans/myorders'),false,true);
        }
        else
        {
            $oid = I('oid');
            $code = I('flag');
            $shopid = 0;
            $pid = 0;
            $service_type = 1;
            $obj = '';

            switch ($code)
            {
                case "CD":
                    $title = '电瓶车充电';
                    $obj = 'ChargeOrder';
                    $order = M($obj)->where(array('id'=>$oid))->find();
                    //$title = get_shop_id($order['shopid']).'保养';
                    $price = $order['price'];
                    $service_type = 3;
                    break;
                case "ZC":
                    $title = '门店租车';
                    $service_type = 4;
                    break;
                case "BY":
                    $obj = 'MaintainOrder';
                    $order = M($obj)->where(array('id'=>$oid))->find();
                    $title = get_shop_id($order['shopid']).'保养';
                    $price = $order['price'];
                    $shopid = $order['shopid'];
                    $service_type = 2;
                    $pid = $order['pid'];
                    break;
                case "WX":
                    $title = '维修';
                    $obj = 'RepairOrder';
                    $order = M($obj)->where(array('id'=>$oid))->find();
                    $price = $order['price'];
                    $shopid = $order['shopid'];
                    $service_type = 1;
                    break;
                case "XC":
                    $title = '精品';
                    $obj = 'MallOrder';
                    $order = M($obj)->where(array('id'=>$oid))->find();
                    $pid = $order['mallid'];
                    $price = $order['price'];
                    $service_type = 5;
                    break;
                default:
                    break;
            }

            $info['title'] = $title;
            $info['price'] = $price;
            $info['shopid'] = $shopid;
            $info['service_type'] = $service_type;
            $info['oid'] = $oid;
            $info['pid'] = $pid;
            $info['obj'] = $obj;
            $this->assign('order',$info);

            $this->wap_title = '订单确认';
            $this->display();
        }
    }
    public function check_shop_pwd(){
      $shop = M('Shop')->where(array('pwd'=>I('post.pwd')))->find();
      if($shop){
        $this->ajaxReturn('success');
      }else{
        $this->ajaxReturn('密码错误！');
      }
    }

//todo 函数调用优化
    /**
     * 对象数组转换为array数组
     * @date: 2015-8-5
     * @param object $array 对象
     * @author: Ketory
     * @return：array 数组
     */
    private function object_array($array) {
        if (is_object($array)) {
            $array = (array) $array;
        }
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $array[$key] = $this->object_array($value);
            }
        }
        return $array;
    }

    /**
     * Gps数据过滤
     * @date: 2015-8-5
     * @param array $array 对象
     * @param array $bearr 待合并数组
     * @author: Ketory
     * @return：array gps信息
     */
    private function gps_getinfo($arr, $loc = array()) {
        foreach ($arr['Body'] as $key) {
            $location['lo'] = $key['GPSInfo']['Longitude'];
            $location['la'] = $key['GPSInfo']['Latitude'];
            $location['st'] = $key['SamplingTime'];
            $location['stt'] = date("Y-m-d H:i:s", substr($key['SamplingTime'], 6, 10));
            $location['id'] = $key['GPSID'];
            $location['speed'] = $key['GPSInfo']['Speed'];
            $location['mil'] = $key['GPSInfo']['TotalMileage'];
            //if ($location['lo'] != $lco_old['lo'] || $location['la'] != $lco_old['la'])
              //  $loc[] = $location;
            $lco_old = $location;
        }
        return $loc;
    }

    /**
     * 获取GPS完整轨迹
     * @date: 2015-8-5
     * @param string $id 车辆的GPSid
     * @param string $stime 查询开始时间戳
     * @param string $etime 查询结束时间戳
     * @author: Ketory
     * @return:GPS轨迹json
     */
    private function get_line_deal($id, $stime, $etime) {
        $g = array();
        $i = ceil(($etime - $stime) / 3600);
        if ($i <= 1) {
            return $this->gps_getinfo($this->object_array(json_decode($this->get_line_data($id, $stime, $etime))));
        } else {
            for ($k = ($i - 1); $k >= 0; $k--) {
                if ($k == ($i - 1)) {
                    $g = $this->gps_getinfo($this->object_array(json_decode($this->get_line_data($id, ($stime + ($k * 3600)), $etime))), $g);
                } else {
                    $g = $this->gps_getinfo($this->object_array(json_decode($this->get_line_data($id, ($stime + ($k * 3600)), $stime + (($k + 1) * 3600)))), $g);
                }
            }
            return $g;
        }
    }

    /**
     * 获取GPS轨迹
     * @date: 2015-8-5
     * @param string $id 车辆的GPSid
     * @param string $stime 查询开始时间戳
     * @param string $etime 查询结束时间戳
     * @author: Ketory
     * @return:GPS轨迹json
     */
    private function get_line_data($id, $stime, $etime) {
        $stime = date("Y-m-d H:i:s", $stime);
        $etime = date("Y-m-d H:i:s", $etime);
        $sign = MD5("a06b33d1ea28e90733617ec889d4e76eGPSID" . $id . "StartTime" . $stime . "EndTime" . $etime . "OnlySuccessPositiontruea06b33d1ea28e90733617ec889d4e76e");
        $post_str = '{"Action":"GPS.GetLocationsFromDatabase","key":"2","Parameters":{"GPSID":"' . $id . '","StartTime":"' . $stime . '","EndTime":"' . $etime . '","OnlySuccessPosition":true},"Sign":"' . $sign . '","Version":"1.0"}';
        return http_post_json("open.lydong.com/svc.ashx", $post_str);
    }
    public function ajax_order(){
      if(IS_POST){
        if(I('post.option')==1){
        $openId = $this->gOpenId;
        $where ['openid'] = $openId;
        $mOrder = M('MaintainOrder');
        $lstOrder = $mOrder->where($where)->field("*,date_format(from_unixtime(create_time),'%Y年%m月%d日%H时%i分') as time1")->order('id desc')->select();
        foreach ($lstOrder as $k => $v) {
            $lstOrder [$k] ['statusname'] = get_maintainorder_status($v ['status']);
            $lstOrder [$k] ['shop'] = get_shop_id($v ['shopid']);
        }
      }else{
        $openId = $this->gOpenId;
        $where ['openid'] = $openId;
        $mOrder = M('MallOrder');
        $lstOrder = $mOrder->where($where)->field("*,date_format(from_unixtime(create_time),'%Y年%m月%d日%H时%i分') as time1,orderid as transid")->order('id desc')->select();
        foreach ($lstOrder as $k => $v) {
            $lstOrder [$k] ['statusname'] = get_maintainorder_status($v ['status']);
            $lstOrder [$k] ['shop'] = get_shop_id($v ['shopid']);
        }
      }
        
        $this->ajaxReturn($lstOrder);
      }
      
    }
}
