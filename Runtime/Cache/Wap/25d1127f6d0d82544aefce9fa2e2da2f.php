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
	
    <link href="/Public/Wap/css/style.css" rel="stylesheet">



 <form id="form1" method="post">
<div class="cont">
<?php if(is_array($mall)): $i = 0; $__LIST__ = $mall;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="list">
    <div class="tb3"></div>
    <div class="upload">产品：<?php echo ($vo["title"]); if($vo['quantity'] != null ): ?><span>数量X<?php echo ($vo["quantity"]); ?></span><?php endif; ?></div>
  </div><?php endforeach; endif; else: echo "" ;endif; ?>
      <div class="list">
          <div class="tb2"></div>
          <?php if($order['shopid'] != 0): ?><div class="upload">提货门店：<?php echo (get_shop_id($order["shopid"])); ?></div>
          <?php else: ?>
              <div class="upload">无需提货门店</div><?php endif; ?>
      </div>
      <div class="list">
     <div class="tb7"></div>
     <div class="upload">费用<span><?php echo ($order["price"]); ?>元</span></div>
  </div>


<div class="but_box">
<div class="ann pay_btn">确认支付</div>
</div>  

</div>


</form>

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

<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$('.pay_btn').on("click",function(){
		doPay(); 
	});
    $('.cont').on("click",".close_btn",function(){
        WeixinJSBridge.call('closeWindow');
	});
}); 

wx.config({
    appId: '<?php echo ($jsapi_sign_package["appId"]); ?>',
    timestamp: '<?php echo ($jsapi_sign_package["timestamp"]); ?>',
    nonceStr: '<?php echo ($jsapi_sign_package["nonceStr"]); ?>',
    signature: '<?php echo ($jsapi_sign_package["signature"]); ?>',
    jsApiList: [
                    'onMenuShareTimeline',
                    'onMenuShareAppMessage',
                    'onMenuShareQQ',
                    'onMenuShareWeibo',
                    'hideOptionMenu',
                    'showOptionMenu',
                    'chooseWXPay'
                    // 所有要调用的 API 都要加到这个列表中
                ]
});
//支付
function doPay() {
    layer.open({type: 2, content: '加载中，请稍后！'});
    $.post("<?php echo C('SITE_URL');?>/wap.php/Mall/prepay", {
                rsid: "<?php echo ($rid); ?>",
                oprice:"<?php echo ($orderprice); ?>"},
            function(data){
                //updateBtnPay(0);
                if (data) {
                    layer.closeAll();
                    if (data.code == 1) {
                        // 微信统一支付成功
                        wx.chooseWXPay({
                            timestamp: data.timeStamp, // 支付签名时间戳，注意微信jssdk中的所有使用timestamp字段均为小写。但最新版的支付后台生成签名使用的timeStamp字段名需大写其中的S字符
                            nonceStr: data.nonceStr, // 支付签名随机串，不长于 32 位
                            package: data.package, // 统一支付接口返回的prepay_id参数值，提交格式如：prepay_id=***）
                            signType: data.signType, // 签名方式，默认为'SHA1'，使用新版支付需传入'MD5'
                            paySign: data.paySign, // 支付签名
                            success: function (res) {
                                var last=JSON.stringify(res);
                                //alert(last);
                                // 支付成功后的回调函数
                                if(res.errMsg == "chooseWXPay:ok" ) {
                                    afterPay();
                                }
                                else
                                    alert('errpay');
                            }
                        });
                    } else { alert(data.msg);

                    }
                }
            },"json");
}


//支付成功
function afterPay($orderid) {
    
     $('.but_box').html("<div class='ann close_btn'>支付成功，点击关闭</div>");
}
</script>
 <!-- 用于加载js代码 -->
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