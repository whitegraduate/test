<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------
namespace Admin\Controller;
use Think\Controller;
use Admin\Model\AuthRuleModel;
use Admin\Model\AuthGroupModel;
use Think\Log;
use OT\Wechat\Wechat;
use OT\Wechat\Refund;
use OT\Wechat\SDKRuntimeException;
use OT\Wechat\UnifiedOrder;
use OT\Wechat\PayConfig;
use OT\Wechat\JsApi;
use OT\Wechat\Transfer;

/**
 * 后台首页控制器
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
class AdminController extends Controller {

	protected $gShop; //店铺
	protected $gShopid; //店铺id
	protected $gShopname; //店铺id
	protected $gUid; //用户id
    protected $weObj; 
    /**
     * 后台控制器初始化
     */
    protected function _initialize(){
        // 获取当前用户ID
        if(defined('UID')) return ;
        define('UID',is_login());
        if( !UID ){// 还没登录 跳转到登录页面
            $this->redirect('Public/login');
        }
        /* 读取数据库中的配置 */
        $config =   S('DB_CONFIG_DATA');
        if(!$config){
            $config =   api('Config/lists');
            S('DB_CONFIG_DATA',$config);
        }
        C($config); //添加配置

        // 是否是超级管理员
        define('IS_ROOT',   is_administrator());
        if(!IS_ROOT && C('ADMIN_ALLOW_IP')){
            // 检查IP地址访问
            if(!in_array(get_client_ip(),explode(',',C('ADMIN_ALLOW_IP')))){
                $this->error('403:禁止访问');
            }
        }
        // 是否门店
        $shop = get_shop_uid(UID);
        $this->gShop = $shop;
        $this->gShopid = $shop['id'];
        $this->gShopname = $shop['name'];
        $this->gUid = UID;
        
        //微信
        if (!$this->weObj) {
        	$this->weObj = new Wechat(C('WEIXIN_CONFIG'));
        	Log::weixin_info('创建了Wechat对象admin');
        }
        
        // 检测系统权限
        if(!IS_ROOT){
            $access =   $this->accessControl();
            if ( false === $access ) {
                $this->error('403:禁止访问');
            }elseif(null === $access ){
                //检测访问权限
                $rule  = strtolower(MODULE_NAME.'/'.CONTROLLER_NAME.'/'.ACTION_NAME);

                if ( !$this->checkRule($rule,array('in','1,2')) ){
                    $this->error('未授权访问!');
                }else{
                    // 检测分类及内容有关的各项动态权限
                    $dynamic    =   $this->checkDynamic();
                    if( false === $dynamic ){
                        $this->error('未授权访问!');
                    }
                }
            }
        }        

        $this->assign('__MENU__', $this->getMenus());
    }

    /**
     * 权限检测
     * @param string  $rule    检测的规则
     * @param string  $mode    check模式
     * @return boolean
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final protected function checkRule($rule, $type=AuthRuleModel::RULE_URL, $mode='url'){
        static $Auth    =   null;
        if (!$Auth) {
            $Auth       =   new \Think\Auth();
        }

        Log::weixin_info('rule='.$rule.',UID='.UID.',type='.$type.',mode='.$mode);
        if(!$Auth->check($rule,UID,$type,$mode)){
            return false;
        }
        return true;
    }

    /**
     * 检测是否是需要动态判断的权限
     * @return boolean|null
     *      返回true则表示当前访问有权限
     *      返回false则表示当前访问无权限
     *      返回null，则表示权限不明
     *
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    protected function checkDynamic(){}


    /**
     * action访问控制,在 **登陆成功** 后执行的第一项权限检测任务
     *
     * @return boolean|null  返回值必须使用 `===` 进行判断
     *
     *   返回 **false**, 不允许任何人访问(超管除外)
     *   返回 **true**, 允许任何管理员访问,无需执行节点权限检测
     *   返回 **null**, 需要继续执行节点权限检测决定是否允许访问
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final protected function accessControl(){
        $allow = C('ALLOW_VISIT');
        $deny  = C('DENY_VISIT');
        $check = strtolower(CONTROLLER_NAME.'/'.ACTION_NAME);
        if ( !empty($deny)  && in_array_case($check,$deny) ) {
            return false;//非超管禁止访问deny中的方法
        }
        if ( !empty($allow) && in_array_case($check,$allow) ) {
            return true;
        }
        return null;//需要检测节点权限
    }

    /**
     * 对数据表中的单行或多行记录执行修改 GET参数id为数字或逗号分隔的数字
     *
     * @param string $model 模型名称,供M函数使用的参数
     * @param array  $data  修改的数据
     * @param array  $where 查询时的where()方法的参数
     * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     *
     * @author 朱亚杰  <zhuyajie@topthink.net>
     */
    final protected function editRow ( $model ,$data, $where , $msg = null){
        $id    = array_unique((array)I('id',0));
        $id    = is_array($id) ? implode(',',$id) : $id;
        //如存在id字段，则加入该条件
        $fields = M($model)->getDbFields();
        if(in_array('id',$fields) && !empty($id)){
            $where = array_merge( array('id' => array('in', $id )) ,(array)$where );
        }

        $msg   = array_merge( array( 'success'=>'操作成功！', 'error'=>'操作失败！', 'url'=>'' ,'ajax'=>IS_AJAX) , (array)$msg );
        if( M($model)->where($where)->save($data)!==false ) {
            $this->success($msg['success'],$msg['url'],$msg['ajax']);
        }else{
            $this->error($msg['error'],$msg['url'],$msg['ajax']);
        }
    }

    /**
     * 禁用条目
     * @param string $model 模型名称,供D函数使用的参数
     * @param array  $where 查询时的 where()方法的参数
     * @param array  $msg   执行正确和错误的消息,可以设置四个元素 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     *
     * @author 朱亚杰  <zhuyajie@topthink.net>
     */
    protected function forbid ( $model , $where = array() , $msg = array( 'success'=>'状态禁用成功！', 'error'=>'状态禁用失败！')){
        $data    =  array('status' => 0);
        $this->editRow( $model , $data, $where, $msg);
    }

    /**
     * 恢复条目
     * @param string $model 模型名称,供D函数使用的参数
     * @param array  $where 查询时的where()方法的参数
     * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     *
     * @author 朱亚杰  <zhuyajie@topthink.net>
     */
    protected function resume (  $model , $where = array() , $msg = array( 'success'=>'状态恢复成功！', 'error'=>'状态恢复失败！')){
        $data    =  array('status' => 1);
        $this->editRow(   $model , $data, $where, $msg);
    }

    /**
     * 还原条目
     * @param string $model 模型名称,供D函数使用的参数
     * @param array  $where 查询时的where()方法的参数
     * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     * @author huajie  <banhuajie@163.com>
     */
    protected function restore (  $model , $where = array() , $msg = array( 'success'=>'状态还原成功！', 'error'=>'状态还原失败！')){
        $data    = array('status' => 1);
        $where   = array_merge(array('status' => -1),$where);
        $this->editRow(   $model , $data, $where, $msg);
    }

    /**
     * 条目假删除
     * @param string $model 模型名称,供D函数使用的参数
     * @param array  $where 查询时的where()方法的参数
     * @param array  $msg   执行正确和错误的消息 array('success'=>'','error'=>'', 'url'=>'','ajax'=>false)
     *                     url为跳转页面,ajax是否ajax方式(数字则为倒数计时秒数)
     *
     * @author 朱亚杰  <zhuyajie@topthink.net>
     */
    protected function delete ( $model , $where = array() , $msg = array( 'success'=>'删除成功！', 'error'=>'删除失败！')) {
        $data['status']         =   -1;
        $this->editRow(   $model , $data, $where, $msg);
    }

    /**
     * 设置一条或者多条数据的状态
     */
    public function setStatus($Model=CONTROLLER_NAME){

        $ids    =   I('request.ids');
        $status =   I('request.status');
        if(empty($ids)){
            $this->error('请选择要操作的数据');
        }

        $map['id'] = array('in',$ids);
        switch ($status){
            case -1 :
                $this->delete($Model, $map, array('success'=>'删除成功','error'=>'删除失败'));
                break;
            case 0  :
                $this->forbid($Model, $map, array('success'=>'禁用成功','error'=>'禁用失败'));
                break;
            case 1  :
                $this->resume($Model, $map, array('success'=>'启用成功','error'=>'启用失败'));
                break;
            default :
                $this->error('参数错误');
                break;
        }
    }

    /**
     * 获取控制器菜单数组,二级菜单元素位于一级菜单的'_child'元素中
     * @author 朱亚杰  <xcoolcc@gmail.com>
     */
    final public function getMenus($controller=CONTROLLER_NAME){
        $menus  =   session('ADMIN_MENU_LIST.'.$controller);
        
        if(empty($menus)){
            // 获取主菜单
            $where['pid']   =   0;
            $where['hide']  =   0;
            if(!C('DEVELOP_MODE')){ // 是否开发者模式
                $where['is_dev']    =   0;
            }
            $menus['main']  =   M('Menu')->where($where)->order('sort asc')->field('id,title,url')->select();

            $menus['child'] =   array(); //设置子节点
            foreach ($menus['main'] as $key => $item) {
                // 判断主菜单权限
                if ( !IS_ROOT && !$this->checkRule(strtolower(MODULE_NAME.'/'.$item['url']),AuthRuleModel::RULE_MAIN,null) ) {
                    unset($menus['main'][$key]);
                    continue;//继续循环
                }
                if(strtolower(CONTROLLER_NAME.'/'.ACTION_NAME)  == strtolower($item['url'])){
                    $menus['main'][$key]['class']='current';
                }
            }

            // 查找当前子菜单
            $wheres = "pid !=0 AND url like '%{$controller}/".ACTION_NAME."%'";
            //var_dump($wheres);
            $pid = M('Menu')->where("pid !=0 AND url like '%{$controller}/".ACTION_NAME."%'")->getField('pid');
          
            if($pid){
                // 查找当前主菜单
                $nav =  M('Menu')->find($pid);
                if($nav['pid']){
                    $nav    =   M('Menu')->find($nav['pid']);
                }
                foreach ($menus['main'] as $key => $item) {
                    // 获取当前主菜单的子菜单项
                    if($item['id'] == $nav['id']){
                        $menus['main'][$key]['class']='current';
                        //生成child树
                        $groups = M('Menu')->where(array('group'=>array('neq',''),'pid' =>$item['id']))->distinct(true)->getField("group",true);
                        //获取二级分类的合法url
                        $where          =   array();
                        $where['pid']   =   $item['id'];
                        $where['hide']  =   0;
                        if(!C('DEVELOP_MODE')){ // 是否开发者模式
                            $where['is_dev']    =   0;
                        }
                        $second_urls = M('Menu')->where($where)->getField('id,url');

                        if(!IS_ROOT){
                            // 检测菜单权限
                            $to_check_urls = array();
                            foreach ($second_urls as $key=>$to_check_url) {
                                if( stripos($to_check_url,MODULE_NAME)!==0 ){
                                    $rule = MODULE_NAME.'/'.$to_check_url;
                                }else{
                                    $rule = $to_check_url;
                                }
                                if($this->checkRule($rule, AuthRuleModel::RULE_URL,null))
                                    $to_check_urls[] = $to_check_url;
                            }
                        }
                        // 按照分组生成子菜单树
                        foreach ($groups as $g) {
                            $map = array('group'=>$g);
                            if(isset($to_check_urls)){
                                if(empty($to_check_urls)){
                                    // 没有任何权限
                                    continue;
                                }else{
                                    $map['url'] = array('in', $to_check_urls);
                                }
                            }
                            $map['pid']     =   $item['id'];
                            $map['hide']    =   0;
                            if(!C('DEVELOP_MODE')){ // 是否开发者模式
                                $map['is_dev']  =   0;
                            }
                            $menuList = M('Menu')->where($map)->field('id,pid,title,url,tip')->order('sort asc')->select();
                            $menus['child'][$g] = list_to_tree($menuList, 'id', 'pid', 'operater', $item['id']);
                        }
                    }
                }
            }
            session('ADMIN_MENU_LIST.'.$controller,$menus);
        }
        return $menus;
    }

    /**
     * 返回后台节点数据
     * @param boolean $tree    是否返回多维数组结构(生成菜单时用到),为false返回一维数组(生成权限节点时用到)
     * @retrun array
     *
     * 注意,返回的主菜单节点数组中有'controller'元素,以供区分子节点和主节点
     *
     * @author 朱亚杰 <xcoolcc@gmail.com>
     */
    final protected function returnNodes($tree = true){
        static $tree_nodes = array();
        if ( $tree && !empty($tree_nodes[(int)$tree]) ) {
            return $tree_nodes[$tree];
        }
        if((int)$tree){
            $list = M('Menu')->field('id,pid,title,url,tip,hide')->order('sort asc')->select();
            foreach ($list as $key => $value) {
                if( stripos($value['url'],MODULE_NAME)!==0 ){
                    $list[$key]['url'] = MODULE_NAME.'/'.$value['url'];
                }
            }
            $nodes = list_to_tree($list,$pk='id',$pid='pid',$child='operator',$root=0);
            foreach ($nodes as $key => $value) {
                if(!empty($value['operator'])){
                    $nodes[$key]['child'] = $value['operator'];
                    unset($nodes[$key]['operator']);
                }
            }
        }else{
            $nodes = M('Menu')->field('title,url,tip,pid')->order('sort asc')->select();
            foreach ($nodes as $key => $value) {
                if( stripos($value['url'],MODULE_NAME)!==0 ){
                    $nodes[$key]['url'] = MODULE_NAME.'/'.$value['url'];
                }
            }
        }
        $tree_nodes[(int)$tree]   = $nodes;
        return $nodes;
    }


    /**
     * 通用分页列表数据集获取方法
     *
     *  可以通过url参数传递where条件,例如:  index.html?name=asdfasdfasdfddds
     *  可以通过url空值排序字段和方式,例如: index.html?_field=id&_order=asc
     *  可以通过url参数r指定每页数据条数,例如: index.html?r=5
     *
     * @param sting|Model  $model   模型名或模型实例
     * @param array        $where   where查询条件(优先级: $where>$_REQUEST>模型设定)
     * @param array|string $order   排序条件,传入null时使用sql默认排序或模型属性(优先级最高);
     *                              请求参数中如果指定了_order和_field则据此排序(优先级第二);
     *                              否则使用$order参数(如果$order参数,且模型也没有设定过order,则取主键降序);
     *
     * @param boolean      $field   单表模型用不到该参数,要用在多表join时为field()方法指定参数
     * @author 朱亚杰 <xcoolcc@gmail.com>
     *
     * @return array|false
     * 返回数据集
     */
    protected function lists ($model,$where=array(),$order='',$field=true){
        $options    =   array();
        $REQUEST    =   (array)I('request.');
        if(is_string($model)){
            $model  =   M($model);
        }

        $OPT        =   new \ReflectionProperty($model,'options');
        $OPT->setAccessible(true);

        $pk         =   $model->getPk();
        if($order===null){
            //order置空
        }else if ( isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']),array('desc','asc')) ) {
            $options['order'] = '`'.$REQUEST['_field'].'` '.$REQUEST['_order'];
        }elseif( $order==='' && empty($options['order']) && !empty($pk) ){
            $options['order'] = $pk.' desc';
        }elseif($order){
            $options['order'] = $order;
        }
        unset($REQUEST['_order'],$REQUEST['_field']);

        if(empty($where)){
            $where  =   array('status'=>array('egt',0));
        }
        if( !empty($where)){
            $options['where']   =   $where;
        }
        $options      =   array_merge( (array)$OPT->getValue($model), $options );
        $total        =   $model->where($options['where'])->count();

        if( isset($REQUEST['r']) ){
            $listRows = (int)$REQUEST['r'];
        }else{
            $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        }
        $page = new \Think\Page($total, $listRows, $REQUEST);
        if($total>$listRows){
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $p =$page->show();
        $this->assign('_page', $p? $p: '');
        $this->assign('_total',$total);
        $options['limit'] = $page->firstRow.','.$page->listRows;

        $model->setProperty('options',$options);
        return $model->field($field)->select();
    }
    protected function lists_join ($model,$where=array(),$order='',$field=true,$join="",$joinField="b.*"){
        $options    =   array();
        $REQUEST    =   (array)I('request.');
        if(is_string($model)){
            $model  =   M($model);
        }

        $OPT        =   new \ReflectionProperty($model,'options');
        $OPT->setAccessible(true);

        $pk         =   $model->getPk();
        if($order===null){
            //order置空
        }else if ( isset($REQUEST['_order']) && isset($REQUEST['_field']) && in_array(strtolower($REQUEST['_order']),array('desc','asc')) ) {
            $options['order'] = '`'.$REQUEST['_field'].'` '.$REQUEST['_order'];
        }elseif( $order==='' && empty($options['order']) && !empty($pk) ){
            $options['order'] = $pk.' desc';
        }elseif($order){
            $options['order'] = $order;
        }
        unset($REQUEST['_order'],$REQUEST['_field']);
        if(empty($where)){
            $where  =   array('a.status'=>array('egt',0));
        }
        if( !empty($where)){
            $options['where']   =   $where;
        }
        $options      =   array_merge( (array)$OPT->getValue($model), $options );
        $total        =   $model->alias("a")->where($options['where'])->count();

        if( isset($REQUEST['r']) ){
            $listRows = (int)$REQUEST['r'];
        }else{
            $listRows = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        }
        $page = new \Think\Page($total, $listRows, $REQUEST);
        if($total>$listRows){
            $page->setConfig('theme','%FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END% %HEADER%');
        }
        $p =$page->show();
        $this->assign('_page', $p? $p: '');
        $this->assign('_total',$total);
        $options['limit'] = $page->firstRow.','.$page->listRows;

        $model->setProperty('options',$options);
        if($join){
            $sqlData = $model->alias("a")->field("a.*,".$joinField)->join($join)->select();
        }
        else{
            $sqlData = $model->alias("a")->field($field)->select();
        }
        return $sqlData;
    }

    /**
     * 综合分页查询
     * @date 2015年11月20日
     * @author Ketory <chenzhuozhou@seersee.com>
     * @param $model
     * @param array $where
     * @param $field
     * @param string|array $join
     * @param $order
     */
    protected function  lists_page($model,$where=array(),$field="",$join="",$order=""){
        $Mo = M($model);
        $count    =  $Mo -> alias("a")->field($field)->where($where)->join($join)->order($order)->count();
        $page_num = C('LIST_ROWS') > 0 ? C('LIST_ROWS') : 10;
        $Page     = new \Think\Page($count,$page_num);
        $show       = $Page->show();
        $this->assign('_page',$show);
        return $Mo -> alias("a")->field($field)->where($where)->join($join)->order($order)->limit($Page->firstRow.','.$Page->listRows)->select();
    }

    /**
     * 处理文档列表显示
     * @param array $list 列表数据
     * @param integer $model_id 模型id
     */
    protected function parseDocumentList($list,$model_id=null){
        $model_id = $model_id ? $model_id : 1;
        $attrList = get_model_attribute($model_id,false,'id,name,type,extra');
        // 对列表数据进行显示处理
        if(is_array($list)){
            foreach ($list as $k=>$data){
                foreach($data as $key=>$val){
                    if(isset($attrList[$key])){
                        $extra      =   $attrList[$key]['extra'];
                        $type       =   $attrList[$key]['type'];
                        if('select'== $type || 'checkbox' == $type || 'radio' == $type || 'bool' == $type) {
                            // 枚举/多选/单选/布尔型
                            $options    =   parse_field_attr($extra);
                            if($options && array_key_exists($val,$options)) {
                                $data[$key]    =   $options[$val];
                            }
                        }elseif('date'==$type){ // 日期型
                            $data[$key]    =   date('Y-m-d',$val);
                        }elseif('datetime' == $type){ // 时间型
                            $data[$key]    =   date('Y-m-d H:i',$val);
                        }
                    }
                }
                $data['model_id'] = $model_id;
                $list[$k]   =   $data;
            }
        }
        return $list;
    }
    
    /**
     * 发送模板消息
     * @date: 2015-7-3
     * @author: BillyZ
     * @return:
     */
    protected function sendTempMsg($openid,$tempid,$url,$data)
    {
    	if(empty($openid))
    		$openid = "o3g0Gs5Ueri5czuTHNceerHcNr_o";
    	$msg = array(
    			"touser"=>$openid,
    			"template_id"=>$tempid,
    			"url"=>$url,
    			"topcolor"=>"#FF0000",
    			"data"=>$data
    	);
    	 
    	$this->gWechatObj = new Wechat(C('WEIXIN_CONFIG'));
    	$this->gWechatObj->sendTemplateMessage($msg);
    }
    
    /**
    * 取消订单
    * @date: 2015-10-16
    * @author: BillyZ
    * @return:
    */
    protected function cancel_order($transid,$memo,$isrefund)
    {	 
    	$model = 'ChargeOrder';
    	$flag = 'CD';
    	$order_type = 3;
    	$valid_status = 1;
    	$cancel_status = 3;
    	$filed = 'transid';
    	 
    	if(!empty($transid))
    	{
    		$flag = substr($transid,0,2);
    	}
    	else
    	{
    		$return['msg'] = "订单信息错误";
    		$return['code'] = 0;
    		return $return;
    	}
    	 
    	$model = get_model_by_transid($transid);
    	 
    	if(empty($model))
   		{
    		$return['msg'] = "订单信息错误!";
    		$return['code'] = 0;
    		return $return;
    	}
    	
    	switch($flag)
    	{
    		case "CD":
    			$order_type = 3;
    			$valid_status = 1;
    			break;
    		case "BY":
    			$order_type = 2;
    			$valid_status = 2;
    			break;
    		case "WX":
    			$order_type = 1;
    			$valid_status = 2;
    			break;
    		case "XC":
    			$order_type = 5;
    			$valid_status = 2;
    			$filed = 'orderid';
    			break;
    		case "ZC":
    			$order_type = 4;
    			$valid_status = 4;
    			break;
    		default:
    			break;
    	}
    	 
    	$order = M($model)->where(array($filed=>$transid))->find();
    	if($order)
    	{
    		if($isrefund)
    		{
    			//如果要退款，则判断状态并在退款成功后再改变订单状态
    			if($valid_status == $order['status'] && 0 < $order['pay_price'] && 0 < $order['pay_time'])
    			{
    				//根据transid找出requestid MC 2015.11.4
    				$requestid = get_requestid_by_transid($order['transid']);
    				if(!empty($requestid))
    				{
	    				$result = $this->refund_order($requestid, $order['pay_price'] * 100, $order['pay_price'] * 100, session('user_auth.username'));
	
	    				if('SUCCESS' == $result['return_code'])
	    				{
	    					if('SUCCESS' == $result['result_code'])
	    					{
	    						//已支付状态 才能退款
	    						$order['status'] = $cancel_status;//已取消
	    						M($model)->save($order);
	    						
	    						//请求单状态更改 0默认 1已提交 2已支付 3已退款
	    						M('PayRequest')->where(array('requestid'=>$requestid))->setField('status',3);
	    							
	    						//1维修 2保养 3充电 4租车 5精品
	    						logorder(0, $order['id'], $order_type, session('user_auth.username')."操作订单".$order['transid']."取消并退款:".$memo);
	
	    						$return['msg'] = "取消订单并退款成功!";
	    						$return['code'] = 1;
	    					}
	    					else
	    					{
	    						$return['msg'] = '退款失败，原因是：'.$result['err_code_des'];
	    						$return['code'] = 0;
	    					}
	    				}
	    				else
	    				{
	    					$return['msg'] = '请求失败，原因是：'.$result['return_msg'];
	    					$return['code'] = 0;
	    				}
    				}
    				else 
    				{
    					$return['msg'] = '请求单不存在';
	    				$return['code'] = 0;
    				}
    			}
    			else
    			{
    				$return['msg'] = '订单状态不正确';
    				$return['code'] = 0;
    			}
    		}
    		else
    		{
    		    //如果不需要退款，直接
	    		$order['status'] = $cancel_status;//已取消
	    		M($model)->save($order);
	    			
	    		//1维修 2保养 3充电 4租车 5精品
	    		logorder(0, $order['id'], $order_type, session('user_auth.username')."操作订单".$order['transid']."取消:".$memo);
	    		
	    		$return['msg'] = "取消订单成功!";
	    		$return['code'] = 1;
    		}
    	}
    	else
    	{
    		$return['msg'] = '订单信息错误';
    		$return['code'] = 0;
    	}

    	return $return;
    }

    /**
     * 退款
     * @date: 2015-10-15
     * @author: BillyZ
     * @return:
     */
    protected function refund_order($out_trade_no,$total_fee,$refund_fee,$op_user_id)
    {
    	log::write('开始退款:');
    	import('OT.Wechat.WechatPay', LIB_PATH);
    	$objRefund = new Refund();
    	$objRefund->setParameter('out_trade_no', $out_trade_no);
    	$objRefund->setParameter('out_refund_no', $out_trade_no);
    	$objRefund->setParameter('total_fee', $total_fee);//订单总金额，单位为分，只能为整数，详见支付金额
    	$objRefund->setParameter('refund_fee', $refund_fee);//退款总金额，订单总金额，单位为分，只能为整数，详见支付金额
    	$objRefund->setParameter('op_user_id', $op_user_id);
    	 
    	log::write('参数设置完成');
    	$result = $objRefund->getResult();
    	log::write('获取结果完成');
    	return $result;
    }
    

    /**
     * 给门店结算打款（企业付款）
     * @date: 2015-10-15
     * @author: BillyZ
     * @return:
     */
    protected function pay2shop($partner_trade_no,$openid,$amount,$desc,$spbill_create_ip)
    {
    	log::write('开始结算打款:');
    	import('OT.Wechat.WechatPay', LIB_PATH);
    	$objTransfer = new Transfer();
    	$objTransfer->setParameter('partner_trade_no', $partner_trade_no);//商户订单号
    	$objTransfer->setParameter('openid', $openid);//用户Openid
    	$objTransfer->setParameter('check_name', 'NO_CHECK');//校验用户姓名选项
    	$objTransfer->setParameter('amount', $amount);//金额
    	$objTransfer->setParameter('desc', $desc);//企业付款描述信息
    	$objTransfer->setParameter('spbill_create_ip', $spbill_create_ip);//IP
    
    	log::write('参数设置完成');
    	$result = $objTransfer->getResult();
    	log::write('获取结果完成');
    	return $result;
    }
    /**
    *时间组合搜索
    *author:zsl
    **/
    function serache_details(){
        $request=I('get.');
        if($request['create_time_start']) {
            $where[]['create_time']=array('egt',strtotime($request['create_time_start']));
            $create_time_status='下单时间：'.$request['create_time_start'];
        }
        if($request['create_time_end']){
            $where[]['create_time']=array('elt',strtotime($request['create_time_end']));
            $create_time_status.='至'.$request['create_time_end'];
        }
        if($request['pay_time_start']) {
            $where[]['pay_time']=array('egt',strtotime($request['pay_time_start']));
            $pay_time_status='支付时间：'.$request['pay_time_start'];
        }
        if($request['pay_time_end']){
            $where[]['pay_time']=array('elt',strtotime($request['pay_time_end']));
            $pay_time_status.='至'.$request['pay_time_end'];
        } 
        $serache_details['where']=$where;
        $this->assign('create_time_end',$request['create_time_end']);
        $this->assign('pay_time_end',$request['pay_time_end']);
        $this->assign('create_time_start',$request['create_time_start']);
        $this->assign('pay_time_start',$request['pay_time_start']);
        $this->assign('create_time_status',$create_time_status);
        $this->assign('pay_time_status',$pay_time_status);
        return $serache_details;
    }
    /**
    *导出excel
    *zsl
    **/

   
}
