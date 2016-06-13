<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: yangweijie <yangweijiester@gmail.com> <code-tech.diandian.com>
// +----------------------------------------------------------------------
namespace Addons\PictureForAdmin;
use Common\Controller\Addon;

/**
 * 系统环境信息插件
 * @author thinkphp
 */
class PictureForAdminAddon extends Addon{

    public $info = array(
        'name' => 'PictureForAdmin',
        'title' => '后台图片上传插件',
        'description' => '用于后台上传图片',
        'status' => 1,
        'author' => 'thinkphp',
        'version' => '0.1'
    );

    public function install(){
        return true;
    }

    public function uninstall(){
        return true;
    }

    /**
     * 后台上传图片的钩子
     * @param array('name'=>'表单name','value'=>'表单对应的值')
     */
    public function adminPictureUpload($data){
        $this->assign('addons_data', $data);
        $this->assign('addons_config', $this->getConfig());
        $this->display('content');
    }
}