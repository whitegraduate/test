<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta  charset="UTF-8">
<meta  name="viewport"  content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<link rel="stylesheet" href="/Public/Wap/css/bootstrap.min.css" type="text/css" />
<link rel="stylesheet" href="/Public/Wap/css/style.css?v=1.5" type="text/css" />
<script type='text/javascript' src='/Public/Wap/js/jquery-2.0.3.min.js'></script>
<script>
$(document).ready(function(){
	$(".topnav").click(function(){
		$(".header a")[0].click();
	});
});
</script>
<title><?php echo C('WEB_SITE_TITLE');?></title>

</head>
<body>
	<!-- 头部 -->
	
<div class="header" >
	<?php if($is_index == 1 ): ?><div class="topnav" onclick="choose_city();">定位<font id="spancity" style="display: none;"></font></div>
		<?php else: ?> <a href="/wap.php?s=/Index/index.html"><div class="topnav">首页</div></a><?php endif; ?>

    <div class="logo"><?php echo ((isset($wap_title) && ($wap_title !== ""))?($wap_title):'滴啊滴租车'); ?></div>
	  <a href="#usermenu" role="button" class="btn" data-toggle="modal">
	  <div class="per"></div></a>
</div>
	<!-- /头部 -->
	
	<!-- 主体 -->
	



 
 <form id="form1" method="post">
   
<div class="cont">
  <div class="list">
    <div class="tb6"></div>
     <div class="upload">订单号：<?php echo $_GET['transid'];?></span></div>
  </div>
  
    
 
  <input type="hidden" name="transid" value="<?php echo $_GET['transid'];?>" />
  <?php if(isset($complain)): ?><div class="list">
      <div class="timo">手机号：<?php echo ($complain["mobile"]); ?></div>
  </div>
  
  <div class="list">
      <div class="timo">投诉内容：<?php echo ($complain["memo"]); ?></div>
  </div>
    
  <div class="list">
      <div class="timo">投诉状态：<?php echo (get_complain_status($complain["status"])); ?></div>
  </div>
  <div class="list">
      <div class="timo">投诉时间：<?php echo (date('Y-m-d H:i',$complain["create_time"])); ?></div>
  </div>
  <?php else: ?>
  
  <div class="list">
      <div class="tb4"></div>
      <div class="timo">手机号：</div>
      <input type="tel" id="mobile" name="mobile" class="input_box" value="<?php echo ($fans["mobile"]); ?>">
  </div>
  <div class="list">
      <div class="timo">投诉内容：</div>
      <input type="text" id="memo" name="memo"  class="input_box select2" value="">
  </div>
  <div class="ann">提交投诉</div><?php endif; ?> 
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
		"ROOT"   : "", //当前网站地址
		"APP"    : "/wap.php?s=", //当前项目地址
		"PUBLIC" : "/Public", //项目公共目录地址
		"DEEP"   : "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
		"MODEL"  : ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
		"VAR"    : ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"]
	}
})();
</script>

<script src="/Public/Wap/js/layer.m/layer.m.js" type="text/javascript"></script>
<script language="javascript">
var isWxPay = true;
  $(document).ready(function(){
      if($("body").height()<$(window).height()){$("body").height($(window).height());}//用于下拉框全屏可点

	  
  	$(".ann").click(function(event){
  		if('' == $("#memo").val())
		{
			galert('请描述您要投诉的内容');
			return;
		}
  		
  		if('' == $("#memo").val())
		{
			galert('请描述您要投诉的内容');
			return;
		}
  		$(this).text('正在提交...');
  		$(this).unbind( event );
  		$("#form1").submit();
  	});
  	
	  
  }); 
  
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
var _hmt = _hmt || [];
(function() {
  var hm = document.createElement("script");
  hm.src = "//hm.baidu.com/hm.js?97ad861300e99ed9bcd2c02f479e0874";
  var s = document.getElementsByTagName("script")[0]; 
  s.parentNode.insertBefore(hm, s);
})();
</script>
<!-- 页面footer钩子，一般用于加载插件JS文件和JS代码 -->
<?php echo hook('pageFooter', 'widget');?>
<div class="hidden"><!-- 用于加载统计代码等隐藏元素 -->
    
</div>

	<!-- /底部 -->
</body>
</html>