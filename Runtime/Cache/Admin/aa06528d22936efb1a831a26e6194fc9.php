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
            

            
    <script type="text/javascript" src="/Public/static/uploadify/jquery.uploadify.min.js"></script>
    <div class="main-title">
        <h2><?php echo isset($info['id'])?'编辑':'新增';?>保养产品</h2>
    </div>
    <form action="<?php echo U();?>" method="post" class="form-horizontal">
        <div class="form-item">
            <label class="item-label">产品名称</label>
            <div class="controls">
                <input type="text" class="text input-large" name="name" value="<?php echo ((isset($info["name"]) && ($info["name"] !== ""))?($info["name"]):''); ?>">
            </div>
        </div>
        <div class="form-item">
            <label class="item-label">产品类型</label>
            <div class="controls">
                <select name="type" class="menu_type">
                        <option value="1">基础</option>
                        <option value="2">套餐</option>
                        <option value="3">过保</option>
                </select>
            </div>
        </div>
        <?php echo hook('province_city_area',array('cityid'=>$info["cityid"],'areaid'=>$info["areaid"]));?>
        <div class="form-item" id="dKeyword">
            <label class="item-label">产品定义<span class="check-tips">（产品定义：例如常规保养）</span></label>
            <div class="controls">
                <input type="text" class="text input-large" name="define" value="<?php echo ((isset($info["define"]) && ($info["define"] !== ""))?($info["define"]):''); ?>">
            </div>
        </div>
        <div class="form-item" id="dUrl">
            <label class="item-label">产品Slogan<span class="check-tips">（产品Slogan，如：远离亚健康，从爱车开始）</span></label>
            <div class="controls">
                <input type="text" class="text input-large" name="slogan" value="<?php echo ((isset($info["slogan"]) && ($info["slogan"] !== ""))?($info["slogan"]):''); ?>">
            </div>
        </div>

        <div class="form-item">
        <label class="item-label">出售产品的店铺</label>
            <div class="controls">
                <?php if(is_array($all_shop)): $i = 0; $__LIST__ = $all_shop;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$canal): $mod = ($i % 2 );++$i;?><div style="width:350px;float: left;">
                    <label class="checkbox"> 
                        <input type="checkbox" name="shopid[]" value="<?php echo ($canal["id"]); ?>" <?php if(in_array(($canal["id"]), is_array($shopid)?$shopid:explode(',',$shopid))): ?>checked="true"<?php endif; ?>><?php echo ($canal["name"]); ?>
                    </label>
                </div><?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        </div>

        <div class="form-item">
            <label class="item-label">产品图片一<span class="check-tips">（建议尺寸640*400）</span></label>
            <div class="controls">
                <?php echo hook('adminPictureUpload', array('name'=>'picture1','value'=>$info['picture1']));?>
            </div>
        </div>
        <div class="form-item">
            <label class="item-label">产品图片二<span class="check-tips">（建议尺寸640*400）</span></label>
            <div class="controls">
                <?php echo hook('adminPictureUpload', array('name'=>'picture2','value'=>$info['picture2']));?>
            </div>
        </div>
        <div class="form-item">
            <label class="item-label">产品图片三<span class="check-tips">（建议尺寸640*400）</span></label>
            <div class="controls">
                <?php echo hook('adminPictureUpload', array('name'=>'picture3','value'=>$info['picture3']));?>
            </div>
        </div>
        <div class="form-item">
            <label class="item-label">产品内容<span class="check-tips">（产品服务包含的内容）</span></label>
            <div class="controls">
                <input type="text" class="text input-large" name="details" value="<?php echo ((isset($info["details"]) && ($info["details"] !== ""))?($info["details"]):''); ?>">
            </div>
            <label class="item-label">产品说明<span class="check-tips">（产品服务的详细说明）</span></label>
            <div>
                <label class="textarea">
                    <textarea name="content"><?php echo ($info["content"]); ?></textarea>
                    <?php echo hook('adminArticleEdit', array('name'=>'content','value'=>'content'));?>
                </label>
            </div>
        </div>
        <div class="form-item">
            <label class="item-label">业务时间<span class="check-tips"></span></label>
            <div class="controls">
                <input type="text" class="text input-large" name="time" value="<?php echo ((isset($info["time"]) && ($info["time"] !== ""))?($info["time"]):''); ?>">
            </div>
        </div>
        
        <div class="form-item">
            <label class="item-label">原价</label>
            <div class="controls">
                <input type="text" class="text input-small" name="oprice" value="<?php echo ((isset($info["oprice"]) && ($info["oprice"] !== ""))?($info["oprice"]):0); ?>">元
            </div>
        </div>

        <div class="form-item">
            <label class="item-label">业务收费</label>
            <div class="controls">
                <input type="text" class="text input-small" name="price" value="<?php echo ((isset($info["price"]) && ($info["price"] !== ""))?($info["price"]):0); ?>">元
            </div>
        </div>

        <div class="form-item">
            <label class="item-label">比例</label>
            <div class="controls">
                <input type="text" class="text input-small" name="price" value="<?php echo ((isset($info["price"]) && ($info["price"] !== ""))?($info["price"]):0); ?>">元
            </div>
        </div>
   
        <div class="form-item">
            <label class="item-label">状态<span class="check-tips"></span></label>
            <div class="controls">
                <label class="radio"><input type="radio" name="is_show" value="1">可用</label>
                <label class="radio"><input type="radio" name="is_show" value="0">禁用</label>
            </div>
        </div>
   
        <div class="form-item">
            <input type="hidden" name="id" value="<?php echo ((isset($info["id"]) && ($info["id"] !== ""))?($info["id"]):''); ?>">
            <input type="hidden" name="sort" value="0">
            <button class="btn submit-btn ajax-post" id="submit" type="submit" target-form="form-horizontal">确 定</button>
            <button class="btn btn-return" onclick="javascript:history.back(-1);return false;">返 回</button>
        </div>
    </form>

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
    
    <script type="text/javascript">
    Think.setValue("type", <?php echo ((isset($info["type"]) && ($info["type"] !== ""))?($info["type"]): 1); ?>);
    Think.setValue("is_show", <?php echo ((isset($info["is_show"]) && ($info["is_show"] !== ""))?($info["is_show"]): 1); ?>);
        //导航高亮
        highlight_subnav('<?php echo U('index');?>');

        $(document).ready(function(){

        });
    </script>

</body>
</html>