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


/**
 * 描述：前台充电服务
 * Class ChargeController
 * @package Home\Controller
 */
class NewsController extends WapController {

    public function __construct() {
        parent::__construct(); 
    }

    /**
     *
     * @author: Nice <hupeipei@seersee.com>
     */
    public function reply() {
        $replyId = I('id');
        $reply = M('Reply')->where(array('id' => $replyId))->find();
        if (empty($reply) || $reply['reply_type'] != 2) {
            $this->error('非法链接！');
        }
        $this->assign('info', $reply);
        $this->display();
    }
}