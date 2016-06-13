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
        <h2>优惠券管理 </h2>
    </div>

    <div class="cf">

        <!-- 高级搜索 -->
         <div class="search-form cf">
             <div style=" float: left; line-height: 30px; text-align: center;">
                 发放时间：
                 <input type="text" name="create_time_start" class="text input-small time" value="<?php echo ($create_time_start); ?>" placeholder="请选择日期">
                 -
                 <input type="text" name="create_time_end" class="text input-small time" value="<?php echo ($create_time_end); ?>" placeholder="请选择日期">
             </div>
             <div style="float: left; line-height: 30px; text-align: center; margin-left: 20px;">
                 使用时间：
                 <input type="text" name="used_time_start" class="text input-small time" value="<?php echo ($used_time); ?>" placeholder="请选择日期">
                 -
                 <input type="text" name="used_time_end" class="text input-small time" value="<?php echo ($used_time); ?>" placeholder="请选择日期">
             </div>
             <div style="float: left; line-height: 30px; text-align: center; margin-left: 20px;">
                 优惠券：
                     <select name="listcoupon" class="listcoupon" >
                         <option value="0">所有</option>
                         <?php if(is_array($listcoupon)): $i = 0; $__LIST__ = $listcoupon;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["id"]); ?>"><?php echo ($vo["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                     </select>
                 <input type="hidden" id="cid" name="cid" value="<?php echo ($cid); ?>">
             </div>
             <div style="float: left; line-height: 30px; text-align: center; margin-left: 20px;">
                 渠道：
                 <select name="listcanalid" class="listcoupon" >
                     <option value="0">所有</option>
                     <?php if(is_array($listcanalid)): $i = 0; $__LIST__ = $listcanalid;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><option value="<?php echo ($vo["canalid"]); ?>"><?php echo ($vo["cname"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                 </select>
                 <input type="hidden" id="canalid" name="canalid" value="<?php echo ($canalid); ?>">
             </div>
            <div class="sleft" style="float: right;">
                <input type="text" name="code" class="search-input" value="<?php echo I('code');?>" placeholder="请输入卡券号">
                <!--<a class="sch-btn" href="javascript:;" id="search" url="/admin.php?s=/Coupon/coupons.html"><i class="btn-search"></i></a>-->
            </div>
             <a id="search"  class="btn" href="javascript:;" url="/admin.php?s=/Coupon/coupons.html">查 询</a>
             <a id="export"  class="btn" href="javascript:;" url="/admin.php?s=/Coupon/export_orders.html">导出</a>
        </div>

        <?php if($create_time_start != null): ?><div class="tip-ans" >
                <div class="box">
                    <div class="nn"><?php echo ($create_time_status); ?></div>
                    <div class="xx"><a style="cursor: pointer;" data-condition="create_time">x</a></div>
                </div>
            </div><?php endif; ?>
        <?php if($used_time_status != null): ?><div class="tip-ans" >
                <div class="box">
                    <div class="nn"><?php echo ($used_time_status); ?></div>
                    <div class="xx"><a style="cursor: pointer;" data-condition="used_time">x</a></div>
                </div>
            </div><?php endif; ?>
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
                        <th>券号</th>
                        <th>优惠券</th>
                        <th>总次数</th>
                        <th>剩余次数</th>
                        <th>领取时间</th>
                        <th>使用时间</th>
                        <th>到期时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
				<?php if(!empty($list)): if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><tr>
                        <td><input class="ids row-selected" type="checkbox" name="id[]" value="<?php echo ($menu["id"]); ?>"></td>
                        <td><?php echo ($menu["id"]); ?></td>
                        <td><?php echo ($menu["code"]); ?></td>
                        <td><?php echo ($menu["cname"]); ?></td>
                        <td><?php echo ($menu["total_times"]); ?></td>
                        <td><?php echo ($menu["remain_times"]); ?></td>
                        <td><?php echo (date("Y-m-d",$menu["create_time"])); ?></td>
                         <td>
                        <!-- eq 1 表示已经未使用 eq 2 表示已经使用 -->
                        <?php if($menu["status"] == 2): if($menu["used_time"] == 0): ?>无使用
                            <?php else: ?> <?php echo (date("Y-m-d",$menu["used_time"])); endif; ?>
                        <?php else: ?>
                            未使用<?php endif; ?>
                        </td>
                        <td><?php echo (date("Y-m-d",$menu["end_time"])); ?></td>
                        <td>
                        <?php if($menu["status"] == 1): ?><a href="<?php echo U('coupon_use',array('id'=>$menu['id'],'value'=>2));?>" class="ajax-get">
                            	使用
                            </a><?php endif; ?>
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
    <?php if(C('COLOR_STYLE')=='blue_color') echo '<link href="/Public/static/datetimepicker/css/datetimepicker_blue.css" rel="stylesheet" type="text/css">'; ?>
    <link href="/Public/static/datetimepicker/css/dropdown.css" rel="stylesheet" type="text/css">
    <script type="text/javascript" src="/Public/static/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
    <script type="text/javascript" src="/Public/static/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
    <script type="text/javascript">
        $(function() {
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

            $("div.tip-ans a").click(function(){
                var con = $(this).data("condition");
                if(con=="create_time")
                {
                    $("input[name='create_time_start']").val("");
                    $("input[name='create_time_end']").val("");
                }
                if(con=="used_time")
                {
                    $("input[name='used_time_start']").val("");
                    $("input[name='used_time_end']").val("");
                }
                $("#search").click();
            });

            //导航高亮
            highlight_subnav('<?php echo U('coupons');?>');

            Think.setValue("listcoupon", <?php echo ((isset($cid) && ($cid !== ""))?($cid): 0); ?>);
            Think.setValue("listcanalid", <?php echo ((isset($canalid) && ($canalid !== ""))?($canalid): 0); ?>);
            //点击排序
        	$('.list_sort').click(function(){
        		var url = $(this).attr('url');
        		var ids = $('.ids:checked');
        		var param = '';
        		if(ids.length > 0){
        			var str = new Array();
        			ids.each(function(){
        				str.push($(this).val());
        			});
        			param = str.join(',');
        		}

        		if(url != undefined && url != ''){
        			window.location.href = url + '/ids/' + param;
        		}
        	});

            $("select[name='listcoupon']").change(function(){
                $("#cid").val($("select[name='listcoupon']").val());
            });
            $("select[name='listcanalid']").change(function(){
                $("#canalid").val($("select[name='listcanalid']").val());
            });
        });
    </script>

</body>
</html>