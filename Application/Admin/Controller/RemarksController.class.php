<?php
namespace Admin\Controller;
use OT\Wechat\Wechat;
use Think\Log;

/**
 * 后台备注控制器
 * @author Ketory <chenzhuozhou@seersee.com>
 */
class RemarksController extends AdminController {
    public function index(){
        $type = $_GET['type'];
        $uid = $_GET['uid'];
        $oid = $_GET['oid'];
        $list = M("OrderLog")->field("a.*,b.nickname")->alias("a")
            ->join("LEFT JOIN gc_member b on b.uid = a.uid")
            ->where(array("order_type"=>$type,"orderid"=>$oid))->order("create_time asc")->select();
        $this->assign('type', $type);
        $this->assign('uid', $uid);
        $this->assign('oid', $oid);
        $this->assign('list', $list);
        $this->display();
    }
    public function repost(){
        $data['order_type'] = $_GET['type'];
        $data['uid']  = $_GET['uid'];
        $data['orderid']  = $_GET['oid'];
        $data['memo']  = $_POST['cont'];
        $data['create_time']=time();
        $sql = M("OrderLog")->add($data);
        if($sql){
            $this->success("1");
        }
        else{
            $this->error("0");
        }
    }
    
    /**
    * 退款备注
    * @date: 2015-10-15
    * @author: BillyZ
    * @return:
    */
    public function refund(){
    	$transid = $_GET['transid'];
    	
    	$this->assign('transid', $transid);
    	$this->display();
    }
    
    /**
    * 投诉处理
    * @date: 2015-10-16
    * @author: BillyZ
    * @return:
    */
    public function complain(){
    	$complainid = $_GET['cid'];
    	
    	$complain = M('OrderComplain')->where(array('id'=>$complainid))->find();
    	
    	$this->assign('complain', $complain);
    	$this->display();
    }
}