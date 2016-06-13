<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta  charset="UTF-8">
<meta  name="viewport"  content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="/Public/Wap/css/bootstrap.min.css" type="text/css" />
<link href="/Public/Wap/css/style2.css" rel="stylesheet">
<script type='text/javascript' src='/Public/Wap/js/jquery-2.0.3.min.js'></script>
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
	



    <div class="changpin">
        <img src="<?php echo ($product["picture1"]); ?>" width="100%" />
        <div class="biaoti"><?php echo ($product["name"]); ?>--<?php echo ($product["slogan"]); ?></div>
    </div>
    <div class="baikuan">
        <div class="baifen">
            <div class="huiyuan">会员价</div>
            <div class="xianjia"><?php echo ($product["price"]); ?></div>
            <div class="yuanjia">原价：<?php echo ($product["oprice"]); ?></div>
            <div class="clearflx"></div>
        </div>
        <div class="baifen" style="display:none;">
            <div class="baixiao">销量：10件</div>
            <div class="baixiao">收藏：30人</div>
            <div class="clearflx"></div>
        </div>
        <div class="weiquan">
            <ul>
                <li>
                    <div class="weitu"><img src="/Public/Wap/images/tu1.jpg" width="15"/></div>
                    <div class="weizi">正品保证</div>
                </li>
                <li>
                    <div class="weitu"><img src="/Public/Wap/images/tu1.jpg" width="15"/></div>
                    <div class="weizi">维修保养</div>
                </li>
                <li>
                    <div class="weitu"><img src="/Public/Wap/images/tu1.jpg" width="15"/></div>
                    <div class="weizi">权威鉴定</div>
                </li>

                <div class="clearflx"></div>
            </ul>
        </div>
    </div>
    <div class="baikuan" style="display:none;">
        <div class="kuan biankuan">
            <div class="kuanzi">颜色</div>
            <div class="kuanjian"><img src="/Public/Wap/images/jian.png" width="100%" /></div>
            <div class="clearflx"></div>
        </div>
        <div class="yanse">
            <ul>
                <li>红色</li>
                <li>黑色</li>
                <li>蓝色</li>
                <div class="clearflx"></div>
            </ul>
        </div>
        <div class="kuan biankuan juli">
            <div class="kuanzi">尺码</div>
            <div class="kuanjian"><img src="/Public/Wap/images/jian.png" width="100%" /></div>
            <div class="clearflx"></div>
        </div>
        <div class="yanse">
            <ul>
                <li>XL</li>
                <li>L</li>
                <li>M</li>
                <div class="clearflx"></div>
            </ul>
        </div>
    </div>
    <div class="baikuan">
        <div class="kuan biankuan">
            <div class="kuanzi">商品详情</div>
            <div class="kuanjian"><img src="/Public/Wap/images/jian.png" width="100%" /></div>
            <div class="clearflx"></div>
        </div>
        <div class="content">
            <?php echo ($product["content"]); ?>
        </div>
    </div>
<div class="ann1">立即下单</div>


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
		"ROOT"   : "", //当前网站地址
		"APP"    : "/wap.php?s=", //当前项目地址
		"PUBLIC" : "/Public", //项目公共目录地址
		"DEEP"   : "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
		"MODEL"  : ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
		"VAR"    : ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"]
	}
})();
</script>

<script language="javascript">
  $(document).ready(function(){
  	$(".ann1").click(function(){
  		location.href = "/wxpay/wap.php?s=/Maintain/order/pid/<?php echo ($product["id"]); ?>.html";
  	});

      $('body').attr("style","background:#efefef;");


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