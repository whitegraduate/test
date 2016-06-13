<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta  charset="UTF-8">
<meta  name="viewport"  content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<link rel="stylesheet" href="/Public/Wap/css/bootstrap.min.css" type="text/css" />
<link rel="stylesheet" href="/Public/Wap/css/style2.css" type="text/css" />
<script type='text/javascript' src='/Public/Wap/js/jquery-2.0.3.min.js'></script>
<script type='text/javascript' src='/Public/Wap/js/base.js'></script>
<script>
$(document).ready(function(){
    $(".topleft").click(function(){
        history.go(-1);
    });
    $(".topright").click(function(){
        location.href = "<?php echo U('Fans/info');?>";
    });
});
</script>
<title><?php echo C('WEB_SITE_TITLE');?></title>

</head>
<body>
	<!-- 头部 -->
	<div class="top">
    <div class="topleft"><div class="topfan"></div></div>
    <div class="topmid"><?php echo ((isset($wap_title) && ($wap_title !== ""))?($wap_title):'凡购'); ?></div>
    <div class="topright"><div class="topren"></div></div>
    <div class="clearflx"></div>
</div>

	<!-- /头部 -->
	
	<!-- 主体 -->
	 



    <div class="geren">
        <div class="touxiang"><img src="<?php echo ($fans["headimgurl"]); ?>" width="100%" /></div>
        <div class="touming"><?php echo ($fans["nickname"]); ?></div>
        <div class="toubang"><a href="#">手机绑定</a></div>
    </div>
    <div class="kuan">
        <ul>
            <li id="li_order">
                <a href="#">
                    <div class="kuantu"><img src="/wxpay/Public/Wap/images/tubiao1.png" width="100%" /></div>
                    <div class="kuanzi">我的订单</div>
                    <div class="kuanjian"><img src="/wxpay/Public/Wap/images/jian.png" width="100%" /></div>
                    <div class="clearflx"></div>
                </a>
            </li>
            <li id="li_account">
                <a href="#">
                    <div class="kuantu"><img src="/wxpay/Public/Wap/images/tubiao2.png" width="100%" /></div>
                    <div class="kuanzi">我的账户</div>
                    <div class="kuanjian"><img src="/wxpay/Public/Wap/images/jian.png" width="100%" /></div>
                    <div class="clearflx"></div>
                </a>
            </li>
            <li style="display:none;">
                <a href="#">
                    <div class="kuantu"><img src="/wxpay/Public/Wap/images/tubiao3.png" width="100%" /></div>
                    <div class="kuanzi">我的优惠券</div>
                    <div class="kuanjian"><img src="/wxpay/Public/Wap/images/jian.png" width="100%" /></div>
                    <div class="clearflx"></div>
                </a>
            </li>
        </ul>
    </div>
    <div class="kuan">
        <ul>
            <li style="display:none;">
                <a href="#">
                    <div class="kuantu"><img src="/wxpay/Public/Wap/images/tubiao4.png" width="100%" /></div>
                    <div class="kuanzi">积分商城</div>
                    <div class="kuanjian"><img src="/wxpay/Public/Wap/images/jian.png" width="100%" /></div>
                    <div class="clearflx"></div>
                </a>
            </li>
            <li style="display:none;">
                <a href="#">
                    <div class="kuantu"><img src="/wxpay/Public/Wap/images/tubiao5.png" width="100%" /></div>
                    <div class="kuanzi">我的积分</div>
                    <div class="kuanjian"><img src="/wxpay/Public/Wap/images/jian.png" width="100%" /></div>
                    <div class="clearflx"></div>
                </a>
            </li>
        </ul>
    </div>
    <div class="kuan">
        <ul>
            <li id="li_auth_mobile">
                <a href="#">
                    <div class="kuantu"><img src="/wxpay/Public/Wap/images/tubiao6.png" width="100%" /></div>
                    <div class="kuanzi">手机认证</div>
                    <div class="kuanjian"><img src="/wxpay/Public/Wap/images/jian.png" width="100%" /></div>
                    <div class="clearflx"></div>
                </a>
            </li>
            <li id="li_auth_mobile">
                <a href="#">
                    <div class="kuantu"><img src="/wxpay/Public/Wap/images/tubiao7.png" width="100%" /></div>
                    <div class="kuanzi">身份认证</div>
                    <div class="kuanjian"><img src="/wxpay/Public/Wap/images/jian.png" width="100%" /></div>
                    <div class="clearflx"></div>
                </a>
            </li>
        </ul>
    </div>

	<!-- /主体 -->

	<!-- 底部 -->
	<div id="usermenu" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

    <div class="modal-body gesou">
        <div class="getiao">
            <a href="<?php echo U('Fans/info');?>">
                <div class="getu1"></div>
                <div class="gezi">个人中心</div>
                <div class="gejian"></div>
            </a>
        </div>
        <div class="getiao">
            <a href="<?php echo U('Fans/my');?>">
                <div class="getu2"></div>
                <div class="gezi">修改资料</div>
                <div class="gejian"></div>
            </a>
        </div>
        <div class="getiao">
            <a href="<?php echo U('Fans/order_repair');?>">
                <div class="getu3"></div>
                <div class="gezi">我的订单</div>
                <div class="gejian"></div>
            </a>
        </div>
    </div>
</div>

<script type="text/javascript" src="/Public/Wap/js/bootstrap.min.js"></script>
<script type='text/javascript' src='/Public/Wap/js/base.js'></script>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=bi6KWlVRxH8q59j9DB18Z1w8"></script>
<script src="/Public/Wap/js/map.js" type="text/javascript"></script>
<script type="text/javascript">
(function(){
	var ThinkPHP = window.Think = {
		"ROOT"   : "/wxpay", //当前网站地址
		"APP"    : "/wxpay/wap.php?s=", //当前项目地址
		"PUBLIC" : "/wxpay/Public", //项目公共目录地址
		"DEEP"   : "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
		"MODEL"  : ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
		"VAR"    : ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"]
	}
})();
</script>

 <script type="text/javascript">
	$(document).ready(function(){
		$(".ann").click(function(){
			$('#form1').submit();
		});
		
		var event = 'click';

		$(".touxiang").on(event,function(){
			//top.window.location = "<?php echo U('my');?>";
		});
		
		$("#li_order").on(event,function(){
			top.window.location = "<?php echo U('myorders');?>";
		});
		
		$("#li_auth_mobile").on(event,function(){
			//top.window.location = "<?php echo U('auth_mobile');?>";
		}); 
		
		$("#li_auth_idcard").on(event,function(){
			//top.window.location = "<?php echo U('auth_idcard');?>";
		});
		
		$('body').attr('style','background:#efefef;');
	});
	</script> 
 <!-- 用于加载js代码 -->
<script>
    // $(function () {
    //     $.get("<?php echo U('Location/getmycity');?>", {},
    //         function (data) {
    //             if (data != null) {
    //                 $("#spancity").html("(" + data['city'] + ")").show();
    //             }
    //             else {
    //             }
    //         });
    // });
</script>
<!-- 页面footer钩子，一般用于加载插件JS文件和JS代码 -->
<!-- <?php echo hook('pageFooter', 'widget');?> -->
<div class="hidden"><!-- 用于加载统计代码等隐藏元素 -->
    
</div>

	<!-- /底部 -->
</body>
</html>