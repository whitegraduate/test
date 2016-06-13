<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com>
// +----------------------------------------------------------------------

namespace Admin\Model;
use Think\Model;
/**
 * 配置模型
 * @author MC <zhouyibin@seersee.com>
 */

class SettleLogModel extends Model {
    protected $_validate = array(
        array('shopid', 'require', '请选择门店', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('openid', 'require', '请先绑定打款微信号', self::MUST_VALIDATE, 'regex', self::MODEL_BOTH),
        array('money_total', array(0.01,20000), '金额不正确', self::MUST_VALIDATE, 'between', self::MODEL_BOTH),
    );

    protected $_auto = array(
        array('create_time', NOW_TIME, self::MODEL_INSERT),
        array('status', '1', self::MODEL_BOTH),
        array('tradeno', 'new_tradeno', self::MODEL_INSERT,'function'),
    ); 
}
