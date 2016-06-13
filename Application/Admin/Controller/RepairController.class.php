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
 * 后台维修控制器
 * @author MC <zhouybin@seersee.com>
 */
class RepairController extends AdminController {

    /**
     * 后台维修配件管理
     * @return none
     */
    public function parts(){
        $pid  = I('get.pid',0);
        if($pid){
            $data = M('RepairParts')->where("id={$pid}")->field(true)->find();
            $this->assign('data',$data);
        }
        $title      =   trim(I('get.title'));
        $map['pid'] =   $pid;
        if($title)
            $map['name'] = array('like',"%{$title}%");
        $list       =   M("RepairParts")->where($map)->field(true)->order('id asc')->select();
         
        $this->assign('list',$list);
        
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);

        $this->meta_title = '维修配件列表';
        $this->display();
    }

    /**
    * 维修配件添加
    * @date: 2015-7-1
    * @author: BillyZ
    * @return:
    */
    public function parts_add(){
        if(IS_POST){
            $mParts = M('RepairParts');
            $data = $mParts->create();
            if($data){
                $id = $mParts->add();
                if($id){
                    $this->success('新增成功', Cookie('__forward__'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($mParts->getError());
            }
        } else {
            $this->meta_title = '新增产品';
            $this->display('parts_edit');
        }
    }

    /**
    * 维修配件编辑
    * @date: 2015-7-1
    * @author: BillyZ
    * @return:
    */
    public function parts_edit($id = 0){
        if(IS_POST){
            $mParts = D('RepairParts');
            $data = $mParts->create();
            if($data){
                if($mParts->save()!== false){
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($mParts->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('RepairParts')->field(true)->find($id);
            $menus = M('RepairParts')->field(true)->select(); 
            
            $repair_parts_types = M('RepairPartsType')->where(array('status'=>1))->select();

            $this->assign('repair_parts_type', $repair_parts_types);
            $this->assign('Menus', $menus);
            if(false === $info){
                $this->error('获取后台菜单信息错误');
            }
            $this->assign('info', $info);
            $this->meta_title = '编辑后台保养产品';
            $this->display();
        }
    }

   /**
   * 删除维修配件
   * @date: 2015-7-1
   * @author: BillyZ
   * @return:
   */
    public function parts_del(){
        $id = array_unique((array)I('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('RepairParts')->where($map)->delete()){
           
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 维修订单列表
     * @return none
     */
    public function orders(){
    	$title      =   trim(I('title'));
        $serache_details=$this->serache_details();
        $where=$serache_details['where'];
        $status=I('status');
    	if(isset($status)&&$status!=null)
    	{
    		if($status != -1)
    			$where['a.status'] = $status;
    	}
    	else
    		$status = -1;
    	if(0 < $this->gShopid)
    	{

    		$where['_string'] = 'shopid = 0 or shopid = '.$this->gShopid;
    	}
    	
    	$this->assign('status',$status);

    
        //1:维修 2：保养 3：充电 4：租车 5：精品
        if($title){
            if(0 < $this->gShopid)
            $map['shopid'] = $this->gShopid;
            $map['nickname']=array('like','%'.$title.'%');
            $map['realname']=array('like','%'.$title.'%');
            $map['transid']=array('like','%'.$title.'%');
            $map['_logic']='or';
            $condition['_complex']=$map;
            $this->lists_page('RepairOrder',$condition,'left join gc_fans on gc_repair_order.openid=gc_fans.openid left join (select memo,oid,star_total,create_time as memo_time,star_service,star_oper from gc_remark where service_type=1) as b on gc_repair_order.id=b.oid ',I('get.p'),20,'gc_repair_order.id desc');
        }else{
        $join[] = "Left join gc_fans b on a.openid = b.openid";
        $join[] = "Left join (select memo,oid,star_total,create_time as memo_time,star_service,star_oper from gc_remark where service_type=1) c on a.id=c.oid";
        $field="a.*,b.id as uid,c.*";
        $list=$this->lists_page("RepairOrder",$where,$field,$join,"a.id desc");
    	$this->assign('list',$list);
        }
    	// 记录当前列表页的cookie
    	Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->assign('user_id', $this->gShopid);
    	$this->meta_title = '维修订单列表';
    	$this->display();
    }

    /**
     * 结束订单
     * @date: 2015-7-18
     * @author: BillyZ
     * @return:
     */
    public function order_cancel()
    {
    	$orderid = $_GET['oid'];
    	$order = M('RepairOrder')->where(array('id'=>$orderid))->find();
    	if($order)
    	{
    		if(0 == $order['status'])
    		{
    			//等待开始的状态才可以
    			$order['status'] = 3;
    			$order['end_time'] = NOW_TIME;
    			M('RepairOrder')->save($order);
    		}
    	}
    	$this->success('取消成功');
    }
    
    /**
     * 抢单
     * @date: 2015-7-18
     * @author: BillyZ
     * @return:
     */
    public function order_grab()
    {
    	$orderid = $_GET['oid'];
    	$order = M('RepairOrder')->where(array('id'=>$orderid))->find();
    	if($order)
    	{
    		if(4 == $order['status'])
    		{
    			//等待开始的状态才可以
    			$order['status'] = 0;
    			$order['shopid'] = $this->gShopid;
    			M('RepairOrder')->save($order);
    			
    			logorder($this->gUid, $orderid, 1, '门店【'.$this->gShopname.'】抢单成功');
    			
    			//发送模板消息
    			if(empty($order['openid']))
    				$openid = "o3g0Gs5Ueri5czuTHNceerHcNr_o";
    			else
    				$openid = $order['openid'];
    			$tempid = 'fvAdpJ_oU7ce7D6XXSCSVMC0FwJLJwAyE40ooY0QCzY';
    			$url = C('site_url')."/wap.php?s=/Fans/order_repair_details/oid/".$order['id'].".html";
    			$data = array(
    							"first"=>array("value"=>"您的求助订单已成功被抢,【".$this->gShopname."】稍后将为您服务","color"=>"#173177"),
    							"keyword1"=>array("value"=>date('Y-m-d',$order['create_time']),"color"=>"#173177"),
    							"keyword2"=>array("value"=>$this->gShopname,"color"=>"#173177"),
    							"keyword3"=>array("value"=>'一键求助维修服务',"color"=>"#173177"),
    							"remark"=>array("value"=>"点击查看详情","color"=>"#173177")
    					);
    			parent::sendTempMsg($openid, $tempid, $url, $data);
    			
    			$this->success('抢单成功');
    		}
    		else 
    			$this->error('已被其他门店抢单');
    	}
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
    		$mOrder = M('RepairOrder');
    		$order = $mOrder->where(array('id'=>$_POST['oid']))->find();
    		if($order)
    		{
    			//1.报价
    			$order['price'] = $_POST['hdnTotal'];
    			$order['pay_price'] = $_POST['hdnTotal'] - I('coupon_price');
    			$order['price_time'] = NOW_TIME;
    			$order['work_hour'] = $_POST['work_hour'];
    			$order['new_parts'] = '';
    			$order['parts_in'] = $_POST['parts_in'];
    			$order['parts_out'] = $_POST['parts_out'];
    			$order['work_price'] = $_POST['work_price'];
    			$order['parts_price'] = $_POST['parts_price'] + $_POST['other_price'];
    			$order['ride_price'] = $_POST['ride_price'];
    			$order['parts_other'] = $_POST['parts_other'];
                $order['dragcar_price'] = $_POST['dragcar_price'];
                $order['sendcar_price'] = $_POST['sendcar_price'];
    			$order['status'] = 1;

				//优惠券
				//增加优惠券优惠金额
				$order['couponid'] = $_POST['couponid'];
				if(0 < $order['couponid'])
				{
					coupon_used($order['couponid']);
					$order['coupon_price'] = $_POST['coupon_price'];
				}
				else
					$order['coupon_price'] = 0;
    			
    			$mOrder->save($order);
    			
    			//2.发消息给客户
    			if(empty($order['openid']))
    				$openid = "o3g0Gs5Ueri5czuTHNceerHcNr_o";
    			else
    				$openid = $order['openid'];
    			
    			$tempid = "U2dIzQou-szoHK9HsRPbqvdkiZdxz4cBqkBxdseKcGs";
    			$url = C('site_url')."/wap.php?s=/Fans/order_repair_details/oid/".$order['id'].".html";
    			$data = array(
    							"first"=>array("value"=>"您有一笔未支付的维修订单","color"=>"#173177"),
    							"ordertape"=>array("value"=>date('Y-m-d',$order['price_time']),"color"=>"#173177"),
    							"ordeID"=>array("value"=>$order['transid'],"color"=>"#173177"),
    							"remark"=>array("value"=>"点击查看详情","color"=>"#173177")
    					);
    			
    			parent::sendTempMsg($openid,$tempid,$url,$data);

    			$this->success('报价成功', Cookie('__forward__'));
    		}
    	}
    	else
    	{
	    	$mParts = M('RepairParts');
	    	$lstParts = $mParts->select();
	    	$this->assign('lstParts',$lstParts);
	    	
	    	$order = M('RepairOrder')->where(array('id'=>$_GET['oid']))->find();
	    	
	    	if(3 == $order['order_type'])
	    	{
	    		$order['distance'] = getDistance($this->gShop['latitude'], $this->gShop['longitude'], $order['latitude'], $order['longitude']);
	    		$order['ride_price'] = $this->get_ride_price($order['distance']);
	    	}
	    	else 
	    	{
	    		$order['ride_price'] = 0;
	    	}

			//用户优惠券
			$fans = get_obj_fans_openid($order['openid'],true);
			if($fans)
			{
				$coupons = M('FansCoupon')->where(array('uid'=>$fans['id'],'service_type'=>1,'status'=>1))->select();
				$this->assign('coupons',$coupons);
			}

	    	$this->meta_title = '维修方案与报价';
	    	$this->assign('order',$order);
	    	$this->display();
    	}
    }
    
    /**
    * 订单详情
    * @date: 2015-7-25
    * @author: BillyZ
    * @return:
    */
    public function details()
    {
    	$orderid = I('oid');
    	$mOrder = M('RepairOrder');
    	$order = $mOrder->where(array('id'=>$orderid))->find();
    	$this->assign('shop',$this->gShop);
    	$this->assign('order',$order);
    	$this->display();
    }

    public function toogleHide($id,$value = 1){
        session('ADMIN_MENU_LIST',null);
        $this->editRow('Menu', array('hide'=>$value), array('id'=>$id));
    }
    
    /**
    * 根据类型获取维修部件
    * @date: 2015-8-13
    * @author: BillyZ
    * @return:
    */
    public function get_parts_by_repair_type($type,$shopid=0)
    {
		$map['type']=$type;
    	$lstParts = M('RepairParts')->where($map)->select();
		if($shopid>0)
		{
			$mgc_repair_parts_city = M('RepairPartsCity');
			foreach($lstParts as $key=>$v)
			{
				$areaid = M('Shop')->where(array('id'=>$shopid))->getField('areaid');
				$cityprice = $mgc_repair_parts_city ->where(array('partid'=>$v['id'],'areaid'=>$areaid))->getField('price');
				if($cityprice>0)
				{
					$lstParts[$key]['price']=$cityprice;
				}
			}
		}
    	$this->ajaxReturn($lstParts);
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
    	
    		$where['_string'] = 'shopid = 0 or shopid = '.$this->gShopid;
    	}
    
    	$list = M('RepairOrder')->where($where)->order('id desc')->select();
    
    	$template = "<td width='150' align='center' height='30' style='border:1px solid'>{val}</td>";
    	 
    	header("Content-type:application/vnd.ms-excel");
    	header("Content-Disposition:attachment;filename=".date('Y-m-d',$serache_details['where'][0]['create_time'][1])."_".date('Y-m-d',$serache_details['where'][1]['create_time'][1])."_维修订单.xls");
    	header("Pragma: no-cache");
    	header("Expires: 0");
    	 
    	//输出内容如下：
    	echo "<table style='border:1em solid'>";
    	echo "<tr>";
    	echo str_replace('{val}', '订单号', $template);
    	echo str_replace('{val}', '订单类型', $template);
    	echo str_replace('{val}', '门店', $template);
    	echo str_replace('{val}', '费用', $template);
    	echo str_replace('{val}', '支付方式', $template);
    	echo str_replace('{val}', '会员', $template);
    	echo str_replace('{val}', '电话', $template);
    	echo str_replace('{val}', '问题', $template);
    	echo str_replace('{val}', '时间', $template);
    	echo str_replace('{val}', '状态', $template);
    	echo "</tr>";
    	 
    	$pay_type = '本人支付';
    	for($i=0;$i<count($list);$i++)
    	{
    		echo "<tr>";
    		echo str_replace('{val}', $list[$i]['transid'], $template);
    		echo str_replace('{val}', get_repair_type($list[$i]['order_type']), $template);
    		echo str_replace('{val}', get_shop_id($list[$i]['shopid']), $template);
    		echo str_replace('{val}', $list[$i]['price'], $template);
    		 
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
    		echo str_replace('{val}', get_fansname_openid($list[$i]['openid']), $template);
    		echo str_replace('{val}', $list[$i]['mobile'], $template);
    		echo str_replace('{val}', $list[$i]['problem'], $template);
    		echo str_replace('{val}', date('Y-m-d H:i',$list[$i]['create_time']), $template);
    		echo str_replace('{val}', get_repairorder_astatus($list[$i]['status']), $template);
    
    		echo "</tr>";
    	}
    	echo "</table>";
    }
    
    /**
    * 计算一键求助上门费
    * @date: 2015-8-9
    * @author: BillyZ
    * @return:
    */
    private function get_ride_price($distance)
    {
    	$distance = $distance * 2;//往返
    	if(10000 > $distance)
    	{
    		return 0;
    	}
    	else if(10000 <= $distance && 30000 > $distance)
    	{
    		return 20;
    	}
    	else
    	{
    		return round(($distance/1000),2);
    	}    	
    }
}