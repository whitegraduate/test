<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 前台公共库文件
 * 主要定义前台公共函数库
 */

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function check_verify($code, $id = 1){
	$verify = new \Think\Verify();
	return $verify->check($code, $id);
}

/**
 * 获取列表总行数
 * @param  string  $category 分类ID
 * @param  integer $status   数据状态
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_list_count($category, $status = 1){
    static $count;
    if(!isset($count[$category])){
        $count[$category] = D('Document')->listCount($category, $status);
    }
    return $count[$category];
}

/**
 * 获取段落总数
 * @param  string $id 文档ID
 * @return integer    段落总数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_part_count($id){
    static $count;
    if(!isset($count[$id])){
        $count[$id] = D('Document')->partCount($id);
    }
    return $count[$id];
}

/**
 * 获取导航URL
 * @param  string $url 导航URL
 * @return string      解析或的url
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_nav_url($url){
    switch ($url) {
        case 'http://' === substr($url, 0, 7):
        case '#' === substr($url, 0, 1):
            break;        
        default:
            $url = U($url);
            break;
    }
    return $url;
}

/**
 * 描述：获取当前页面完整URL地址
 * @return string
 * @author koumiba <jiangxiaobo@seersee.com>
 */
function get_current_url()
{
	$sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
	$php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
	$path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
	$relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self . (isset($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : $path_info);
	return $sys_protocal . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '') . $relate_url;
}

/**
 * 从session获取粉丝openid
 * @author: Nice <hupeipei@seersee.com>
 */
function session_get_openid() {
	$wechat = session('wechat');
	if (empty($wechat)) {
		return null;
	} else {
		return $wechat['openid'];
	}
}

/**
 * 从session获取粉丝unionid
 * @author: Nice <hupeipei@seersee.com>
 */
function session_get_unionid() {
	$wechat = session('wechat');
	if (empty($wechat)) {
		return null;
	} else {
		return $wechat['unionid'];
	}
}


/**
* 将emoj进行转换
* @date: 2015-7-14
* @author: BillyZ
* @return:
*/
function emoj($text)
{
	//$text = "你好  hello 123"; //可以为收到的微信消息，可能包含二进制emoji表情字符串
	$tmpStr = json_encode($text); //暴露出unicode
	$tmpStr = preg_replace("#(\\\ue[0-9a-f]{3})#ie","addslashes('\\1')",$tmpStr); //将emoji的unicode留下，其他不动
	$text = json_decode($tmpStr);
	 
	echo $text;//你好 \ue415 hello 123
}

/**
* 新订单号
* @date: 2015-10-27
* @author: BillyZ
* @return:
*/
function neworderid($type,$openid)
{
	if($openid)
		$openid = substr($openid, 10, 3);
	
	$rand = rand(1000,9999);
	
	$time = NOW_TIME;
	
	return $time.$openid.$rand;
}

/**
* 新请求单号
* @date: 2015-10-27
* @author: BillyZ
* @return:
*/
function newrequestid($type,$openid)
{
	if($openid)
		$openid = substr($openid, 10, 3);
	
	$rand = rand(1000,9999);
	
	$time = NOW_TIME;
	
	return 'G'.$type.$time.$openid.$rand;
}

function product_shop($pid){
    $lstShop = explode(',',$pid);
    $all_shop = M("Shop")->field('id,name')->select();
    foreach ($all_shop as $key => $val) {
    	if(in_array($val['id'], $lstShop, false)){
    		$shop[$key]['name'] = $val['name'];
    		$shop[$key]['id'] = $val['id'];
    	}
    }
    return $shop;
}