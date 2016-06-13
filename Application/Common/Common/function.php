<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

// OneThink常量定义
const ONETHINK_VERSION    = '1.1.141101';
const ONETHINK_ADDON_PATH = './Addons/';

/**
 * 系统公共库文件
 * 主要定义系统公共函数库
 */

/**
 * 检测用户是否登录
 * @return integer 0-未登录，大于0-当前登录用户ID
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_login(){
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['uid'] : 0;
    }
}

/**
 * 检测当前用户是否为管理员
 * @return boolean true-管理员，false-非管理员
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function is_administrator($uid = null){
    $uid = is_null($uid) ? is_login() : $uid;
    return $uid && (intval($uid) === C('USER_ADMINISTRATOR'));
}

/**
 * 字符串转换为数组，主要用于把分隔符调整到第二个参数
 * @param  string $str  要分割的字符串
 * @param  string $glue 分割符
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function str2arr($str, $glue = ','){
    return explode($glue, $str);
}

/**
 * 数组转换为字符串，主要用于把分隔符调整到第二个参数
 * @param  array  $arr  要连接的数组
 * @param  string $glue 分割符
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function arr2str($arr, $glue = ','){
    return implode($glue, $arr);
}

/**
 * 字符串截取，支持中文和其他编码
 * @static
 * @access public
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true) {
    if(function_exists("mb_substr"))
        $slice = mb_substr($str, $start, $length, $charset);
    elseif(function_exists('iconv_substr')) {
        $slice = iconv_substr($str,$start,$length,$charset);
        if(false === $slice) {
            $slice = '';
        }
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
    }
    return $suffix ? $slice.'...' : $slice;
}

/**
 * 系统加密方法
 * @param string $data 要加密的字符串
 * @param string $key  加密密钥
 * @param int $expire  过期时间 单位 秒
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_encrypt($data, $key = '', $expire = 0) {
    $key  = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = base64_encode($data);
    $x    = 0;
    $len  = strlen($data);
    $l    = strlen($key);
    $char = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    $str = sprintf('%010d', $expire ? $expire + time():0);

    for ($i = 0; $i < $len; $i++) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1)))%256);
    }
    return str_replace(array('+','/','='),array('-','_',''),base64_encode($str));
}

/**
 * 系统解密方法
 * @param  string $data 要解密的字符串 （必须是think_encrypt方法加密的字符串）
 * @param  string $key  加密密钥
 * @return string
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function think_decrypt($data, $key = ''){
    $key    = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data   = str_replace(array('-','_'),array('+','/'),$data);
    $mod4   = strlen($data) % 4;
    if ($mod4) {
       $data .= substr('====', $mod4);
    }
    $data   = base64_decode($data);
    $expire = substr($data,0,10);
    $data   = substr($data,10);

    if($expire > 0 && $expire < time()) {
        return '';
    }
    $x      = 0;
    $len    = strlen($data);
    $l      = strlen($key);
    $char   = $str = '';

    for ($i = 0; $i < $len; $i++) {
        if ($x == $l) $x = 0;
        $char .= substr($key, $x, 1);
        $x++;
    }

    for ($i = 0; $i < $len; $i++) {
        if (ord(substr($data, $i, 1))<ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        }else{
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }
    return base64_decode($str);
}

/**
 * 数据签名认证
 * @param  array  $data 被认证的数据
 * @return string       签名
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function data_auth_sign($data) {
    //数据类型检测
    if(!is_array($data)){
        $data = (array)$data;
    }
    ksort($data); //排序
    $code = http_build_query($data); //url编码并生成query字符串
    $sign = sha1($code); //生成签名
    return $sign;
}

/**
* 对查询结果集进行排序
* @access public
* @param array $list 查询结果
* @param string $field 排序的字段名
* @param array $sortby 排序类型
* asc正向排序 desc逆向排序 nat自然排序
* @return array
*/
function list_sort_by($list,$field, $sortby='asc') {
   if(is_array($list)){
       $refer = $resultSet = array();
       foreach ($list as $i => $data)
           $refer[$i] = &$data[$field];
       switch ($sortby) {
           case 'asc': // 正向排序
                asort($refer);
                break;
           case 'desc':// 逆向排序
                arsort($refer);
                break;
           case 'nat': // 自然排序
                natcasesort($refer);
                break;
       }
       foreach ( $refer as $key=> $val)
           $resultSet[] = &$list[$key];
       return $resultSet;
   }
   return false;
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function list_to_tree($list, $pk='id', $pid = 'pid', $child = '_child', $root = 0) {
    // 创建Tree
    $tree = array();
    if(is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] =& $list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId =  $data[$pid];
            if ($root == $parentId) {
                $tree[] =& $list[$key];
            }else{
                if (isset($refer[$parentId])) {
                    $parent =& $refer[$parentId];
                    $parent[$child][] =& $list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree  原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array  $list  过渡用的中间数组，
 * @return array        返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list($tree, $child = '_child', $order='id', &$list = array()){
    if(is_array($tree)) {
        foreach ($tree as $key => $value) {
            $reffer = $value;
            if(isset($reffer[$child])){
                unset($reffer[$child]);
                tree_to_list($value[$child], $child, $order, $list);
            }
            $list[] = $reffer;
        }
        $list = list_sort_by($list, $order, $sortby='asc');
    }
    return $list;
}

/**
 * 格式化字节大小
 * @param  number $size      字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function format_bytes($size, $delimiter = '') {
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 设置跳转页面URL
 * 使用函数再次封装，方便以后选择不同的存储方式（目前使用cookie存储）
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function set_redirect_url($url){
    cookie('redirect_url', $url);
}

/**
 * 获取跳转页面URL
 * @return string 跳转页URL
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function get_redirect_url(){
    $url = cookie('redirect_url');
    return empty($url) ? __APP__ : $url;
}

/**
 * 处理插件钩子
 * @param string $hook   钩子名称
 * @param mixed $params 传入参数
 * @return void
 */
function hook($hook,$params=array()){
    \Think\Hook::listen($hook,$params);
}

/**
 * 获取插件类的类名
 * @param strng $name 插件名
 */
function get_addon_class($name){
    $class = "Addons\\{$name}\\{$name}Addon";
    return $class;
}

/**
 * 获取插件类的配置文件数组
 * @param string $name 插件名
 */
function get_addon_config($name){
    $class = get_addon_class($name);
    if(class_exists($class)) {
        $addon = new $class();
        return $addon->getConfig();
    }else {
        return array();
    }
}

/**
 * 插件显示内容里生成访问插件的url
 * @param string $url url
 * @param array $param 参数
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function addons_url($url, $param = array()){
    $url        = parse_url($url);
    $case       = C('URL_CASE_INSENSITIVE');
    $addons     = $case ? parse_name($url['scheme']) : $url['scheme'];
    $controller = $case ? parse_name($url['host']) : $url['host'];
    $action     = trim($case ? strtolower($url['path']) : $url['path'], '/');

    /* 解析URL带的参数 */
    if(isset($url['query'])){
        parse_str($url['query'], $query);
        $param = array_merge($query, $param);
    }

    /* 基础参数 */
    $params = array(
        '_addons'     => $addons,
        '_controller' => $controller,
        '_action'     => $action,
    );
    $params = array_merge($params, $param); //添加额外参数

    return U('Addons/execute', $params);
}

/**
 * 时间戳格式化
 * @param int $time
 * @return string 完整的时间显示
 * @author huajie <banhuajie@163.com>
 */
function time_format($time = NULL,$format='Y-m-d H:i'){
    $time = $time === NULL ? NOW_TIME : intval($time);
    return date($format, $time);
}

/**
 * 根据用户ID获取用户名
 * @param  integer $uid 用户ID
 * @return string       用户名
 */
function get_username($uid = 0){
    static $list;
    if(!($uid && is_numeric($uid))){ //获取当前登录用户名
        return session('user_auth.username');
    }

    /* 获取缓存数据 */
    if(empty($list)){
        $list = S('sys_active_user_list');
    }

    /* 查找用户信息 */
    $key = "u{$uid}";
    if(isset($list[$key])){ //已缓存，直接使用
        $name = $list[$key];
    } else { //调用接口获取用户信息
        $User = new User\Api\UserApi();
        $info = $User->info($uid);
        if($info && isset($info[1])){
            $name = $list[$key] = $info[1];
            /* 缓存用户 */
            $count = count($list);
            $max   = C('USER_MAX_CACHE');
            while ($count-- > $max) {
                array_shift($list);
            }
            S('sys_active_user_list', $list);
        } else {
            $name = '';
        }
    }
    return $name;
}

/**
 * 根据用户ID获取用户昵称
 * @param  integer $uid 用户ID
 * @return string       用户昵称
 */
function get_nickname($uid = 0){
    static $list;
    if(!($uid && is_numeric($uid))){ //获取当前登录用户名
        return session('user_auth.username');
    }

    /* 获取缓存数据 */
    if(empty($list)){
        $list = S('sys_user_nickname_list');
    }

    /* 查找用户信息 */
    $key = "u{$uid}";
    if(isset($list[$key])){ //已缓存，直接使用
        $name = $list[$key];
    } else { //调用接口获取用户信息
        $info = M('Member')->field('nickname')->find($uid);
        if($info !== false && $info['nickname'] ){
            $nickname = $info['nickname'];
            $name = $list[$key] = $nickname;
            /* 缓存用户 */
            $count = count($list);
            $max   = C('USER_MAX_CACHE');
            while ($count-- > $max) {
                array_shift($list);
            }
            S('sys_user_nickname_list', $list);
        } else {
            $name = '';
        }
    }
    return $name;
}

/**
 * 获取分类信息并缓存分类
 * @param  integer $id    分类ID
 * @param  string  $field 要获取的字段名
 * @return string         分类信息
 */
function get_category($id, $field = null){
    static $list;

    /* 非法分类ID */
    if(empty($id) || !is_numeric($id)){
        return '';
    }

    /* 读取缓存数据 */
    if(empty($list)){
        $list = S('sys_category_list');
    }

    /* 获取分类名称 */
    if(!isset($list[$id])){
        $cate = M('Category')->find($id);
        if(!$cate || 1 != $cate['status']){ //不存在分类，或分类被禁用
            return '';
        }
        $list[$id] = $cate;
        S('sys_category_list', $list); //更新缓存
    }
    return is_null($field) ? $list[$id] : $list[$id][$field];
}

/* 根据ID获取分类标识 */
function get_category_name($id){
    return get_category($id, 'name');
}

/* 根据ID获取分类名称 */
function get_category_title($id){
    return get_category($id, 'title');
}

/**
 * 获取顶级模型信息
 */
function get_top_model($model_id=null){
    $map   = array('status' => 1, 'extend' => 0);
    if(!is_null($model_id)){
        $map['id']  =   array('neq',$model_id);
    }
    $model = M('Model')->where($map)->field(true)->select();
    foreach ($model as $value) {
        $list[$value['id']] = $value;
    }
    return $list;
}

/**
 * 获取文档模型信息
 * @param  integer $id    模型ID
 * @param  string  $field 模型字段
 * @return array
 */
function get_document_model($id = null, $field = null){
    static $list;

    /* 非法分类ID */
    if(!(is_numeric($id) || is_null($id))){
        return '';
    }

    /* 读取缓存数据 */
    if(empty($list)){
        $list = S('DOCUMENT_MODEL_LIST');
    }

    /* 获取模型名称 */
    if(empty($list)){
        $map   = array('status' => 1, 'extend' => 1);
        $model = M('Model')->where($map)->field(true)->select();
        foreach ($model as $value) {
            $list[$value['id']] = $value;
        }
        S('DOCUMENT_MODEL_LIST', $list); //更新缓存
    }

    /* 根据条件返回数据 */
    if(is_null($id)){
        return $list;
    } elseif(is_null($field)){
        return $list[$id];
    } else {
        return $list[$id][$field];
    }
}

/**
 * 解析UBB数据
 * @param string $data UBB字符串
 * @return string 解析为HTML的数据
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function ubb($data){
    //TODO: 待完善，目前返回原始数据
    return $data;
}

/**
 * 记录行为日志，并执行该行为的规则
 * @param string $action 行为标识
 * @param string $model 触发行为的模型名
 * @param int $record_id 触发行为的记录id
 * @param int $user_id 执行行为的用户id
 * @return boolean
 * @author huajie <banhuajie@163.com>
 */
function action_log($action = null, $model = null, $record_id = null, $user_id = null){

    //参数检查
    if(empty($action) || empty($model) || empty($record_id)){
        return '参数不能为空';
    }
    if(empty($user_id)){
        $user_id = is_login();
    }

    //查询行为,判断是否执行
    $action_info = M('Action')->getByName($action);
    if($action_info['status'] != 1){
        return '该行为被禁用或删除';
    }

    //插入行为日志
    $data['action_id']      =   $action_info['id'];
    $data['user_id']        =   $user_id;
    $data['action_ip']      =   ip2long(get_client_ip());
    $data['model']          =   $model;
    $data['record_id']      =   $record_id;
    $data['create_time']    =   NOW_TIME;

    //解析日志规则,生成日志备注
    if(!empty($action_info['log'])){
        if(preg_match_all('/\[(\S+?)\]/', $action_info['log'], $match)){
            $log['user']    =   $user_id;
            $log['record']  =   $record_id;
            $log['model']   =   $model;
            $log['time']    =   NOW_TIME;
            $log['data']    =   array('user'=>$user_id,'model'=>$model,'record'=>$record_id,'time'=>NOW_TIME);
            foreach ($match[1] as $value){
                $param = explode('|', $value);
                if(isset($param[1])){
                    $replace[] = call_user_func($param[1],$log[$param[0]]);
                }else{
                    $replace[] = $log[$param[0]];
                }
            }
            $data['remark'] =   str_replace($match[0], $replace, $action_info['log']);
        }else{
            $data['remark'] =   $action_info['log'];
        }
    }else{
        //未定义日志规则，记录操作url
        $data['remark']     =   '操作url：'.$_SERVER['REQUEST_URI'];
    }

    M('ActionLog')->add($data);

    if(!empty($action_info['rule'])){
        //解析行为
        $rules = parse_action($action, $user_id);

        //执行行为
        $res = execute_action($rules, $action_info['id'], $user_id);
    }
}

/**
 * 解析行为规则
 * 规则定义  table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
 * 规则字段解释：table->要操作的数据表，不需要加表前缀；
 *              field->要操作的字段；
 *              condition->操作的条件，目前支持字符串，默认变量{$self}为执行行为的用户
 *              rule->对字段进行的具体操作，目前支持四则混合运算，如：1+score*2/2-3
 *              cycle->执行周期，单位（小时），表示$cycle小时内最多执行$max次
 *              max->单个周期内的最大执行次数（$cycle和$max必须同时定义，否则无效）
 * 单个行为后可加 ； 连接其他规则
 * @param string $action 行为id或者name
 * @param int $self 替换规则里的变量为执行用户的id
 * @return boolean|array: false解析出错 ， 成功返回规则数组
 * @author huajie <banhuajie@163.com>
 */
function parse_action($action = null, $self){
    if(empty($action)){
        return false;
    }

    //参数支持id或者name
    if(is_numeric($action)){
        $map = array('id'=>$action);
    }else{
        $map = array('name'=>$action);
    }

    //查询行为信息
    $info = M('Action')->where($map)->find();
    if(!$info || $info['status'] != 1){
        return false;
    }

    //解析规则:table:$table|field:$field|condition:$condition|rule:$rule[|cycle:$cycle|max:$max][;......]
    $rules = $info['rule'];
    $rules = str_replace('{$self}', $self, $rules);
    $rules = explode(';', $rules);
    $return = array();
    foreach ($rules as $key=>&$rule){
        $rule = explode('|', $rule);
        foreach ($rule as $k=>$fields){
            $field = empty($fields) ? array() : explode(':', $fields);
            if(!empty($field)){
                $return[$key][$field[0]] = $field[1];
            }
        }
        //cycle(检查周期)和max(周期内最大执行次数)必须同时存在，否则去掉这两个条件
        if(!array_key_exists('cycle', $return[$key]) || !array_key_exists('max', $return[$key])){
            unset($return[$key]['cycle'],$return[$key]['max']);
        }
    }

    return $return;
}

/**
 * 执行行为
 * @param array $rules 解析后的规则数组
 * @param int $action_id 行为id
 * @param array $user_id 执行的用户id
 * @return boolean false 失败 ， true 成功
 * @author huajie <banhuajie@163.com>
 */
function execute_action($rules = false, $action_id = null, $user_id = null){
    if(!$rules || empty($action_id) || empty($user_id)){
        return false;
    }

    $return = true;
    foreach ($rules as $rule){

        //检查执行周期
        $map = array('action_id'=>$action_id, 'user_id'=>$user_id);
        $map['create_time'] = array('gt', NOW_TIME - intval($rule['cycle']) * 3600);
        $exec_count = M('ActionLog')->where($map)->count();
        if($exec_count > $rule['max']){
            continue;
        }

        //执行数据库操作
        $Model = M(ucfirst($rule['table']));
        $field = $rule['field'];
        $res = $Model->where($rule['condition'])->setField($field, array('exp', $rule['rule']));

        if(!$res){
            $return = false;
        }
    }
    return $return;
}

//基于数组创建目录和文件
function create_dir_or_files($files){
    foreach ($files as $key => $value) {
        if(substr($value, -1) == '/'){
            mkdir($value);
        }else{
            @file_put_contents($value, '');
        }
    }
}

if(!function_exists('array_column')){
    function array_column(array $input, $columnKey, $indexKey = null) {
        $result = array();
        if (null === $indexKey) {
            if (null === $columnKey) {
                $result = array_values($input);
            } else {
                foreach ($input as $row) {
                    $result[] = $row[$columnKey];
                }
            }
        } else {
            if (null === $columnKey) {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row;
                }
            } else {
                foreach ($input as $row) {
                    $result[$row[$indexKey]] = $row[$columnKey];
                }
            }
        }
        return $result;
    }
}

/**
 * 获取表名（不含表前缀）
 * @param string $model_id
 * @return string 表名
 * @author huajie <banhuajie@163.com>
 */
function get_table_name($model_id = null){
    if(empty($model_id)){
        return false;
    }
    $Model = M('Model');
    $name = '';
    $info = $Model->getById($model_id);
    if($info['extend'] != 0){
        $name = $Model->getFieldById($info['extend'], 'name').'_';
    }
    $name .= $info['name'];
    return $name;
}

/**
 * 获取属性信息并缓存
 * @param  integer $id    属性ID
 * @param  string  $field 要获取的字段名
 * @return string         属性信息
 */
function get_model_attribute($model_id, $group = true,$fields=true){
    static $list;

    /* 非法ID */
    if(empty($model_id) || !is_numeric($model_id)){
        return '';
    }

    /* 获取属性 */
    if(!isset($list[$model_id])){
        $map = array('model_id'=>$model_id);
        $extend = M('Model')->getFieldById($model_id,'extend');

        if($extend){
            $map = array('model_id'=> array("in", array($model_id, $extend)));
        }
        $info = M('Attribute')->where($map)->field($fields)->select();
        $list[$model_id] = $info;
    }

    $attr = array();
    if($group){
        foreach ($list[$model_id] as $value) {
            $attr[$value['id']] = $value;
        }
        $model     = M("Model")->field("field_sort,attribute_list,attribute_alias")->find($model_id);
        $attribute = explode(",", $model['attribute_list']);
        if (empty($model['field_sort'])) { //未排序
            $group = array(1 => array_merge($attr));
        } else {
            $group = json_decode($model['field_sort'], true);

            $keys = array_keys($group);
            foreach ($group as &$value) {
                foreach ($value as $key => $val) {
                    $value[$key] = $attr[$val];
                    unset($attr[$val]);
                }
            }

            if (!empty($attr)) {
                foreach ($attr as $key => $val) {
                    if (!in_array($val['id'], $attribute)) {
                        unset($attr[$key]);
                    }
                }
                $group[$keys[0]] = array_merge($group[$keys[0]], $attr);
            }
        }
        if (!empty($model['attribute_alias'])) {
            $alias  = preg_split('/[;\r\n]+/s', $model['attribute_alias']);
            $fields = array();
            foreach ($alias as &$value) {
                $val             = explode(':', $value);
                $fields[$val[0]] = $val[1];
            }
            foreach ($group as &$value) {
                foreach ($value as $key => $val) {
                    if (!empty($fields[$val['name']])) {
                        $value[$key]['title'] = $fields[$val['name']];
                    }
                }
            }
        }
        $attr = $group;
    }else{
        foreach ($list[$model_id] as $value) {
            $attr[$value['name']] = $value;
        }
    }
    return $attr;
}

/**
 * 调用系统的API接口方法（静态方法）
 * api('User/getName','id=5'); 调用公共模块的User接口的getName方法
 * api('Admin/User/getName','id=5');  调用Admin模块的User接口
 * @param  string  $name 格式 [模块名]/接口名/方法名
 * @param  array|string  $vars 参数
 */
function api($name,$vars=array()){
    $array     = explode('/',$name);
    $method    = array_pop($array);
    $classname = array_pop($array);
    $module    = $array? array_pop($array) : 'Common';
    $callback  = $module.'\\Api\\'.$classname.'Api::'.$method;
    if(is_string($vars)) {
        parse_str($vars,$vars);
    }
    return call_user_func_array($callback,$vars);
}

/**
 * 根据条件字段获取指定表的数据
 * @param mixed $value 条件，可用常量或者数组
 * @param string $condition 条件字段
 * @param string $field 需要返回的字段，不传则返回整个数据
 * @param string $table 需要查询的表
 * @author huajie <banhuajie@163.com>
 */
function get_table_field($value = null, $condition = 'id', $field = null, $table = null){
    if(empty($value) || empty($table)){
        return false;
    }

    //拼接参数
    $map[$condition] = $value;
    $info = M(ucfirst($table))->where($map);
    if(empty($field)){
        $info = $info->field(true)->find();
    }else{
        $info = $info->getField($field);
    }
    return $info;
}

/**
 * 获取链接信息
 * @param int $link_id
 * @param string $field
 * @return 完整的链接信息或者某一字段
 * @author huajie <banhuajie@163.com>
 */
function get_link($link_id = null, $field = 'url'){
    $link = '';
    if(empty($link_id)){
        return $link;
    }
    $link = M('Url')->getById($link_id);
    if(empty($field)){
        return $link;
    }else{
        return $link[$field];
    }
}

/**
 * 获取文档封面图片
 * @param int $cover_id
 * @param string $field
 * @return 完整的数据  或者  指定的$field字段值
 * @author huajie <banhuajie@163.com>
 */
function get_cover($cover_id, $field = null){
    if(empty($cover_id)){
        return false;
    }
    $picture = M('Picture')->where(array('status'=>1))->getById($cover_id);
    if($field == 'path'){
        if(!empty($picture['url'])){
            $picture['path'] = $picture['url'];
        }else{
            $picture['path'] = __ROOT__.$picture['path'];
        }
    }
    return empty($field) ? $picture : $picture[$field];
}

/**
 * 检查$pos(推荐位的值)是否包含指定推荐位$contain
 * @param number $pos 推荐位的值
 * @param number $contain 指定推荐位
 * @return boolean true 包含 ， false 不包含
 * @author huajie <banhuajie@163.com>
 */
function check_document_position($pos = 0, $contain = 0){
    if(empty($pos) || empty($contain)){
        return false;
    }

    //将两个参数进行按位与运算，不为0则表示$contain属于$pos
    $res = $pos & $contain;
    if($res !== 0){
        return true;
    }else{
        return false;
    }
}

/**
 * 获取数据的所有子孙数据的id值
 * @author 朱亚杰 <xcoolcc@gmail.com>
 */

function get_stemma($pids,Model &$model, $field='id'){
    $collection = array();

    //非空判断
    if(empty($pids)){
        return $collection;
    }

    if( is_array($pids) ){
        $pids = trim(implode(',',$pids),',');
    }
    $result     = $model->field($field)->where(array('pid'=>array('IN',(string)$pids)))->select();
    $child_ids  = array_column ((array)$result,'id');

    while( !empty($child_ids) ){
        $collection = array_merge($collection,$result);
        $result     = $model->field($field)->where( array( 'pid'=>array( 'IN', $child_ids ) ) )->select();
        $child_ids  = array_column((array)$result,'id');
    }
    return $collection;
}

/**
 * 验证分类是否允许发布内容
 * @param  integer $id 分类ID
 * @return boolean     true-允许发布内容，false-不允许发布内容
 */
function check_category($id){
    if (is_array($id)) {
		$id['type']	=	!empty($id['type'])?$id['type']:2;
        $type = get_category($id['category_id'], 'type');
        $type = explode(",", $type);
        return in_array($id['type'], $type);
    } else {
        $publish = get_category($id, 'allow_publish');
        return $publish ? true : false;
    }
}

/**
 * 检测分类是否绑定了指定模型
 * @param  array $info 模型ID和分类ID数组
 * @return boolean     true-绑定了模型，false-未绑定模型
 */
function check_category_model($info){
    $cate   =   get_category($info['category_id']);
    $array  =   explode(',', $info['pid'] ? $cate['model_sub'] : $cate['model']);
    return in_array($info['model_id'], $array);
}


/**
 * GET 请求
 * @param string $url
 */
function http_get($url)
{
	$oCurl = curl_init();
	if (stripos($url, "https://") !== FALSE) {
		curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
	}
	curl_setopt($oCurl, CURLOPT_URL, $url);
	curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
	$sContent = curl_exec($oCurl);
	$aStatus = curl_getinfo($oCurl);
	curl_close($oCurl);
	if (intval($aStatus["http_code"]) == 200) {
		return $sContent;
	} else {
		return false;
	}
}


/**
 * POST 请求
 * @param string $url
 * @param string $data
 * @author: Ketory
 * @return: $sContent
 */
function http_post_json($url,$data)
{
	$oCurl = curl_init();
	if (stripos($url, "https://") !== FALSE) {
		curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, FALSE);
	}
	curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS, $data);
	curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($oCurl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Content-Length: ' . strlen($data)));
    $sContent = curl_exec($oCurl);
	$aStatus = curl_getinfo($oCurl);
	curl_close($oCurl);
	if (intval($aStatus["http_code"]) == 200) {
		return $sContent;
	} else {
		return false;
	}
}

/**
 * 获取会员名称
 * @date: 2015-7-19
 * @author: BillyZ
 * @return:
 */
function get_fansname_openid($openid)
{
	$fans = get_obj_fans_openid($openid);
	return $fans['nickname'];
}
function get_fans_real_name_openid($openid)
{
	$fans = get_obj_fans_openid($openid);
	return $fans['realname'];
}

/**
* 会员头像
* @date: 2015-8-6
* @author: BillyZ
* @return:
*/
function get_fanshead_openid($openid)
{
	$fans = get_obj_fans_openid($openid);
	return $fans['headimgurl'];
}

/**
 * 获取会员
 * @date: 2015-7-19
 * @author: BillyZ
 * @return:
 */
function get_obj_fans_openid($openid,$fromdb = false)
{
	$key = "obj_fans_openid_".$openid;
		 
	if(S($key) && !$fromdb)
	{
		return S($key);
	}
	else
	{
		$mFans = M('Fans');
		$fans = $mFans->where(array('openid'=>$openid))->find();
		S($key,$fans,60);
		return $fans;
	}
}

/**
* 商品对象
* @date: 2015-8-7
* @author: BillyZ
* @return:
*/
function get_obj_mall_id($id)
{
	$key = "obj_mall_id_".$id;
	if(S($key))
	{
		return S($key);
	}
	else
	{
		$mObj = M('Mall');
		$mall = $mObj->where(array('id'=>$id))->find();
		S($key,$mall,300);
		return $mall;
	}
}

/**
* 颜色名称
* @date: 2015-8-8
* @author: BillyZ
* @return:
*/
function get_color_id($id)
{
	$key = "colors_id_".$id;
	if(S($key))
	{
		return S($key);
	}
	else
	{
		$mObj = M('Color');
		$color = $mObj->where(array('id'=>$id))->getField('color_name');
		S($key,$color);
		return $color;
	}
}

function get_mallname_id($id)
{
	$obj = get_obj_mall_id($id);
	return $obj['title'];
}
//mallid 为数组的时候输出quatity mallid
function get_mall_id($id){
    $type=gettype(json_decode($id));
    if($type=="integer"){
        $mallid=$id;
        $where=array('id'=>$mallid);
    }else{
        $mallid=json_decode($id,true);
        foreach($mallid as $key=>$val){
            $mall_id[$key]=$key;
            $mall_quantity[$key]=$val;
        }
        $where['id']=array('in',$mall_id);
    }
    $shopping_cart_order=M("Mall")->where($where)->select();
    $res['quantity']=$mall_quantity;
    $res['info']=$shopping_cart_order;
    return $res;
}

function array_mallid_get_info($id){
    if(!$id) return "";
    $res=get_mall_id($id);
    $mall=$res['info'];
    foreach ($mall as $key => $val) {
        $info.=$val['title']."，";
    }
    $newstr = substr($info,0,strlen($info)-3);
    return $newstr;
}

/**
 * 获取会员
 * @date: 2015-7-19
 * @author: BillyZ
 * @return:
 */
function get_obj_fans($id)
{
	$key = "obj_fans_".$id;
	if(S($key))
	{
		return S($key);
	}
	else
	{
		$mFans = M('Fans');
		$fans = $mFans->where(array('id'=>$id))->find();
		S($key,$fans);
		return $fans;
	}
}

/**
 * 获取会员等级名称
 * @date: 2015-7-19
 * @author: BillyZ
 * @return:
 */
function get_gradename_id($gradeid)
{
	$key = "gradename_".$gradeid;
	if(S($key))
	{
		return S($key);
	}
	else
	{
		$mGrade = M('FansGrade');
		$grade = $mGrade->where(array('id'=>$gradeid))->find();
		S($key,$grade['name']);
		return $grade['name'];
	}
}

/**
 * 获取店铺列表
 * @date: 2015-7-19
 * @author: BillyZ
 * @code:所有:ALL,租车:ZC,保养:BY,维修:WX,精品:XC
 * @pid:产品id，不同产品请到config中配置参加活动的门店
 * @return:
 */
function get_shops($code,$pid = "")
{
	$mShop = M('Shop');
    $where['status']=1;
	$where['shop_type'] = 1;
	switch ($code)
	{
		case "ZC":
			$where['s_rent'] = 1;
			break;
		case "BY":
            if(16==$pid) //清凉一夏活动，活动多了有待改进
                $where['uid'] =array('in',C('PRODUCT_ACTIVITY.qingliang'));
			$where['s_maintain'] = 1;
			break;
		case "WX":
			$where['s_repair'] = 1;
			break;
		case "XC":
			$where['s_mall'] = 1;
			break;
		default:
			break;
	}
	$shops = $mShop->where($where)->select();
	return $shops;
}

/**
 * 获取店铺
 * @date: 2015-7-19
 * @author: BillyZ
 * @return:
 */
function get_obj_shop($id)
{
	$key = "obj_shop_".$id;
	if(S($key))
	{
		return S($key);
	}
	else
	{
		$mShop = M('Shop');
		$shop = $mShop->where(array('id'=>$id))->find();
		S($key,$shop,300);
		return $shop;
	}
}


/**
 * 获取店铺名称
 * @date: 2015-7-19
 * @author: BillyZ
 * @return:
 */
function get_shop_id($shopid)
{
	$shop = get_obj_shop($shopid);
    if($shopid==0){
        return '无';
    }
	return $shop['name'];
}

/**
 * 获取车辆名称
 * @date: 2015-7-19
 * @author: BillyZ
 * @return:
 */
function get_bike_id($bikeid)
{
	$mBike = M('Rentbike');
	$bike = $mBike->where(array('id'=>$bikeid))->find();
	return $bike['name'];
}
/**
*获取车辆mos
**/
function get_charger_mos($charger){
    $mBike=M('Charger');
    $bikeMos=$mBike->where(array('name'=>$charger))->find();
    return $bikeMos['deviceid'];
}
/**
 * 获取品牌
 * @date: 2015-7-19
 * @author: BillyZ
 * @return:
 */
function get_brand_id($bikeid)
{
	$mBrand = M('Brand');
	$brand = $mBrand->where(array('id'=>$bikeid))->find();
	return $brand['name'];
}

/**
 * 获取保养部件名称
 * @date: 2015-7-19
 * @author: BillyZ
 * @return:
 */
function get_maintain_parts_id($id)
{
	$mObj = M('MaintainParts');
	$obj = $mObj->where(array('id'=>$id))->find();
	return $obj['name'];
}

/**
 * 获取保养产品名称
 * @date: 2015-7-19
 * @author: BillyZ
 * @return:
 */
function get_maintain_product_id($productid)
{
	$mProduct = M('MaintainProduct');
	$product = $mProduct->where(array('id'=>$productid))->find();
	return $product['name'];
}

/**
 * 获取优惠券名称
 * @date: 2015-7-19
 * @author: BillyZ
 * @return:
 */
function get_coupon_id($id)
{
	$obj = get_obj_coupon($id);
	return $obj['name'];
}

/**
 * 获取优惠券
 * @date: 2015-7-19
 * @author: BillyZ
 * @return:
 */
function get_obj_coupon($id)
{
	$mObj = M('Coupon');
	$obj = $mObj->where(array('id'=>$id))->find();
	return $obj;
}

/**
* 增加订单日志
* @date: 2015-7-28
* @order_type:1:维修 2:保养 3:充电 4:租车 5:购车 6:其它
* @author: BillyZ
* @return:
*/
function logorder($uid,$orderid,$order_type,$memo)
{
	
	$log['uid'] = $uid;
	$log['orderid'] = $orderid;
	$log['order_type'] = $order_type;
	$log['memo'] = $memo;
	$log['create_time'] = NOW_TIME;
	
	M('OrderLog')->add($log);
}


/**
 * 积分记录类型
 * @date: 2015-7-18
 * @author: BillyZ
 * @return:
 */
function get_scorelog_type_id($id)
{
	switch($id)
	{
		case 0:
			return '扣减';
			break;
		case 1:
			return '增加';
			break;
		default:
			return '扣减';
			break;
	}
}

/**
 * 保养订单状态
 * @date: 2015-7-18
 * @author: BillyZ
 * @return:
 */
function get_maintainorder_status($id)
{
	$list = C('ORDER_STATUS_MAINTAIN');
	return $list[$id];
}

/**
 * 维修订单状态
 * @date: 2015-7-18
 * @author: BillyZ
 * @return:
 */
function get_repairorder_status($id)
{
	$list = C('ORDER_STATUS_REPAIR');
	return $list[$id];
}



/**
 * 后台租车订单状态
 * @date: 2015-7-18
 * @author: BillyZ
 * @return:
 */
function get_repairorder_astatus($id)
{
	$list = C('ORDER_STATUS_REPAIR_A');
	return $list[$id];
}


/**
 * 租车订单状态
 * @date: 2015-7-18
 * @author: BillyZ
 * @return:
 */
function get_rentorder_status($id)
{
	$list = C('ORDER_STATUS_RENT');
	return $list[$id];
}

/**
 * 租车订单状态
 * @date: 2015-7-18
 * @author: BillyZ
 * @return:
 */
function get_rentorder_astatus($id)
{
	$list = C('ORDER_STATUS_RENT_A');
	return $list[$id];
}

/**
 * 充电订单状态
 * @date: 2015-7-18
 * @author: BillyZ
 * @return:
 */
function get_chargeorder_status($id)
{
	$list = C('ORDER_STATUS_CHARGE');
	return $list[$id];
}

/**
 * 商城订单状态
 * @date: 2015-7-18
 * @author: BillyZ
 * @return://0:已下单 1：已取货 2：已支付 3：已取消
 */
function get_mallorder_status($id)
{
	$list = C('ORDER_STATUS_MALL');
	return $list[$id];
}


/**
 * 优惠券类型
 * @date: 2015-7-18
 * @author: BillyZ
 * @return:
 */
function get_coupon_type($id)
{
	$list = C('COUPON_TYPE');
	return $list[$id];
}

/**
 * 获取优惠券状态
 * @param string $status 
 * @return string
 */
function get_coupon_status($status=0){
	$list = C('COUPON_STATUS');
	return $list[$status];
}

/**
 * 订单类型
 * @date: 2015-7-18
 * @author: BillyZ
 * @return:
 */
function get_order_type($id)
{
	$list = C('ORDER_TYPE');
	return $list[$id];
}

/**
 * 动作类型
 * @date: 2015-7-18
 * @author: BillyZ
 * @return:
 */
function get_coupon_action_type($id)
{
	$list = C('COUPON_ACTION');
	return $list[$id];
}


/**
 * 商品状态
 * @date: 2015-11-6
 * @author: BillyZ
 * @return:
 */
function get_mall_status($id)
{
	$list = C('MALL_STATUS');
	return $list[$id];
}

/**
 * 维修配件类型
 * @return:
 */
function get_repair_parts_type_new($id)
{
	$type = get_obj_repair_parts_type($id);
	
	return $type['name'];
}

function get_repair_parts_type($id)
{
	$list = C('REPAIR_PARTS_TYPE');
	return $list[$id];
}

function get_obj_repair_parts_type($id)
{
	$key = "obj_repair_parts_type_".$id;
	if(S($key))
	{
		return S($key);
	}
	else
	{
		$mType = M('RepairPartsType');
		$type = $mType->where(array('id'=>$id))->find();
		S($key,$type,300);
		return $type;
	}
}

/**
 * 配件/部件大类
 * @return:
 */
function get_parts_type($id)
{
	$list = C('PARTS_TYPE');
	return $list[$id];
}

/**
 * 二手车状态
 * @date: 2015-7-18
 * @author: BillyZ
 * @return:
 */
function get_sechand_status($id)
{
	$list = C('SECHAND_STATUS');
	return $list[$id];
}

/**
 * 投诉状态
 * @date: 2015-10-15
 * @author: BillyZ
 * @return:
 */
function get_complain_status($id)
{
	$list = C('COMPLAIN_STATUS');
	return $list[$id];
}

/**
 * 结算打款状态
 * @date: 2015-11-10
 * @author: BillyZ
 * @return:
 */
function get_settle_status($id)
{
	$list = C('SETTLE_STATUS');
	return $list[$id];
}

/**
* 维修订单类型
* @date: 2015-7-20
* @author: BillyZ
* @return:
*/
function get_repair_type($id)
{
	if(1 == $id)
		return '预约';
	else if(2 == $id)
		return '到店';
	else
		return '一键救援';
}

/**
 * 获取用户组名称
 * @param $id
 * @return string
 * @author:
 */
function get_user_group_name($id) {
    switch($id)
    {
        case 0:
            return '超管';
            break;
        case 1:
            return '总部人员';
            break;
        case 2:
            return '运营商';
            break;
        case 3:
            return '门店';
            break;
        default:
            return '未知';
            break;
    }
}

/**
 * 获取订单类型描述
 * @param $type
 * @return string
 * @author:
 */
function get_order_type_desc($type) {
    switch($type)
    {
        case 'MaintainOrder':
            return '保养';
            break;
        case 'MallOrder':
            return '商城';
            break;
        case 'RentbikeOrder':
            return '租车';
            break;
        case 'RepairOrder':
            return '维修';
            break;
        case 'ChargeOrder':
            return '充电';
            break;
        default:
            return '未知';
            break;
    }
}

/**
* 星级说明
* @author: BillyZ
* @return:
*/
function get_star_desc($id)
{
	switch($id)
	{
		case 1:
			return '差';
		case 2:
			return '一般';
		case 3:
			return '良好';
		case 4:
			return '优秀';
		case 5:
			return '完美';
		default:
			return '优秀';
	}
}

/**
 * 隐藏部分手机号码
 * @param $mobile
 * @return string
 * @author:
 */
function get_safe_mobile($mobile)
{
	if(!empty($mobile) && strlen($mobile) == 11)
		return substr($mobile, 0, 3)."****".substr($mobile,7,4);
	else
		return $mobile;
}

/**
 * 隐藏部分身份证号码
 * @param $idcard
 * @return string
 * @author:
 */
function get_safe_idcard($idcard)
{
	if(!empty($idcard) && strlen($idcard) == 18)
		return substr($idcard, 0, 4)."*****".substr($idcard,14,4);
	else
		return $idcard;
}

/**
* 是否
* @date: 2015-7-31
* @author: BillyZ
* @return:
*/
function get_yesorno($val)
{
	if(1 == $val)
		return '是';
	else
		return '否';
}


/**
 * 兑换礼品类型
 * @date: 2015-9-14
 * @author: BillyZ
 * @return:
 */
function get_gift_type($val)
{
	if(1 == $val)
		return '实物';
	else if(2 == $val)
		return '优惠券';
	else
		return '未知';
}

/**
 * 生成卡券id
 * @date: 2015-7-31
 * @author: BillyZ
 * @return:
 */
function new_coupon_id($type,$openid)
{
	if($openid)
		$openid = strtoupper(substr($openid, 10, 3));

	$rand = rand(100,999);

	$time = NOW_TIME;

	return $type.$time.$openid.$rand;
}

/**
 * 生成阶段交易号
 * @date: 2015-11-7
 * @author: BillyZ
 * @return:
 */
function new_tradeno()
{
	$rand = rand(100,999);

	$time = NOW_TIME;

	return 'P'.$time.$rand;
}

/**
* 优惠券使用
* @date: 2015-11-2
* @author: BillyZ
* @return:
*/
function coupon_used($ucouponid)
{
	$mFansCoupon = M('FansCoupon');
	$fans_coupon = $mFansCoupon->where(array('id'=>$ucouponid))->find();
	$fans_coupon['status'] = 2;
	$fans_coupon['used_time'] = NOW_TIME;
	return $mFansCoupon->save($fans_coupon);
}

/**
 * 保存文件
 *
 * @param string $fileName 文件名（含相对路径）
 * @param string $text 文件内容
 * @return boolean
 */
function saveFile($fileName, $text) {
    if (!$fileName || !$text)
        return false;

    if (makeDir(dirname($fileName))) {
        if ($fp = fopen($fileName, "w")) {
            if (@fwrite($fp, $text)) {
                fclose($fp);
                return true;
            } else {
                fclose($fp);
                return false;
            }
        }
    }
    return false;
}

/**
 * 连续创建目录
 *
 * @param string $dir 目录字符串
 * @param int $mode 权限数字
 * @return boolean
 */
function makeDir($dir, $mode = "0777") {
    if (!$dir) return false;

    if(!file_exists($dir)) {
        return mkdir($dir,$mode,true);
    } else {
        return true;
    }
}

/**
* 获取应付价格
* @date: 2015-8-5
* @author: BillyZ
* @return:
*/
function get_pay_price($total,$coupon)
{
	if($coupon >= $total)
	{
		$pay_price = 0;
	}
	else
	{
		$pay_price = $total - $coupon;
	}
	
	return $pay_price;
}

/**
 * 统计会员未支付订单数量
 * @param $openid
 * @return mixed
 * @author:
 */
function count_unpaid_order($openid) {
    $count = S('count_unpaid_order_' . $openid);
    if (empty($count)) {
        $map['openid'] = $openid;
        $map['status'] = array('elt', 1);
        $countMaintain = M('MaintainOrder')->where($map)->count();
        $map['status'] = 0;
        $countMall = M('MallOrder')->where($map)->count();
        $map['status'] = 0;
        $countRentbike = M('RentbikeOrder')->where($map)->count();
        $map['status'] = array('elt', 1);
        $countRepair = M('RepairOrder')->where($map)->count();
        $map['status'] = 0;
        $countCharge = M('ChargeOrder')->where($map)->count();
        $count = $countMaintain + $countMall + $countRentbike + $countRepair + $countCharge;
        S('count_unpaid_order_' . $openid, $count, 600);
    }
    return empty($count) ? 0 : $count;
}

/**
 * 统计会员已支付订单数量
 * @param $openid
 * @return mixed
 * @author:
 */
function count_paid_order($openid) {
    $count = S('count_paid_order_' . $openid);
    if (empty($count)) {
        $map['openid'] = $openid;
        $map['status'] = 2;
        $countMaintain = M('MaintainOrder')->where($map)->count();
        $map['status'] = 2;
        $countMall = M('MallOrder')->where($map)->count();
        $map['status'] = array('gt', 0);
        $countRentbike = M('RentbikeOrder')->where($map)->count();
        $map['status'] = 2;
        $countRepair = M('RepairOrder')->where($map)->count();
        $map['status'] = array('in', '1,2');
        $countCharge = M('ChargeOrder')->where($map)->count();
        $count = $countMaintain + $countMall + $countRentbike + $countRepair + $countCharge;
        S('count_paid_order_' . $openid, $count, 600);
    }
    return empty($count) ? 0 : $count;
}


/**
 * 统计会员二手车数量
 * @param $openid
 * @return mixed
 * @author:
 */
function count_sechand_bikes($openid) {
	$count = S('count_sechand_bikes_' . $openid);
	if (empty($count)) {
		$map['openid'] = $openid;
		$count = M('Sechand')->where($map)->count(); 
		S('count_sechand_bikes_' . $openid, $count, 600);
	}
	return empty($count) ? 0 : $count;
}

/**
 * POST 请求
 * @param string $url
 * @param array $param
 * @return string content
 */
function http_post($url, $param)
{
    $oCurl = curl_init();
    if (stripos($url, "https://") !== FALSE) {
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($oCurl, CURLOPT_SSL_VERIFYHOST, false);
    }
    if (is_string($param)) {
        $strPOST = $param;
    } else {
        $aPOST = array();
        foreach ($param as $key => $val) {
            $aPOST[] = $key . "=" . urlencode($val);
        }
        $strPOST = join("&", $aPOST);
    }
    curl_setopt($oCurl, CURLOPT_URL, $url);
    curl_setopt($oCurl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($oCurl, CURLOPT_POST, true);
    curl_setopt($oCurl, CURLOPT_POSTFIELDS, $strPOST);
    $sContent = curl_exec($oCurl);
    $aStatus = curl_getinfo($oCurl);
    curl_close($oCurl);
    if (intval($aStatus["http_code"]) == 200) {
        return $sContent;
    } else {
        return false;
    }
}

/**
 * 描述：企信通平台接口 http://web.cnsms.cn
 * 短信发送
 * @param $uid : 账号id
 * @param $pwd ：32位MD5加密
 * @param $mobile ：同时发送给多个号码时,号码之间用英文半角逗号分隔(,)如:13972827282,13072827282,02185418874
 * @param $content ：发送内容需要进行URL字符标准化转码
 * @return string
 */
function api_sms_send($uid, $pwd, $mobile, $content)
{
    $url = "http://api.cnsms.cn/?ac=send";
    $data = array(
        "uid" => $uid,
        "pwd" => $pwd,
        "mobile" => $mobile,
        "content" => urlencode($content)
    );
    $resultCode = http_post($url, $data);
    /*$result = array(
        "code" => 40000 + $resultCode
    );*/

    return $resultCode;
}

/**
* 清空会员缓存
* @date: 2015-8-8
* @author: BillyZ
* @return:
*/
function flush_fans($openid)
{
	S('obj_fans_openid_'.$openid,null);
}

/**
 * @desc 根据两点间的经纬度计算距离
 * @param float $lat 纬度值
 * @param float $lng 经度值
 */
function getDistance($lat1, $lng1, $lat2, $lng2)
{
	$earthRadius = 6367000; //approximate radius of earth in meters

	/*
	 Convert these degrees to radians
	to work with the formula
	*/

	$lat1 = ($lat1 * pi() ) / 180;
	$lng1 = ($lng1 * pi() ) / 180;

	$lat2 = ($lat2 * pi() ) / 180;
	$lng2 = ($lng2 * pi() ) / 180;

	/*
	 Using the
	Haversine formula

	http://en.wikipedia.org/wiki/Haversine_formula

	calculate the distance
	*/

	$calcLongitude = $lng2 - $lng1;
	$calcLatitude = $lat2 - $lat1;
	$stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);
	$stepTwo = 2 * asin(min(1, sqrt($stepOne)));
	$calculatedDistance = $earthRadius * $stepTwo;

	return round($calculatedDistance);
}



/**
 * 发放优惠券
 * @date: 2015-8-5
 * @author: BillyZ
 * @return:
 */
function giveCoupon($uid,$action,$canalid=0)
{
	//1.查询需要发放的优惠券
	$lstConfigs = M('CouponConfig')->where(array('actions'=>$action,'status'=>1))->select();

	if($lstConfigs)
	{
		$mFansCoupon = M('FansCoupon');
		
		//增加发放渠道处理
		$fans_canal = 0;
		$fans = get_obj_fans($uid);
		if($fans)
		{
			$fans_canal = $fans['canalid'];
		}
		
		foreach($lstConfigs as $config)
		{
			if(!empty($config['canals']))
			{
				if(0 < $fans_canal)
				{
					//设置过发放渠道并且用户是渠道关注用户
					if(in_array($fans_canal, explode(',', $config['canals'])))
					{
						//如果在渠道内则发放给该用户
						give_coupon_one($uid,$config['couponid'],$config['num'],$canalid);
					}
				}
			}
			else
			{
				//没有设定渠道则全部发放
				//2.发放(支持多张)
				give_coupon_one($uid,$config['couponid'],$config['num'],$canalid);

			}
		}
	}
}


/**
 * 发一张券
 * @date: 2015-9-21
 * @author: BillyZ
 * @param $uid
 * @param $couponid
 */
function give_coupon_one($uid,$couponid,$num = 1,$canalid=0)
{
	$mFansCoupon = M('FansCoupon');
	$fans = get_obj_fans($uid);
	$coupon = get_obj_coupon($couponid);
	$fans_coupon['uid'] = $uid;
	$fans_coupon['cid'] = $couponid;
	$fans_coupon['cname'] = $coupon['name'];
	$fans_coupon['total_times'] = 1;
	$fans_coupon['remain_times'] = 1;
	$fans_coupon['create_time'] = NOW_TIME;
	$fans_coupon['end_time'] = $fans_coupon['create_time'] + $coupon['days'] * 24 * 3600;
	$fans_coupon['status'] = 1;
	$fans_coupon['service_type'] = $coupon['service_type'];
	$fans_coupon['code'] = new_coupon_id($coupon['service_type'], $fans['openid']);
	$fans_coupon['face'] = $coupon['face'];
    $fans_coupon['arrive_face'] = $coupon['arrive_face'];
    $fans_coupon['reduce_face'] = $coupon['reduce_face'];
    $fans_coupon['canalid'] = $canalid;
    for($i=0;$i<$num;$i++){
        $mFansCoupon->add($fans_coupon);
    }
}

/**
 * 通过transid获取订单表名
 * @date: 2015-10-15
 * @author: BillyZ
 * @return:
 */
function get_model_by_transid($transid)
{
	$model = '';
	if(!empty($transid))
	{
		$flag = substr($transid,0,2);
	}
	else
		return $model;
	
	switch($flag)
	{
		case "CD":
			$model = 'ChargeOrder';
			break;
		case "BY":
			$model = 'MaintainOrder';
			break;
		case "WX":
			$model = 'RepairOrder';
			break;
		case "XC":
			$model = 'MallOrder';
			break;
		case "ZC":
			$model = 'RentbikeOrder';
			break;
		default:
			break;
	}
	
	return $model;
}

/**
* 通过transid获取订单信息
* @date: 2015-10-15
* @author: BillyZ
* @return:
*/
function get_order_by_transid($transid)
{
	$model = get_model_by_transid($transid);
	$order = M($model)->where(array('transid'=>$transid))->find();
	return $order;
}


/**
 * 打印商品订单属性
 * @date: 2015-10-16
 * @author: Ketory
 * @return:
 */
function show_mall_attr($obj){
    $attrLen = '';
    $attrdate=json_decode($obj);
    foreach($attrdate as $vo){
        $attrLen .='-'.$vo->name;
    }
    return $attrLen;
}


/**
* 添加请求记录
* @date: 2015-10-27
* @author: BillyZ
* @return:
*/
function add_payrequest($transid,$requestid)
{

	$log['transid'] = $transid;
	$log['requestid'] = $requestid;
	$log['create_time'] = NOW_TIME;
	$log['status'] = 1;

	M('PayRequest')->add($log);
}

/**
 * 通过支付请求单号获取订单号
 * @date: 2015-10-27
 * @author: BillyZ
 * @return:
 */
function get_transid_by_requestid($requestid)
{
	$request = M('PayRequest')->where(array('requestid'=>$requestid))->find();
	if($request)
		return $request['transid'];
	else
		return $requestid;
}

/**
 * 通过支付请求单号获取订单号
 * @date: 2015-10-27
 * @author: BillyZ
 * @return:
 */
function get_array_by_requestid($requestid)
{
	$request = M('PayRequest')->where(array('requestid'=>$requestid))->find();
	if($request)
		return $request;
	else
		return '';
}

/**
* 根据订单号找到有效的支付请求单号
* @date: 2015-10-27
* @author: BillyZ
* @return:
*/
function get_requestid_by_transid($transid)
{
	$request = M('PayRequest')->where(array('transid'=>$transid,'status'=>2))->find();
	if($request)
		return $request['requestid'];
	else 
		return '';
}

/**
 * 统计备注信息数量
 * @date: 2015-10-27
 * @author: Ketory
 * @return:
 */
function count_remark($oid,$type)
{
    return M('OrderLog')->where(array('orderid'=>$oid,'order_type'=>$type))->count();
}


/**
 * 添加提醒队列记录
 * @date: 2015-11-27
 * @author: BillyZ
 * @return:
 */
function add_remindlog($orderid,$openid,$remindtime,$expiretime)
{
    $log['oid'] = $orderid;
    $log['name'] = '保养提醒';
    $log['openid'] = $openid;
    $log['status'] = 0;
    $log['createtime'] = NOW_TIME;
    $log['remindtime'] = $remindtime;
    $log['expiretime'] = $expiretime;

    M('RemindLog')->add($log);
}


/*
 *秒换算时间
*/
function Sec2Time($time1,$time2){
    $time=$time1-$time2;
    if($time && $time > 0){
    $value = array(
      "hours" => 0, "minutes" => 0, "seconds" => 0,
    );
    if($time >= 3600){
      $value["hours"] = floor($time/3600);
      $time = ($time%3600);
      $massge=$value["hours"] ."小时";
    }
    if($time >= 60){
      $value["minutes"] = floor($time/60);
      $time = ($time%60);
      $massge.=$value["minutes"]."分";
    }
    $value["seconds"] = floor($time);
    $massge.=$value["seconds"]."秒";
    if($value["hours"]<=0 ){
        $massge="<span style='color:red'>".$massge."</span>";
    }
    Return $massge;
     }else{
    return (bool) FALSE;
    }
 }

function get_charge_price($outletid)
{
    $result = C('CHARGE_PRICE');
    $chargeid = M('Outlet')->where(array('id'=>$outletid))->getField("chargerid");
    $cityid = M('Charger')->where(array('id'=>$chargeid))->getField("cityid");
    $city_price =M('City')->where(array('id'=>$cityid))->getField('chargeprice');
    if($city_price>0)
    {
        $result = $city_price;
    }
    return $result;
}