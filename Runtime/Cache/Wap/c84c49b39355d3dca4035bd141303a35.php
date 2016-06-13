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
		<?php else: ?> <a href="/wap.php?s=/Index/index.html"><div class="topnav"></div></a><?php endif; ?>

    <div class="logo"><?php echo ((isset($wap_title) && ($wap_title !== ""))?($wap_title):'凡购'); ?></div>
	  <a href="#usermenu" role="button" class="btn" data-toggle="modal">
	  <div class="per"></div></a>
</div>
	<!-- /头部 -->
	
	<!-- 主体 -->
	  


 
 
<div class="cont">
<?php if(is_array($lstAddr)): $i = 0; $__LIST__ = $lstAddr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><div class="add" id="div<?php echo ($list["id"]); ?>" data="<?php echo ($list["id"]); ?>">
    <div class="wz"> 
      <span>收货人：<?php echo ($list["realname"]); ?></span> <span><?php echo ($list["mobile"]); ?></span>
      <p><?php echo ($list["address"]); ?></p>
      <em>（收货不方便时，可以发货至滴啊滴社区店）</em>
      <span class="moren">设为默认</span>
      <span class="shanchu" data="<?php echo ($list["id"]); ?>">删除地址</span>
    </div>
    <div class="kajian"></div>
  </div><?php endforeach; endif; else: echo "" ;endif; ?>
</div>
<div class="pj_foot">
  <a href="<?php echo U('Fans/fans_address_add');?>"><div class="pj_ann">新增收货地址</div></a>
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
		"ROOT"   : "", //当前网站地址
		"APP"    : "/wap.php?s=", //当前项目地址
		"PUBLIC" : "/Public", //项目公共目录地址
		"DEEP"   : "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
		"MODEL"  : ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
		"VAR"    : ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"]
	}
})();
</script>
 
<script>
$(document).ready(function(){
	$('.shanchu').click(function(){
		if(confirm('删除后将不可恢复，是否继续?'))
		{
			var id = $(this).attr('data');
			
			$.ajax({
	             type: "GET",
	             url: "<?php echo U('Fans/fans_address_del');?>",
	             data: {aid:id},
	             dataType: "json",
	             success: function(data){
	                          //alert('删除成功!');
	                          if(data.flag == '1')
	                          {
	                          	$('#div'+id).slideUp();
	                          }else
	                        	  alert(data.msg);
	             			}
	         });
		}
	});
	
	$(".add").click(function(){
		
		var id = $(this).attr('data');
		top.window.location = "/wap.php?s=/Fans/fans_address_edit/aid/" + id + ".html";
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