<?php

namespace Addons\Ssq;
use Common\Controller\Addon;

/**
 * 省市区插件
 * @author 无名
 */

    class SsqAddon extends Addon{

        public $info = array(
            'name'=>'Ssq',
            'title'=>'省市区',
            'description'=>'这是一个临时描述',
            'status'=>1,
            'author'=>'无名',
            'version'=>'0.1'
        );

        public function install(){
            return true;
        }

        public function uninstall(){
            return true;
        }

        //实现的province-city-area钩子方法
        public function province_city_area($param){
            $citylist = M('City')->select();
            $this->assign('citylist',$citylist);
            $this->assign('cityid',$param['cityid']);
            $this->assign('areaid',$param['areaid']);
            $this->display('ssq');
        }
    }