<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie <yangweijiester@gmail.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Admin\Controller;

/**
 * 后台报表控制器
 * @author yangweijie <yangweijiester@gmail.com>
 */
class StatsController extends AdminController {

    /***
     * 服务报表
     * @author: Nice <hupeipei@seersee.com>
     */
    public function index(){
        $type = I('type', 1);
        $startDate = I('start_date', date('Y-m-d', strtotime('-10 day')));
        $endDate = I('end_date', date('Y-m-d', time()));
        $map['create_time'] = array(array('egt', strtotime($startDate)), array('elt', strtotime($endDate) + 3600 * 24));
        
        if(0 < $this->gShopid)
        {
        	$map['shopid'] = $this->gShopid;
        }
        
        if ($type == 1) {
            //统计服务单量
            $maintainCount = M('MaintainOrder')->where($map)->count();
            $mallCount = M('MallOrder')->where($map)->count();
            $rentbikeCount = M('RentbikeOrder')->where($map)->count();
            $repairCount = M('RepairOrder')->where($map)->count();
            $chargeCount = M('ChargeOrder')->where($map)->count();
            $data = "['保养',   $maintainCount],
                   ['商城',   $mallCount],
                   ['租车',   $rentbikeCount],
                   ['维修',   $repairCount],
                   ['充电',   $chargeCount]";
            $dataTitle = "服务单量统计（{$startDate} 至 {$endDate}）";
        } else if ($type == 2) {
            //统计服务金额
            $maintainTotal = M('MaintainOrder')->where($map)->sum('pay_price');
            $mallTotal = M('MallOrder')->where($map)->sum('price');
            $rentbikeTotal = M('RentbikeOrder')->where($map)->sum('pay_price');
            $repairTotal = M('RepairOrder')->where($map)->sum('pay_price');
            $chargeTotal = M('ChargeOrder')->where($map)->count() * 1;
            $data = "['保养',   $maintainTotal],
                   ['商城',   $mallTotal],
                   ['租车',   $rentbikeTotal],
                   ['维修',   $repairTotal],
                   ['充电',   $chargeTotal]";
            $dataTitle = "服务金额统计（{$startDate} 至 {$endDate}）";
        } else {
            $data = '';
            $dataTitle = '';
        }
        $this->assign('data',$data);
        $this->assign('dataTitle',$dataTitle);
        $this->assign('start_date', $startDate);
        $this->assign('end_date', $endDate);
        $this->meta_title = '服务报表';
        $this->display();
    } 
    
    /***
     * 优惠券报表
    * @author: MC <zhouyibin@seersee.com>
    */
    public function coupon(){
    	//$type = I('type', 1);
    	//$startDate = I('start_date', date('Y-m-d', strtotime('-10 day')));
    	//$endDate = I('end_date', date('Y-m-d', time()));
    	//$map['create_time'] = array(array('egt', strtotime($startDate)), array('elt', strtotime($endDate) + 3600 * 24));
     
    	$mCoupon = M('FansCoupon');
    	//今天0点时间戳
    	$today = strtotime(date("Y-m-d"));
    	$oneday = 3600 * 24;
    	
    	//7天前
    	$where7['create_time'] = array('between',($today - $oneday * 7).','.($today - $oneday * 6));
    	$count7 = $mCoupon->where($where7)->count();
    	
    	$where_use7['used_time'] = array('between',($today - $oneday * 7).','.($today - $oneday * 6));
    	$count_use7 = $mCoupon->where($where_use7)->count();
    	
    	//6天前
    	$where6['create_time'] = array('between',($today - $oneday * 6).','.($today - $oneday * 5));
    	$count6 = $mCoupon->where($where6)->count();
    	
    	$where_use6['used_time'] = array('between',($today - $oneday * 6).','.($today - $oneday * 5));
    	$count_use6 = $mCoupon->where($where_use6)->count();
    	//5天前
    	$where5['create_time'] = array('between',($today - $oneday * 5).','.($today - $oneday * 4));
    	$count5 = $mCoupon->where($where5)->count();

    	$where_use5['used_time'] = array('between',($today - $oneday * 5).','.($today - $oneday * 4));
    	$count_use5 = $mCoupon->where($where_use5)->count();
    	//4天前
    	$where4['create_time'] = array('between',($today - $oneday * 4).','.($today - $oneday * 3));
    	$count4 = $mCoupon->where($where4)->count();

    	$where_use4['used_time'] = array('between',($today - $oneday * 4).','.($today - $oneday * 3));
    	$count_use4 = $mCoupon->where($where_use4)->count();
    	//3天前
    	$where3['create_time'] = array('between',($today - $oneday * 3).','.($today - $oneday * 2));
    	$count3 = $mCoupon->where($where3)->count();
    	
    	$where_use3['used_time'] = array('between',($today - $oneday * 3).','.($today - $oneday * 2));
    	$count_use3 = $mCoupon->where($where_use3)->count();
    	//2天前
    	
    	$where2['create_time'] = array('between',($today - $oneday * 2).','.($today - $oneday));
    	$count2 = $mCoupon->where($where2)->count();
    	
    	$where_use2['used_time'] = array('between',($today - $oneday * 2).','.($today - $oneday));
    	$count_use2 = $mCoupon->where($where_use2)->count();
    	//1天前
    	
    	$where1['create_time'] = array('between',($today - $oneday).','.$today);
    	$count1 = $mCoupon->where($where1)->count();
    	
    	$where_use1['used_time'] = array('between',($today - $oneday).','.$today);
    	$count_use1 = $mCoupon->where($where_use1)->count();
    	//今天
    	$where0['create_time'] = array('egt',$today);
    	$count0 = $mCoupon->where($where0)->count();
    	
    	$where_use0['used_time'] = array('egt',$today);
    	$count_use0 = $mCoupon->where($where_use0)->count();
    	
    	
    	
    	
    	//M('FansCoupon')
    	$data = "[{
    		name: '发放数',
    		data: [ $count7, $count6, $count5, $count4, $count3, $count2, $count1, $count0]
    	}, {
    		name: '使用数',
    		data: [ $count_use7, $count_use6, $count_use5, $count_use4, $count_use3, $count_use2, $count_use1, $count_use0]
    	}]";
    		 
    	 
    	$this->assign('data',$data);
    	$this->assign('dataTitle',$dataTitle);
    	$this->assign('start_date', $startDate);
    	$this->assign('end_date', $endDate);
    	$this->meta_title = '服务报表';
    	$this->display();
    }

    /***
     * 订单报表
     * @author: Nice <hupeipei@seersee.com>
     */
    public function order(){
        $type = I('type', '2');
        $startDate = I('start_date', date('Y-m-d', strtotime('-10 day')));
        $endDate = I('end_date', date('Y-m-d', time()));
        $map['create_time'] = array(array('egt', strtotime($startDate)), array('elt', strtotime($endDate) + 3600 * 24));
        
        switch($type)
        {
        	case "1":
        		$table = 'RepairOrder';
        		$where_p['pay_time'] = array('gt',0);
        		$where_p['create_time'] = $map['create_time'];
        		$where_up['pay_time'] = 0;
        		$where_up['create_time'] = $map['create_time'];
        		break;
        	case "2":
        		$table = 'MaintainOrder';
        		$where_p['pay_time'] = array('gt',0);
        		$where_p['create_time'] = $map['create_time'];
        		$where_up['pay_time'] = 0;
        		$where_up['create_time'] = $map['create_time'];
        		break;
        	case "3":
        		$table = 'ChargeOrder';
        		$where_p['pay_time'] = array('gt',0);
        		$where_p['create_time'] = $map['create_time'];
        		$where_up['pay_time'] = 0;
        		$where_up['create_time'] = $map['create_time'];
        		break;
        	case "4":
        		$table = 'RentbikeOrder';
        		$where_p['pay_time'] = array('gt',0);
    			$where_p['status'] = 4;
        		$where_p['create_time'] = $map['create_time'];
        		$where_up['pay_time'] = 0;
        		$where_up['create_time'] = $map['create_time'];
        		break;
        	case "5":
        		$table = 'MallOrder';
        		$where_p['pay_time'] = array('gt',0);
        		$where_up['pay_time'] = array('exp','is null');

        		$where_p['create_time'] = $map['create_time'];
        		$where_up['create_time'] = $map['create_time'];
        		break;
        	default:
        		$table = 'MaintainOrder';
        		$where_p['pay_time'] = array('gt',0);
        		$where_up['pay_time'] = 0;
        		$where_p['create_time'] = $map['create_time'];
        		$where_up['create_time'] = $map['create_time'];
        		break;
        }
        
        
        $paid = M($table)->where($where_p)->count();
        $unpaid = M($table)->where($where_up)->count();
        $data = "['已支付',   $paid],
                   ['未支付',   $unpaid]";
        $dataTitle = get_order_type($type) . "订单统计（{$startDate} 至 {$endDate}）";
        $this->assign('data',$data);
        $this->assign('dataTitle',$dataTitle);
        $this->assign('start_date', $startDate);
        $this->assign('end_date', $endDate);
        $this->meta_title = '订单报表';
        $this->display();
    }
    

    /***
     * 门店报表
    * @author: Nice <hupeipei@seersee.com>
    */
    public function shop(){
    	
    	$list = M('Shop')->select();
    	$this->assign('list',$list);
    	
    	$type = I('type', '2');
    	$startDate = I('start_date', date('Y-m-d', strtotime('-10 day')));
    	$endDate = I('end_date', date('Y-m-d', time()));
    	$map['create_time'] = array(array('egt', strtotime($startDate)), array('elt', strtotime($endDate) + 3600 * 24));
    
    	switch($type)
    	{
    		case "1":
    			$table = 'RepairOrder';
    			$where_p['pay_time'] = array('gt',0);
    			$where_p['create_time'] = $map['create_time'];
    			break;
    		case "2":
    			$table = 'MaintainOrder';
    			$where_p['pay_time'] = array('gt',0);
    			$where_p['create_time'] = $map['create_time'];
    			break;
    		case "3":
    			$table = 'ChargeOrder';
    			$where_p['pay_time'] = array('gt',0);
    			$where_p['create_time'] = $map['create_time'];
    			break;
    		case "4":
    			$table = 'RentbikeOrder';
    			$where_p['pay_time'] = array('gt',0);
    			$where_p['status'] = 4;
    			$where_p['create_time'] = $map['create_time'];
    			break;
    		case "5":
    			$table = 'MallOrder';
    			$where_p['pay_time'] = array('gt',0);
    			$where_p['create_time'] = $map['create_time'];
    			break;
    		default:
    			$table = 'MaintainOrder';
    			$where_p['pay_time'] = array('gt',0);
    			$where_p['create_time'] = $map['create_time'];
    			break;
    	}
    
    
    	$paid = M($table)->where($where_p)->count();
    	
    	$data = '';
    	for($i=0;$i<count($list);$i++)
    	{
    		$where_p['shopid'] = $list[$i]['id'];
    		$count = M($table)->where($where_p)->count();
    		$data .= "['".$list[$i]['name']."',".$count."],"; 
    	}
    	
    	$data = substr($data, 0, strlen($data) - 1);
    	//$data = "['门店1',   $paid],['门店2',   $unpaid]";
    	$dataTitle = get_order_type($type) . "订单统计（{$startDate} 至 {$endDate}）";
    	$this->assign('data',$data);
    	$this->assign('dataTitle',$dataTitle);
    	$this->assign('start_date', $startDate);
    	$this->assign('end_date', $endDate);
    	$this->meta_title = '订单报表';
    	$this->display();
    }

    /***
     * 用户报表
     * @author: Nice <hupeipei@seersee.com>
     */
    public function user(){
        $list = M('Member')->where(array('status' => 1))->field('group_id,count(1) as group_count')->group('group_id')->select();
        $data = "";
        foreach ($list as $group) {
            $groupName = get_user_group_name($group['group_id']);
            $groupCount = $group['group_count'];
            if (empty($data)) {
                $data = "['{$groupName}',   {$groupCount}]";
            } else {
                $data .= ",['{$groupName}',   {$groupCount}]";
            }
        }
        $dataTitle = "用户统计";
        $this->assign('data',$data);
        $this->assign('dataTitle',$dataTitle);
        $this->meta_title = '用户报表';
        $this->display();
    }

    /***
     * 会员报表
     * @author: Nice <hupeipei@seersee.com>
     */
    public function fans(){
        $today = strtotime(date('Y-m-d', NOW_TIME));
        $mFans = M('Fans');

        $total = $mFans->count();
        $info['total'] = $total;

        $map = array();
        $map['create_time'] = array('egt', $today);
        $todayCount = $mFans->where($map)->count();
        $info['today'] = $todayCount;

        $map = array();
        $map['create_time'] = array(array('egt', $today - 3600 * 24), array('lt', $today));
        $yesterday = $mFans->where($map)->count();
        $info['yesterday'] = $yesterday;

        $map = array();
        $map['create_time'] = array(array('egt', $today - 3600 * 24 * 7), array('lt', $today));
        $lastweek = $mFans->where($map)->count();
        $info['lastweek'] = $lastweek;

        $map = array();
        $map['create_time'] = array(array('egt', $today - 3600 * 24 * 30), array('lt', $today));
        $lastmonth = $mFans->where($map)->count();
        $info['lastmonth'] = $lastmonth;

        $this->assign('info', $info);

        $map = array();
        $map['create_time'] = array(array('egt', $today - 3600 * 24 * 10), array('lt', $today));
        $list = $mFans->where($map)->field("FROM_UNIXTIME(create_time, '%Y-%m-%d') as stat_date,count(1) as fans_count")->group("FROM_UNIXTIME(create_time, '%Y-%m-%d')")->select();
        $this->assign('list', $list);
        $this->meta_title = '会员报表';
        $this->display();
    }


    /**
     * 导出资金报表
     * @return: none
     */
    public function export_orders()
    {
        $status = $_GET['s'];//1:维修 2：保养 3:充电 4:租车 5:精品

        $shop = I("get.shop");
        if(0 < $this->gShopid)
        {
            $where['shopid'] = $this->gShopid;
        }
        $startDate = I('start_date', date('Y-m-d', strtotime('-30 day')));
        $endDate = I('end_date', date('Y-m-d', time()));
        $where['pay_time'] = array(array('egt', strtotime($startDate)), array('elt', strtotime($endDate) + 3600 * 24));

        $shop_name = "全部门店";
        if(!empty($shop)) {
            $where['shopid'] = $shop;
            $shop_name = get_shop_id($shop);
        }
        $sum = 0;
        switch($status)
        {
            case '1':
                $mOrder = M('RepairOrder');
                $where['pay_price'] = array('gt',0);
                $list = $mOrder->where($where)->order('id desc')->select();
                $sum = $mOrder->where($where)->sum('money_shop');
                break;
            case '2':
                $mOrder = M('MaintainOrder');
                $where['order_type'] = 1;
                $where['pay_price'] = array('gt',0);
                $list = $mOrder->where($where)->order('id desc')->select();
                $sum = $mOrder->where($where)->sum('money_shop');
                break;
            case '3':
                $mOrder = M('ChargeOrder');
                break;
            case '4':
                $mOrder = M('RentbikeOrder');
                $where['pay_price'] = array('gt',0);
                $where['status'] = 4;
                $list = $mOrder->where($where)->order('id desc')->select();
                $sum = $mOrder->where($where)->sum('money_shop');
                break;
            case '5':
                $mOrder = M('MallOrder');
                $where['price'] = array('gt',0);
                $where['status'] = 2;
                $list = $mOrder->where($where)->order('id desc')->select();
                $sum = $mOrder->where($where)->sum('money_shop');
                foreach($list as $k=>$v)
                {
                    $list[$k]['transid'] = $v['orderid'];
                }
                break;
            default:
                break;
        }

        $order_name = array('1'=>'维修','2'=>'保养','4'=>'租车','5'=>'精品');
        $template = "<td width='150' align='center' height='30' style='border:1px solid'>{val}</td>";

        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=".$startDate."_".$endDate."_".$shop_name."_".$order_name[$status]."订单.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        //输出内容如下：
        echo "<table style='border:1em solid'>";
        echo "<tr>";
        echo str_replace('{val}', '订单号', $template);
        echo str_replace('{val}', '门店', $template);
        echo str_replace('{val}', '用户', $template);
        echo str_replace('{val}', '下单时间', $template);
        echo str_replace('{val}', '支付时间', $template);
        echo str_replace('{val}','结算金额',$template);
        echo "</tr>";

        for($i=0;$i<count($list);$i++)
        {
            echo "<tr>";

            echo str_replace('{val}', $list[$i]['transid'], $template);
            echo str_replace('{val}', get_shop_id($list[$i]['shopid']), $template);
            echo str_replace('{val}', get_fansname_openid($list[$i]['openid']), $template);
            echo str_replace('{val}', date('Y-m-d H:i',$list[$i]['create_time']), $template);
            echo str_replace('{val}', date('Y-m-d H:i',$list[$i]['pay_time']), $template);
            echo str_replace('{val}',$list[$i]['money_shop'],$template);
            echo "</tr>";
        }
        echo str_replace('{val}',  "订单数", $template);
        echo str_replace('{val}', count($list), $template);
        echo str_replace('{val}', "总金额", $template);
        echo str_replace('{val}',$sum, $template);
        echo "</table>";
    }

    /**
     * 资金结算
     * @return none
     */
    public function money(){
    	 
    	$status = $_GET['s'];//1:维修 2：保养

        $shop = I("get.shop");
    	if(0 < $this->gShopid)
    	{
    		$where['shopid'] = $this->gShopid;
    	}
        $startDate = I('start_date', date('Y-m-d', strtotime('-30 day')));
        $endDate = I('end_date', date('Y-m-d', time()));
        $where['pay_time'] = array(array('egt', strtotime($startDate)), array('elt', strtotime($endDate) + 3600 * 24));

        if(!empty($shop)) {
            $where['shopid'] = $shop;
        }
    	$sum = 0;
    	switch($status)
    	{
    		case '1':
    			$mOrder = M('RepairOrder');
    			$where['pay_price'] = array('gt',0);
    			$list = $mOrder->where($where)->order('id desc')->select();
    			$sum = $mOrder->where($where)->sum('money_shop');
    			break;
    		case '2':
    			$mOrder = M('MaintainOrder');
    			//1.order_type = 1 即非保养提醒订单
    			$where['order_type'] = 1;
    			$where['pay_price'] = array('gt',0);
    			$list = $mOrder->where($where)->order('id desc')->select();
    			$sum = $mOrder->where($where)->sum('money_shop');
    			break;
    		case '3':
    			$mOrder = M('ChargeOrder');
    			break;
    		case '4':
    			$mOrder = M('RentbikeOrder');
    			$where['pay_price'] = array('gt',0);
    			$where['status'] = 4;
    			$list = $mOrder->where($where)->order('id desc')->select();
    			$sum = $mOrder->where($where)->sum('money_shop');
    			break;
    		case '5':
    			$mOrder = M('MallOrder');
    			$where['price'] = array('gt',0);
    			$where['status'] = 2;
    			$list = $mOrder->where($where)->order('id desc')->select();
    			$sum = $mOrder->where($where)->sum('money_shop');
    			foreach($list as $k=>$v)
    			{
    				$list[$k]['transid'] = $v['orderid'];
    			}
    			break;
    		default:
    			break;
    	}
    
        $shop = M("shop")->select();
        $this->assign("shops",$shop);
        $this->assign('start_date', $startDate);
        $this->assign('end_date', $endDate);
    	$this->assign('sum',$sum);
    	$this->assign('list',$list);    
        $this->assign('status',$status);
    	// 记录当前列表页的cookie
    	Cookie('__forward__',$_SERVER['REQUEST_URI']);
    
    	$this->meta_title = '资金结算';
    	$this->display();
    }
}