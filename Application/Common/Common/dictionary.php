<?php
// +----------------------------------------------------------------------
// | 物业2.0 - Estate
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2015 http://www.seersee.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Nice <hupeipei@seersee.com>
// +----------------------------------------------------------------------

/**
 * 系统公共库文件
 * 主要定义字典类函数
 */

/**
 * 获取布尔值描述
 * @param $value
 * @return string
 * @author: Nice <hupeipei@seersee.com>
 */
function get_bool_desc($value) {
    switch ($value){
        case 0:
            return "否";
        case 1:
            return "是";
        default:
            return "否";
    }
}

/**
 * 获取性别描述
 * @param $value
 * @return string
 * @author: Nice <hupeipei@seersee.com>
 */
function get_sex_desc($value) {
    switch ($value){
        case 1:
            return "男";
        case 2:
            return "女";
        default:
            return "未定义";
    }
}

/**
 * 获取通用的状态描述
 * @param $value
 * @return string
 * @author: Nice <hupeipei@seersee.com>
 */
function get_status_desc($value) {
    switch ($value){
        case 1:
            return "正常";
        case 0:
            return "禁用";
        default:
            return "未定义";
    }
}

/**
 * 获取字典数据的描述
 * @param $dic
 * @param $value
 * @return mixed
 * @author: Nice <hupeipei@seersee.com>
 */
function get_dic_desc($dic, $value) {
    $list = C($dic);
    foreach ($list as $k => $v) {
        if ($k == $value) {
            return $v;
        }
    }
    return $value;
}

/**
 * 获取字典数据的下拉列表option
 * @param $dic
 * @return mixed
 * @author: Nice <hupeipei@seersee.com>
 */
function get_dic_options($dic) {
    $list = C($dic);
    $options = '';
    foreach ($list as $k => $v) {
        $options .= '<option value="' . $k . '">' . $v . '</option>';
    }
    return $options;
}