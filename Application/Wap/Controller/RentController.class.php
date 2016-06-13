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
use OT\Wechat\SDKRuntimeException;
use OT\Wechat\UnifiedOrder;
use OT\Wechat\PayConfig;
use OT\Wechat\JsApi;

/**
 * 描述：前台租车服务
 * Class RentController
 * @package Home\Controller
 */
class RentController extends WapController {

    public function __construct() {
        parent::__construct();
    }

    /**
     * 查看所有租车点
     * @date: 2015-7-20
     * @author: BillyZ
     * @return:
     */
    public function index() {
        $lstShop = get_shops('ZC');
        $this->assign("lstShop", $lstShop);
        $this->wap_title = '租车点';
        $this->display();
    }

    /**
     * 租车下单
     * @author: MC <zhouyibin@seersee.com>
     */
    public function order() {
        $bikeid = $_GET['bid'];
        if (IS_POST) {
            //下单
            $mOrder = M('RentbikeOrder');
            $orderid = neworderid('ZC', $this->gOpenId);
            $order['bikeid'] = $bikeid;
            $order['shopid'] = $_POST['shopid'];
            $order['uid'] = 0;
            $order['openid'] = $this->gOpenId;
            $order['transid'] = $orderid;
            $order['status'] = 0; //0:已下单 1：已确认开始 2：已n还车 3：已取消 4: 已支付
            $order['create_time'] = $_POST['now_time'];
            $order['tel'] = $_POST['mobile'];
            if (M('RentbikeOrder')->where(array("create_time" => $order['create_time'], "openid" => $this->gOpenId))->find()) {
                $this->error("请勿重复提交订单！");
            }
            if (M('RentbikeOrder')->where(array("bikeid" => $bikeid, "status" => "1"))->find()) {
                $this->error("该车已经被租用，请您换辆车！");
            }
            $map['status'] = 1;
            $map['openid'] = $this->gOpenId;
            $rent_bike_num = M('RentbikeOrder')->where($map)->count();
            if ($rent_bike_num < 5) {
                $oid = $mOrder->add($order);
                //发送模板消息通知门店
                parent::sendMsg2Shop($order['shopid'], '您有新的租车订单', '车辆维修', date('Y-m-d H:i:s', $order['create_time']), '未计算', true, '请登录门店后台进行处理');
                $this->success("下单成功，等待门店确认!", U('Fans/order_rent_details', array('oid' => $oid)), false, true);
            } else {
                $this->error("您租车已超限制");
            }
        } else {
            $price = explode(',', C("RENTBIKE_SET"));
            $bike = M('Rentbike')->where(array('id' => $bikeid))->find();
            if ($bike) {
                $this->assign('bike', $bike);
                $shop = M('Shop')->where(array('id' => $bike['shopid']))->find();
                $this->assign('shop', $shop);
                $areaprice = M('RentbikeTypeArea')->where(array('rentbiketypeid'=>$bike['rentbiketypeid'],'cityid'=>$shop['cityid'],'areaid'=>$shop['areaid']))->getField('priceconfig');
                if(!empty($areaprice))
                {
                    $price = explode(',', $areaprice);
                }
            }
            $this->assign('rent_price', $price);
            $fans = get_obj_fans_openid($this->gOpenId);
            $this->assign('fans', $fans);
            $this->assign('current', NOW_TIME);
            $this->wap_title = "开始租车";
            $this->display();
        }
    }

    /**
     * @author: 小童 JasonZ 2016.3.4 
     * 生成红包的方法,租车支付成功后进入评价页面执行
     */
    public function createRedPaper() {
         $oid = $_GET['oid'];
      //  $para = I('post.');
      //  $oid = $para['oid'];
        // $service_type = $para['service_type'];
        $openid = $this->gOpenId;

        //生成红包数据
        $mRedpaper = M('Redpaper')->where(array('service_type' => '4', array('status' => '1')))->find(); //获得租车业务红包配置
        $mRedpaperSend = M('RedpaperSend');
        if ($mRedpaper['id'] > 0)
            $redpapersend['id'] = $mRedpaper['id'];
        else
            $this->error('红包未配置或已禁用');

        $redpapersend['openid'] = $openid;
        $redpapersend['couponid'] = $mRedpaper['couponid'];
        $redpapersend['qty'] = $mRedpaper['qty'];
        $redpapersend['sendtime'] = NOW_TIME;

        $redpapersend['days'] = $mRedpaper['days'];
        $redpapersend['orderid'] = $oid;
        $mRedpaperOrder = M('RedpaperSend')->where(array('orderid' => $oid))->find();

        if (empty($mRedpaperOrder)) {
            $ids = $mRedpaperSend->add($redpapersend);
        } else {
            $ids = $mRedpaperOrder['ids'];
            //$ids = null;
        }
        if ($ids) {
            $this->success($ids);
        } else {
            $this->error('新增失败');
        }
    }

    /**
     * 租车订单支付
     * @date: 2015-7-18
     * @author: BillyZ
     * @return:
     */
    public function pay() {
        $orderid = $_GET['oid'];
        $mOrder = M('RentbikeOrder');
        $order = $mOrder->where(array('id' => $orderid))->find();

        $unpay_status = 2;
        if (empty($order)) {
            $data['code'] = 0;
            $data['msg'] = '订单不存在！';
            die(json_encode($data));
        } else {
            if ($unpay_status != $order['status']) {
               $this->success('订单无需重新支付！',"",false,true);exit;
            }
        }
        $requestid = newrequestid('ZC', $order['openid']);
        add_payrequest($order['transid'], $requestid);
        if (!empty($df)) {
            $order['is_df'] = 1;
            $mOrder->save($order);
        } else {
            if ($order['is_df'] == 1) {
                $order['is_df'] = 0;
                $mOrder->save($order);
            }
        }


        $this->assign('rid', $requestid);
        $this->assign('order', $order);
        $this->wap_title = "订单支付";
        $this->assign('jsapi_sign_package',$this->gWechatObj->getSignPackage());
        $this->display();
    }

    /**
     * 微信统一支付下单(ajax调用)
     * @author: Nice <hupeipei@seersee.com>
     */
    public function prepay() {
        $transid = $_POST['rsid'];
        $price = $_POST['oprice'] * 100;
        $body = '租车';
        $data = parent::prepay($body, $transid, $price);
        echo $data;
    }

    /**
     * 租车车辆状态
     * @date: 2015-7-18
     * @author: BillyZ
     * @return:
     */
    public function show() {
        $shopid = $_GET['sid'];
        $mShop = M('Shop');
        $count = 0;
        $shop = $mShop->where(array('id' => $shopid))->find();
        $bikelist = M('Rentbike')->where(array('shopid' => $shopid))->select();
        foreach ($bikelist as $k => $vo) {
            $bike[$count][] = $vo;
            if (($k + 1) % 5 == 0)
                $count++;
        }
        $this->assign('shop', $shop);
        $this->assign('bike', $bike);
        $this->wap_title = "租车状态";
        $this->display();
    }

    /**
     * 还车
     * @date: 2015-8-8
     * @author: BillyZ
     * @return:
     */
    public function return_bike() {
        $oid = I('oid');
        $mOrder = M('RentbikeOrder');
        $order = $mOrder->where(array('id' => $oid))->find();
        if ($order) {
            if ($this->gOpenId == $order['openid']) {
                $order['status'] = 5; //已还车
                $order['return_time'] = NOW_TIME;
                $mOrder->save($order);

                $this->ajaxReturn('还车成功，等待后台确认！');
            } else
                $this->ajaxReturn('用户验证失败');
        }
    }

    /**
     * 确认订单（价格为0订单的处理）
     * @date: 2015-11-2
     * @author: BillyZ
     * @return:
     */
    public function confirm() {
        $orderid = I('oid');
        $mOrder = M('RentbikeOrder');
        $order = $mOrder->where(array('id' => $orderid))->find();
        $data['flag'] = 0;
        if ($order) {
            if ('2' == $order['status'] && 0 == $order['pay_price']) {
                $order['status'] = 4;
                $order['pay_time'] = NOW_TIME;
                $mOrder->save($order);


                $data['flag'] = 1;
                $data['msg'] = "确认成功";
            } else
                $data['msg'] = "状态不正确";
        } else
            $data['msg'] = "信息不存在";

        $this->ajaxReturn($data);
    }
}
