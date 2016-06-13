<?php
namespace Wap\Controller;

use Think\Model;
/**
 * Description of JumpController
 *
 * @author 金君耀
 */
class JumpController extends WapController{
    protected $gOpenId; //用户openid
    public function __construct() {
        parent::__construct(); 
    }
    
    public function index(){
        $this->gOpenId = session_get_openid();
        $url = base64_decode($_REQUEST['u']);
        $str1 = '';
        if (!strpos( $url,'?')){
            $str1 = '?___=2';
        }
        header('Location: ' . $url .$str1.'&code='.$this->gOpenId);
        $this->display();
    }
    
    public function  setmobile(){
        $mobile = $_REQUEST['mobile'];//移动电话
        $openid = $_REQUEST['openid'];//openid
        $sql = 'update gc_fans set mobile = \''.$mobile.'\' where openid = \''.$openid.'\'';
        $Model = new Model();
        $this->assign('str',$Model ->execute($sql));
    }

    public function getasstoken(){
        $asstoken = $this->gWechatObj->checkAuth();
        $ip = get_client_ip();
        $key = 'access_token_' . C("WX_APPID");
        $act = S($key);
        if($ip=="122.226.37.26"||$ip=="122.226.37.242"){
            echo json_encode($act);
        }
        
    }

//    public function  getfans(){
//        $Model = new Model();
//        $list = $Model->query('select * from gc_fans where openid=\''.$_REQUEST['openid'].'\'');
//        $this->assign('str',json_encode($list,true) );
//        $this->display();
//    }
//
//    public function  addfans(){
//        $mobile = $_REQUEST['mobile'];//移动电话
//        $nickname = $_REQUEST['nickname'];//昵称
//        $realname = $_REQUEST['realname'];//真实姓名
//        $intro = $_REQUEST['intro'];//注册来源
//        $address = $_REQUEST['address'];//地址
//        $subscribe_time = $_REQUEST['subscribe_time'];//关注时间
//        $unsubscribe_time = $_REQUEST['unsubscribe_time'];//取消关注时间
//        $openid = $_REQUEST['openid'];//openid
//
//        //$wechat_binded_time = $_REQUEST['wechat_binded_time'];//null
//        $headimgurl = $_REQUEST['headimgurl'];//头像地址
//        $sex = $_REQUEST['sex'];//sex
//        //$unionid = $_REQUEST['unionid'];//null
//        //$source = $_REQUEST['source'];//LOCAL
//        //$score = $_REQUEST['score'];//0.00
//        //$grade = $_REQUEST['grade'];//1
//        $create_time = $_REQUEST['create_time'];//注册时间
//        $idcard = $_REQUEST['idcard'];//身份证号
//        //$idcard_img = $_REQUEST['idcard_img'];//身份证图片地址
//        $job = $_REQUEST['job'];//职业
//
//        $sql = 'insert into `gc_fans` (`mobile`,`nickname`,`realname`,`intro`,`address`,`subscribe_time`,`unsubscribe_time`,`openid`,'
//                . '`wechat_binded_time`,`wechat_binded_time`,`sex`,`unionid`,`source`,`score`,`grade`,`create_time`,`idcard`,`idcard_img`,`job`) '
//                . 'VALUES '
//                . '(\''.$mobile.'\',\''.$nickname.'\',\''.$realname.'\',\''.$intro.'\',\''.$address.'\',\''.$subscribe_time.'\',\''.$unsubscribe_time.'\',\''.$openid.'\','
//                . 'null,\''.$headimgurl.'\',\''.$sex.'\',null,\'LOCAL\',\'0.00\',\'1\',\''.$create_time.'\',\''.$idcard.'\',\'\',\''.$job.'\')';
//        $Model = new Model();
//        $this->assign('str',$Model ->execute($sql));
//        $this->display();
//    }
}
