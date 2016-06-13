<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo ($meta_title); ?>|滴啊滴管理平台</title>
    <link href="/Public/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/base.css" media="all">
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/common.css" media="all">
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/module.css">
    <link rel="stylesheet" type="text/css" href="/Public/Admin/css/style.css" media="all">
	<link rel="stylesheet" type="text/css" href="/Public/Admin/css/<?php echo (C("COLOR_STYLE")); ?>.css" media="all">
     <!--[if lt IE 9]>
    <script type="text/javascript" src="/Public/static/jquery-1.10.2.min.js"></script>
    <![endif]--><!--[if gte IE 9]><!-->
    <script type="text/javascript" src="/Public/static/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="/Public/Admin/js/jquery.mousewheel.js"></script>
    <!--<![endif]-->
    
</head>
<body>
    <!-- 头部 -->
    <div class="header">
        <!-- Logo -->
        <span class="logo"></span>
        <!-- /Logo -->

        <!-- 主导航 -->
        <ul class="main-nav">
            <?php if(is_array($__MENU__["main"])): $i = 0; $__LIST__ = $__MENU__["main"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li class="<?php echo ((isset($menu["class"]) && ($menu["class"] !== ""))?($menu["class"]):''); ?>"><a href="<?php echo (U($menu["url"])); ?>"><?php echo ($menu["title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
        <!-- /主导航 -->

        <!-- 用户栏 -->
        <div class="user-bar">
            <a href="javascript:;" class="user-entrance"><i class="icon-user"></i></a>
            <ul class="nav-list user-menu hidden">
                <li class="manager">你好，<em title="<?php echo session('user_auth.username');?>"><?php echo session('user_auth.username');?></em></li>
                <?php if(session('user_auth.shopid') != 0): ?><li><a href="<?php echo U('User/myshop');?>">我的门店</a><?php endif; ?>
                <li><a href="<?php echo U('User/updatePassword');?>">修改密码</a></li>
                <li><a href="<?php echo U('User/updateNickname');?>">修改昵称</a></li>
                <li><a href="<?php echo U('Public/logout');?>">退出</a></li>
            </ul>
        </div>
    </div>
    <!-- /头部 -->

    <!-- 边栏 -->
    <div class="sidebar">
        <!-- 子导航 -->
        
            <div id="subnav" class="subnav">
                <?php if(!empty($_extra_menu)): ?>
                    <?php echo extra_menu($_extra_menu,$__MENU__); endif; ?>
                <?php if(is_array($__MENU__["child"])): $i = 0; $__LIST__ = $__MENU__["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub_menu): $mod = ($i % 2 );++$i;?><!-- 子导航 -->
                    <?php if(!empty($sub_menu)): if(!empty($key)): ?><h3><i class="icon icon-unfold"></i><?php echo ($key); ?></h3><?php endif; ?>
                        <ul class="side-sub-menu">
                            <?php if(is_array($sub_menu)): $i = 0; $__LIST__ = $sub_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li>
                                    <a class="item" href="<?php echo (U($menu["url"])); ?>"><?php echo ($menu["title"]); ?></a>
                                </li><?php endforeach; endif; else: echo "" ;endif; ?>
                        </ul><?php endif; ?>
                    <!-- /子导航 --><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        
        <!-- /子导航 -->
    </div>
    <!-- /边栏 -->

    <!-- 内容区 -->
    <div id="main-content">
        <div id="top-alert" class="fixed alert alert-error" style="display: none;">
            <button class="close fixed" style="margin-top: 4px;">&times;</button>
            <div class="alert-content">这是内容</div>
        </div>
        <div id="main" class="main">
            
            <!-- nav -->
            <?php if(!empty($_show_nav)): ?><div class="breadcrumb">
                <span>您的位置:</span>
                <?php $i = '1'; ?>
                <?php if(is_array($_nav)): foreach($_nav as $k=>$v): if($i == count($_nav)): ?><span><?php echo ($v); ?></span>
                    <?php else: ?>
                    <span><a href="<?php echo ($k); ?>"><?php echo ($v); ?></a>&gt;</span><?php endif; ?>
                    <?php $i = $i+1; endforeach; endif; ?>
            </div><?php endif; ?>
            <!-- nav -->
            

            
    <div class="main-title">
        <h2><?php if(isset($data)): ?>[ <?php echo ($data["title"]); ?> ] 子<?php endif; ?>保养订单管理 </h2>
    </div>

 <div class="search-form cf">
        <div style=" float: left; line-height: 30px; text-align: center;">
            下单时间：
            <input type="text" name="create_time_start" class="text input-small time" value="<?php echo ($create_time_start); ?>" placeholder="请选择日期">
            -
            <input type="text" name="create_time_end" class="text input-small time" value="<?php echo ($create_time_end); ?>" placeholder="请选择日期">

        </div>
        <div style="float: left; line-height: 30px; text-align: center; margin-left: 20px;">
            支付时间：
            <input type="text" name="pay_time_start" class="text input-small time" value="<?php echo ($pay_time_start); ?>" placeholder="请选择日期">
            -
            <input type="text" name="pay_time_end" class="text input-small time" value="<?php echo ($pay_time_end); ?>" placeholder="请选择日期">
        </div>
     &nbsp;&nbsp;&nbsp;
        <a id="search"  class="btn" href="javascript:;" url="/admin.php?s=/Maintain/orders.html">查 询</a>
        <a id="export" class="btn" href="javascript:;" url="/admin.php?s=/Maintain/export_orders.html">导出</a>
        <input type="hidden" id="status" name="status" value="<?php echo I('status', -1);?>">
    </div>

    <?php if($create_time_start != null): ?><div class="tip-ans" >
            <div class="box">
                <div class="nn"><?php echo ($create_time_status); ?></div>
                <div class="xx"><a href="<?php echo U('Maintain/orders',array('pay_time_start'=>$pay_time_start,'pay_time_end'=>$pay_time_end,'status'=>I('status')));?>">x</a></div>
            </div>
        </div><?php endif; ?>
    <?php if($pay_time_start != null): ?><div class="tip-ans" >
            <div class="box">
                <div class="nn"><?php echo ($pay_time_status); ?></div>
                <div class="xx"><a href="<?php echo U('Maintain/orders',array('create_time_start'=>$create_time_start,'create_time_end'=>$create_time_end,'status'=>I('status')));?>">x</a></div>
            </div>
        </div><?php endif; ?>
    <!-- <div class="tip-ans" >
            <div class="box">
                <div class="nn">关键字：小强</div>
                <div class="xx"><a href="#">x</a></div>
            </div>
    </div> -->
<div style="clear: both;"></div>
<ul class="tab-nav nav">
	<li <?php if($status == -1): ?>class="current"<?php endif; ?>><a href="<?php echo U('Maintain/orders',array('status'=>-1));?>">全部</a></li>
	
	<?php if(is_array(C("ORDER_STATUS_MAINTAIN"))): $i = 0; $__LIST__ = C("ORDER_STATUS_MAINTAIN");if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$type): $mod = ($i % 2 );++$i;?><li <?php if($status == $key): ?>class="current"<?php endif; ?>><a href="<?php echo U('Maintain/orders',array('status'=>$key));?>"><?php echo ($type); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>


 <div class="cf"> 
        <!-- 高级搜索 -->
        <div class="search-form fr cf">
            <div class="sleft">
                <input type="text" name="title" class="search-input" value="<?php echo I('title');?>" placeholder="订单号或用户">
                <a class="sch-btn" href="javascript:;" id="search" url="/admin.php?s=/Maintain/orders"><i class="btn-search"></i></a>
            </div>
        </div>
    </div>
</ul>
   

    <div class="data-table table-striped">
        <form class="ids">
            <table>
                <thead>
                    <tr>
                        <th class="row-selected">
                            <input class="checkbox check-all" type="checkbox">
                        </th>
                        <th>用户id</th>
                        <th>订单号</th>
                        <th>保养产品</th>
                        <th>用户</th>
                        <th>所在门店</th>
                        <th>费用</th>
                        <th>支付方式</th>
                        <th>状态</th>
                        <th>评价</th>
                        <th>下单时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
				<?php if(!empty($list)): if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$order): $mod = ($i % 2 );++$i;?><tr>
                        <td><input class="ids row-selected" type="checkbox" name="id[]" value="<?php echo ($order["id"]); ?>"></td>
                        <td><?php echo ($order["uid"]); ?></td>
                        <td><?php echo ($order["transid"]); ?></td>
                        <td><?php echo (get_maintain_product_id($order["pid"])); ?></td>
                        <td><?php echo (get_fans_real_name_openid($order["openid"])); ?>(<?php echo (get_fansname_openid($order["openid"])); ?>)</td>
                        <td><?php echo (get_shop_id($order["shopid"])); ?></td>
                        <td><?php echo ($order["price"]); ?></td>
                        <td><?php if($order["is_df"] == 1): ?>代付<?php elseif(($order["status"] != 4) and ($order["status"] != 2)): if($order["status"] == 3): ?>已取消<?php else: ?>未支付<?php endif; elseif($order["couponid"] != 0): ?>优惠券<?php else: ?>本人支付<?php endif; ?></td>
                        <td>
                            <?php if($order['status'] != 1): echo (get_maintainorder_status($order["status"])); ?>
                                <?php else: ?>
                                <a href="#" data-img="/wxpay/wap.php?s=/Index/qrcode/code/BY/oid/<?php echo ($order["id"]); ?>.html" class="pay_qr_img"> <?php echo (get_maintainorder_status($order["status"])); ?></a><?php endif; ?>

                        </td>
                        <td>
                        <?php if($order["memo"] == NULL): ?>未评价
                        <?php else: ?>
                        <a href="##" class="memoinfo" commentinfo="<?php echo ($order["memo"]); ?>" star-oper="<?php echo ($order["star_oper"]); ?>" star-service="<?php echo ($order["star_service"]); ?>" 
                            memo-time="<?php echo ($order["memo_time"]); ?>" star-total="<?php echo ($order["star_total"]); ?>"
                        >已评价</a><?php endif; ?>
                        </td>
                        <td><?php echo (date('Y-m-d H:i',$order["create_time"])); ?></td>
                        <td>
                             <?php if($order["status"] == 0): ?><!-- 0->1 已下单->已取消 -->
                            	<button class="btn ajax-post confirm" url="<?php echo U('order_cancel',array('oid'=>$order['id']));?>" target-form="ids">取消订单</button>
                            <?php elseif($order["status"] == 1): ?>
                            <?php elseif($order["status"] == 2): ?>
        						<button class="btn ajax-post confirm" url="<?php echo U('service_finish',array('oid'=>$order['id']));?>" target-form="ids">服务完成</button>
								<button class="btn ajax-post confirm" url="<?php echo U('refund',array('oid'=>$order['id']));?>" target-form="ids">退款</button>

                            	<?php else: endif; ?>
                             <a href="#">详情</a>
                            <a href="#" id="remark" oid="<?php echo ($order["id"]); ?>">备注（<?php echo count_remark($order['id'],2);?>）</a>
                        </td>
                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
				<?php else: ?>
				<td colspan="10" class="text-center"> aOh! 暂时还没有内容! </td><?php endif; ?>
                </tbody>
            </table>
        </form>
        <!-- 分页 -->
        <div class="page">
	        <?php echo ($_page); ?>
            <?php echo ($page); ?>
        </div>
    </div>

        </div>
        <div class="cont-ft">
            <div class="copyright">
                <div class="fl">滴啊滴</div>
                <div class="fr">V<?php echo (ONETHINK_VERSION); ?></div>
            </div>
        </div>
    </div>
    <!-- /内容区 -->
    <script type="text/javascript">
    (function(){
        var ThinkPHP = window.Think = {
            "ROOT"   : "", //当前网站地址
            "APP"    : "/admin.php?s=", //当前项目地址
            "PUBLIC" : "/Public", //项目公共目录地址
            "DEEP"   : "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
            "MODEL"  : ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
            "VAR"    : ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"]
        }
    })();
    </script>
    <script type="text/javascript" src="/Public/static/think.js"></script>
    <script type="text/javascript" src="/Public/Admin/js/common.js"></script>
    <script type="text/javascript">
        +function(){
            var $window = $(window), $subnav = $("#subnav"), url;
            $window.resize(function(){
                $("#main").css("min-height", $window.height() - 130);
            }).resize();

            /* 左边菜单高亮 */
            url = window.location.pathname + window.location.search;
            url = url.replace(/(\/(p)\/\d+)|(&p=\d+)|(\/(id)\/\d+)|(&id=\d+)|(\/(group)\/\d+)|(&group=\d+)/, "");
            $subnav.find("a[href='" + url + "']").parent().addClass("current");

            /* 左边菜单显示收起 */
            $("#subnav").on("click", "h3", function(){
                var $this = $(this);
                $this.find(".icon").toggleClass("icon-fold");
                $this.next().slideToggle("fast").siblings(".side-sub-menu:visible").
                      prev("h3").find("i").addClass("icon-fold").end().end().hide();
            });

            $("#subnav h3 a").click(function(e){e.stopPropagation()});

            /* 头部管理员菜单 */
            $(".user-bar").mouseenter(function(){
                var userMenu = $(this).children(".user-menu ");
                userMenu.removeClass("hidden");
                clearTimeout(userMenu.data("timeout"));
            }).mouseleave(function(){
                var userMenu = $(this).children(".user-menu");
                userMenu.data("timeout") && clearTimeout(userMenu.data("timeout"));
                userMenu.data("timeout", setTimeout(function(){userMenu.addClass("hidden")}, 100));
            });

	        /* 表单获取焦点变色 */
	        $("form").on("focus", "input", function(){
		        $(this).addClass('focus');
	        }).on("blur","input",function(){
				        $(this).removeClass('focus');
			        });
		    $("form").on("focus", "textarea", function(){
			    $(this).closest('label').addClass('focus');
		    }).on("blur","textarea",function(){
			    $(this).closest('label').removeClass('focus');
		    });

            // 导航栏超出窗口高度后的模拟滚动条
            var sHeight = $(".sidebar").height();
            var subHeight  = $(".subnav").height();
            var diff = subHeight - sHeight; //250
            var sub = $(".subnav");
            if(diff > 0){
                $(window).mousewheel(function(event, delta){
                    if(delta>0){
                        if(parseInt(sub.css('marginTop'))>-10){
                            sub.css('marginTop','0px');
                        }else{
                            sub.css('marginTop','+='+10);
                        }
                    }else{
                        if(parseInt(sub.css('marginTop'))<'-'+(diff-10)){
                            sub.css('marginTop','-'+(diff-10));
                        }else{
                            sub.css('marginTop','-='+10);
                        }
                    }
                });
            }
        }();
    </script>
    
    <link href="/Public/static/datetimepicker/css/datetimepicker.css" rel="stylesheet" type="text/css">
    <?php if(C('COLOR_STYLE')=='blue_color') echo '<link href="/Public/static/datetimepicker/css/datetimepicker_blue.css" rel="stylesheet" type="text/css">'; ?>
    <link href="/Public/static/datetimepicker/css/dropdown.css" rel="stylesheet" type="text/css">
    <link href="/Public/Admin/js/layer.m/skin/layer.css" rel="stylesheet">
    <script type="text/javascript" src="/Public/static/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="/Public/static/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
    <script src="/Public/Admin/js/layer.m/layer.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(function () {
            var uid = <?php echo ((isset($user_id) && ($user_id !== ""))?($user_id):"0"); ?>;
            $(document).on("click","#remark",function(){
                //订单类型  1:维修 2:保养 3:充电 4:租车 5:购车
                $.get('/admin.php?s=/Remarks/index.html', {"type":2,"uid":uid,"oid":$(this).attr('oid')}, function(str){
                    layer.open({
                        type: 1,
                        title:"备注",
                        shade: 0.1,
                        shadeClose:true,
                        skin: 'layui-layer-rim', //加上边框
                        content: str
                    });
                });

            });
            $('.time').datetimepicker({
                format: 'yyyy-mm-dd',
                language:"zh-CN",
                minView:2,
                autoclose:true
            });
        $('.pay_qr_img').on("click",function(){
            layer.open({
                type: 1,
                title: false,
                closeBtn: true,
                shadeClose: true,
                skin: 'yourclass',
                content: '<img src="'+$(this).attr("data-img")+'" width="300">'
            });
        });
            //搜索功能
            $("#search,#export").click(function() {
                var url = $(this).attr('url');
                var query = $('.search-form').find('input').serialize();
                query = query.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g, '');
                query = query.replace(/^&/g, '');
                if (url.indexOf('?') > 0) {
                    url += '&' + query;
                } else {
                    url += '?' + query;
                }
                
                url+='&'+$('.search-form').find('select').serialize()
            
                window.location.href = url;
            });
            //回车搜索
            $(".search-input").keyup(function(e) {
                if (e.keyCode === 13) {
                    $("#search").click();
                    return false;
                }
            });
            //导航高亮
            highlight_subnav('<?php echo U('index');?>');

$('.memoinfo').click(function(){
        
    var memoinfo="<table style='line-height:30px;font-size:15px;margin:0 20px 20px 20px;'><tr><td>服务评价："+$(this).attr('star-service')+"颗星</td></tr><br/>"
            +"<tr><td>操作评价："+$(this).attr('star-oper')+"颗星</td></tr>"+"<tr><td>总体评价："+$(this).attr('star-total')+"颗星</td></tr>"
            +"<tr><td>详细评价："+$(this).attr('commentinfo')+"</td></tr></table>";
        layer.open({
            title:"评价信息",
            type: 1,
            skin: 'layui-layer-demo', //样式类名
            closeBtn: false, //不显示关闭按钮
            shift: 2,
            shadeClose: true, //开启遮罩关闭
            content: memoinfo,
            area:"400px",

        });
});
});   
    </script>

</body>
</html>