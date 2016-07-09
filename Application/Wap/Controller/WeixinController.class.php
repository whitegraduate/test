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
use OT\Wechat\Wechat;
use Think\Log;
use User\Api\UserApi;


/**
 * 描述：微信接口控制器
 * Class WeixinController
 * @package Home\Controller
 */
class WeixinController extends CommonController {
    //private $weObj;

    public function __construct() {
        parent::__construct();
        //$weixinConfig = C('WEIXIN_CONFIG');
        //$this->weObj = new Wechat($weixinConfig);
    }

    /**
     * 接口接入点
     * @author:
     */
    public function index(){
        $this->weObj->valid();
        $type = $this->weObj->getRev()->getRevType();
        switch($type) {
            case Wechat::MSGTYPE_TEXT:
                $keyword = $this->weObj->getRevContent();
            	$this->doReply($keyword);
                break;
            case Wechat::MSGTYPE_EVENT:
                $evenArray = $this->weObj->getRevEvent();
                $openid = $this->weObj->getRevFrom();
                switch($evenArray['event']){
                   case 'subscribe'://关注
                       $this->doSubscribe($openid, $evenArray['key']);
                       break;
                   case 'unsubscribe': //反查粉丝，将其标记为未关注，且记录取消时间
                       $this->doUnSubscribe($openid);
                       break;
                   case 'SCAN': 
                       $this->doScan($evenArray['key'], $openid);
                       break;
                   case 'LOCATION':
                        $this->doLocation($this->weObj->getRevData());
                       break;
                   case 'CLICK':
                       break;
                   case 'VIEW':
                       break;
               }
                break;
            case Wechat::MSGTYPE_IMAGE:
                break;
            default:
                break;
        }
    }

    /**
    * 请求回调
    * @date: 2015-7-15
    * @author: BillyZ
    * @return:
    */
    public function notify()
    {
    	log::weixin_info('支付回调请求');
    	
    	$postStr = file_get_contents("php://input");
       
//    	$this->log($postStr);
    	if (!empty($postStr)) {
    		$return = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
    		
    		if('SUCCESS' == $return['return_code'])
    		{
    			$requestid = $return['out_trade_no'];
    			log::weixin_info('通信链接成功，支付请求单号为：'.$requestid);

    			//增加请求单机制，反查订单
    			$transid = get_transid_by_requestid($requestid);
    			M('PayRequest')->where(array('requestid'=>$requestid))->setField('status',2);
    			
    			log::weixin_info('订单号为：'.$transid.'支付成功');
    			
    			$orderType = substr($transid, 0, 2);
                parent::afterpay_maintain($transid);
    		}
    		else
    		{
    			log::weixin_info('通信失败'.$return['return_msg']);
    		}
    	}
    	else 
    	{
    		log::weixin_info('获取信息为空');
    	}
    	
    	exit('<xml>
		   <return_code><![CDATA[SUCCESS]]></return_code>
		   <return_msg><![CDATA[OK]]></return_msg>
		</xml>');
    	
    }

    public function getfans(){
        $the_openid = M("fans")->where(" subscribe_time is null")->getField("openid");
        $fans_info = M("fans")->where(array('openid' => $the_openid ))->find();
        $userinfo = $this->weObj->getUserInfo($the_openid);
        dump($userinfo);
        if(!$userinfo){
        	$fansset['openid'] = $the_openid;
        	$fansset['subscribe'] = 0;
        	$fansset['subscribe_time'] = NOW_TIME;
        	if(M('Fans')->where(array('openid' => $the_openid))->save($fansset)){
	            echo "Save Fans success 失败!".$fansset['nickname']."--".$the_openid ;
	            Log::getWeixinInfo("用户信息补充失败！状态更新成功".$fansset['nickname']."--".$the_openid."   TIME:".date('Y-m-d', NOW_TIME),"","",C('LOG_PATH')."Fans".date('y_m_d').".log");
	            echo "<script>location.reload()</script>";
	        }
	        else{
	            echo "Save Fans fail! 失败".$fansset['nickname']."--".$the_openid;
	            Log::getWeixinInfo("用户信息补充失败！状态更新失败".$fansset['nickname']."--".$the_openid."   TIME:".date('Y-m-d', NOW_TIME),"","",C('LOG_PATH')."Fans".date('y_m_d').".log");
	        }
        }
        elseif($userinfo['subscribe']==0){
        	echo 123;
			$fansset['openid'] = $the_openid;
        	$fansset['subscribe'] = 0;
        	$fansset['subscribe_time'] = NOW_TIME;
        	if(M('Fans')->where(array('openid' => $the_openid))->save($fansset)){
	            echo "Save Fans success 失败!".$fansset['nickname']."--".$the_openid ;
	            Log::getWeixinInfo("用户取消关注,用户信息补充失败！状态更新成功".$fansset['nickname']."--".$the_openid."   TIME:".date('Y-m-d', NOW_TIME),"","",C('LOG_PATH')."Fans".date('y_m_d').".log");
	            echo "<script>location.reload()</script>";
	        }
	        else{
	            echo "Save Fans fail! 失败".$fansset['nickname']."--".$the_openid;
	            Log::getWeixinInfo("用户取消关注,用户信息补充失败！状态更新失败".$fansset['nickname']."--".$the_openid."   TIME:".date('Y-m-d', NOW_TIME),"","",C('LOG_PATH')."Fans".date('y_m_d').".log");
	        }
        }
        else{
	        $fansset['openid'] = $the_openid;
	        $fansset['nickname'] = $userinfo['nickname'];
	        $fansset['headimgurl'] = $userinfo['headimgurl'];
	        $fansset['sex'] = $userinfo['sex'];
	        $fansset['subscribe'] = 1;
	        $fansset['subscribe_time'] = $userinfo['subscribe_time'];
	        $fansset['address'] = $userinfo['country'] . $userinfo['province'] . $userinfo['city'];
	        $fansset['unionid'] = $userinfo['unionid'];
	        if(M('Fans')->where(array('openid' => $the_openid))->save($fansset)){
	            echo "Save Fans success!".$fansset['nickname']."--".$the_openid ;
	            Log::getWeixinInfo("用户信息补充成功！状态更新成功!".$fansset['nickname']."--".$the_openid ."   TIME:".date('Y-m-d', NOW_TIME),"","",C('LOG_PATH')."Fans".date('y_m_d').".log");
	            echo "<script>location.reload()</script>";

	        }
	        else{
	            echo "Save Fans fail!".$fansset['nickname']."--".$the_openid;
	            Log::getWeixinInfo("用户信息补充成功！状态更新失败".$fansset['nickname']."--".$the_openid ."   TIME:".date('Y-m-d', NOW_TIME),"","",C('LOG_PATH')."Fans".date('y_m_d').".log");

	        }
        }
        
    }

    public function getnickname(){
    	$id = $_GET['id'];
        $the_openid = M("fans")->where(" nickname = '' and id > ".$id)->getField("openid");
        $fans_info = M("fans")->where(array('openid' => $the_openid ))->find();
        $userinfo = $this->weObj->getUserInfo($the_openid);
        dump($userinfo);
        dump($fans_info);
        if(!$userinfo){
        	$fansset['openid'] = $the_openid;
        	$fansset['subscribe'] = 0;
        	$fansset['subscribe_time'] = NOW_TIME;
        	if(M('Fans')->where(array('openid' => $the_openid))->save($fansset)){
	            echo "Save Fans success 失败!".$fansset['nickname']."--".$the_openid ;
	            Log::getWeixinInfo("用户昵称补充失败！状态更新成功".$fansset['nickname']."--".$the_openid."   TIME:".date('Y-m-d', NOW_TIME),"","",C('LOG_PATH')."Fans".date('y_m_d').".log");
	            echo "<script>window.location.href='/wap.php?s=/Weixin/getnickname/id/".$fans_info['id']."';</script>";
	        }
	        else{
	            echo "Save Fans fail! 失败".$fansset['nickname']."--".$the_openid;
	            Log::getWeixinInfo("用户昵称补充失败！状态更新失败".$fansset['nickname']."--".$the_openid."   TIME:".date('Y-m-d', NOW_TIME),"","",C('LOG_PATH')."Fans".date('y_m_d').".log");
	        }
        }
        elseif($userinfo['subscribe']==0){
        	echo 123;
			$fansset['openid'] = $the_openid;
        	$fansset['subscribe'] = 0;
        	$fansset['subscribe_time'] = NOW_TIME;
        	if(M('Fans')->where(array('openid' => $the_openid))->save($fansset)){
	            echo "Save Fans success 失败!".$fansset['nickname']."--".$the_openid ;
	            Log::getWeixinInfo("用户取消关注,用户昵称补充失败！状态更新成功".$fansset['nickname']."--".$the_openid."   TIME:".date('Y-m-d', NOW_TIME),"","",C('LOG_PATH')."Fans".date('y_m_d').".log");
	           echo "<script>window.location.href='/wap.php?s=/Weixin/getnickname/id/".$fans_info['id']."';</script>";
	        }
	        else{
	            echo "Save Fans fail! 失败".$fansset['nickname']."--".$the_openid;
	            Log::getWeixinInfo("用户取消关注,用户昵称补充失败！状态更新失败".$fansset['nickname']."--".$the_openid."   TIME:".date('Y-m-d', NOW_TIME),"","",C('LOG_PATH')."Fans".date('y_m_d').".log");
	        }
        }
        else{
	        $fansset['openid'] = $the_openid;
	        $fansset['nickname'] = $userinfo['nickname'];
	        $fansset['headimgurl'] = $userinfo['headimgurl'];
	        $fansset['sex'] = $userinfo['sex'];
	        $fansset['subscribe'] = 1;
	        $fansset['subscribe_time'] = $userinfo['subscribe_time'];
	        $fansset['address'] = $userinfo['country'] . $userinfo['province'] . $userinfo['city'];
	        $fansset['unionid'] = $userinfo['unionid'];
	        if(M('Fans')->where(array('openid' => $the_openid))->save($fansset)){
	            echo "Save Fans success!".$fansset['nickname']."--".$the_openid ;
	            Log::getWeixinInfo("用户昵称补充成功！状态更新成功!".$fansset['nickname']."--".$the_openid ."   TIME:".date('Y-m-d', NOW_TIME),"","",C('LOG_PATH')."Fans".date('y_m_d').".log");
	            echo "<script>window.location.href='/wap.php?s=/Weixin/getnickname/id/".$fans_info['id']."';</script>";

	        }
	        else{
	            echo "Save Fans fail!".$fansset['nickname']."--".$the_openid;
	            Log::getWeixinInfo("用户昵称补充成功！状态更新失败".$fansset['nickname']."--".$the_openid ."   TIME:".date('Y-m-d', NOW_TIME),"","",C('LOG_PATH')."Fans".date('y_m_d').".log");
	             echo "<script>window.location.href='/wap.php?s=/Weixin/getnickname/id/".$fans_info['id']."';</script>";


	        }
        }
        
    }

    /***************************************************************************************************
     *                                      内部私有方法定义
     ********************************************************************************
    /**
     * 描述：获取微信用户信息，创建Fans
     * @author
     */
    private function createWechatFans($openId, $sceneId){
        $userinfo =  $this->weObj->getUserInfo($openId); //UnionID机制拉取用户信息(为判断是否已经关注平台)
        Log::getWeixinInfo("抓取用户信息：".json_encode($userinfo)."   TIME:".date('Y-m-d', NOW_TIME),"","",C('LOG_PATH')."Fans".date('y_m_d').".log");
        if ($userinfo['subscribe']==0) {
            Log::getWeixinInfo("抓取用户信息失败：".json_encode($userinfo)."   TIME:".date('Y-m-d', NOW_TIME),"","",C('LOG_PATH')."Fans".date('y_m_d').".log");
            return false;
        }

        $fansset['openid'] = $openId;
        $fansset['nickname'] = $userinfo['nickname'];
        $fansset['headimgurl'] = $userinfo['headimgurl'];
        $fansset['sex'] = $userinfo['sex'];
        $fansset['subscribe'] = 1;
        $fansset['subscribe_time'] = $userinfo['subscribe_time'];
        $fansset['address'] = $userinfo['country'] . $userinfo['province'] . $userinfo['city'];
        $fansset['unionid'] = $userinfo['unionid'];
        $fans = M('Fans')->where(array('openid' => $openId))->find();
        if($fans){
            if ($sceneId) {
                $fansset['canalid'] = $sceneId;
                $fansset['intro'] = '通过场景id=' . $sceneId . '关注';
            }
            if(M('Fans')->where(array('openid' => $openId))->save($fansset)){
                Log::getWeixinInfo("Save Fans success!".$fans['nickname']."--".$openId );
            }
            else{
                Log::getWeixinInfo("Save Fans fail!".$fans['nickname']."--".$openId  );
            }
        }
        else{
            $fansset['create_time'] =NOW_TIME;
            if ($sceneId) {
                $fansset['canalid'] = $sceneId;
                $fansset['intro'] = '通过场景id=' . $sceneId . '关注';
            }
            $uid = M('Fans')->add($fansset);
            if($uid){
                giveCoupon($uid,1);
                Log::getWeixinInfo("Add Fans success!".$fans['nickname']."--".$openId );
            }
            else{
                Log::getWeixinInfo("Add Fans fail!".$fans['nickname']."--".$openId  );
            }
        }

    }

    /**
     * 描述：处理关注事件
     * @param $openId
     * @param $evenKey
     * @author
     */
    private function doSubscribe($openId, $evenKey)
    {
        // 根据openid拉去用户信息，并自动注册用户
        if ($openId) {
            $sceneIdTemp = explode('_', $evenKey);
            $sceneIdTemp = $sceneIdTemp[1];
            $sceneId = is_numeric($sceneIdTemp) ? intval($sceneIdTemp) : 0;
            Log::weixin_info('用户关注：' . $openId);
            $this->createWechatFans($openId, $sceneId);
            if(0 != $sceneId)
            {
            	//场景回复
            	$datas = $this->canalReply($sceneId,true,$openId);
            	if(null != $datas)
            		$this->weObj->news($datas)->reply();
            }
            else
            {
            	$this->doReply('关注时回复');



            }
        }
    }

    /**
     * 描述：处理取消关注事件
     * @param $openId
     * @author
     */
    private function doUnSubscribe($openId){
        Log::weixin_info('用户取消关注：' . $openId);
        M('Fans')->where(array('openid' => $openId))->setField(array('subscribe' => 0));
    }

    /**
     * 描述：处理扫码事件，将回复三篇图文：
     *  1. 短期（特殊活动时的）砸蛋活动页面（图文、链接）
     *  2. 长期（永久的）砸蛋活动页面（图文、链接）
     *  3. 商家详情
     * @param $key
     * @param $openId
     * @author
     */
    private function doScan($key, $openId){

    	$title = '充电口';
    	$url = '';
    	
    	$datas = $this->canalReply($key,false,$openId);
    	
    	if($datas)
    		$this->weObj->news($datas)->reply();
    }

    /**
     * 进入公众号后定位
     * @param  array $data 微信发来的
     * @return void     
     */
    private function doLocation($data){
        if($data['Latitude']){
            $mLocation = M('FansLocation');
            $arr['openid'] = $data['FromUserName'];
            $arr['lng'] = $data['Longitude'];
            $arr['lat'] = $data['Latitude'];
            $mLocation->where(array('openid'=>$arr['openid']))->find()?$mLocation->where(array('openid'=>$arr['openid']))->save($arr):$mLocation->add($arr);
        }
    }

    /**
     * 描述：记录场景事件到数据库表scene_record
     * 如果该数据已经存在，则记录，并返回ture
     * @param $infos
     * @return bool: true 已经存在该数据    false:未存在该数据
     * @author
     */
    private function noteSceneRecord($infos){
        $data['event']= $infos['Event'];
        $data['scene_id']= $infos['EventKey'];
        $data['wechat_id']= $infos['FromUserName'];
        $data['time']=  $infos['CreateTime'];
        $data['message']= json_encode($infos);
        if('LOCATION' ==  $infos['Event']){
            $data['location'] =  $infos['Longitude'].','.$infos['Latitude'];
        }
        $mSceneRecord = M('SceneRecord');
        if($mSceneRecord->where(
                array(
                    'event'     =>$infos['Event'],
                    'wechat_id' =>$infos['FromUserName'],
                    'time'      =>$infos['CreateTime']
                )
            )->count() < 1){//不存在该条数据才插入
            M('SceneRecord')->add($data);
            return true;
        }
        return false;
    }
    
    /**
    * 渠道扫码处理
    * @date: 2015-7-16
    * @author: BillyZ
    * @return:
    */
    private function canalReply($canalid,$subscribe,$openid)
    {
		$datas=null;
    	$canal = M('Canal')->where(array('id'=>$canalid))->find();
		Log::weixin_info('canal：' . json_encode($canal));
    	if($canal)
    	{

    		switch($canal['code'])
    		{
    			case 'CD':
    				//充电口
    				$datas = $this->chargeReply($canal['pid']);
    				break;
    			case 'ZC':
    				//租车
    				$datas = $this->rentReply($canal['pid']);
    				break;
    			case 'YX':

    				//渠道二维码
    				if($subscribe)
    				{
    					$this->canalClick($canal['id']);
    					
    					//if($canal['keyword'])
    						//$this->doReply($canal['keyword']);
    					//else
    						$this->doReply('关注时回复');
    				}
    				else
					{
						$this->doReply($canal['keyword']);
					}

    				break;
    			default:
    				$datas = null;
    				break;
    		}
    	}
    	
    	return $datas;
    }
    
    /**
    * 渠道访问次数增加
    * @date: 2015-7-16
    * @author: BillyZ
    * @return:
    */
    private function canalClick($canalid)
    {
    	M('Canal')->where(array('id'=>$canalid))->setInc('click');
    }
    
    /**
    * 充电回复图文
    * @date: 2015-7-15
    * @author: BillyZ
    * @return:
    */
    private function chargeReply($outletid)
    {
    	$outlet = M('Outlet')->where(array('id'=>$outletid))->find();
    	$title = $outlet['name'];
    	$url = C('site_url')."/wxpay/wap.php?s=/Charge/order/oid/".$outlet['id'].".html";
    	$datas = array(
    			"0"=>array(
    					'Title'=> '点击图片确定充电',
    					'Description'=>'点击立即充电吧！！',
    					'PicUrl'=>C('site_url').'/Public/Wap/images/cd2.jpg?v=1',
    					'Url'=>$url
    			));
    	
    	return $datas;
    }
    
    /**
    * 租车回复图文
    * @date: 2015-7-15
    * @author: BillyZ
    * @return:
    */
    private function rentReply($bikeid)
    {
    	$bike = M('Rentbike')->where(array('id'=>$bikeid))->find();
    	$title = $bike['name'];
    	$url = C('site_url')."/wap.php?s=/Rent/order/bid/".$bike['id'].".html";
    	$datas = array(
    			"0"=>array(
    					'Title'=> '点击图片确定租车',
    					'Description'=>'点击立即开始租车吧！！',
    					'PicUrl'=>C('site_url').'/Public/Wap/images/zc.jpg?v=1',
    					'Url'=>$url
    			));
    	
    	return $datas;
    }

    /**
     * 发送模板消息
     * @date: 2015-7-3
     * @author: BillyZ
     * @return:
     */
    private function sendTempMsg($openid,$tempid,$url,$data)
    {
    	$msg = array(
    			"touser"=>$openid,
    			"template_id"=>$tempid,
    			"url"=>$url,
    			"topcolor"=>"#FF0000",
    			"data"=>$data
    	);
    	 
    	$this->gWechatObj = new Wechat(C('WEIXIN_CONFIG'));
    	$this->gWechatObj->sendTemplateMessage($msg);
    }

    /**
     * 微信关键词回复
     * @param $keyword
     * 
     * 
     * 
     * 
     * @author:
     */
    private function doReply($keyword) {
    	if(empty($keyword))
    		return;

        if($keyword == '关注时回复')
        {
            //原先的关注回复
            // $news = array(0=>array(
            //     'Title' => '司机推荐计划',
            //     'Description' => '司机推荐计划',
            //     'PicUrl' => 'http://d.7744go.com/Uploads/Picture/2016-04-24/571c6e1c18d42.png',
            //     'Url' => 'http://www.baidu.com',
            // ),
            //     1=>array(
            //         'Title' => '优惠券',
            //         'Description' => '优惠券',
            //         'PicUrl' => 'http://img.25pp.com/uploadfile/soft/images/2013/0412/20130412104107178.jpg',
            //         'Url' => 'http://mp.weixin.qq.com/s?__biz=MzIwMDQ5NzM5OQ==&mid=100000003&idx=1&sn=bfd290a2dab7ba665c8a811bff3d06c2#rd',
            //     ),
            //     2=>array(
            //         'Title' => '洗洁一空，洗洗你的空调吧',
            //         'Description' => '夏天到了，洗洗你的空调吧',
            //         'PicUrl' => 'http://pic.58pic.com/58pic/13/45/84/77t58PICw7T_1024.jpg',
            //         'Url' => 'http://d.7744go.com/wap.php?s=/Maintain/details/pid/16.html',
            //     )
            // );
            $news = array(0=>array(
                    'Title' => '“险”中求胜',
                    'Description' => '搜索“凡购”微信公众号参加车辆保险，即可享受多重好礼，让你在车险中脱颖而出！',
                    'PicUrl' => 'https://mmbiz.qlogo.cn/mmbiz/y6pnAJqEzN4V8rd4IWtMo2YnjZwEKLn9sOiacsZy4y0icUBbAib8aXlNI1FYLJKE1BESYJh5leo1xsS9KQ6mxGic7A/0?wx_fmt=png',
                    'Url' => 'http://mp.weixin.qq.com/s?__biz=MzIwMDQ5NzM5OQ==&tempkey=D8%2BYYyAhr2hLdW1UaKL%2BxaXF9S%2BUh7RYUyuT3S0Wdwhi3JiClGSZqIQikwMwnl2DLUm6JNa8i8cdz1et9eGSSzsTCuPk88DCUqT2lEV2hKGJYmQNQ%2F%2BkQn3BZMvkgzsPbaf9R6AxH4TRULxCU04OzQ%3D%3D&#rd',
                )
            )
            $this->weObj->news($news)->reply();
        }
    	
        $key = md5('wx_reply_' . $keyword);
        $reply = S($key);
        if (empty($reply)) {
            //先全字匹配再模糊匹配
            $mReply = M('Reply');
            $map['status'] = 1;
            $map['keyword'] = $keyword;
            $reply = $mReply->where($map)->order('reply_type desc,sort desc,create_time desc')->find();
            if (empty($reply)) {
                $map['status'] = 1;
                $map['keyword'] = array('like', '%' . $keyword . '%');
                $reply = $mReply->where($map)->order('reply_type desc,sort desc,create_time desc')->find();
                if (!empty($reply)) {
                    S($key, $reply, 60);
                }
            }
        }

        if (!empty($reply)) {
            if ($reply['reply_type'] == 1) {
                $this->weObj->text($reply['content'])->reply();
            } else if ($reply['reply_type'] == 2) {
                $url = $reply['link_url'];
                if (empty($url)) {
                    $url = U('News/reply', array('id' => $reply['id']), true, true);
                    Log::weixin_info('reply_url=' . $url);
                }
                $news = array( 0 => array(
                    'Title' => $reply['title'],
                    'Description' => $reply['info'],
                    'PicUrl' => C('site_url') . $reply['cover_img'],
                    'Url' => $url,
                ));
                $this->weObj->news($news)->reply();
            }
        } else {
            /**
             * TODO
             * $text = '客官，您的需求还真是有点奇特呢，待我问问老板去~';
             *$this->weObj->text($text)->reply();
             */
        }
    }

}