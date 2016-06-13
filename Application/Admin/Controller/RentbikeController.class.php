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
 * 租车控制器
 * @author MC <zhouyibin@seersee.com>
 */
class RentbikeController extends AdminController {

    /**
     * 后台租车首页
     * @return none
     */
    public function index(){
        $shop_id=I('get.shopid');
        $used_status=I('get.used_status');
        $shop=M('Shop')->where(array('status'=>1))->select();
        $where['status']=array('EGT',0);
    	if(0 < $this->gShopid)
    	{
    		$where['shopid'] = $this->gShopid;
        }else{
            if($shop_id){
                $where['shopid']=$shop_id;
            }
            if($shop_id=='blank'){  
                $where['shopid']=0;
            }
            if($shop_id=='all'){
                $where['shopid']=array('GT',0);
            }
            if(is_numeric($used_status)){
                $where['status']=$used_status;
            }
        }
        $title=I('get.title');
        if($title){
            echo 'title not null';
            $list=M('Rentbike')->where(array('id'=>$title))->select();
        }else{
        $list       =   M("Rentbike")->field(true)->where($where)->order('shopid desc,id asc')->select();
        }
        if($list) {
        	foreach($list as $k => $v)
    		{
    			//$list[$k]['statusname'] = get_maintainorder_status($v['status']);
    			$list[$k]['shop'] = get_shop_id($v['shopid']);
    		}
            $this->assign('list',$list);
        }
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->assign('present',$shop_id);
        $this->assign('used_status',$used_status);
        $this->assign('shop',$shop);
        $this->assign('user',$this->gShopid);
        $this->meta_title = '出租车辆列表';
        $this->display();
    }

    /**
    * 出租车辆添加
    * @date: 2015-7-1
    * @author: BillyZ
    * @return:
    */
    public function add(){
        if(IS_POST){
            $mBike = M('Rentbike');
            $data = $mBike->create();
            if($data){
                $id = $mBike->add();
                if($id){
                    $this->success('新增成功', Cookie('__forward__'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($mBike->getError());
            }
        } else {
        	$shops = M('Shop')->select();
        	$this->assign('shops',$shops);
            $this->meta_title = '新增出租车辆';
            $this->display('edit');
        }
    }

    /**
    * 出租车辆编辑
    * @date: 2015-7-1
    * @author: BillyZ
    * @return:
    */
    public function edit($id = 0){
        if(IS_POST){
            $mBike = M('Rentbike');
            $data = $mBike->create();
            if($data){
                if($mBike->save()!== false){
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($mBike->getError());
            }
        } else {
        	$shops = M('Shop')->select();
        	$this->assign('shops',$shops);
            $info = array();
            /* 获取数据 */
            $info = M('Rentbike')->field(true)->find($id);
            $this->assign('info', $info);
            $this->meta_title = '编辑出租车辆';
            $this->display();
        }
    }

    /**
     * 删除车辆
     * @author yangweijie <yangweijiester@gmail.com>
     */
    public function del(){
        $id = array_unique((array)I('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }
        $data['status']=-1;
        $map = array('id' => array('in', $id) );
        if(M('Rentbike')->where($map)->setfield($data)){
            
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }  
    
    /**
    * 生成出租车二维码
    * @date: 2015-7-14
    * @author: BillyZ
    * @return:
    */
    public function generate_qrcode()
    {
    	//1.添加渠道
    	$bikeid = $_GET['bikeid'];
		
    	$mCanal = M('Canal');
    	$canal = $mCanal->where(array('code'=>'ZC','pid'=>$bikeid))->find();
    	if($canal)
    	{
    		$canalid = $canal['id'];
    	}
    	else
    	{
	    	$canal['name'] = '出租车';
	    	$canal['code'] = 'ZC';
	    	$canal['pid'] = $bikeid;
	    	$canalid = $mCanal->add($canal);
    	}
    	
    	if('' == $canal['qimg'])
    	{
	    	//2.生成渠道二维码（根据渠道id）
	    	$objWechat = new Wechat(C('WEIXIN_CONFIG'));
	    	$jsonQrcode = $objWechat->getQRCode($canalid,1);    	
	
	    	//3.写入img到渠道表
	    	$canal['id'] = $canalid;
	    	$qimg =  'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$jsonQrcode['ticket'];
	    	$canal['qimg'] = $qimg;
	    	$mCanal->save($canal);
    	}
    	else
    	{
    		$qimg = $canal['qimg'];
    	}
    	
    	//4.写入img到rentbike表
    	$bike = M('Rentbike')->where(array('id'=>$bikeid))->find();
    	$bike['qimg'] = $qimg;
    	M('Rentbike')->save($bike);
    	
    	$this->success('二维码生成成功');
    }

    /**
     * 租车订单列表
     * @return none
     */
    public function orders(){
        //参数设置
        $limitStr = "/Rentbike/orders";
        //抓取条件
        $status = $_GET['status'];
        $renter = $_GET['openid'];
        $bikeid = $_GET['bikeid'];
        $key = $_GET['title'];
        $request=I('get.');
         if($request['create_time_start']) {
            $where[]['a.create_time']=array('egt',strtotime($request['create_time_start']));
            $create_time_status='下单时间：'.$request['create_time_start'];
        }
        if($request['create_time_end']){
            $where[]['a.create_time']=array('elt',strtotime($request['create_time_end']));
            $create_time_status.='至'.$request['create_time_end'];
        }
        if($request['pay_time_start']) {
            $where[]['pay_time']=array('egt',strtotime($request['pay_time_start']));
            $pay_time_status='支付时间：'.$request['pay_time_start'];
        }
        if($request['pay_time_end']){
            $where[]['pay_time']=array('elt',strtotime($request['pay_time_end']));
            $pay_time_status.='至'.$request['pay_time_end'];
        } 
        //条件分析
        if($this->gShopid)$where['a.shopid'] = $this->gShopid;
        if(isset($status)&&$status!=null)
        {
            $limitStr .= '/status/'.$status;
            if($status != -1) {$where['a.status'] = $status;}
            else{$where['a.status'] = array("NEQ","3");}
        }
        else {
            $where['a.status'] = array("NEQ","3");
            $status=-1;
        }

        if($renter){
            $limitStr .= '/renter/'.$renter;
            $where['a.openid'] = $renter;
        }
        if($bikeid){
            $limitStr .= '/bikeid/'.$bikeid;
            $where['a.bikeid'] = $bikeid;
        }
        if($key){
            $limitStr .= '/title/'.$key;
            $where['a.transid'] =array('like','%'.$key.'%');
        }
        $limitStr .= '/start_date/'.$startDate;
        $limitStr .= '/end_date/'.$endDate;
        $join[] = "Left join gc_fans b on a.openid = b.openid";
        $join[] = "Left join gc_rentbike c on a.bikeid = c.id";
        $join[] = "Left join gc_shop d on a.shopid = d.id";
        $field = "a.*,b.nickname,b.realname,c.name as bikename,d.name as shopname,b.id as uid";

        //查询
        $list = $this->lists_page("RentbikeOrder",$where,$field,$join,"a.id desc");

        //输出
        $this->assign('status',$status);
        $this->assign('shopid',$this->gShopid);
        $this->assign('limitStr',$limitStr);
        $this->assign('list',$list);
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->assign('user_id', $this->gShopid);
        $this->assign('create_time_end',$request['create_time_end']);
        $this->assign('pay_time_end',$request['pay_time_end']);
        $this->assign('create_time_start',$request['create_time_start']);
        $this->assign('pay_time_start',$request['pay_time_start']);
        $this->assign('create_time_status',$create_time_status);
        $this->assign('pay_time_status',$pay_time_status);
        $this->meta_title = '租车订单列表';
        $this->display();
    }

    /*
        修改订单状态


    */
    public function modify_status(){
        
    }

    /**
     * 导出订单数据
     * @date: 2015-8-24
     * @author: BillyZ
     * @return:
     */
    public function export_orders()
    {
    	$status = I('status');
    	$serache_details=$this->serache_details();
        $where=$serache_details['where'];

    	if($status)
    	{
    		if($status != -1)
    			$where['status'] = $status;
    	}
    	else
    		$status = -1;

    	if(0 < $this->gShopid)
    	{
    		$where['shopid'] = $this->gShopid;
    	}
    	
    
    	$list = M('RentbikeOrder')->alias('a')->join('left join (select id as uid,openid from gc_fans) b on b.openid=a.openid')->where($where)->order('a.id desc')->select();
    
    	$template = "<td width='150' align='center' height='30' style='border:1px solid'>{val}</td>";
    	 
    	header("Content-type:application/vnd.ms-excel");
    	header("Content-Disposition:attachment;filename=".date('Y-m-d',$serache_details['where'][0]['create_time'][1])."_".date('Y-m-d',$serache_details['where'][1]['create_time'][1])."_租车订单.xls");
    	header("Pragma: no-cache");
    	header("Expires: 0");
    	 
    	//输出内容如下：
    	echo "<table style='border:1em solid'>";
    	echo "<tr>";
        echo str_replace('{val}', '用户id', $template);
    	echo str_replace('{val}', '订单号', $template);
    	echo str_replace('{val}', '会员', $template);
    	echo str_replace('{val}', '车辆', $template);
    	echo str_replace('{val}', '门店', $template);
        echo str_replace('{val}', '优惠券', $template);
    	echo str_replace('{val}', '现金', $template);
    	echo str_replace('{val}', '支付方式', $template);
        echo str_replace('{val}', '创建时间', $template);
    	echo str_replace('{val}', '还车时间', $template);
    	echo str_replace('{val}', '状态', $template);
    	echo "</tr>";
    	 
    	$pay_type = '本人支付';
    	for($i=0;$i<count($list);$i++)
    	{
    		echo "<tr>";
            echo str_replace('{val}', $list[$i]['uid'], $template);
    		echo str_replace('{val}', $list[$i]['transid'], $template);
    		echo str_replace('{val}', get_fansname_openid($list[$i]['openid']), $template);
    		echo str_replace('{val}', get_bike_id($list[$i]['bikeid']), $template);
    		echo str_replace('{val}', get_shop_id($list[$i]['shopid']), $template);
            echo str_replace('{val}', $list[$i]['coupon_price'], $template);
    		echo str_replace('{val}', $list[$i]['pay_price'], $template);
    		 
    		if(1 == $list[$i]['is_df'])
    		{
    			$pay_type = '代付';
    		}
    		else if(0 < $list[$i]['couponid'])
    		{
    			$pay_type = '优惠券';
    		}
    		else
    			$pay_type = '本人支付';
    
    		echo str_replace('{val}', $pay_type, $template);
            echo str_replace('{val}', date('Y-m-d H:i',$list[$i]['create_time']), $template);
            if(0==$list[$i]['return_time'] && $list[$i]['status']!==3){
                $return_time="未还车";
            }else if(0==$list[$i]['return_time'] && $list[$i]['status']==3){
                $return_time="已取消";
            }else{
                $return_time= date('Y-m-d H:i',$list[$i]['return_time']);
            }
    		echo str_replace('{val}',$return_time, $template);
    		echo str_replace('{val}', get_rentorder_astatus($list[$i]['status']), $template);
    
    		echo "</tr>";
    	}
    	echo "</table>";
    }  
    /**
    * 确认开始
    * @date: 2015-7-18
    * @author: BillyZ
    * @return:
    */
    public function rent_start()
    {
    	$orderid = $_GET['oid'];
        //判断租车数量
        $rent_bike_num=M('RentbikeOrder')->where(array('status'=>1,'openid'=>I('get.openid')))->group('openid')->count('openid');
        if($rent_bike_num>4){
            $this->error('该用户租车超过五辆'.$rent_bike_num);
        }
        $order = M('RentbikeOrder')->where(array('id'=>$orderid,"status"=>0))->find();
        if($order)
        {    
            if(!M('RentbikeOrder')->where(array('bikeid'=>$order['bikeid'],"status"=>array("in","1,5")))->find()){
                $bikecode = M('Rentbike')->where(array('id'=>$order['bikeid']))->getField("code");
                //等待开始的状态才可以
                $order['status'] = 1;
                $order['start_time'] = NOW_TIME;
                M('RentbikeOrder')->where(array('id'=>$orderid))->save($order);
               
                M('Rentbike')->where(array('id'=>$order['bikeid']))->setfield('status',1);
            }
            else{
                $this->error('该车辆使用中或未结算，请等待归还或结算，或取消本订单！');
            }

    	}

        //计算骑行价格
        $rent_price='5.00元';
        $bike = M('Rentbike')->where(array('id' => $order['bikeid']))->find();
        if ($bike) {
            $shop = M('Shop')->where(array('id' => $bike['shopid']))->find();
            $areaprice = M('RentbikeTypeArea')->where(array('rentbiketypeid'=>$bike['rentbiketypeid'],'cityid'=>$shop['cityid'],'areaid'=>$shop['areaid']))->getField('priceconfig');
            if(!empty($areaprice))
            {
                $price = explode(',', $areaprice);
                $rent_price =$price[3];
            }
        }


    	
    	//开启车辆MOS管
    	if($this->open_bike_mos($bikecode)){
            $this->mos_log(1,$order['id'],1,1,$bikecode);
        	$data = array(
    							"first"=>array("value"=>"欢迎使用滴啊滴租车服务，您的车辆已经开通成功！","color"=>"#173177"),
    							"keyword1"=>array("value"=>get_shop_id($order['shopid']),"color"=>"#173177"),
    							"keyword2"=>array("value"=>get_bike_id($order['bikeid']),"color"=>"#173177"),
    							"keyword3"=>array("value"=>$rent_price.'.00元',"color"=>"#173177"),
    							"keyword4"=>array("value"=>'0.00元',"color"=>"#173177"),
    							"keyword5"=>array("value"=>date('Y-m-d H:i'),"color"=>"#173177"),
    							"remark"=>array("value"=>"点击查看详情","color"=>"#173177")
    					);
        	parent::sendTempMsg($order['openid'], 'Ae8jmC0agAeDQ3-bpL9kFetZHCE9uxx4XQ_OQW2cVDM', C('site_url').'/wap.php?s=/Fans/order_rent_details/oid/'.$orderid.'.html', $data);
        	
            $this->success('确认成功');

        }
        else{
            $this->mos_log(1,$order['id'],0,0,$bikecode);//eventid siwtch_status success
            $this->error('开启失败');
        }
    }
    
    /**
    * 结束用车，计费
    * @date: 2015-7-18
    * @author: BillyZ
    * @return:
    */
    public function rent_end()
    {
        //todo 开关顺序有空可以优化下 Ketory
    	$orderid = $_GET['oid'];
    	$order = M('RentbikeOrder')->where(array('id'=>$orderid))->find();
        $bikecode = M('Rentbike')->where(array('id'=>$order['bikeid']))->getField("code");
        if($order)
    	{
    		if(5 == $order['status'])
    		{
    			$order['status'] = 2;//已还车->已结算
    			$now = NOW_TIME;
    			$order['end_time'] = $now;
    			$rentspan = $now - $order['start_time'];
    			$order['timespan'] = $rentspan;
    			//计算金额
    			$order['price'] = $this->get_rent_price($rentspan);
    			$order['pay_price'] = $order['price'];
    			M('RentbikeOrder')->where(array('id'=>$orderid))->save($order);
 
    			//发送模板消息给用户
    			$data = array(
    					"first"=>array("value"=>"您有一笔未支付的租车订单","color"=>"#173177"),
    					"ordertape"=>array("value"=>date('Y-m-d',$order['create_time']),"color"=>"#173177"),
    					"ordeID"=>array("value"=>$order['transid'],"color"=>"#173177"),
    					"remark"=>array("value"=>"点击查看详情","color"=>"#173177")
    			);
    			
    			parent::sendTempMsg($order['openid'], "U2dIzQou-szoHK9HsRPbqvdkiZdxz4cBqkBxdseKcGs", C('site_url')."/wxpay/wap.php?s=/Rent/pay/oid/".$order['id'].".html", $data);
    		}
    	}

    	if($this->close_bike_mos($bikecode)){
            $this->mos_log(1,$order['id'],0,1,$bikecode);//eventid switch_status success
            $this->success('确认成功');
        }
        else{
            $this->mos_log(1,$order['id'],1,0,$bikecode);//eventid siwtch_status success
            $this->error('关闭失败');
        }
    }
    
    /**
    * 结束订单
    * @date: 2015-7-18
    * @author: BillyZ
    * @return:
    */
    public function rent_cancel()
    {
    	$orderid = $_GET['oid'];
    	$order = M('RentbikeOrder')->where(array('id'=>$orderid))->find();
    	if($order)
    	{
    		//if(0 == $order['status'])
    		//{
    			//等待开始的状态才可以
    			$order['status'] = 3;
    			$order['end_time'] = NOW_TIME;
            if(M('RentbikeOrder')->where(array('id'=>$orderid))->save($order)){
             $this->success('取消成功');
            }
            else{
                $this->error('取消失败');
            }

    		//}
    	}

    }
    
    /**
    * 开启车辆
    * @date: 2015-8-9
    * @author: BillyZ
    * @return:
    */
    public function bike_open()
    {
    	$bikeid = $_GET['bikeid'];
    	
    	$bikecode = M('Rentbike')->where(array('id'=>$bikeid))->getField('code');
    	if(!empty($bikecode))
    	{
    		if($this->open_bike_mos($bikecode)){
                $this->mos_log(2,$this->gShopid,1,1,$bikecode);//eventid switch_status success
                $this->success('开启成功！');
            }
    		else{
                $this->mos_log(2,$this->gShopid,0,0,$bikecode);//eventid switch_status success
                $this->success('开启失败！');
            }
    	}
    	else
    		$this->success('开启失败，找不到对应车辆或车辆编码未设置');
    }
    
    /**
    * 关闭车辆
    * @date: 2015-8-9
    * @author: BillyZ
    * @return:
    */
    public function bike_close()
    {
    	$bikeid = $_GET['bikeid'];
    	
    	$bikecode = M('Rentbike')->where(array('id'=>$bikeid))->getField('code');
    	if(!empty($bikecode))
    	{
    		if($this->close_bike_mos($bikecode)){
                $this->mos_log(2,$this->gShopid,0,1,$bikecode);//eventid switch_status success
                $this->success('关闭成功！');
            }
            else{
                $this->mos_log(2,$this->gShopid,1,0,$bikecode);//eventid switch_status success
                $this->success('关闭失败！');
            }

    	}
    	else
    		$this->success('关闭失败，找不到对应车辆或车辆编码未设置');
    }
    
    /**
    * 订单详情
    * @date: 2015-7-25
    * @author: BillyZ
    * @return:
    */
    public function details()
    {
    	$lng =  119.654889;
    	$lat =  29.08597;
    	$shop = M('Shop')->where(array('id'=>$this->gShopid))->find();
    	if($shop)
    	{
    		$lat = $shop['latitude'];
    		$lng = $shop['longitude'];
    	}


    	$this->assign('lat',$lat);
    	$this->assign('lng',$lng);
    	$this->assign('shop',$shop);
    	
    	$orderid = I('oid');
    	$mOrder = M('RentbikeOrder');
    	$order = $mOrder->where(array('id'=>$orderid))->find();
    	$bike = M('Rentbike')->where(array('id'=>$order['bikeid']))->find();
        if($order['end_time']==0){
            $endtime = time();
        }
        else{
            $endtime = $order['end_time'];
        }
        $fans = M('fans')->where(array('openid'=>$order['openid']))->find();
//        $gpsinfo = $this->get_line_deal($bike['code'],$order['start_time'],$endtime);
        $gpsinfo = $this->get_line_deal($bike['code'],$endtime-3600,$endtime);
//        dump($gpsinfo);exit;
        $this->assign('gps',$gpsinfo);
        $this->assign('fans',$fans);
    	$this->assign('order',$order);
    	$this->display();
    }

    /**
     * 报价方案
     * @date: 2015-7-20
     * @author: BillyZ
     * @return:
     */
    public function orderprice()
    {
    	if(IS_POST)
    	{
    		$mOrder = M('RentbikeOrder');
    		$order = $mOrder->where(array('id'=>$_POST['oid']))->find();
    		if($order)
    		{
    			if(5 == $order['status'])
    			{
                    M('Rentbike')->where(array('id'=>$order['bikeid']))->setfield('status',0);
	    			$order['status'] = 2;//已还车->已结算
	    			$now = NOW_TIME;
	    			$order['end_time'] = $now;
	    			$rentspan = $order['return_time'] - $order['start_time'];
	    			$order['timespan'] = $rentspan;
	    			//计算金额
	    			//增加优惠券优惠金额
	    			$order['couponid'] = $_POST['couponid'];
	    			if(0 < $order['couponid'])
	    			{
	    				coupon_used($order['couponid']);
	    				$order['coupon_price'] = $_POST['coupon_price'];
	    			}
	    			else 
	    				$order['coupon_price'] = 0;
                    $alert_price=I('post.alert_price',0);
                    //获取区域价格配置
                    $priceconfig =$this->get_biketype_priceconfig($order['bikeid']);

                    $order['rent_price'] = $this->get_rent_price($order['timespan'],$priceconfig,$order['return_time'],$order['start_time']);
	    			$order['damage_price'] = $_POST['damage_price'];
	    			//总价
	    			$order['price'] = $order['rent_price'] + $order['damage_price'];
	    		
	    			$order['pay_price'] = $order['price'] - $order['coupon_price']-$alert_price;
                    $order['night_price'] = I('night_price');
	    			if(0 > $order['pay_price'])
	    				$order['pay_price'] = 0;
	    			
	    			$mOrder->where(array('id'=>$_POST['oid']))->save($order);
	    			 
	    			//发送模板消息给用户
	    			$data = array(
	    					"first"=>array("value"=>"您有一笔未支付的租车订单","color"=>"#173177"),
	    					"ordertape"=>array("value"=>date('Y-m-d',$order['create_time']),"color"=>"#173177"),
	    					"ordeID"=>array("value"=>$order['transid'],"color"=>"#173177"),
	    					"remark"=>array("value"=>"点击查看详情","color"=>"#173177")
	    			);
	    			
	    			parent::sendTempMsg($order['openid'], "U2dIzQou-szoHK9HsRPbqvdkiZdxz4cBqkBxdseKcGs", C('site_url')."/wxpay/wap.php?s=/Rent/pay/oid/".$order['id'].".html", $data);
	    			 
	    			
	    			//3.关闭MOS管
	    			$bikecode = M('Rentbike')->where(array('id'=>$order['bikeid']))->getField('code');
                    if($bikecode)
                        if($this->close_bike_mos($bikecode)){
                            $this->mos_log(1,$order['id'],0,1,$bikecode);//eventid switch_status success
                        }
                        else{
                            $this->mos_log(1,$order['id'],1,0,$bikecode);//eventid switch_status success
                        }
	    			$this->success('报价成功', Cookie('__forward__'));
    			}
    			else
    				$this->success('订单状态不正确');
    		}
    	}
    	else
    	{
            $day=0;
    		$order = M('RentbikeOrder')->where(array('id'=>$_GET['oid']))->find();

            $order['showtimespan'] = ceil(($order['return_time'] - $order['start_time'])/60);
            $order['timespan'] = ($order['return_time'] - $order['start_time']);
            //获取区域价格配置
            $priceconfig =$this->get_biketype_priceconfig($order['bikeid']);

            $order['rent_price'] = $this->get_rent_price($order['timespan'],$priceconfig,$order['return_time'],$order['start_time'],$day);
    		
    		//用户优惠券
    		$fans = get_obj_fans_openid($order['openid'],true);
    		if($fans)
    		{
    			$coupons = M('FansCoupon')->where(array('uid'=>$fans['id'],'service_type'=>4,'status'=>1))->select();
    			$this->assign('coupons',$coupons);
    		}
            $this->assign('day',$day);
    		$this->assign('order',$order);
            $this->assign('alert_price',I('get.alert_price',0));
    		$this->display();
    	}
    }

    public function test(){
//        $gpsinfo = $this->get_line_deal("800004454","1438853352","1438895328");
        dump($this->get_rent_price(320*60));
//        dump($gpsinfo);exit;
//        $this->assign('loc',$gpsinfo);
//        $this->display();
    }
    public function test2(){
        $key = $_GET['key'];
        echo $key."<br>";
//        $where['__FANS__.nikename'] = array("like","%".$key."%");
//        $list = $this->lists_page("RentbikeOrder",$where,1,"LEFT JOIN __FANS__ b on a.openid = __FANS__.openid");
        $join[]='left join gc_fans  b on b.openid=a.openid ';
        $join[]='left join gc_maintain_product c on c.id=a.pid ';
        $field = "b.nickname,b.realname,c.name as pname,a.transid";
        $where['b.nickname']=array('like','%'.$key.'%');
        $where['c.name']=array('like','%保养%');
        $list = $this->lists_page("MaintainOrder",$where,$field,$join,"a.id desc");
        $this->dumparray($list);
        echo $show;
    }

    /**
    * 租车价格计算
    * @date: 2015-7-18
    * @author: BillyZ
    * @return:
    */
    private function get_rent_price($timespan,$areaprice='',$rent_end_time,$rent_start_time,&$day=0)
    {
        $conf = explode(",",C("RENTBIKE_SET"));
        if(!empty($areaprice))
        {
            $conf =explode(",",$areaprice);
        }

//        $rent_end_date = date('Y-m-d', $rent_end_time);
//        $rent_end_date_hour = date('H', $rent_end_time);
//        $rent_start_date = date('Y-m-d', $rent_start_time);
//        $date_span = (strtotime($rent_end_date)-strtotime($rent_start_date))/86400;//过夜天数
//
//        if($date_span>0)
//        {
//            if((int)$rent_end_date_hour<10)
//            {
//                //one part
//                $night_timspan = 36000;//没过一夜的时间戳（0：00~10：00）共36000秒
//                $night_timspan_onepart = ($date_span-1)*$night_timspan;
//                //two part
//                $night_timspan_twopart = $rent_end_date_hour*3600;
//                $timespan = $timespan-$night_timspan_onepart-$night_timspan_twopart;
//            }
//            else if((int)$rent_end_date_hour>=10)
//            {
//                $night_timspan = 36000;//没过一夜的时间戳（0：00~10：00）共36000秒
//                $night_timspan_all = $date_span*$night_timspan;
//                $timespan = $timespan-$night_timspan_all;
//            }
//        }
        $rent_end_date = date('Y-m-d', $rent_end_time);
        $rent_start_date = date('Y-m-d', $rent_start_time);
        $rent_start_time_p1 = date('Y-m-d', $rent_start_time+3600*24);
        $rent_end_time_zero = date('Y-m-d', $rent_end_time);

        $date_span = (strtotime($rent_end_date)-strtotime($rent_start_date))/86400;//过夜天数
        $hours=0;
        $price = 0;
        if($date_span>0) {
            $top_hour = ceil((strtotime($rent_start_time_p1)-$rent_start_time) / 3600);
            $middle_hour = ($date_span-1)*14;
            $bottom_hour=ceil(($rent_end_time-strtotime($rent_end_time_zero)) / 3600);
            if($bottom_hour>10)
            {
                $bottom_hour=$bottom_hour-10;
            }
            else
            {
                $bottom_hour=0;
            }
            $hours = $top_hour+$middle_hour+$bottom_hour;
        }
        //conf{首时（H）,首价（元）,续时（H），续价（元），封顶（元）,优惠时间（min）}
    	//秒数转化为小时数
    	Log::write('get_rent_price,');
    	$hours = ceil(($timespan-($conf[5]*60)) / 3600);
    	if($hours <= $conf[0])//首时
    	{
    		$price = $conf[1];//首价
    	}
    	else
    	{
            $price = (($hours-$conf[0])/$conf[2] * $conf[3]) + $conf[1];
            //价格 = (H-首时)/续时*续价 + 首价
    		if($price > $conf[4])$price = $conf[4];
    	}
        $day = $date_span;
    	return $price;
    }

    /**
     * 租车价格计算
     * @date: 2015-7-18
     * @author: BillyZ
     * @return:
     */
    private function get_rent_price_areaconfig($bikeid)
    {

        return $price;
    }

//todo 函数调用优化
//todo gps查询缓存优化
    /**
     * 对象数组转换为array数组
     * @date: 2015-8-5
     * @param object $array 对象
     * @author: Ketory
     * @return：array 数组
     */
    private function object_array($array) {
        if(is_object($array)) {
            $array = (array)$array;
        } if(is_array($array)) {
            foreach($array as $key=>$value) {
                $array[$key] = $this->object_array($value);
            }
        }
        return $array;
    }

    /**
     * 获取gps最近状态
     * @date: 2015-9-6
     * @author: Ketory
     */
    public function get_gps_status(){
        $id= $_GET['id'];
        $gpsinfo = $this->get_line_deal($id,time()-300,time(),"false",1);
//        dump($gpsinfo);
        $this->success(count($gpsinfo));
    }

    /**
     * Gps数据过滤
     * @date: 2015-8-5
     * @param array $array 对象
     * @param array $bearr 待合并数组
     * @param bollen $filter 是否过滤相同位置信息 0过滤 1不过滤
     * @author: Ketory
     * @return：array gps信息
     */
    private function gps_getinfo($arr,$loc=array(),$filter=0){
        foreach($arr['Body'] as $key){
            $location['lo'] = $key['GPSInfo']['Longitude'];
            $location['la'] = $key['GPSInfo']['Latitude'];
            $location['st'] = $key['SamplingTime'];
            $location['stt'] = date("Y-m-d H:i:s",substr($key['SamplingTime'],6,10));
            $location['id'] = $key['GPSID'];
            $location['speed'] = $key['GPSInfo']['Speed'];
            $location['mil'] = $key['GPSInfo']['TotalMileage'];
            if(($location['lo']!=$lco_old['lo']||$location['la']!=$lco_old['la'])||$filter) $loc[] = $location;
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
     * @param bollen $filter 是否过滤相同位置信息 0过滤 1不过滤
     * @author: Ketory
     * @return:GPS轨迹json
     */
    private function get_line_deal($id,$stime,$etime,$status="true",$filter=0){
        $g = array();
        $i = ceil(($etime-$stime)/3600);
        if($i<=1){
            return $this->gps_getinfo($this->object_array(json_decode($this->get_line_data($id,$stime,$etime,$status))),array(),$filter);
        }
        else{
            for($k=($i-1);$k>=0;$k--){
                if($k==($i-1)){
                    $g = $this->gps_getinfo($this->object_array(json_decode($this->get_line_data($id,($stime+($k*3600)),$etime,$status))),$g,$filter);
                }
                else{
                    $g = $this->gps_getinfo($this->object_array(json_decode($this->get_line_data($id,($stime+($k*3600)),$stime+(($k+1)*3600),$status))),$g,$filter);
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
    private function get_line_data($id,$stime,$etime,$status ="true"){
        $stime = date("Y-m-d H:i:s",$stime);
        $etime = date("Y-m-d H:i:s",$etime);
        $sign = MD5("a06b33d1ea28e90733617ec889d4e76eGPSID".$id."StartTime".$stime."EndTime".$etime."OnlySuccessPosition".$status."a06b33d1ea28e90733617ec889d4e76e");
        $post_str='{"Action":"GPS.GetLocationsFromDatabase","key":"2","Parameters":{"GPSID":"'.$id.'","StartTime":"'.$stime.'","EndTime":"'.$etime.'","OnlySuccessPosition":'.$status.'},"Sign":"'.$sign.'","Version":"1.0"}';
//        echo $post_str;
        $s = http_post_json("open.lydong.com/svc.ashx",$post_str);
//        dump($s);
        return $s;
    }
    
    /**
    * 开启mos管
    * @date: 2015-8-9
    * @author: BillyZ
    * @return:
    */
    private function open_bike_mos($bikecode)
    {
        $operation="EBike.OnPower";
        return $this->operate_bike_mos($bikecode,$operation);
        
    }
    
    /**
    * 关闭mos管
    * @date: 2015-8-9
    * @author: BillyZ
    * @return:
    */
    private function close_bike_mos($bikecode)
    {
        $operation="EBike.OffPower";
        return $this->operate_bike_mos($bikecode,$operation);
        
    }

    /**
   * $operation  操作开启EBike.OnPower;关闭EBike.OffPower
   *@return string result id
   */
  public function operate_bike_mos($bikeid,$operation){
    $data["Key"]="wedfwef34645w4cbnm";
    $data["TimeStamp"]=time();
    $data["SerialNumber"]=time()."-".$bikeid."-"."0";
    $data["Version"]="2.0";
    $data["Action"]=$operation;
    $data["Parameters"]=array("EBikeTerminalID"=>$bikeid);
    $data["Sign"]=create_sign_request($data,"adafs32436fdgdfg");
    $url="http://open.lydong.com:8080/LuYuanDongOpen/index.jsp";
    $res=http_post_json($url,json_encode($data));
    $res_json=json_decode($res,true);
    if(0==$res_json['Result']){
    		return true;
    }else{
        $operate_result=change_to_string($res_json,array("Result"=>array("0"=>"调用成功","1"=>"post数据内容不符合规范","2"=>"接入appkey未注册","3"=>"接入app被禁用","4"=>"签名错误","5"=>"非法来源IP","6"=>"应用Session失效","7"=>"调用时间戳与服务器存在较大误差，不允许","8"=>"重复指令，非法","9"=>"调用类型不存在","10"=>"请求方法不够权限或不存在","11"=>"其他错误，如参数值错误，执行错误")));
        Log::rentbike_info("车辆：".$bikeid."操作结果".$operate_result["Result_text"]);
    		return false;
    }
  }


  public function operate_result(){
    if(IS_POST){
      $res=I("post.");
      Log::rentbike_info(json_decode($res)."post");
      $res=I("get.");
      Log::rentbike_info(json_decode($res)."get");
      $code=I("post.status");
      $operate_post_result=change_to_string($code,array("Status"=>array("1"=>"接口已收到指令","2"=>"指令已成功发送到终端","3"=>"终端指令执行成功","4"=>"终端离线（未上线或者断线）","5"=>"无法访问指令中转程序","6"=>"指令发送到终端失败","7"=>"指令执行失败","8"=>"执行设备未应答")));
      Log::rentbike_info("指令执行状态".$operate_post_result["Status_text"]);
    }
  }

    private function mos_log($eventid,$orderid,$switch_status,$success,$bikecode){
        
        $mos_log=array(
                    "eventid"=>$eventid,
                    "event"=>$orderid,
                    "switch_status"=>$switch_status,
                    "success"=>$success,
                    "ex_time"=>NOW_TIME,
                    "gps"=>$bikecode
                );
            if(!M('MosLog')->add($mos_log)){
                log::rentbike_info('数据库MOS记录失败'.date("Y-m-d H:i:s",NOW_TIME));
            }
    }
    public function get_mos_log(){
        $bikecode=I('get.bikecode');
        $mos=M('MosLog')->where(array('gps'=>$bikecode))->limit(20)->order('id desc')->select();
        $this->ajaxReturn($mos);
    }

    public function dumparray($list){
        if(is_array($list)){
            echo "<table style='border-left: 1px solid #505050;border-top: 1px solid #505050;'>";echo "<tr>";
            foreach(array_keys(current($list)) as $vo ){
                echo "<th style='border-right: 1px solid #505050;border-bottom: 1px solid #505050; padding: 5px;'>".$vo."</th>";
            }
            echo "</tr>";
            foreach($list as $li ){
                echo "<tr>";
                foreach($li as $l){
                    echo "<td style='border-right: 1px solid #505050;border-bottom: 1px solid #505050;padding: 5px;'>";
                    if(!is_array($l)){
                        echo $l;
                    }else{
                        $this->dumparray($li);
                    }
                    echo "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }
        else{
            echo $list;
            echo "<br>";
        }
    }

    public function getrentbiketype()
    {
        $shopid = I('get.shopid');
        $shopinfo = M('Shop')->where(array('id'=>$shopid))->find();
        $list = M('RentbikeTypeArea')->join('gc_rentbike_type on gc_rentbike_type.id = gc_rentbike_type_area.rentbiketypeid')->where(array('cityid'=>$shopinfo['cityid'],'areaid'=>$shopinfo['areaid']))->select();
        $this->ajaxReturn($list,'JSON');
    }

    private function get_biketype_priceconfig($bikeid)
    {
        $rentbikeinfo = M('Rentbike')->where(array('id'=>$bikeid))->field('rentbiketypeid,shopid')->find();
        $shopinfo = M('Shop')->where(array('id'=>$rentbikeinfo['shopid']))->field('cityid,areaid')->find();
        $priceconfig = M('RentbikeTypeArea')->where(array('cityid'=>$shopinfo['cityid'],'areaid'=>$shopinfo['areaid'],'rentbiketypeid'=>$rentbikeinfo['rentbiketypeid'],'status'=>1))->getField('priceconfig');
        return $priceconfig;
    }

    //租车改价入口
    public function alert_price(){
        $data=I('get.oid'); //oid type
        $order=M('RentbikeOrder')->where(array('id'=>$data))->find();
        if($order['status']!=5){
            if(!$order['return_time']){
                $arr['return_time']=time();
            }
            $arr['status']=5;
            $res=M('RentbikeOrder')->where(array('id'=>$data))->save($arr);
        }
        $destination=C('LOG_PATH').'/alert_price/'.date('y_m_d').'.log';
        Log::write('改价订单ID：'.$data,'ERROR',$type='',$destination=$destination);
        header('location:'.U('Rentbike/orderprice?alert_price=1&oid='.$data));
    }
}