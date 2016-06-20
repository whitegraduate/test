<?php
// +----------------------------------------------------------------------
// | 视界无限 - SEERSEE
// +----------------------------------------------------------------------
// | Copyright (c) 2015
// +----------------------------------------------------------------------
// | Author:
// +----------------------------------------------------------------------

namespace Wap\Controller;
use Think\Controller;
use Think\Log;
use User\Api\UserApi;
use OT\Wechat\SDKRuntimeException;
use OT\Wechat\UnifiedOrder;
use OT\Wechat\PayConfig;
use OT\Wechat\JsApi;
use Admin\Controller\WeixinController;



/**
 * 描述：前台保养服务
 * Class MaintainController
 * @package Home\Controller
 */
class MaintaintController extends WapController {

    public function __construct() {
        parent::__construct(); 
    }

    /**
     * 保养产品列表
     * @author:
     */
    public function index(){
        $shop = $this->get_location_shop();
        var_dump($shop);
    }
    
    /**
    * 保养详情
    * @date: 2015-7-19
    * @author: BillyZ
    * @return:
    */
    public function details()
    {
        $productid = I('pid');
        $product = M('MaintainProduct')->where(array('id'=>$productid))->find();
        $this->assign('product',$product);
        $this->wap_title = $product['name'];
        
        //评价列表
        $lstRemark = M('Remark')->where(array('pid'=>$productid))->limit(10)->order('id desc')->select();
        $this->assign('lstRemark',$lstRemark);
        
        $this->display();
    }
    
    /**
    * 保养下单
    * @date: 2015-7-19
    * @author: BillyZ
    * @return:
    */
    public function order()
    {
        $productid = $_GET['pid'];
        $mFansCoupon = M('FansCoupon');
        $product = M('MaintainProduct')->where(array('id'=>$productid))->find();
        if(IS_POST)
        {
            //下单
            $mOrder = M('MaintainOrder');
            $orderid = neworderid('BY', $this->gOpenId);
            $order['pid'] = $productid;
            $order['shopid'] = $_POST['shopid'];
            $order['uid'] = 0;
            $order['openid'] = $this->gOpenId;
            $order['transid'] = $orderid;
            $order['create_time'] = NOW_TIME;
            $order['price'] = $product['price'];
            $order['couponid'] = $_POST['couponid'];
            $pay_price = $product['price'];

            $fans_coupon = $mFansCoupon->where(array('id'=>$order['couponid']))->find();
            
            $needPay = true;
            if(0 < $order['couponid'])
            {
                if($fans_coupon)
                {
                    if($fans_coupon['status'] == 1)
                    {
                        $coupon = get_obj_coupon($fans_coupon['cid']);
                        if($coupon)
                        {
                            $pay_price = get_pay_price($product['price'],$coupon['face']);
                        }
                    }
                }
            }
            
            if($pay_price <= 0)
            {
                $needPay = false;
            }
            
            $order['pay_price'] = $pay_price;
            $order['order_type'] = 1;

            if($needPay)
            {
                $order['status'] = 1;//0:已下单 1：服务完成，等待支付 2：已支付，订单完成 3：已取消
                $orderid = $mOrder->add($order);
                redirect(C('site_url').'/wxpay/wap.php?s=/Maintain/pay/oid/'.$orderid.'.html');
            }
            else 
            {
                //1.设置订单为已支付
                $order['status'] = 2;//直接已支付
                $order['pay_time'] = NOW_TIME;
                $orderid = $mOrder->add($order);
                
                //2.优惠券使用
                coupon_used($order['couponid']);
                
                $this->success('支付成功',U("Fans/order_maintain_details",array('oid'=>$orderid)),false,true);
            }
        }
        
        //基础套餐才能用券 @20150806 MC
        if(1 == $product['type'])
        {
            //优惠券
            $fans = get_obj_fans_openid($this->gOpenId);
            $where['uid'] = $fans['id'];
            $where['service_type'] = 2;
            $where['status'] = 1;
            $where['end_time'] = array('gt',NOW_TIME);
            $coupons = M("FansCoupon")->where($where)->select();
            foreach($coupons as $key=>$value) {
                $coupons[$key]['face'] = M('Coupon')->where(array('id'=>$coupons[$key]['cid']))->getField('face');
            }
            
            $this->assign('coupons',$coupons);
        }
        
        $this->assign('product',$product);
         
        // $lstShop = get_shops('BY',$productid); 
        $lstShop = product_shop($product['shopid']);
        $this->assign('lstShop',$lstShop);
        
        $this->assign('jsapi_sign_package',$this->gWechatObj->getSignPackage());
        $this->wap_title = '保养下单';
        $this->display();
        
    }

    /**
     * 保养提醒下单
     * @date: 2015-7-19
     * @author: BillyZ
     * @return:
     */
    public function order_remind()
    {
        $productid = $_GET['pid'];
    
        $product = M('MaintainProduct')->where(array('id'=>$productid))->find();
        if(IS_POST)
        {
            //下单
            $mOrder = M('MaintainOrder');
            $orderid = neworderid('BY', $this->gOpenId);
            $order['pid'] = $productid;
            $order['shopid'] = $_POST['shopid'];
            $order['uid'] = 0;
            $order['openid'] = $this->gOpenId;
            $order['transid'] = $orderid;
            $order['status'] = 1;//0:已下单 1：服务完成，等待支付 2：已支付，订单完成 3：已取消
            $order['create_time'] = NOW_TIME;
            $order['price'] = $product['price'];
            $order['pay_price'] = $product['price'];
            $order['brandid'] = $_POST['brandid'];
            $order['order_type'] = 2;
            $oid = $mOrder->add($order);
            
            redirect(C('site_url').'/wxpay/wap.php?s=/Maintain/pay/oid/'.$oid.'.html');
             
            //$this->success("下单成功!",U('Fans/order_maintain'),false,false);
        }
        else
        {
            $fans = M('Fans')->where(array('openid'=>$this->gOpenId))->find();
             
            $this->assign('fans',$fans);
            
            $this->assign('product',$product);
             
            $lstShop =get_shops('BY');
            $this->assign('lstShop',$lstShop);
            
            //$lstBrand = M('Brand')->select();
            //$this->assign('lstBrand',$lstBrand);
            $lstBike = M('FansBike')->where(array('uid'=>$fans['id']))->select();
            $this->assign('lstBike',$lstBike);
            
            $this->assign('jsapi_sign_package',$this->gWechatObj->getSignPackage());
            $this->wap_title = '保养下单';
            $this->display();
        }
    }
    

    /**
     * 微信统一支付下单(ajax调用)
     * @author:
     */
    public function prepay() {
        $transid = $_POST['rsid'];
        $price = $_POST['oprice'] * 100;
        $body = '保养';
        $data = parent::prepay($body, $transid, $price);
        echo $data;
    }

    /**
     * 保养订单支付
     * @date: 2015-7-18
     * @author: BillyZ
     * @return:
     */
    public function pay()
    {
        
        $orderid = $_GET['oid'];
        $mOrder = M('MaintainOrder');
        $order = $mOrder->where(array('id'=>$orderid))->find();
        $unpay_status = 1;
        if (empty($order)) {
            $data['code'] = 0;
            $data['msg'] = '订单不存在！';
            die(json_encode($data));
        }
        else
        {
            if($unpay_status != $order['status'])
            {
                $data['code'] = 0;
                $data['msg'] = '订单状态不正确！';
                die(json_encode($data));
            }
        }
        $requestid = newrequestid('BY',$order['openid']);
        add_payrequest($order['transid'],$requestid);
        if(!empty($df))
        {
            $order['is_df'] = 1;
            $mOrder->save($order);
            $msg = '订单代付发起';
        }
        else
        {
            if(1 == $order['is_df'])
            {
                $order['is_df'] = 0;
                $mOrder->save($order);
                $msg = '订单本人支付发起（替代代付）';
            }
            else
                $msg = '订单本人支付发起';
        }

        $order['shop'] = get_shop_id($order['shopid']);
        $order['product'] = get_maintain_product_id($order['pid']);
        $this->assign('rid',$requestid);
        $this->assign('order',$order);
        $this->wap_title = "订单支付";
        
        //优惠券
        /*
        $fans = M('Fans')->where(array('openid'=>$this->gOpenId))->find();

        $where['uid'] = $fans['id'];
        $where['service_type'] = 2;
        $coupons = M("FansCoupon")->where($where)->select();
        foreach($coupons as $key=>$value) {
            $coupons[$key]['face'] = M('Coupon')->where(array('id'=>$coupons[$key]['cid']))->getField('face');
        }
        $this->assign('coupons',$coupons);
        */
        $this->assign('jsapi_sign_package',$this->gWechatObj->getSignPackage());
        $this->display();
    } 
    
    /**
     * 保养提醒
     * @date: 2015-7-18
     * @author: BillyZ
     * @return:
     */
    public function remind()
    {
        //TODO 获取上还需优化
        $oid = I('oid');
        //1.找到这个订单车子的品牌
        $order = M('MaintainOrder')->where(array('id'=>$oid))->find();
        $bikeid = $order['brandid'];
        
        $bike = M('FansBike')->where(array('id'=>$bikeid))->find();
        $brandid = $bike['brandid'];//车辆id
        $buytime = $bike['buy_time'];//车辆购买时间
        //2.这个品牌所有部件的提醒时间
        $list = M('MaintainPartsRemind')->distinct(true)->field('remind_month')->where(array('brandid'=>$brandid))->order('remind_month')->select();
        $now = time();
        
        for($i=0;$i<count($list);$i++)
        {
            $list_parts = M('MaintainPartsRemind')->where(array('brandid'=>$brandid,'remind_month'=>$list[$i]['remind_month']))->select();
            $str = '';
            for($j=0;$j<count($list_parts);$j++)
            {
                $str .= get_maintain_parts_id($list_parts[$j]['partid']).',';
            }
            
            $list[$i]['parts'] = $str;
            $list[$i]['expire_time'] = $buytime + $list[$i]['remind_month'] * 30 * 24 * 3600;   //过保时间：购买时间+月份
            $remain_days = round(($list[$i]['expire_time'] - $now)/3600/24);
            if(0 < $remain_days)
                $list[$i]['remain_days'] = '还有'.$remain_days.'天'; //还有多少天
            else
                $list[$i]['remain_days'] = '已到期';
        }
        $this->assign('bike',$bike);
        $this->assign('list',$list);
        
        //3.过保项目
        //
        $this->wap_title = '保修提醒';
        $this->display();
    }
    private function get_location_shop(){
        $lstMaintain = M("MaintainProduct")->select();
        $location = M('FansLocation')->where(array('openid'=>$this->gOpenId))->find();
        $maxlng=$location['lng'] + 0.05;
        $minlng=$location['lng'] - 0.05;
        $maxlat=$location['lat'] + 0.07;
        $minlat=$location['lat'] - 0.07;
        $shop = M('Shop')->where(array('longitude'=>array('between',"'".$minlng.",".$maxlng."'"),'latitude'=>array('between',"'".$minlat.",".$maxlat."'")))->select();
        foreach ($lstMaintain as $key => $val) {
            if($val['shopid']){
                $shopid = explode(",",$val['shopid']);
                if(is_array($shopid)){
                    foreach ($shop as $p => $q) {
                        if(false!==array_search($q['id'],$shopid,false)){
                            $info[$key]['product'] = $val;
                            $y[$key]['sid'] = $val['id'];
                            $y[$key]['shopid'] = $q['id'];
                            break;
                        }
                    }
                }
            }
        }
        return $y;
    }
}