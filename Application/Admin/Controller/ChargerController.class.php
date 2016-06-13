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
 * 后台充电桩控制器
 * @author MC <zhouyibin@seersee.com>
 */
class ChargerController extends AdminController {

    /**
     * 后台充电桩首页
     * @return none
     */
    public function index(){
       
        $list       =   M("Charger")->field(true)->order('location desc')->select();
        int_to_string($list,array('hide'=>array(1=>'是',0=>'否'),'is_dev'=>array(1=>'是',0=>'否')));
        if($list) {
            foreach($list as &$key){
                if($key['pid']){
                    $key['up_title'] = $all_menu[$key['pid']];
                }
            }
			$list=get_provice_city_area($list);
            $this->assign('list',$list);
        }
        // 记录当前列表页的cookie
        Cookie('__forward__',$_SERVER['REQUEST_URI']);

        $this->meta_title = '菜单列表';
        $this->display();
    }


    /**
     * 批量生成二维码
     * @author: ZhouWC
     */
    public function batch() {
        $id = $_GET['chargerid'];
        $len = M("outlet")->where(array("chargerid"=>$id))->count();
        $numb = 10;
        for( $i = $len+1 ; $i <= $numb ; $i ++ ) {
            $data = array("chargerid"=>$id,"name"=>$i."号口",code=>$i);
            $outletid = M("outlet")->data($data)->add();
            $mCanal = M('Canal');
            $canal = $mCanal->where(array('code'=>'CD','pid'=>$outletid))->find();
            if($canal)
            {
                $canalid = $canal['id'];
            }
            else
            {
                $canal['name'] = '充电口';
                $canal['code'] = 'CD';
                $canal['pid'] = $outletid;
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
            //4.写入img到outlet表
            $outlet = M('Outlet')->where(array('id'=>$outletid))->find();
            $outlet['qimg'] = $qimg;
            M('Outlet')->save($outlet);
        }
        $this->success("成功","","",true);
    }

    /**
    * 充电桩添加
    * @date: 2015-7-1
    * @author: BillyZ
    * @return:
    */
    public function add(){
        if(IS_POST){
            $mCharger = M('Charger');
            $data = $mCharger->create();
            if($data){
                $id = $mCharger->add();
                if($id){
                    $this->success('新增成功', Cookie('__forward__'));
                } else {
                    $this->error('新增失败');
                }
            } else {
                $this->error($mCharger->getError());
            }
        } else {
            $this->assign('info',array('pid'=>I('pid')));
            $menus = M('Charger')->field(true)->select();
            $this->assign('Menus', $menus);
            $this->meta_title = '新增充电桩';
            $this->display('edit');
        }
    }

    /**
    * 充电桩编辑
    * @date: 2015-7-1
    * @author: BillyZ
    * @return:
    */
    public function edit($id = 0){
        if(IS_POST){
            $mCharger = M('Charger');
            $data = $mCharger->create();
            if($data){
                if($mCharger->save()!== false){
                    $this->success('更新成功', Cookie('__forward__'));
                } else {
                    $this->error('更新失败');
                }
            } else {
                $this->error($mCharger->getError());
            }
        } else {
            $info = array();
            /* 获取数据 */
            $info = M('Charger')->field(true)->find($id);
            $this->assign('info', $info);
            $this->meta_title = '编辑充电桩';
            $this->display();
        }
    }

    /**
     * 删除后台充电桩
     * @author MC <yangweijiester@gmail.com>
     */
    public function del(){
        $id = array_unique((array)I('id',0));

        if ( empty($id) ) {
            $this->error('请选择要操作的数据!');
        }

        $map = array('id' => array('in', $id) );
        if(M('Charger')->where($map)->delete()){
            
            $this->success('删除成功');
        } else {
            $this->error('删除失败！');
        }
    }
    
    /**
     * 充电口列表
     * @return none
     */
    public function outlet(){
    	$chargerid  = I('get.chargerid',0);
        $this->assign("chargerid",$chargerid);
    	if($chargerid){
    		$data = M('Charger')->where("id={$chargerid}")->field(true)->find();
    		$this->assign('data',$data);
    	}
    	$map['chargerid'] =   $chargerid;
    	
    	$list       =   M("Outlet")->where($map)->field(true)->order('id asc')->select();
    	if($list) {
    		$this->assign('list',$list);
    	}
    	// 记录当前列表页的cookie
    	Cookie('__forward__',$_SERVER['REQUEST_URI']);
    
    	$this->meta_title = '充电口列表';
    	$this->display();
    }
    
    /**
     * 充电口添加
     * @date: 2015-7-1
     * @author: BillyZ
     * @return:
     */
    public function outlet_add(){
    	if(IS_POST){
    		$mOutlet = M('Outlet');
    		$data = $mOutlet->create();
    		if($data){
    			$id = $mOutlet->add();
    			if($id){
    				$this->success('新增成功', Cookie('__forward__'));
    			} else {
    				$this->error('新增失败');
    			}
    		} else {
    			$this->error($mOutlet->getError());
    		}
    	} else {
    		$this->meta_title = '新增充电口';
    		$this->display('outlet_edit');
    	}
    }
    
    /**
    * 生成充电口二维码
    * @date: 2015-7-14
    * @author: BillyZ
    * @return:
    */
    public function generate_qrcode()
    {
    	//1.添加渠道
    	$outletid = $_GET['outletid'];
		
    	$mCanal = M('Canal');
    	$canal = $mCanal->where(array('code'=>'CD','pid'=>$outletid))->find();
    	if($canal)
    	{
    		$canalid = $canal['id'];
    	}
    	else
    	{
	    	$canal['name'] = '充电口';
	    	$canal['code'] = 'CD';
	    	$canal['pid'] = $outletid;
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
    	
    	//4.写入img到outlet表
    	$outlet = M('Outlet')->where(array('id'=>$outletid))->find();
    	$outlet['qimg'] = $qimg;
    	M('Outlet')->save($outlet);
    	
    	$this->success('二维码生成成功');
    }
    
    /**
     * 充电口编辑
     * @date: 2015-7-1
     * @author: BillyZ
     * @return:
     */
    public function outlet_edit($id = 0){
    	if(IS_POST){
    		$mOutlet = M('Outlet');
    		$data = $mOutlet->create();
    		if($data){
    			if($mOutlet->save()!== false){
    				$this->success('更新成功', Cookie('__forward__'));
    			} else {
    				$this->error('更新失败');
    			}
    		} else {
    			$this->error($mOutlet->getError());
    		}
    	} else {
    		$info = array();
    		/* 获取数据 */
    		$info = M('Outlet')->field(true)->find($id);
    		$this->assign('info', $info);
    		$this->meta_title = '编辑充电口';
    		$this->display();
    	}
    }
    
    /**
     * 删除充电口
     * @author MC <zhouyibin@seersee.com>
     */
    public function outlet_del(){
    	$id = array_unique((array)I('id',0));
    
    	if ( empty($id) ) {
    		$this->error('请选择要操作的数据!');
    	}
    
    	$map = array('id' => array('in', $id) );
    	if(M('Outlet')->where($map)->delete()){
    
    		$this->success('删除成功');
    	} else {
    		$this->error('删除失败！');
    	}
    }
    

    /**
     * 充电订单列表
     * @return none
     */
    public function orders(){
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
        $serache_details['where']=$where;
        

        $status=I('get.status');
    	$key = I('title');
        if($key)$where['transid'] =array('like','%'.$key.'%');
    	if(isset($status) && $status!=null)
    	{
    		if($status != -1)
    			$where['a.status'] = $status;
    	}
    	else{$status = -1;}
    	$list = $this->lists_join('ChargeOrder', $where, 'id desc',true,"left join gc_outlet b on a.outletid = b.id left join gc_fans c on a.openid=c.openid","b.name as outletname,c.id as uid");

        $charge_time=sec2time($list['end_time']-$list['create_time']);
        $this->assign('charge_time',$charge_time);
    	$this->assign('list',$list);
        $this->assign('status',$status);
        $this->assign('create_time_end',$request['create_time_end']);
        $this->assign('pay_time_end',$request['pay_time_end']);
        $this->assign('create_time_start',$request['create_time_start']);
        $this->assign('pay_time_start',$request['pay_time_start']);
        $this->assign('create_time_status',$create_time_status);
        $this->assign('pay_time_status',$pay_time_status);
    	// 记录当前列表页的cookie
    	Cookie('__forward__',$_SERVER['REQUEST_URI']);
        $this->assign('user_id', $this->gShopid);
    	$this->meta_title = '充电订单列表';
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
    	if($status)
    	{
    		if($status != -1)
    			$where['status'] = $status;
    	}
    	else
    		$status = -1;
    	
    
    	$list = M('ChargeOrder')->where($where)->order('id desc')->select();
    
    	$template = "<td width='150' align='center' height='30' style='border:1px solid'>{val}</td>";
    	 
    	header("Content-type:application/vnd.ms-excel");
    	header("Content-Disposition:attachment;filename=".date('Y-m-d',$serache_details['where'][0]['create_time'][1])."_".date('Y-m-d',$serache_details['where'][1]['create_time'][1])."充电订单.xls");
    	header("Pragma: no-cache");
    	header("Expires: 0");
    	 
    	//输出内容如下：
    	echo "<table style='border:1em solid'>";
    	echo "<tr>";
    	echo str_replace('{val}', '订单号', $template);
    	echo str_replace('{val}', '会员', $template);
    	echo str_replace('{val}', '充电桩', $template);
    	echo str_replace('{val}', '充电口', $template);
    	echo str_replace('{val}', '费用', $template);
    	echo str_replace('{val}', '支付方式', $template);
    	echo str_replace('{val}', '时间', $template);
    	echo str_replace('{val}', '状态', $template);
    	echo "</tr>";
    	 
    	$pay_type = '本人支付';
    	for($i=0;$i<count($list);$i++)
    	{
    		echo "<tr>";
    		echo str_replace('{val}', $list[$i]['transid'], $template);
    		echo str_replace('{val}', get_fansname_openid($list[$i]['openid']), $template);
    		echo str_replace('{val}', $list[$i]['charger'], $template);
    		echo str_replace('{val}', $list[$i]['outletid'], $template);
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
    		echo str_replace('{val}', get_chargeorder_status($list[$i]['status']), $template);
    
    		echo "</tr>";
    	}
    	echo "</table>";
    }
    
    /**
    * 订单详情
    * @date: 2015-8-24
    * @author: BillyZ
    * @return:
    */
    public function order_details()
    {
    	$oid = I('id');
    	$order = M('ChargeOrder')->where(array('id'=>$oid))->find();
    	$this->assign('order',$order);
    	$this->display();
    }
    
}