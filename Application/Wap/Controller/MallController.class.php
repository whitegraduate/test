<?php
// +----------------------------------------------------------------------
// | 视界无限 - SEERSEE
// +----------------------------------------------------------------------
// | Copyright (c) 2015
// +----------------------------------------------------------------------
// | Author: badboy <zhaoyunfeng@seersee.com>
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
 * 描述：新车服务
 * Class MallController
 * @package Home\Controller
 */
class MallController extends WapController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 新车首页
     * @author: MC <zhouyibin@seersee.com>
     */
    public function index()
    {
        $this->wap_title = "商城";
        $this->display();
    }

    /**
     * 新车首页
     * @author: MC <zhouyibin@seersee.com>
     */
    public function indexlist()
    {
        $para = I('get.');
        $map['type'] = 1;
        switch ($para['aa']) {
            case 0:
                $map['fashiion'] = array(array('EQ',0),array('EQ',2), 'or') ;
                break;
            case 1:
                $map['fashiion'] = array(array('EQ',1),array('EQ',2), 'or') ;
                break;
        }
        switch ($para['bb']) {
            case 0:
                $map['endurance'] = array(array('EQ',0),array('EQ',2), 'or') ;
                break;
            case 1:
                $map['endurance'] = array(array('EQ',1),array('EQ',2), 'or') ;
                break;
        }
        switch ($para['cc']) {
            case "0":
                $map['member_price'] = array(array('egt', 2000), array('lt', 2999));
                break;
            case "1":
                $map['member_price'] = array(array('egt', 3000), array('lt', 3999));
                break;
        }
        $map['status'] = 1;
        $list = M('Mall')->where($map)->order("sort desc")->select();
        $this->ajaxreturn($list);
    }

    /**
     * 新车首页
     * @author: MC <zhouyibin@seersee.com>
     */
    public function accessory_list()
    {
        $para = I('get.');
        $order = I('get.order','sort desc');
        $map['type'] = $para['type'];
        $map['status'] = 1;
        //$map['cityid']=$this->fancityid;
        $list = M('Mall')->where($map)->order($order)->select();
        Log::write(M('Mall')->getLastSql());
        $this->ajaxreturn($list);
    }

    /**
     * 描述：
     * @author badboy <zhaoyunfeng@seersee.com>
     */
    public function iwantsell()
    {
        if (IS_POST) {
            $data = I('post.');
            $data['pic'] = I('icon');
            $data['create_time'] = time();
            $data['status'] = 1;
            $data['is_changedbattery'] = I('sport');


            $res = M('Sechand')->add($data);
            if ($res) {
                $this->ajaxReturn('<script type="text/javascript">alert("提交成功，等待审核");window.location.href="' . U('index') . '";</script>', 'EVAL');
            } else {
                $this->ajaxReturn('<script type="text/javascript">alert("操作失败！");window.location.href="' . U('index') . '";</script>', 'EVAL');
            }
        } else {
            $this->wap_title = "我想卖";
            $this->display();
        }
    }

    public function search()
    {
        if (IS_POST) {
            $data = I('post.');

        } else {
            $this->display();
        }
    }

    public function plist()
    {
        $para = I('get.');
        $price = $para['price'];


        switch ($price) {
            case '2000以下':
                $map['member_price'] = array('lt', 2000);
                break;
            case '2000~3000':
                $map['member_price'] = array(array('egt', 2000), array('lt', 3000));
                break;
            case '3000~4000':
                $map['member_price'] = array(array('egt', 3000), array('lt', 4000));
                break;
            case '4000以上':
                $map['member_price'] = array('gt', 4000);
                break;
        }
        $list = M('Mall')->where($map)->select();

        $this->assign('price', $price);
        $this->assign('list', $list);
        $this->display();
    }

    /**
    * 新车详情
    * @date: 2015-11-6
    * @return:
    */
    public function detail()
    {
        if (IS_POST) {

        } else {
            $id = I('id');
            $info = M('Mall')->where(array('id' => $id))->find();

            $color = $info['color'];
            if(!empty($color))
            {
                $colorlist = M('Color')->where('id in ('.$color.')')->select();
                $this->assign('colorlist',$colorlist);
            }

            $this->wap_title = '商品详情';
            $this->assign('info', $info);
            $this->display();
        }
    }
    
    /**
     * 附件详情
     * @date: 2015-11-6
     * @return:
     */
    public function detail_accessory()
    {
        if (IS_POST) {

        } else {
            $id = I('id');
            $info = M('Mall')->where(array('id' => $id))->find();
            $info_attr = M('MallAttr')->where(array('mallid' => $id))->select();
            //todo 后台功能添加后去除
            foreach($info_attr as $k => $v){
                $info_attr[$k]['attr'] = explode(",",$v['attrinfo']);
            }
            $color = $info['color'];
            if(!empty($color))
            {
                $colorlist = M('Color')->where('id in ('.$color.')')->select();
                $this->assign('colorlist',$colorlist);
            }

            $this->wap_title = '商品详情';
            $this->assign('info', $info);
            $this->assign('infoattr', $info_attr);
            $this->display();
        }
    }

    /**
    * 商城下单
    * @date: 2015-8-8
    * @return:
    */
    public function order()
    {
        $productid = $_GET['id'];
        if(!$productid)$this->error("产品参数错误");
        $product = M('Mall')->where(array('id' => $productid))->find();
        $product_attr = M('MallAttr')->where(array('mallid' => $productid))->select();
        //todo 后台功能添加后去除

        foreach($product_attr as $k => $v){
            $productattr[$v['id']]=$v;
            $productattr[$v['id']]['attr'] = explode(",",$v['attrinfo']);
        }
        foreach($productattr as $v){
            $attr[$v['id']]['id'] =$_GET['attr_'.$v['id']]-1;
            $attr[$v['id']]['name'] =$productattr[$v['id']]['attr'][$_GET['attr_'.$v['id']]-1];
        }
        if (IS_POST) {
            $mOrder = M('MallOrder');
            $orderid = neworderid('XC', $this->gOpenId);
            $order['mallid'] = $productid;
            $order['shopid'] = $_POST['shopid'];
            $order['addr'] = $_POST['addr'];
            $order['mobile'] = $_POST['mobile'];
            $order['openid'] = $this->gOpenId;
            $order['orderid'] = $orderid;
            $order['is_pay'] = 0;
            $order['status'] = 0; //0:待支付 1：已取货 2：已支付 3：已取消
            $order['create_time'] = NOW_TIME;
            $order['price'] = $product['member_price'];
            $order['pay_price'] = $product['member_price'];
            $order['color'] = $_POST['hdcolor'];
            $order['attr'] = json_encode($attr);
            $res = $mOrder->add($order);
            redirect('/wxpay/wap.php?s=/Mall/pay/oid/' . $res . '.html');
        } else {
        	$lstShop =get_shops('XC');
        	$this->assign('lstShop',$lstShop);
        	$this->color = $_GET['cl'];
            $this->assign('product', $product);
            $this->assign('attr', $attr);
            $this->assign('productattr', $productattr);
            $this->assign('jsapi_sign_package', $this->gWechatObj->getSignPackage());
            $this->wap_title = '商城下单';
            $this->display();
        }
    }


    /**
     * 商城订单支付
     * @date: 2015-7-18
     * @author: BillyZ
     * @return:
     */
    public function pay()
    {
        $orderid = $_GET['oid'];
        $mOrder = M('MallOrder');
        $attrLen = '';
        $order = $mOrder->where(array('id' => $orderid))->find();
        $product_info=get_mall_id($order['mallid']);
        $shopping_cart_order=$product_info['info'];
        $mall_quantity=$product_info['quantity'];

        foreach ($shopping_cart_order as $key => $val) {
            $shopping_cart_order[$key]['quantity']=$mall_quantity[$val['id']];
        }
        // $attrdate=json_decode($order['attr']);
        foreach($attrdate as $vo){
            $attrLen .='-'.$vo->name;
        }

        //建立支付单
        $unpay_status = 0;//已支付状态
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
                $data['msg'] = '订单已支付，请勿重新发起！';
                die(json_encode($data));
            }
        }
        //代付冲突处理 开始
        $requestid = newrequestid('XC',$order['openid']);
        //添加请求记录、避免支付途中订单号被篡改问题
        add_payrequest($order['orderid'],$requestid);
        if(!empty($df))
        {
            $order['is_df'] = 1;
            $mOrder->save($order);
        }
        else
        {
            if(1 == $order['is_df'])
            {
                $order['is_df'] = 0;
                $mOrder->save($order);
            }
        }

        session("mallid",null);
        session("shopping_cart",null);

        $this->assign('order', $order);
        $this->assign('attrLen', $attrLen);
        $this->assign('mall', $shopping_cart_order);
        $this->assign('rid', $requestid);
        $this->assign('orderprice', $order['price']);
        $this->wap_title = "订单支付";
        $this->assign('jsapi_sign_package', $this->gWechatObj->getSignPackage());
        $this->display();
    }

    /**
     * 微信统一支付下单(ajax调用)
     * @author: Nice <hupeipei@seersee.com>
     */
    public function prepay()
    {

        $transid = $_POST['rsid'];
        $price = $_POST['oprice'] * 100;
        $body = '商城';
        $data = parent::prepay($body, $transid, $price);
        log::Qwrite("7".$data);
        echo $data; 
    }
    /**
     * [shopping_cart description]
     * @return [type] [description]
     */
    public function shopping_cart(){
        $shopping_cart=session('shopping_cart');
        $data=session("mallid");
        if(IS_POST){
            $product_info=I('post.');
            $shopping_cart[$product_info['id']]['quantity']+=$product_info['quantity'];
            $shopping_cart[$product_info['id']]['cl']=$product_info['cl'];
            $shopping_cart[$product_info['id']]['checked']=$product_info['checked'];
            if($product_info['checked']==1){
                $data[$product_info['id']]=$product_info['id'];
                session("mallid",$data);
            }elseif ($product_info['checked']==0) {
                $id=$product_info['id'];
                session("mallid.$id",null);
            }
            session('shopping_cart',$shopping_cart);
            $this->ajaxreturn($shopping_cart);
        }
        $mallid=array_keys(session("shopping_cart"));
        if($mallid){
            $where['id']=array('in',$mallid);
            $mall_product=M('Mall')->where($where)->select();
            foreach ($mall_product as $key => $val) {
                $product_sort[$val['id']] = $val;
            }
            foreach($product_sort as $key=>$val){
                $product_sort[$key]['checked']=$shopping_cart[$key]['checked'];
                $product_sort[$key]['quantity']=$shopping_cart[$key]['quantity'];
            }
        }
        $this->wap_title = "购物车";
        $this->assign('shopping_cart',$product_sort);
        $this->display();
    }
    public function shopping_details(){
        $this->display();
    }
    public function shopping_product_delete(){
       if(IS_POST){
        $id=I('post.id');
        session("shopping_cart.$id",null);
       }
    }
    public function shopping_cart_order(){
        if(session('mallid')){
            $mallid=session('mallid',"");
        }else{
            $this->success('请勿提交订单','http://www.lvling.com/index.php?s=/Wap/Fans/order_mall.html');
        }
        $shopping_cart=session("shopping_cart");
        $where['id']=array('in',$mallid);
        $product=M("Mall")->where($where)->select();
        foreach ($product as $key => $val) {
            $product_sort[$val['id']] = $val;
        }
        foreach($product_sort as $key=>$val){
            $product_sort[$key]['checked']=$shopping_cart[$key]['checked'];
            $product_sort[$key]['quantity']=$shopping_cart[$key]['quantity'];
            $mall_id[$key]=$shopping_cart[$key]['quantity'];
        }
        foreach ($product_sort as $key => $val) {
            $price+=$val['member_price']*$val['quantity'];
        }
        $id=json_encode($mall_id);
        $product_info=get_mall_id($id);
        $shopping_cart_order=$product_info['info'];
        $mall_quantity=$product_info['quantity'];
        foreach ($shopping_cart_order as $key => $val) {
            $shopping_cart_order[$key]['quantity']=$mall_quantity[$val['id']];
        }   
        if (IS_POST) {
            $mallid=json_encode($mall_id);
            $orderid = neworderid('XC', $this->gOpenId);
            $order['mallid'] = $mallid;
            $order['shopid'] = $_POST['shopid'];
            $order['openid'] = $this->gOpenId;
            $order['orderid'] = $orderid;
            $order['is_pay'] = 0;
            $order['status'] = 0; //0:待支付 1：已取货 2：已支付 3：已取消
            $order['create_time'] = NOW_TIME;
            $order['price'] = $price;
            $order['pay_price'] = $price;
            $res = M("MallOrder")->add($order);
            session('oid',$res);
            redirect('/wxpay/wap.php?s=/Mall/pay/oid/' . $res . '.html');
        } else {
            $lstShop =get_shops('XC');
            $this->assign('lstShop',$lstShop);
            $this->assign('mall', $shopping_cart_order);
            $this->assign('price', $price);
            $this->assign('jsapi_sign_package', $this->gWechatObj->getSignPackage());
            $this->wap_title = '商城下单';
            $this->display();
        }
        
    }
    public function test10 (){
        $res=M("MallOrder")->where(array('id'=>'468'))->find();
        if(gettype(json_decode($res['mallid']))=="integer"){
            $mallid=$res['mallid'];
        }else{
            $mallid=json_decode($res["mallid"],true);
        }
        echo is_null($res['mallid']);
        $where['id']=array('in',$mallid);
        $result=M("Mall")->where($where)->select();
        var_dump($result);
        // $this->assign("res",$res);
        // $this->display();
    }
    public function test9(){
        for($k=1;$k<1000;$k++){ 
            $result_old=M("MallOrder")->where(array('id'=>$k))->select();
            var_dump($k);
            if(!$result_old) return $this->error("结束了！");
            $json_mallid=json_encode($result['malli']);
            $res=M("MallOrder")->where(array('id'=>$k))->setfield('mallid',$json_mallid);
            var_dump($res);
        }
    }
}