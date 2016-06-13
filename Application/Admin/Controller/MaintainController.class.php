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
 * 后台保养控制器
 * @author yangweijie <yangweijiester@gmail.com>
 */
class MaintainController extends AdminController {

    /**
     * 后台保养首页
     * @return none
     */
    public function products(){
        $title=I('get.title');
        $pid  = I('get.pid',0);
        if($pid){
            $data = M('MaintainProduct')->where("id={$pid}")->field(true)->find();
            $this->assign('data',$data);
        }
        $title      =   trim(I('get.title'));
        if($title){
            $map['id'] = array('like',"%{$title}%");
            $map['name'] = array('like',"%{$title}%");
            $map['_logic']='or';
            $where['_complex']=$map;
            $this->lists_page('MaintainProduct',$where,'','id desc');
        }else{

        $list       =   M("MaintainProduct")->where($map)->field(true)->order('sort asc,id asc')->select();
		$list = get_provice_city_area($list);
        $this->assign('list',$list);
        }
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);

        $this->meta_title = '菜单列表';
        $this->display();
    }
 

    /**
    * 保养产品添加
    * @date: 2015-7-1
    * @author: BillyZ
    * @return:
    */
    public function product_add(){
        if(IS_POST){
            $mProduct = M('MaintainProduct');
            $data = $mProduct->create();
            $data->shopid = implode(',',array_unique(I('shopid')));
            if($data){
                $id = $mProduct->add();
                if($id){
                    $this->success('新增成功', Cookie('__forward__'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($mProduct->getError());
            }
        } else {
            $all_shop = M('shop')->select();
            $menus = M('MaintainProduct')->field(true)->select();
            $menus = array_merge(array(0=>array('id'=>0,'title_show'=>'顶级菜单')), $menus);
            $this->assign('Menus', $menus);
            $this->assign('all_shop', $all_shop);
            $this->meta_title = '新增产品';
            $this->display('product_edit');
        }
    }

    /**
    * 保养产品编辑
    * @date: 2015-7-1
    * @author: BillyZ
    * @return:
    */
    public function product_edit($id = 0){
        if(IS_POST){
            $mProduct = D('MaintainProduct');
            $arr = I('post.');
            $arr['shopid']=implode(',',array_unique($arr['shopid']));

            $data = $mProduct->create($arr);
            if($data){
                if($mProduct->save()!== false){
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($mProduct->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('MaintainProduct')->field(true)->find($id); 
            $all_shop = M('shop')->select();
            $shopid = explode(',',$info['shopid']);
            if(false === $info){
                $this->error('获取后台菜单信息错误');
            }
            $this->assign('info', $info);
            $this->assign('shopid', $shopid);
            $this->assign('all_shop', $all_shop);
            $this->meta_title = '编辑后台保养产品';
            $this->display();
        }
    }

    /**
     * 删除保养产品
     * @author
     */
    public function product_del(){
        $id = array_unique((array)I('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('MaintainProduct')->where($map)->delete()){
           
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }

    /**
     * 保养订单列表
     * @return none
     */
       
    public function orders(){
        $serache_details=$this->serache_details();
        $where=$serache_details['where'];
        $status=I('get.status');
        $where['order_type'] = 1;
        if($status){
            if($status != -1)
                $where['a.status'] = $status;
        }
        else{
            $status = -1;
        }
        if(0 < $this->gShopid)
        {
            $where['shopid'] = $this->gShopid;
        }
        // $list = $this->lists_join('MaintainOrder',$where,'id desc',true,"left join (select memo,oid,star_total,create_time as memo_time,star_service,star_oper from gc_remark where service_type=2) as b on a.id=b.oid left join (select gc_fans.id,gc_fans.openid as uid from gc_fans) as d on a.openid=d.openid "); 
        $join[] = "Left join fg_fans b on a.openid = b.openid";
        $join[] = "Left join (select memo,oid,star_total,create_time as memo_time,star_service,star_oper from fg_remark where service_type=2) c on a.id=c.oid";
        $field="a.*,b.id as uid,c.*";
        $list=$this->lists_page("MaintainOrder",$where,$field,$join,"a.id desc");
        $this->assign('status',$status);
        $this->assign('list',$list);
        Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->meta_title = '保养订单列表';
        $this->display();
    }


    /**
     * 保养提醒订单列表
     * @return none
     */
    public function orders_remind(){
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
    	$where['order_type'] = 2;
        if(0 < $this->gShopid){
    	   $where['shopid'] = $this->gShopid;
        }
    	$this->assign('status',$status);
        //1:维修 2：保养 3：充电 4：租车 5：精品
        $list = $this->lists_join('MaintainOrder',$where,'id desc',true,"left join (select memo,oid,star_total,create_time as memo_time,star_service,star_oper from gc_remark where service_type=2) as b on a.id=b.oid ");  
    	 //$list = $this->lists('MaintainOrder', $where, 'id desc');
    	$this->assign('list',$list);
    	// 记录当前列表页的cookie
    	Cookie('__forward__',$_SERVER['REQUEST_URI']);
    
    	$this->meta_title = '保养订单列表';
    	$this->display();
    }

    /**
     * 保养提醒配置
     * @return none
     */
    public function remind(){
    	$list       =   M("MaintainPartsRemind")->field(true)->order('id asc')->select();
    	if($list) {
    		foreach($list as $k => $v)
    		{
    			$list[$k]['statusname'] = get_maintainorder_status($v['status']);
    			$list[$k]['shop'] = get_shop_id($v['shopid']);
    			$list[$k]['product'] = get_maintain_product_id($v['pid']);
    			$list[$k]['fansname'] = get_fansname_openid($v['openid']);
    		}
    
    		$this->assign('list',$list);
    	}
    
    	// 记录当前列表页的cookie
    	Cookie('__forward__',$_SERVER['REQUEST_URI']);
    
    	$this->meta_title = '保养订单列表';
    	$this->display();
    }
    
    

    /**
     * 保养提醒配置增加
     * @date: 2015-7-1
     * @author: BillyZ
     * @return:
     */
    public function remind_add(){
    	if(IS_POST){
    		$mProduct = M('MaintainPartsRemind');
    		$partid = $_POST['partid'];
    		$brandid = $_POST['brandid'];
    		$remind = $mProduct->where(array('partid'=>$partid,'brandid'=>$brandid))->find();
    		if($remind)
    		{
    			$this->error('当前配置已存在');
    		}
    		
    		$data = $mProduct->create();
    		if($data){
    			$id = $mProduct->add();
    			if($id){
    				$this->success('新增成功', Cookie('__forward__'));
    			} else {
    				$this->error('新增失败');
    			}
    		} else {
    			$this->error($mProduct->getError());
    		}
    	} else {
    		
    		$lstBrand = M('Brand')->select();
    		$lstParts = M('MaintainParts')->select();

    		$this->assign('lstBrand',$lstBrand);
    		$this->assign('lstParts',$lstParts);
    		
    		$this->meta_title = '新增产品';
    		$this->display('remind_edit');
    	}
    }
    
    /**
     * 保养提醒配置编辑
     * @date: 2015-7-1
     * @author: BillyZ
     * @return:
     */
    public function remind_edit($id = 0){
    	if(IS_POST){
    		$mProduct = D('MaintainPartsRemind');
    		$data = $mProduct->create();
    		if($data){
    			if($mProduct->save()!== false){
    				$this->success('更新成功', Cookie('__forward__'));
    			} else {
    				$this->error('更新失败');
    			}
    		} else {
    			$this->error($mProduct->getError());
    		}
    	} else {
    		$info = array();
    		/* 获取数据 */
    		$info = M('MaintainPartsRemind')->field(true)->find($id);
    		
    		$this->assign('info', $info);
    		$this->meta_title = '编辑保养提醒配置';
    		$this->display();
    	}
    }
    
    /**
     * 删除保养提醒配置
     * @author
     */
    public function remind_del(){
    	$id = array_unique((array)I('id',0));
    
    	if ( empty($id) ) {
    		$this->error('请选择要操作的数据!');
    	}
    
    	$map = array('id' => array('in', $id) );
    	if(M('MaintainPartsRemind')->where($map)->delete()){
    		 
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败！');
    	}
    }
     
    
    private function get_maintain_product_id($productid)
    {
    	$mProduct = M('MaintainProduct');
    	$product = $mProduct->where(array('id'=>$productid))->find();
    	return $product['name'];
    }

    /**
     * 完成服务，等待买家支付
     * @date: 2015-7-18
     * @author: BillyZ
     * @return:
     */
    public function service_finish()
    {
    	$orderid = $_GET['oid'];
    	$order = M('MaintainOrder')->where(array('id'=>$orderid))->find();
    	if($order)
    	{
    		if(2 == $order['status'])
    		{
    			//已支付状态 才能结束
    			$order['status'] = 4;//完成
    			$order['end_time'] = NOW_TIME;
    			M('MaintainOrder')->save($order);
    
    			//发送模板消息给用户 TODO
    			if(empty($order['openid']))
    				$openid = "o3g0Gs5Ueri5czuTHNceerHcNr_o";
    			else
    				$openid = $order['openid'];
    			$msg = array(
    					"touser"=>$openid,
    					"template_id"=>"OFwzTCtwUlUcDq_sgA1ETKZ6WzbsSqLwOrZwSQUagMY",
    					"url"=>C('site_url')."/wap.php?s=/Fans/remark/flag/BY/oid/".$order['id'].'.html',
    					"topcolor"=>"#FF0000",
    					"data"=>array(
    							"first"=>array(
    									"value"=>"保养服务已完成，请您对本次服务进行评价",
    									"color"=>"#173177"     //参数颜色
    							),
    							"keyword1"=>array(
    									"value"=>'门店人员',
    									"color"=>"#173177"
    							),
    							"keyword2"=>array(
    									"value"=>'保养',
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
     * 保养提醒
     * @date: 2015-7-18
     * @author: BillyZ
     * @return:
     */
    public function order_remind()
    {
    	$orderid = $_GET['oid'];
    	$order = M('MaintainOrder')->where(array('id'=>$orderid))->find();
    	if($order)
    	{
    		if(2 == $order['status'])
    		{
    			//已支付状态才能发送
    			//TODO 增加已发送标记 需要区分部件
    			//$order['status'] = 1;//服务已完成，等待支付
    			//$order['end_time'] = NOW_TIME;
    			//M('MaintainOrder')->save($order);
    
    			//发送模板消息给用户 TODO
    			//通电
    			if(empty($order['openid']))
    				$openid = "o3g0Gs5Ueri5czuTHNceerHcNr_o";
    			else
    				$openid = $order['openid'];
    			
    			//会员姓名
				$fans = get_obj_fans_openid($openid);
    			$fansname = $fans['nickname'];
    			//上次保养时间
    			$end_time = M('MaintainOrder')->field('end_time')->where(array('openid'=>$openid,'status'=>2, 'order_type'=>1))->order('end_time desc')->find();
    			//到期时间 TODO
    			$expired = NOW_TIME + (3600 * 24 * 30 * 6);
    			
    			
    			$msg = array(
    					"touser"=>$openid,
    					"template_id"=>"yBBT8Fp8zVf8sE9U5kByYm8EUsUO0nf931PxVgsTJBU",
    					"url"=>C('site_url')."/wap.php?s=/Maintain/remind/oid/".$order['id'].'.html',
    					"topcolor"=>"#FF0000",
    					"data"=>array(
    							"first"=>array(
    									"value"=>"您好，您的电动车部分部件即将过保",
    									"color"=>"#173177"     //参数颜色
    							),
    							"keyword1"=>array(
    									"value"=>$fansname,
    									"color"=>"#173177"
    							),
    							"keyword2"=>array(
    									"value"=>$end_time,
    									"color"=>"#173177"
    							),
    							"keyword3"=>array(
    									"value"=> date('Y-m-d',$expired),
    									"color"=>"#173177"
    							),
    							"remark"=>array(
    									"value"=>"建议您在近期到滴啊滴租车做免费检测！以便在保修范围享受保修实惠！",
    									"color"=>"#173177"
    							)
    					)
    			);
    
    			$this->gWechatObj = new Wechat(C('WEIXIN_CONFIG'));
    			$this->gWechatObj->sendTemplateMessage($msg);
    		}
    	}
    	$this->success('提醒成功');
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
    	$order = M('MaintainOrder')->where(array('id'=>$orderid))->find();
    	if($order)
    	{
    		if(0 == $order['status'])
    		{
    			//等待开始的状态才可以
    			$order['status'] = 3;
    			$order['end_time'] = NOW_TIME;
    			M('MaintainOrder')->save($order);
    		}
    	}
    	$this->success('取消成功');
    }
    
    
    public function toogleHide($id,$value = 1){
        $this->editRow('Menu', array('hide'=>$value), array('id'=>$id));
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
    	 
    	$list = M('MaintainOrder')->where($where)->order('id desc')->select();
    	 
    	$template = "<td width='150' align='center' height='30' style='border:1px solid'>{val}</td>";
    	
    	header("Content-type:application/vnd.ms-excel");
    	header("Content-Disposition:attachment;filename=".date('Y-m-d',$serache_details['where'][0]['create_time'][1])."_".date('Y-m-d',$serache_details['where'][1]['create_time'][1])."_保养订单.xls");
    	header("Pragma: no-cache");
    	header("Expires: 0");
    	
    	//输出内容如下：
    	echo "<table style='border:1em solid'>";
    	echo "<tr>";
    	echo str_replace('{val}', '订单号', $template);
    	echo str_replace('{val}', '保养产品', $template);
    	echo str_replace('{val}', '用户', $template);
    	echo str_replace('{val}', '所在门店', $template);
    	echo str_replace('{val}', '费用', $template);
    	echo str_replace('{val}', '支付方式', $template);
    	echo str_replace('{val}', '状态', $template);
    	echo str_replace('{val}', '下单时间', $template);
    	echo "</tr>";
    	
    	$pay_type = '本人支付';
    	for($i=0;$i<count($list);$i++)
    	{  
    		echo "<tr>";

    		echo str_replace('{val}', $list[$i]['transid'], $template);
    		echo str_replace('{val}', get_maintain_product_id($list[$i]['pid']), $template); 
    		echo str_replace('{val}', get_fansname_openid($list[$i]['openid']), $template); 
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
    		echo str_replace('{val}', get_maintainorder_status($list[$i]['status']), $template);
    		echo str_replace('{val}', date('Y-m-d H:i',$list[$i]['create_time']), $template);
    		
    		echo "</tr>";
    	}
    	echo "</table>";
    }
}