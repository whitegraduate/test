<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2015 http://www.seersee.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: MC <zhouyibin@seersee.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 用户相关操作
 */
 
/**
* 添加积分
* @date: 2015-7-22
* @author: BillyZ
* @return:
*/
function addscore($openid, $score, $logtype, $memo){
	//增加日志
	$log['openid'] = $openid;
	$log['score'] = $score;
	$log['log_type'] = $logtype;//1.加 0.减
	$log['intro'] = $memo;
	$log['createtime'] = NOW_TIME;
	$log['uid'] = 0;
	if("积分兑换"==$memo){
		$log['score']=$score['score'];
		$log['uid']=$score['gid'];
		$score=$score['score'];
	}
	M('ScoreLog')->add($log);
	
	//修改记录
	$fans = M('Fans')->where(array('openid'=>$openid))->find();
	if(1 == $logtype)
		$fans['score'] = $fans['score'] + $score;	
	else 
		$fans['score'] = $fans['score'] - $score;
	if(0 > $fans['score'])
		$fans['score'] = 0;
	M('Fans')->save($fans);
	return true;
}

/**
* 注册送积分
* @date: 2015-7-22
* @author: BillyZ
* @return:
*/
function addscore_bind($openid)
{
	$score = C('CONFIG_SCORE_BIND');
	if($score > 0)
	{
		addscore($openid, $score, 1, '注册送积分');
	}
}

/**
* 注册送积分
* @date: 2015-7-22
* @author: BillyZ
* @return:
*/
function addscore_remark($openid)
{
	$score = C('CONFIG_SCORE_REMARK');
	if($score > 0)
	{
		addscore($openid, $score, 1, '评价送积分');
	}
}

/**
* 认证送积分
* @date: 2015-7-22
* @author: BillyZ
* @return:
*/
function addscore_auth($openid)
{
	$score = C('CONFIG_SCORE_AUTH');
	if($score > 0)
	{
		addscore($openid, $score, 1, '认证送积分');
	}
}