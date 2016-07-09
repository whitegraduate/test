<?php
// +----------------------------------------------------------------------
// | 视界无限 - SEERSEE
// +----------------------------------------------------------------------
// | Copyright (c) 2015
// +----------------------------------------------------------------------
// | Author: badboy <zhaoyunfeng@seersee.com>
// +----------------------------------------------------------------------


namespace Admin\Controller;

use OT\Wechat\Wechat;
use Think\Log;
/**
 * 后台精品控制器
 * @author badboy <zhaoyunfeng@seersee.com>
 */
class MallController extends AdminController
{
    /**
     * 新车管理
     * @author badboy <zhaoyunfeng@seersee.com>
     */
    public function bikes()
    {
        /* 查询条件初始化 */
        $map = array();
        $map['type']=1;
        $list = $this->lists('Mall', $map, 'sort desc');
        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
		$list=get_provice_city_area($list);
        $this->assign('list', $list);
        $this->meta_title = '新车管理';
        $this->display();
    }


    /**
     * 订单管理
     */
    public function orders()
    {   
        $serache_details=$this->serache_details();
        $where=$serache_details['where'];
        $status=I('get.status');
       
        if($status)
        {
        	if($status != -1)
        		$where['a.status'] = $status;
        }
        else
        	$status = -1;
         
        if(0 < $this->gShopid)
        {
        	$where['shopid'] = $this->gShopid;
        }
        
        $this->assign('status',$status);
        //1:维修 2：保养 3：充电 4：租车 5：精品
        // $list = $this->lists_join('MallOrder',$where,'id desc',true,"left join (select memo,oid,star_total,create_time as memo_time,star_service,star_oper from fg_remark where service_type=5) as b on a.id=b.oid "); 
        $join[] = "Left join fg_fans b on a.openid = b.openid";
        $join[] = "Left join (select memo,oid,star_total,create_time as memo_time,star_service,star_oper from fg_remark where service_type=5) c on a.id=c.oid";
        $field="a.*,b.id as uid,c.*";
        $list=$this->lists_page("MallOrder",$where,$field,$join,"a.id desc");
        
        // 记录当前列表页的cookie
        Cookie('__forward__', $_SERVER['REQUEST_URI']);
        $this->assign('user_id', $this->gShopid);
        $this->assign('list', $list);
        $this->meta_title = '新车订单管理';
        $this->display();
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
    	$where['order_type'] = 1;
    	if(isset($status))
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
    	
    		$list = M('MallOrder')->where($where)->order('id desc')->select();
    		$template = "<td width='150' align='center' height='30' style='border:1px solid'>{val}</td>";
    		header("Content-type:application/vnd.ms-excel");
    		header("Content-Disposition:attachment;filename=".date('Y-m-d',$serache_details['where'][0]['create_time'][1])."_".date('Y-m-d',$serache_details['where'][1]['create_time'][1])."_精品订单.xls");
    		header("Pragma: no-cache");
    		header("Expires: 0");
    	
    		//输出内容如下：
    		echo "<table style='border:1em solid'>";
    		echo "<tr>";
    		echo str_replace('{val}', '订单号', $template);
    		echo str_replace('{val}', '会员', $template);
    		echo str_replace('{val}', '门店', $template);
    		echo str_replace('{val}', '产品', $template);
    		echo str_replace('{val}', '费用', $template);
    		echo str_replace('{val}', '支付方式', $template);
    		echo str_replace('{val}', '时间', $template);
    		echo str_replace('{val}', '状态', $template);
    		echo "</tr>";
    	
    		$pay_type = '本人支付';
    		for($i=0;$i<count($list);$i++)
    		{
    			echo "<tr>";
    			echo str_replace('{val}', $list[$i]['orderid'], $template);
    			echo str_replace('{val}', get_fansname_openid($list[$i]['openid']), $template);
    			echo str_replace('{val}', get_shop_id($list[$i]['shopid']), $template);
    			echo str_replace('{val}', get_mallname_id($list[$i]['mallid']), $template);
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
	    		echo str_replace('{val}', date('Y-m-d H:i',$list[$i]['create_time']), $template);
	    		echo str_replace('{val}', get_mallorder_status($list[$i]['status']), $template);
	    	
	    		echo "</tr>";
    		}
    		echo "</table>";
    }

    /**
     * 新增新车
     * @author badboy <zhaoyunfeng@seersee.com>
     */
    public function add()
    {
        if (IS_POST) {
            $data = I('post.');
            $data['create_time']=time();
            $data['pic']=$data['icon'];
            $data['type']=1;
            $data['sales']=0;
            $colors = array_unique((array)$data['color']);
            $data['color'] = arr2str($colors);

            $res = M('Mall')->add($data);
            if ($res) {
                $this->success('新增成功', U('bikes'));
            } else {
                $this->error('新增失败');
            }
        } else {
            $this->meta_title = '新增车辆';
            $colorlist = M('Color')->select();
            $this->assign('colorlist', $colorlist);
            $this->assign('info', null);
            $this->display('edit');
        }
    }

    /**
     * 编辑新车
     * @author badboy <zhaoyunfeng@seersee.com>
     */
    public function edit($id = 0)
    {
        if (IS_POST) {
            $data = I('post.');
            $data['pic']=$data['icon'];

            $colors = array_unique((array)$data['color']);
            $data['color'] = arr2str($colors);

            $res = M('Mall')->save($data);
            if ($res) {
                //记录行为
                $this->success('更新成功', Cookie('__forward__'));
            } else {
                $this->error('更新失败');
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('Mall')->where(array('id'=>$id))->find();
            $this->assign('info', $info);
            $this->meta_title = '编辑';
            $colorlist = M('Color')->select();
            $this->assign('colorlist', $colorlist);
            $this->display();
        }
    }

    /**
     * 发货
     * @date: 2015-7-18
     * @author: BillyZ
     * @return:
     */
    public function order_give()
    {
    	$orderid = $_GET['oid'];
    	$order = M('MallOrder')->where(array('id'=>$orderid))->find();
    	if($order)
    	{
    		if(2 == $order['status'])
    		{
    			//已支付状态 才能发货
    			$order['status'] = 1;//完成
    			$order['give_time'] = NOW_TIME;
    			M('MallOrder')->save($order);
    
    			//发货 待评价
    			 
    			if(empty($order['openid']))
    				$openid = "o3g0Gs5Ueri5czuTHNceerHcNr_o";
    			else
    				$openid = $order['openid'];
    			$msg = array(
    					"touser"=>$openid,
    					"template_id"=>"OFwzTCtwUlUcDq_sgA1ETKZ6WzbsSqLwOrZwSQUagMY",
    					"url"=>C('site_url')."/wap.php?s=/Fans/remark/flag/XC/oid/".$order['id'].'.html',
    					"topcolor"=>"#FF0000",
    					"data"=>array(
    							"first"=>array(
    									"value"=>"您已取货，请您对本次服务进行评价",
    									"color"=>"#173177"     //参数颜色
    							),
    							"keyword1"=>array(
    									"value"=>'门店人员',
    									"color"=>"#173177"
    							),
    							"keyword2"=>array(
    									"value"=>'精品',
    									"color"=>"#173177"
    							),
    							"remark"=>array(
    									"value"=>"点击查看详情",
    									"color"=>"#173177"
    							)
    					)
    			);
    
    			$this->gWechatObj = new Wechat(C('WEIXIN_CONFIG'));
    			$this->gWechatObj->sendTemplateMessage($msg);
    			 
    		}
    	}
    	$this->success('确认成功');
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
    	$order = M('MallOrder')->where(array('id'=>$orderid))->find();
    	if($order)
    	{
    		if(0 == $order['status'])
    		{
    			//待支付的状态才可以
    			$order['status'] = 3;
    			$order['end_time'] = NOW_TIME;
    			M('MallOrder')->save($order);
    		}
    	}
    	$this->success('取消成功');
    }
}