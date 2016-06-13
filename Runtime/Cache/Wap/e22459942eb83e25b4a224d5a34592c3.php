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
	



    <form id="form1" method="post">
    <div class="content">
        <div class="xiaokuan">
            <div class="xiaozi">选择门店</div>
            <div class="xiaojian2">
                <select name="shopid" id="shopid">
                    <option value="">请选择...</option>
                    <?php if(is_array($lstShop)): $i = 0; $__LIST__ = $lstShop;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><option value="<?php echo ($list["id"]); ?>"><?php echo ($list["name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                </select></div>
            <div class="clearflx"></div>
        </div>
        <div class="xiaokuan">
            <div class="xiaozi">选择产品</div>
            <div class="xiaojian changui"><?php echo ($product["name"]); ?></div>
            <div class="clearflx"></div>
        </div>
        <div class="xiaokuan">
            <div class="xiaozi">保养费用</div>
            <div class="xiaojian feiyong">￥<?php echo ($product["price"]); ?></div>
            <div class="clearflx"></div>
        </div>
        <div class="xiaokuan">
            <div class="xiaozi">实际应付</div>
            <div class="xiaojian feiyong">￥<?php echo ($product["price"]); ?></div>
            <div class="clearflx"></div>
        </div>
        <div class="anniu">提交</div>
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

<script src="/Public/Wap/js/layer.m/layer.m.js" type="text/javascript"></script>
<script language="javascript">
var isWxPay = true;
var pay_money = <?php echo ($product["price"]); ?>;
  $(document).ready(function(){
      if($("body").height()<$(window).height()){$("body").height($(window).height());}//用于下拉框全屏可点

	  
  	$(".anniu").click(function(event){
  		if('' == $("#shopid").val())
		{
			lalert('请选择门店');
			return;
		}
  		$(this).text('正在提交...');
  		$(this).unbind( event );
  		$("#form1").submit();
  	});
  	
	$(".heikuan").click(function(){
		
		if($(this).prop('checked'))
		{
			var cprice = parseFloat($(this).attr('data')).toFixed(2);
			var tprice = parseFloat(<?php echo ($product["price"]); ?>).toFixed(2);

			var pprice = (tprice - cprice).toFixed(2);
			if(pprice > 0)
			{
				pay_money = pprice;
				$("#couponid").val($(this).val());
				$("#pprice").text(pprice + '元');
			}
			else
			{
				pay_money = 0;
				$("#couponid").val($(this).val());
				$("#pprice").text('0元');
			}
		}
		else
		{
			$("#couponid").val('0');
			pay_money = <?php echo ($product["price"]); ?>;
			$("#pprice").text('<?php echo ($product["price"]); ?>元');
		}
	});
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