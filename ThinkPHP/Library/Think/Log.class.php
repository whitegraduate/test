<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Think;
/**
 * 日志处理类
 */
class Log {

    // 日志级别 从上到下，由低到高
    const EMERG     = 'EMERG';  // 严重错误: 导致系统崩溃无法使用
    const ALERT     = 'ALERT';  // 警戒性错误: 必须被立即修改的错误
    const CRIT      = 'CRIT';  // 临界值错误: 超过临界值的错误，例如一天24小时，而输入的是25小时这样
    const ERR       = 'ERR';  // 一般错误: 一般性错误
    const WARN      = 'WARN';  // 警告性错误: 需要发出警告的错误
    const NOTICE    = 'NOTIC';  // 通知: 程序可以运行但是还不够完美的错误
    const INFO      = 'INFO';  // 信息: 程序输出信息
    const DEBUG     = 'DEBUG';  // 调试: 调试信息
    const SQL       = 'SQL';  // SQL：SQL语句 注意只在调试模式开启时有效

    // 日志信息
    static protected $log       =  array();

    // 日志存储
    static protected $storage   =   null;

    // 日志初始化
    static public function init($config=array()){
        $type   =   isset($config['type'])?$config['type']:'File';
        $class  =   strpos($type,'\\')? $type: 'Think\\Log\\Driver\\'. ucwords(strtolower($type));           
        unset($config['type']);
        self::$storage = new $class($config);
    }

    /**
     * 记录日志 并且会过滤未经设置的级别
     * @static
     * @access public
     * @param string $message 日志信息
     * @param string $level  日志级别
     * @param boolean $record  是否强制记录
     * @return void
     */
    static function record($message,$level=self::ERR,$record=false) {
        if($record || false !== strpos(C('LOG_LEVEL'),$level)) {
            self::$log[] =   "{$level}: {$message}\r\n";
        }
    }

    /**
     * 日志保存
     * @static
     * @access public
     * @param integer $type 日志记录方式
     * @param string $destination  写入目标
     * @return void
     */
    static function save($type='',$destination='') {
        if(empty(self::$log)) return ;

        if(empty($destination))
            $destination = C('LOG_PATH').date('y_m_d').'.log';
        if(!self::$storage){
            $type = $type?:C('LOG_TYPE');
            $class  =   'Think\\Log\\Driver\\'. ucwords($type);
            self::$storage = new $class();            
        }
        $message    =   implode('',self::$log);
        self::$storage->write($message,$destination);
        // 保存后清空日志缓存
        self::$log = array();
    }

    /**
     * 日志直接写入
     * @static
     * @access public
     * @param string $message 日志信息
     * @param string $level  日志级别
     * @param integer $type 日志记录方式
     * @param string $destination  写入目标
     * @return void
     */
    static function write($message,$level=self::ERR,$type='',$destination='') {
        if(!self::$storage){
            $type = $type?:C('LOG_TYPE');
            $class  =   'Think\\Log\\Driver\\'. ucwords($type);
            self::$storage = new $class();            
        }
        if(empty($destination))
            $destination = C('LOG_PATH').date('y_m_d').'.log';        
        self::$storage->write("{$level}: {$message}", $destination);
    }

    static function writeNotime($message,$level=self::ERR,$type='',$destination='') {
        if(!self::$storage){
            $type = $type?:C('LOG_TYPE');
            $class  =   'Think\\Log\\Driver\\'. ucwords($type);
            self::$storage = new $class();
        }
        if(empty($destination))
            $destination = C('LOG_PATH').date('y_m_d').'.log';
        self::$storage->writeNoTime("{$level}: {$message}", $destination);
    }
    /**
     * weixin日志直接写入
     * @static
     * @access public
     * @param string $message 日志信息
     * @param string $level  日志级别
     * @param integer $type 日志记录方式
     * @param string $destination  写入目标
     * @return void
     */
    static function weixin_info($message,$level=self::ERR,$type='',$destination='') {
        if(!self::$storage){
            $type = $type?:C('LOG_TYPE');
            $class  =   'Think\\Log\\Driver\\'. ucwords($type);
            self::$storage = new $class();            
        }
        if(empty($destination))
            $destination = C('LOG_PATH').'wx'.date('y_m_d').'.log';        
        self::$storage->write("{$level}: {$message}", $destination);
    }

    /**
     * 快速日志记录
     * @static
     * @access public
     * @param string $message 日志信息
     * @param string $level  日志级别
     * @param string $destination  写入目标
     * @return void
     */
    static function Qwrite($message,$destination='',$level=self::ERR) {
        if(!self::$storage){
            $type = C('LOG_TYPE');
            $class  =   'Think\\Log\\Driver\\'. ucwords($type);
            self::$storage = new $class();
        }
        if(empty($destination))
            $destination = C('LOG_PATH').'Q'.date('y_m_d').'.log';
        self::$storage->writeNoTime("{$level}: {$message}", $destination);
    }

    /**
     * 租车MOS管日志直接写入
     * @static
     * @access public
     * @param string $message 日志信息
     * @param string $level  日志级别
     * @param integer $type 日志记录方式
     * @param string $destination  写入目标
     * @return void
     */
    static function rentbike_info($message,$level=self::ERR,$type='',$destination='') {
        if(!self::$storage){
            $type = $type?:C('LOG_TYPE');
            $class  =   'Think\\Log\\Driver\\'. ucwords($type);
            self::$storage = new $class();
        }
        if(empty($destination))
            $destination = C('LOG_PATH').'mos'.date('y_m_d').'.log';
        self::$storage->writeNoTime("{$level}: {$message}", $destination);
    }


    /**
     * job日志直接写入
     * @static
     * @access public
     * @param string $message 日志信息
     * @param string $level  日志级别
     * @param integer $type 日志记录方式
     * @param string $destination  写入目标
     * @return void
     */
    static function job_info($message,$level=self::ERR,$type='',$destination='') {
    	if(!self::$storage){
    		$type = $type?:C('LOG_TYPE');
    		$class  =   'Think\\Log\\Driver\\'. ucwords($type);
    		self::$storage = new $class();
    	}
    	if(empty($destination))
    		$destination = C('LOG_PATH').'job'.date('y_m_d').'.log';
    	self::$storage->write("{$level}: {$message}", $destination);
    }



    /**
     * Access_token日志直接写入，无时间日期
     * @static
     * @access public
     * @param string $message 日志信息
     * @param string $level  日志级别
     * @param integer $type 日志记录方式
     * @param string $destination  写入目标
     * @return void
     */
    static function getWeixinInfo($message,$level=self::ERR,$type='',$destination='') {
        if(!self::$storage){
            $type = $type?:C('LOG_TYPE');
            $class  =   'Think\\Log\\Driver\\'. ucwords($type);
            self::$storage = new $class();
        }
        if(empty($destination))
            $destination = C('LOG_PATH')."Get_Weixin_Info".date('y_m_d').'.log';
        self::$storage->writeNoTime("{$level}: {$message}", $destination);
    }
    /**
     * 创建文件夹的日志
     * @author Timo  <zhushuliang@seersee.com>
     */
    static function my_log($message,$destination='test',$level=self::ERR,$type='') {
        if(!self::$storage){
            $type = $type?:C('LOG_TYPE');
            $class  =   'Think\\Log\\Driver\\'. ucwords($type);
            self::$storage = new $class();            
        }
        $destination = C('LOG_PATH')."/".$destination."/".date('y_m_d').'.log';        
        self::$storage->write("{$level}: {$message}", $destination);
    }
}