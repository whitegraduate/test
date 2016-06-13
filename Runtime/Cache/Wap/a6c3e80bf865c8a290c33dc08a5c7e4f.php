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
	

<link rel="stylesheet" type="text/css" href="/wxpay/Public/Wap/css/ll.css" />



<?php if($order["status"] == 4): ?><!-- 抢单 -->
 
<?php else: ?> <!-- 非抢单 -->
 
   
<div style="margin-top: 0;">
	<div id="myTabContent" class="tab-content">
		<ul role="tabpanel" class="tab-pane active order" id="home">
			<li style="border: 0;">
				<div class="biaotid">
					<p>
						订单号：<?php echo ($order["transid"]); ?> <span><?php echo ($order["statusname"]); ?></span>
					</p>
					<div class="clearfix"></div>
				</div>

				<div class="dingaw">
					<div class="neirong">保养产品：<?php echo ($order["product"]); ?></div>
					<div class="neirong">
						 
						下单时间：<?php echo (date('Y-m-d H:i',$order["create_time"])); endif; ?>
					</div>
				 
					<div class="neirong">维修地址：滴啊滴-<?php echo ($order["shop"]); ?></div>
					<div class="neirong">费用：<?php if($order["price"] == '0.00'): ?>等待计算<?php else: echo ($order["price"]); ?>元<?php endif; ?></div>
					<div class="clearfix"></div>
				</div>


			</li>

		</ul>

	</div>
</div>
<div class="clearfix"></div>
<?php if($order["status"] == 1): ?><!-- 待支付 -->
<div id="btn_pay" class="ann">立即支付</div>
<?php elseif($order["status"] == 0): ?>
<!-- 待检查 -->
<form id="form2" method="post">
<div id="btn_cancel2" class="ann">取消订单</div>
<input type="hidden" name="oid" id="oid" value="<?php echo $_GET['oid'];?>" />
</form><?php endif; ?> 
<div class="sh_foot" style="display:none;">
	<?php if($order["status"] == 0): ?><div class="price2">取消订单</div><?php endif; ?>
	<?php if($order["order_type"] == 1): ?><div class="price1">到店维修</div><?php endif; ?>
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
 <script>
$(document).ready(function(){
	$(".topleft").click(function(){
		top.window.location = "<?php echo U('Fans/order_maintain');?>";
	});

	$("#btn_cancel2").click(function(){
		if(confirm('确定要取消订单？'))
			$('#form2').submit();
	});
	
	$("#btn_pay").click(function(){
		top.location.href = "<?php echo U('Maintain/pay',array('oid'=>$order['id']));?>";
	});
	
	$("body").attr('style','background:#f1f1ef;');
});
</script>  <!-- 用于加载js代码 -->
<script>
    $(function () {
        $.get("<?php echo U('Location/getmycity');?>", {},
            function (data) {
                if (data != null) {
                    console.log('1');
                    $("#spancity").html("(" + data['city'] + ")").show();
                }
                else {
                    console.log('2');
                    //loc_ser_now();
                }
            });
    });
</script>
<!-- 页面footer钩子，一般用于加载插件JS文件和JS代码 -->
<?php echo hook('pageFooter', 'widget');?>
<div class="hidden"><!-- 用于加载统计代码等隐藏元素 -->
    
</div>

	<!-- /底部 -->
</body>
</html>