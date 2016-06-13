<?php
// +----------------------------------------------------------------------
// | 视界无限 - SEERSEE
// +----------------------------------------------------------------------
// | Copyright (c) 2015
// +----------------------------------------------------------------------
// | Author: Nice <hupeipei@seersee.com>
// +----------------------------------------------------------------------

namespace Wap\Controller;
use Think\Controller;
use Think\Log;
use OT\Wechat\Wechat;
use User\Api\UserApi;

use OT\Wechat\SDKRuntimeException;
use OT\Wechat\UnifiedOrder;
use OT\Wechat\PayConfig;
use OT\Wechat\JsApi;
use OT\Wechat\Transfer;

/**
 * 前台公共控制器
 * 为防止多分组Controller名称冲突，公共Controller名称统一使用分组名称
 */
class WapController extends CommonController {

    protected $gWechatObj; //微信接口
    protected $gOpenId; //用户openid
    protected $gUnionId; //用户unionid
    protected $fancityid;//用户cityid
    protected $uid; //用户ID

	/* 空操作，用于输出404页面 */
	public function _empty(){
		$this->redirect('Index/404');
	}

    /**
     * 初始化
     * @author: Nice <hupeipei@seersee.com>
     */
    protected function _initialize(){
    	parent::_initialize();
        /* 读取站点配置 */
        $config = api('Config/lists');
        C($config); //添加配置

        if(!C('WEB_SITE_CLOSE')){
            $this->error('站点已经关闭，请稍后访问~');
        }

        /*if (!is_weixin_agent()) {
            echo '请在微信中打开页面。';
            exit;
        }*/

        if (!$this->gWechatObj) {
            $this->gWechatObj = $this->weObj;
            Log::weixin_info('复制了Wechat对象');
        }

        $callBack = get_current_url();
        Log::weixin_info('callback='.$callBack);
        $this->gOpenId = session_get_openid();
        $this->gUnionId = session_get_unionid();
        $this->fancityid = $this->get_fans_cityid();
        
        ////test
        //$this->gOpenId = "123456";
        //$this->gUnionId = "111111";
        
        if (empty($this->gOpenId)) {
        	Log::weixin_info('empty openid');
            $result = $this->getOpenId();
            if(!isset($_GET['from']))
            {
	            if (!$result) {
        			Log::weixin_info('!result');
	                // 跳转网页基础授权，获取openid和unionid
	                $url = $this->gWechatObj->getOauthRedirect($callBack, 'base', 'snsapi_base');
	                header('Location: ' . $url);
	                exit;
	            } else {
        			Log::weixin_info('result');
	                $mFans = M('Fans');
	                $wechat = $mFans->where(array('openid' => $this->gOpenId))->find();
	                if (empty($wechat)) {
	                    $wechat['nickname'] = '';
	                    $wechat['openid'] = $this->gOpenId;
	                    $wechat['unionid'] = $this->gUnionId;
	                    $wechat['sex'] = 0;
	                    $wechat['wecha_binded'] = 0;
	                    $wechat['create_time'] = NOW_TIME;
	                    //$mFans->add($wechat);
	                }
	                session('wechat', $wechat);
	            }
            }
            else
            {
                if(isset($_GET['fromoid'])){
                    $this->gOpenId = $_GET['fromoid'];
                    $wechat = M('Fans')->where(array('openid' => $this->gOpenId))->find();
                    if($wechat){
                        session('wechat', $wechat);
                    }
                }
            	Log::weixin_info('logininfo');
            }
        }
        else
        {
        	Log::weixin_info('not empty openid');
        }
    }

    /***************************************************************************************************
     *                                      内部私有方法定义
     *************************************************************************************************/

    /**
     * 获取粉丝openid，优先级：缓存 -> 网页授权
     * @return bool
     * @author: Nice <hupeipei@seersee.com>
     */
    private function getOpenId()
    {
        $code =  I('code');
        if (!empty($code)) {
            $json = $this->gWechatObj->getOauthAccessToken();
            if ($json) {
                $this->gOpenId = $json["openid"];
                $this->gUnionId = $json["unionid"];
                Log::weixin_info('网页基础授权 OpenId===' . $this->gOpenId . '; UnionId===' . $this->gUnionId);
                return true;
            } else {
                Log::weixin_info('网页基础授权 JSON为空');
            }
        } else {
            Log::weixin_info('网页基础授权 code为空，转重定向');
        }
        return false;
    }

    /**
     * 网页授权获取用户信息
     * @return bool
     * @author: Nice <hupeipei@seersee.com>
     */
    private function getOauthUserInfo() {
        $code =  I('code');
        if (!empty($code)) {
            $json = $this->gWechatObj->getOauthAccessToken();
            if ($json && !empty($json['access_token'])) {
                $userToken = $json['access_token'];
                $openId = $json['openid'];
                $oauthUserInfo = $this->gWechatObj->getOauthUserinfo($userToken, $openId);
                return $oauthUserInfo;
            } else {
                Log::weixin_info('网页用户授权 AccessToken获取失败！');
            }
        } else {
            Log::weixin_info('网页用户授权 code为空，转重定向');
        }
        return false;
    }

    /**
     * 检测用户是否登录
     * @author: Nice <hupeipei@seersee.com>
     */
    protected function wxlogin()
    {
        if (!is_login()) {
            $openId = $this->gOpenId;
            $unionId = $this->gUnionId;
           
            $mFans = M('Fans');
            $wechat = $mFans->where(array('openid' => $openId))->find();
            if (empty($wechat)) {
                $this->error('微信用户身份获取失败！');
            } else {
                $callBack = get_current_url();
                if ('' == $wechat['nickname']) {
                        //信息不全，需要网页授权
                        $oauth = $this->getOauthUserInfo();
                        if (empty($oauth)) {
                            $url = $this->gWechatObj->getOauthRedirect($callBack, 'userinfo', 'snsapi_userinfo');
                            Log::weixin_info('网页用户授权 url=' . $url);
                            header('Location: ' . $url);
                            exit;
                        }
                        Log::weixin_info('nickname='.$oauth['nickname']);
                        $wechat['nickname'] = emoj($oauth['nickname']);
                        Log::weixin_info('emoj='.$wechat['nickname']);
                        $wechat['sex'] = $oauth['sex'];
                        $wechat['headimgurl'] = $oauth['headimgurl'];
                        $wechat['address'] = $oauth['country'] . $oauth['province'] . $oauth['city'];
                        $wechat['unionid'] = $oauth['uinonid'];
                        //$wechat['wechat_binded'] = 1;
                        $mFans->save($wechat);
                    }
                    else if('' == $wechat['mobile'])
                    {
	                    //跳转到绑定会员
	                    $url =  '/wap.php/Fans/bind/return_url/'.urlencode($callBack);
	                    
	                    header('Location: ' . $url);
	                    exit;
                    }
                }
        } else {
            $this->uid = is_login();
        }
    }

    /**
     * 检测用户是否关注
     * @author: Nice <hupeipei@seersee.com>
     */
    protected function subscribe()
    {
        $wechat = session('wechat');
        if (empty($wechat['subscribe'])) {
            //未记录关注，先获取用户信息
            $openId = $this->gOpenId;
            $userInfo = $this->gWechatObj->getUserInfo($openId);
            if (empty($userInfo) || empty($userInfo['subscribe'])) {
                $this->error('请先关注', 'http://mp.weixin.qq.com/s?__biz=MzA3NDI0OTA2Mw==&mid=217447953&idx=1&sn=d6864127c9af1ba04ff0ba1d5ffcb5d0#rd');
            } else {
                $wechat['openid'] = $openId;
                $wechat['nickname'] = $userInfo['nickname'];
                $wechat['headimgurl'] = $userInfo['headimgurl'];
                $wechat['sex'] = $userInfo['sex'];
                $wechat['subscribe'] = $userInfo['subscribe'];
                $wechat['subscribe_time'] = $userInfo['subscribe_time'];
                $wechat['address'] = $userInfo['province'] . $userInfo['city'];
                $wechat['unionid'] = $userInfo['unionid'];
                $wechat['is_auth'] = 1;
                $val = M('WechatFans')->where(array('openid' => $openId))->save($wechat);
                if (false !== $val) {
                    session('wechat', $wechat);
                }
            }
        }
    }

    /**
     * 检测用户是否绑定业主
     * @author: Nice <hupeipei@seersee.com>
     */
    protected function checkOwner() {
        $this->checkCommunity();
        $owner = is_owner($this->cid);
        if (empty($owner)) {
            $callBack = get_current_url();
            //跳转到绑定会员
            $url = U('/Wap/Public/bindowner') . '?return_url=' . urlencode($callBack);
            header('Location: ' . $url);
            exit;
        } else {
            $this->cid = $owner['community_id'];
            $this->eid = $owner['estate_id'];
            $this->oid = $owner['id'];
        }
    }

    /**
     * 检测用户是否已选默认小区
     * @author: Nice <hupeipei@seersee.com>
     */
    protected function checkCommunity() {
        $cid = get_default_community();
        if (0 == $cid) {
            $callBack = get_current_url();
            //跳转到选择小区页面
            $url = U('/Wap/Public/communitylist') . '?return_url=' . urlencode($callBack);
            header('Location: ' . $url);
            exit;
        } else {
            $this->cid = $cid;
        }
    }

    /**
     * 注册会员
     * @param $wechat
     * @return int
     * @author: Nice <hupeipei@seersee.com>
     */
    protected function createMember($wechat) {
        $User = new UserApi;
        $mobile = $wechat['mobile'];
        $realname = $wechat['realname'];
        $password = '888888';
        $uid = $User->register($mobile, $password, null, $mobile);
        if (0 < $uid) {
            $mMember = D('Member');
            $member['uid'] = $uid;
            $member['username'] = $mobile;
            $member['nickname'] = $wechat['nickname'];
            $member['headimgurl'] = $wechat['headimgurl'];
            $member['sex'] = $wechat['sex'];
            $member['mobile'] = $mobile;
            $member['realname'] = $realname;
            $member['address'] = $wechat['address'];
            $member['intro'] = $wechat['remark'];
            $member['wechat_id'] = $wechat['openid'];
            $member['unionid'] = $wechat['unionid'];
            $member['wechat_binded_time'] = NOW_TIME;
            $member['member_group'] = 0;
            $member['status'] = 1;
            $member['reg_ip'] = get_client_ip();
            $member['reg_time'] = NOW_TIME;
            $memberId = $mMember->add($member);
            if (0 < $memberId) {
                $mMember->login($uid);
                return $uid;
            } else {
                $this->error('会员注册失败！（-101）');
            }
        } else {
            $this->error('会员注册失败！（' . $uid . '）');
        }
    }

    /**
    * 微信预支付
    * @date: 2015-8-19
    * @author: BillyZ
    * @return:
    */
	protected function prepay($body,$transid,$price)
	{
    	import('OT.Wechat.WechatPay', LIB_PATH);
		$objUnifiedOrder = new UnifiedOrder();
		$objUnifiedOrder->setParameter('body', $body);
		$objUnifiedOrder->setParameter('attach', $transid);
		$objUnifiedOrder->setParameter('out_trade_no', $transid);
		$objUnifiedOrder->setParameter('total_fee', $price);
		$objUnifiedOrder->setParameter('spbill_create_ip', get_client_ip());
		$objUnifiedOrder->setParameter('notify_url', PayConfig::NOTIFY_URL);
		$objUnifiedOrder->setParameter('trade_type', 'JSAPI');
		$objUnifiedOrder->setParameter('openid', $this->gOpenId);
		$prepayId = $objUnifiedOrder->getPrepayId();
		if (empty($prepayId)) {
			$data['code'] = 0;
			$data['msg'] = '支付出现异常！'.$this->gOpenId;
			return (json_encode($data));
		} else {
			$jsapi = new JsApi();
			$jsapi->setPrepayId($prepayId);
			$data = $jsapi->getParameters();
			return $data;
		}
	}
	
	/**
	* 通用分页方法
	* @date: 2015-9-1
	* @author: BillyZ
	* @return:
	*/
	protected function lists ($model,$where=array(),$order='',$field=true,$current=1){
		$options    =   array();
		$REQUEST    =   (array)I('request.');
		if(is_string($model)){
			$model  =   M($model);
		}
	
		$OPT        =   new \ReflectionProperty($model,'options');
		$OPT->setAccessible(true);
	
		$pk         =   $model->getPk();
		if($order===null){
			//order置空
		}else if ( isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']),array('desc','asc')) ) {
			$options['order'] = '`'.$REQUEST['_field'].'` '.$REQUEST['_order'];
		}elseif( $order==='' && empty($options['order']) && !empty($pk) ){
			$options['order'] = $pk.' desc';
		}elseif($order){
			$options['order'] = $order;
		}
		unset($REQUEST['_order'],$REQUEST['_field']);
	
		if(empty($where)){
			$where  =   array('status'=>array('egt',0));
		}
		if( !empty($where)){
			$options['where']   =   $where;
		}
		$options      =   array_merge( (array)$OPT->getValue($model), $options );
		$total        =   $model->where($options['where'])->count();
	
		if( isset($REQUEST['r']) ){
			$listRows = (int)$REQUEST['r'];
		}else{
			$listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
		}
		$page = new \Think\WapPage($total, $listRows, $REQUEST);
		if($total>$listRows){
			$page->setConfig('theme','%LINK_PAGE%');
		}
		$p =$page->show();
		$this->assign('_page', $p? $p: '');
		$this->assign('_total',$total);
		 
	    $options['limit'] = ($current - 1)*$listRows.','.$current * $listRows;
		$model->setProperty('options',$options);
		
		$return['datas'] = $model->field($field)->select();
		$return['page'] = $p;
	
		return $return;
	}


    /**
     * 微信预支付
     * @date: 2015-8-19
     * @author: BillyZ
     * @return:
     */
    protected function transfer($transid,$price,$openid,$desc)
    {
        import('OT.Wechat.WechatPay', LIB_PATH);
        $objTransfer = new Transfer();
        $objTransfer->setParameter('partner_trade_no', $transid);
        $objTransfer->setParameter('openid', $openid);
        $objTransfer->setParameter('check_name', 'NO_CHECK');
        $objTransfer->setParameter('amount', $price);
        $objTransfer->setParameter('desc', $desc);
        $objTransfer->setParameter('spbill_create_ip', get_client_ip());
        $result = $objTransfer->getResult();

        if($result)
        {
            if($result['return_code'] == 'SUCCESS')
            {
                if($result['result_code'] == 'SUCCESS')
                {
                    $data['code'] = 1;
                    $data['msg'] = '支付成功！'.$price;
                }
                else
                {
                    $data['code'] = $result['return_code'];
                    $data['msg'] = '支付出现异常！'.$result['err_code_des'];
                }
            }
            else
            {
                $data['code'] = $result['return_code'];
                $data['msg'] = '支付出现异常！'.$result['return_msg'];
            }
        }
        else
        {
            $data['code'] = 0;
            $data['msg'] = '支付出现异常！返回结果为空';
        }
        return $data;
    }

    private function get_fans_cityid()
    {
        $cityid = M('FansCity')->where(array('openid'=>$this->gOpenId))->getField('city');
        if($cityid)
            return $cityid;
        else
            return 1;
    }
}
