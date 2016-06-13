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
        <h2><?php echo isset($info['id'])?'编辑':'新增';?>门店</h2>
    </div>
    <form action="<?php echo U();?>" method="post" class="form-horizontal">
        <div class="form-item">
            <label class="item-label">门店编号</label>
            <div class="controls">
                <input type="text" class="text input-large" name="shopno" value="<?php echo ((isset($info["shopno"]) && ($info["shopno"] !== ""))?($info["shopno"]):''); ?>">
            </div>
        </div> 
        
        <div class="form-item">
            <label class="item-label">名称</label>
            <div class="controls">
                <input type="text" class="text input-large" name="name" value="<?php echo ((isset($info["name"]) && ($info["name"] !== ""))?($info["name"]):''); ?>">
            </div>
        </div>

        <div class="form-item">
			<label class="item-label">
				维修业务<span class="check-tips">（是否支持维修业务）</span>
			</label>
			<div class="controls">
				<label class="inline radio"><input type="radio" name="s_repair" value="0"  checked>不支持</label>
				<label class="inline radio"><input type="radio" name="s_repair" value="1" >支持</label>
			</div>
	    </div>
        
        <div class="form-item">
			<label class="item-label">
				租车业务<span class="check-tips">（是否支持租车业务）</span>
			</label>
			<div class="controls">
				<label class="inline radio"><input type="radio" name="s_rent" value="0" checked>不支持</label>
				<label class="inline radio"><input type="radio" name="s_rent" value="1">支持</label>
			</div>
	    </div>
        
        <div class="form-item">
			<label class="item-label">
				保养业务<span class="check-tips">（是否支持保养业务）</span>
			</label>
			<div class="controls">
				<label class="inline radio"><input type="radio" name="s_maintain" value="0" checked>不支持</label>
				<label class="inline radio"><input type="radio" name="s_maintain" value="1">支持</label>
			</div>
	    </div>
        
        <div class="form-item">
			<label class="item-label">
				门店显示<span class="check-tips">（是否在前台页面显示）</span>
			</label>
			<div class="controls">
				<label class="inline radio"><input type="radio" name="status" value="0" >不显示</label>
				<label class="inline radio"><input type="radio" name="status" value="1" checked>显示</label>
			</div>
	    </div>

        <div class="form-item">
            <label class="item-label">
                精品业务<span class="check-tips">（是否支持精品业务）</span>
            </label>
            <div class="controls">
                <label class="inline radio"><input type="radio" name="s_mall" value="0" checked>不支持</label>
                <label class="inline radio"><input type="radio" name="s_mall" value="1">支持</label>
            </div>
        </div>


        <div class="form-item">
            <label class="item-label">所属运营商<span class="check-tips">（运营商）</span></label>
            <div class="controls">
                <select name="carrierid" class="carrierid" >
					<?php if(is_array($lstCarrier)): $i = 0; $__LIST__ = $lstCarrier;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$carrier): $mod = ($i % 2 );++$i;?><option value="<?php echo ($carrier["id"]); ?>"><?php echo ($carrier["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                </select>
            </div>
        </div>
        <?php echo hook('province_city_area',array('cityid'=>$info["cityid"],'areaid'=>$info["areaid"]));?>
        <div class="form-item">
            <label class="item-label">门店图片一<span class="check-tips">（建议尺寸640*400）</span></label>
            <div class="controls">
                <?php echo hook('adminPictureUpload', array('name'=>'picture1','value'=>$info['picture1']));?>
            </div>
        </div>
        <div class="form-item">
            <label class="item-label">门店图片二<span class="check-tips">（建议尺寸640*400）</span></label>
            <div class="controls">
                <?php echo hook('adminPictureUpload', array('name'=>'picture2','value'=>$info['picture2']));?>
            </div>
        </div>
        <div class="form-item">
            <label class="item-label">门店图片三<span class="check-tips">（建议尺寸640*400）</span></label>
            <div class="controls">
                <?php echo hook('adminPictureUpload', array('name'=>'picture3','value'=>$info['picture3']));?>
            </div>
        </div>
        
         <div class="form-item">
            <label class="item-label">地址</label>
            <div class="controls">
                <input type="text" class="text input-large" id="address" name="address" value="<?php echo ((isset($info["address"]) && ($info["address"] !== ""))?($info["address"]):''); ?>">
            </div>
        </div>
        
        <div class="form-item">
             <div style="width:500px;height:300px;" id="allmap"></div>
        </div>
      
        <div class="form-item">
            <label class="item-label">经度</label>
            <div class="controls">
                <input type="text" class="text input-large" id="longitude" name="longitude" value="<?php echo ((isset($info["longitude"]) && ($info["longitude"] !== ""))?($info["longitude"]):''); ?>">
            </div>
        </div>
      
        <div class="form-item">
            <label class="item-label">纬度</label>
            <div class="controls">
                <input type="text" class="text input-large" id="latitude" name="latitude" value="<?php echo ((isset($info["latitude"]) && ($info["latitude"] !== ""))?($info["latitude"]):''); ?>">
            </div>
        </div>
   
      
        <div class="form-item">
            <label class="item-label">固话</label>
            <div class="controls">
                <input type="text" class="text input-large" name="tel" value="<?php echo ((isset($info["tel"]) && ($info["tel"] !== ""))?($info["tel"]):''); ?>">
            </div>
        </div>
   
      
        <div class="form-item">
            <label class="item-label">手机号</label>
            <div class="controls">
                <input type="text" class="text input-large" name="mobile" value="<?php echo ((isset($info["mobile"]) && ($info["mobile"] !== ""))?($info["mobile"]):''); ?>">
            </div>
        </div>
        
        <div class="form-item">
			<label class="item-label">门店介绍<span class="check-tips">（门店基本介绍）</span> </label>
			<div class="controls">
			<label class="textarea input-large">
				<textarea name="intro"><?php echo ($info["intro"]); ?></textarea>
			</label>				
			</div>
		</div>
   
        <div class="form-item">
            <input type="hidden" name="id" value="<?php echo ((isset($info["id"]) && ($info["id"] !== ""))?($info["id"]):''); ?>">
            <input type="hidden" name="shop_type" value="1">
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
    
    <script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=bi6KWlVRxH8q59j9DB18Z1w8"></script>
    <script type="text/javascript">
        Think.setValue("lstCarrier", <?php echo ((isset($info["lstCarrier"]) && ($info["lstCarrier"] !== ""))?($info["lstCarrier"]): 1); ?>);
		Think.setValue("s_repair", <?php echo ((isset($info["s_repair"]) && ($info["s_repair"] !== ""))?($info["s_repair"]):0); ?>);
		Think.setValue("s_rent", <?php echo ((isset($info["s_rent"]) && ($info["s_rent"] !== ""))?($info["s_rent"]):0); ?>);
		Think.setValue("s_maintain", <?php echo ((isset($info["s_maintain"]) && ($info["s_maintain"] !== ""))?($info["s_maintain"]):0); ?>);
        Think.setValue("s_mall", <?php echo ((isset($info["s_mall"]) && ($info["s_mall"] !== ""))?($info["s_mall"]):0); ?>);
		Think.setValue("status", <?php echo ((isset($info["status"]) && ($info["status"] !== ""))?($info["status"]):1); ?>);
        //导航高亮
        highlight_subnav('<?php echo U('index');?>');

        // 百度地图API功能
    	var map = new BMap.Map("allmap");
    	map.centerAndZoom(new BMap.Point(119.654889, 29.08597), 11);
    	
    	var top_left_control = new BMap.ScaleControl({anchor: BMAP_ANCHOR_TOP_LEFT});// 左上角，添加比例尺
    	var top_left_navigation = new BMap.NavigationControl();  //左上角，添加默认缩放平移控件
    	map.addControl(top_left_control);        
		map.addControl(top_left_navigation); 
		
    	function showInfo(e){
    		//alert(e.point.lng + ", " + e.point.lat);
    		$('#latitude').val(e.point.lat);
    		$('#longitude').val(e.point.lng);
    	} 
    	map.addEventListener("click", showInfo);
    	
    	function address2point(addr)
    	{
    		var myGeo = new BMap.Geocoder();
    		myGeo.getPoint(addr, function(point){
    			if (point) {
    				map.centerAndZoom(point, 16);
    				map.addOverlay(new BMap.Marker(point));
    				$('#latitude').val(point.lat);
    	    		$('#longitude').val(point.lng);
    				
    			}else{
    				alert("您选择地址没有解析到结果!");
    			}
    		}, "金华市");
    	}
    	
    	$(document).ready(function(){
			$("#address").blur(function(){
				address2point($(this).val());
			});
    	});
    </script>

</body>
</html>