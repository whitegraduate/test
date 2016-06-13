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
        <h2><?php echo ($meta_title); ?></h2>
    </div>
    <form action="<?php echo U();?>" method="post" class="form-horizontal">
        <div class="form-item">
            <label class="item-label">产品</label>
            <div class="controls">
                <input type="text" class="text input-large" name="title" value="<?php echo ((isset($info["title"]) && ($info["title"] !== ""))?($info["title"]):''); ?>">
            </div>
        </div>
        <div class="form-item">
            <label class="item-label">货物名称</label>

            <div class="controls">
                <input type="text" class="text input-large" name="subtitle" value="<?php echo ((isset($info["subtitle"]) && ($info["subtitle"] !== ""))?($info["subtitle"]):''); ?>">
            </div>
        </div>
        <?php echo hook('province_city_area',array('cityid'=>$info["cityid"],'areaid'=>$info["areaid"]));?>
        <div class="form-item">
            <div class="controls">
                <label class="item-label with80">原价:</label>
                <input type="text" class="text input-small" name="original_price" value="<?php echo ((isset($info["original_price"]) && ($info["original_price"] !== ""))?($info["original_price"]):0); ?>">
            </div>
        </div>
        <div class="form-item">
            <div class="controls">
                <label class="item-label with80">会员价</label>
                <input type="text" class="text input-small" name="member_price" value="<?php echo ((isset($info["member_price"]) && ($info["member_price"] !== ""))?($info["member_price"]):0); ?>">
            </div>
        </div>
        <div class="form-item">
            <div class="controls">
                <label class="item-label with80">规格型号</label>
                <input type="text" class="text input-small" name="specification" value="<?php echo ($info["specification"]); ?>">
            </div>
        </div>
        <div class="form-item">
            <div class="controls">
                <label class="item-label with80">选择默认门店</label>
                <select name="default_shopid" >
                    <option value="0">所有门店</option>
                    <option value="3">道院塘社区店</option>
                    <option value="4">南苑社区店</option>
                    <option value="5">八一南街社区店</option>
                    <option value="6">解放西路社区店</option>
                    <option value="7">骆家塘社区店</option>
                    <option value="8">大学城租车</option>
                    <option value="11">上海财大租车店</option>
                    <option value="12">田畈社区店</option>
                    <option value="13">充电桩门店</option>
                </select>
            </div>
        </div>
        <div class="form-item">
            <div class="controls">
                <label class="item-label  with80">单位</label>
                <input type="text" class="text input-small" name="unit" value="<?php echo ($info["unit"]); ?>">
            </div>
        </div>
        <div class="form-item">
            <div class="controls">
                <label class="item-label  with80">数量</label>
                <input type="text" class="text input-small" name="quantity" value="<?php echo ($info["quantity"]); ?>">
            </div>
        </div>
        <div class="form-item">
            <div class="controls">
                <label class="item-label  with80">包装</label>
                <input type="text" class="text input-small" name="pack" value="<?php echo ($info["pack"]); ?>">
            </div>
        </div>
        <div class="form-item">
            <div class="controls">
                <label class="item-label  with80">厂家</label>
                <input type="text" class="text input-4x" name="factory" value="<?php echo ($info["factory"]); ?>">
            </div>
        </div>
        <div class="form-item">
            <div class="controls">
                <label class="item-label with80">尺寸</label>
                <input type="text" class="text input-small" name="size" value="<?php echo ($info["size"]); ?>">
            </div>
        </div>
        <div class="form-item">
            <div class="controls">
                <label class="item-label with80">购买后动作</label>
                <input type="text" class="text input-large" name="doaction" value="<?php echo ($info["doaction"]); ?>" placeholder="非开发者请勿配置">
            </div>
        </div>
        <div class="form-item attr">
            <label class="item-label">附加属性</label>
            <?php if(is_array($attr)): $i = 0; $__LIST__ = $attr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="controls">
                        <input type="text" class="text input-small" value="<?php echo ($vo["attrname"]); ?>" disabled>
                        <input type="text" class="text input-large" value="<?php echo ($vo["attrinfo"]); ?>" disabled>
                        &nbsp;&nbsp;<a href="#" data-id="<?php echo ($vo["id"]); ?>" id="del_attr">删除</a>
                    </div><?php endforeach; endif; else: echo "" ;endif; ?>
            <div class="controls">
                <input type="text" class="text input-small attrname"  placeholder="附加属性名">
                <input type="text" class="text input-large attrval"   placeholder="附加属值，用小逗号隔开">
                &nbsp;&nbsp;<a href="#" id="add_attr">添加</a>
            </div>
        </div>
        <div class="form-item">
            <label class="item-label">颜色<?php echo ($info['color']); ?></label>

            <div class="controls">
                <?php if(is_array($colorlist)): $i = 0; $__LIST__ = $colorlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><input type="checkbox" value="<?php echo ($vo["id"]); ?>" name="color[]"
                    <?php if(in_array(($vo['id']), is_array($info['color'])?$info['color']:explode(',',$info['color']))): ?>checked<?php endif; ?>
                    ><?php echo ($vo["color_name"]); ?>|<?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        </div>
        <div>
            <label class="textarea">
                <textarea name="content"><?php echo ($info['content']); ?></textarea> <?php echo hook('adminArticleEdit', array('name'=>'content','value'=>'content'));?>
            </label>
        </div>
        <div class="controls">
            <div class="upload">
                上传照片 <input type="file" id="upload_picture">
                <input type="hidden" name="icon" id="icon" value="<?php echo ((isset($info['pic']) && ($info['pic'] !== ""))?($info['pic']):''); ?>"/>

                <div class="upload-img-box">
                    <div class="upload-pre-item"><img src="<?php echo ($info["pic"]); ?>"/></div>
                </div>
                <script type="text/javascript" src="/Public/static/uploadify/jquery.uploadify.min.js"></script>
                <script type="text/javascript">
                    //上传图片
                    /* 初始化上传插件 */
                    $("#upload_picture").uploadify({
                        "height": 30,
                        "swf": "/Public/static/uploadify/uploadify.swf",
                        "fileObjName": "download",
                        "buttonText": "上传图片",
                        "uploader": "<?php echo U('Admin/File/uploadPicture',array('session_id'=>session_id()));?>",
                        "width": 120,
                        'removeTimeout': 1,
                        'fileTypeExts': '*.jpg; *.png; *.gif;',
                        "onUploadSuccess": uploadPicture,
                        'onFallback': function () {
                            //alert('未检测到兼容版本的Flash.');
                        }
                    });
                    function uploadPicture(file, data) {
                        var data = $.parseJSON(data);
                        var src = '';
                        if (data.status) {
                            $("#icon").val(data.path);
                            src = data.url || '' + data.path;
                            $("#icon").parent().find('.upload-img-box').html(
                                    '<div class="upload-pre-item"><img src=./' + src + ' width="100px" height="100px"></div>'
                            );
                        } else {
                            updateAlert(data.info);
                            setTimeout(function () {
                                $('#top-alert').find('button').click();
                                $(that).removeClass('disabled').prop('disabled', false);
                            }, 1500);
                        }
                    }
                    $(function(){
                        $("#add_attr").click(function(){
                            if($(".attrname").val()==""){alert("请输入属性名称！");return false;}
                            if($(".attrval").val()==""){alert("请输入至少一个属性值！");return false;}
                            $.post("",{"an":$(".attrname").val(),"av":$(".attrval").val(),"act":"attr"},function(data){
                                if(data.status==1){
                                    alert("属性添加成功！");
                                    location.reload(true);
                                }
                                else{
                                    alert("属性添加失败！");
                                }
                            },"json");
                        });
                        $(".attr").on("click","#del_attr",function(){
                            $.post("",{"act":"del","did":$(this).attr("data-id")},function(data){
                                if(data.status==1){
                                    alert("删除成功！");
                                    location.reload(true);
                                }
                                else{
                                    alert("删除失败！");
                                }
                            },"json")
                        });
                    });
                </script>
            </div>
        </div>
         <div class="form-item">
            <label class="item-label">商品排序<span class="check-tips">（值越大越靠前）</span></label>

            <div class="controls">
                <input type="text" class="text input-small" name="sort"
                       value="<?php echo ((isset($info["sort"]) && ($info["sort"] !== ""))?($info["sort"]):1); ?>">
            </div>
        </div>
         <div class="form-item">
			<label class="item-label">商品状态</label>
			<div class="controls">
				<?php if(is_array(C("MALL_STATUS"))): $i = 0; $__LIST__ = C("MALL_STATUS");if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$status): $mod = ($i % 2 );++$i;?><label class="inline radio"><input type="radio" name="status" value="<?php echo ($key); ?>"><?php echo ($status); ?></label><?php endforeach; endif; else: echo "" ;endif; ?>
			</div>
		</div>
        <div class="form-item">
            <input type="hidden" name="id" value="<?php echo ((isset($info["id"]) && ($info["id"] !== ""))?($info["id"]):''); ?>">
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
        Think.setValue("color", "<?php echo ($info["color"]); ?>");
        Think.setValue("status", <?php echo ((isset($info["status"]) && ($info["status"] !== ""))?($info["status"]): 0); ?>);
        //导航高亮
        highlight_subnav('<?php echo U('Config / index');?>');
    </script>

</body>
</html>