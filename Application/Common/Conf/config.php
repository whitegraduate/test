<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 系统配文件
 * 所有系统级别的配置
 */
return array(
		'LOAD_EXT_FILE' => 'dictionary',
    /* 模块相关配置 */
    'AUTOLOAD_NAMESPACE' => array('Addons' => ONETHINK_ADDON_PATH), //扩展模块列表
    'DEFAULT_MODULE'     => 'Home',
    'MODULE_DENY_LIST'   => array('Common','User','Admin','Install'),
    //'MODULE_ALLOW_LIST'  => array('Home','Admin'),

    /* 系统数据加密设置 */
    'DATA_AUTH_KEY' => '|e:c9g[%mSVjlb?IF^p/XW+!k#@noTtE(GD;2qyu', //默认数据加密KEY

    /* 用户相关设置 */
    'USER_MAX_CACHE'     => 1000, //最大缓存用户数
    'USER_ADMINISTRATOR' => 1, //管理员用户ID

    /* URL配置 */
    'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'            => 3, //URL模式
    'VAR_URL_PARAMS'       => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR'    => '/', //PATHINFO URL分割符

    /* 全局过滤配置 */
    'DEFAULT_FILTER' => '', //全局过滤函数


    /* 数据库配置 */

    'DB_TYPE'   => 'mysql', // 数据库类型
    'DB_HOST'   => 'rdsb7wfpb0i3452w68t7.mysql.rds.aliyuncs.com', // 服务器地址
    'DB_NAME'   => 'didi', // 数据库名
    'DB_USER'   => 'faigo', // 用户名
    'DB_PWD'    => 'faigo2015',  // 密码
    'DB_PORT'   => '3306', // 端口
    'DB_PREFIX' => 'fg_', // 数据库表前缀
    

    /* 文档模型配置 (文档模型核心配置，请勿更改) */
    'DOCUMENT_MODEL_TYPE' => array(2 => '主题', 1 => '目录', 3 => '段落'),

	//企信通的短信账号
    'SMS_CONFIG' => array(
    		'uid' => '102809',
    		'pwd' => 'CE77D8DCBA19059D917455B444F55E15',
    ),
    //参加活动的门店
    'PRODUCT_ACTIVITY' => array(
        'qingliang' => array('3','4','5','13'),//清凉一夏
    ),
);
