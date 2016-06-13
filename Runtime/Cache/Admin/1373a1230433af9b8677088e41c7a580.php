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
        <h2>会员管理 </h2>
    </div>

    <div class="search-form cf">
        <div style="margin-top: 10px; float: left;">
            手机号：
            <input type="text" name="mobile" class="text input-small" value="<?php echo ($condition['mobile']); ?>" />
            昵称:
            <input type="text" name="nickname" class="text input-small" value="<?php echo ($condition['nickname']); ?>">
            实名认证状态：
            <select id="idcard_status" style="width: 100px">
                <option value="-1">请选择..</option>
                <option value="0">未认证</option>
                <option value="1">认证中</option>
                <option value="2">已认证</option>
            </select>
            关注时间：
            <input type="text" name="create_time_start" class="text input-small time" value="<?php echo ($create_time_start); ?>" placeholder="请选择日期">
            -
            <input type="text" name="create_time_end" class="text input-small time" value="<?php echo ($create_time_end); ?>" placeholder="请选择日期">
        <a id="search" class="btn" href="javascript:;" url="<?php echo U('index');?>">查 询</a>
        <a id="export"  class="btn" href="javascript:;" url="/admin.php?s=/Fans/fans_export.html">导出</a>

        </div>
        
        </div>

    <div class="data-table table-striped">
        <form class="ids">
            <table>
                <thead>
                    <tr>
                        <th class="row-selected">
                            <input class="checkbox check-all" type="checkbox">
                        </th>
                        <th>ID</th>
                        <th>昵称</th>
                        <th>姓名</th>
                        <th>手机号</th>
                        <th>所在位置</th>
                        <th>注册时间</th>
                        <th>是否绑定微信</th>
                        <th>车辆数</th>
                        <th>实名认证</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
				<?php if(!empty($list)): if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                        <td><input class="ids row-selected" type="checkbox" name="id[]" value="<?php echo ($vo["id"]); ?>"></td>
                        <td><?php echo ($vo["id"]); ?></td>
                        <td><?php echo ($vo["nickname"]); ?></td>
                        <td><?php echo ($vo["realname"]); ?></td>
                        <td><?php echo ($vo["mobile"]); ?></td>
                        <td><?php echo ($vo["address"]); ?></td>
                        <td><?php echo (date("Y-m-d",$vo["subscribe_time"])); ?></td>
                        <td><?php echo (get_bool_desc($vo["wechat_binded"])); ?></td>
                        <td>
                           <?php if($vo["auth_bike"] == 1): ?><a href="##" id="bike-info" uid="<?php echo ($vo["id"]); ?>"> <?php echo ($vo["bikecount"]); ?>辆</a>
                            <?php else: ?>无车辆<?php endif; ?>
                        </td>
                        <td>
                            <?php if(($vo["idcard_status"]) == "0"): ?>未认证<?php endif; ?>
                            <?php if(($vo["idcard_status"]) == "1"): ?><a href="<?php echo ($vo["idcard_img"]); ?>" target="_blank">认证中</a> &nbsp; | &nbsp;
                                <a class="confirm ajax-get" title="通过" href="<?php echo U('authIdCard?id='.$vo['id'].'&status=2');?>">通过</a>&nbsp; | &nbsp;
                                <a class="confirm ajax-get" title="驳回" href="<?php echo U('authIdCard?id='.$vo['id'].'&status=0');?>">驳回</a><?php endif; ?>
                            <?php if(($vo["idcard_status"]) == "2"): ?><a href="<?php echo ($vo["idcard_img"]); ?>" target="_blank">已认证</a><?php endif; ?>
                        </td>
                        <td><?php echo (get_status_desc($vo["status"])); ?></td>
                        <td>
                            <a title="订单" href="<?php echo U('orders?uid='.$vo['id']);?>">订单</a>
                            <a title="积分" href="<?php echo U('scores?uid='.$vo['id']);?>">积分</a>
                            <a title="优惠券" href="<?php echo U('coupons?uid='.$vo['id']);?>">优惠券</a>
                            <a class="confirm ajax-get" title="删除" href="<?php echo U('delFans?uid='.$vo['id']);?>">删除</a>
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
<link href="/Public/static/datetimepicker/css/dropdown.css" rel="stylesheet" type="text/css">
    <link href="/Public/Admin/js/layer.m/skin/layer.css" rel="stylesheet">
    <script type="text/javascript" src="/Public/Admin/js/layer.m/layer.js"></script>
    <script type="text/javascript" src="/Public/static/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="/Public/static/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
    <script type="text/javascript">
        $(function() {
            //搜索功能
            $("#search,#export").click(function() {
                var url = $(this).attr('url');
                var status = $('#idcard_status').val();
                var query = $('.search-form').find('input').serialize();
                query = query.replace(/(&|^)(\w*?\d*?\-*?_*?)*?=?((?=&)|(?=$))/g, '');
                query = query.replace(/^&/g, '');
                if(status != '-1'){
                    query = 'idcard_status=' + status + "&" + query;
                }
                if (url.indexOf('?') > 0) {
                    url += '&' + query;
                } else {
                    url += '?' + query;
                }
                window.location.href = url;
            });
         
            //回车搜索
            $(".search-input").keyup(function(e) {
                if (e.keyCode === 13) {
                    $("#search").click();
                    return false;
                }
            });
            $('.time').datetimepicker({
                format: 'yyyy-mm-dd',
                language:"zh-CN",
                minView:2,
                autoclose:true
            });
            $('.time').datetimepicker({
                format: 'yyyy-mm-dd',
                language:"zh-CN",
                minView:2,
                autoclose:true
            }); 
            //导航高亮
            highlight_subnav('<?php echo U('index');?>');
            $('#idcard_status').val('<?php echo ($condition["idcard_status"]); ?>');
            //车辆信息
            $(document).on("click","#bike-info",function(){
                var bike_info="<table class='table table-striped' style='text-align:center; width:100%;'><tr><th>车辆品牌</th><th>车辆型号</th><th>认证时间</th></tr>";
                 $.get('/admin.php?s=/Fans/FansIndexPost.html',{"uid":$(this).attr('uid')},function(bikeinfo){
                   $.each(bikeinfo,function(idx,itme){
                       var unixTimestamp = new Date(itme.buytime * 1000);
                       commonTime = unixTimestamp.toLocaleString();
                        bike_info+="<tr style='line-height:35px;'><td>"+itme.bikename+"</td><td>"+itme.biketype+"</td><td>"+commonTime+"</td></tr>";
                   });
                bike_info+="</table></tr>";
                $(function() {
                    $("table tr:nth-child(odd)").addClass("striped");
                    $("table tr:nth-child(even)").addClass("striped1");
                });
                layer.open({
                    title:"车辆信息",
                    type: 1,
                    skin: 'layui-layer-demo', //样式类名
                    closeBtn: false, //不显示关闭按钮
                    shift: 2,
                    shadeClose: true, //开启遮罩关闭
                    content: bike_info,
                    area:"400px",
                });  
                 });
            
            });
            
        });
    </script>

</body>
</html>