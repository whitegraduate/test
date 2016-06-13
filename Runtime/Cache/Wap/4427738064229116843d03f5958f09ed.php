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
	
    <link rel="stylesheet" href="/Public/Wap/css/style.css" type="text/css" />




<div style="margin-top:0;">
  <div  id="myTabContent" class="tab-content">
   <ul role="tabpanel"  class="tab-pane active order" id="home">
      <?php if(isset($lstOrder)): if(is_array($lstOrder)): $i = 0; $__LIST__ = $lstOrder;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><li>
          <p>订单号：<?php echo ($list["transid"]); ?> <span><?php echo ($list["statusname"]); ?></span></p>
          <p>下单时间：<?php echo (date("Y年m月d日H时i分",$list["create_time"])); ?></p>
          <p>门店：<?php echo ($list["shop"]); ?></p>
          <div class="anniu2">
            <a href="<?php echo U('Fans/order_maintain_details',array('oid'=>$list['id']));?>"> <div class="sc_ann">详情</div></a>
            <?php if($list["status"] == 1): ?><a href="/wxpay/wap.php?s=/Maintain/pay/oid/<?php echo ($list['id']); ?>"><div class="pj_ann">支付</div></a><?php endif; ?>
            <?php if(($list["status"] == 2)): ?><a href="<?php echo U('Fans/order_confirm',array('transid'=>$list['transid']));?>"><div class="pj_ann">商家使用</div></a><?php endif; ?>
          </div>
       </li><?php endforeach; endif; else: echo "" ;endif; ?>
       <?php else: ?>
       <div class="noorder">
          <div class="tb"></div>
          <p>您还没有相关订单</p>
          <span>去看看您需要什么</span>
          <a href="<?php echo U('Maintain/index');?>">去看看</a>
      </div><?php endif; ?>
    </ul>
   </div>
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

	<script type="text/javascript">
	$(document).ready(function(){

		$('#myTab a').click(function (e) {
		  //e.preventDefault();
		  //$(this).tab('show');
		})
		
		$(".ann").click(function(){
			$("#form1").submit();
		});
		
		$("#my").click(function(){
			top.location.href = "<?php echo U('Fans/my');?>";
		});
		
		$("#score").click(function(){
			top.location.href = "<?php echo U('Fans/score_get');?>";
		});
		
		$("#myorder").click(function(){
			top.location.href = "<?php echo U('Fans/index');?>";
		});
		
		$(".pj_ann").click(function(){
			var oid = $(this).attr('id');
			top.location.href = "/wap.php/Fans/remark/flag/BY/oid/"+oid+'.html';
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