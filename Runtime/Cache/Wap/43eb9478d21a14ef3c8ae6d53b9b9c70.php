<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta  charset="UTF-8">
<meta  name="viewport"  content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<link rel="stylesheet" href="/Public/Wap/css/bootstrap.min.css" type="text/css" />
<link rel="stylesheet" href="/Public/Wap/css/ll.css" type="text/css" />
<script type='text/javascript' src='/Public/Wap/js/jquery-2.0.3.min.js'></script>
<script type='text/javascript' src='/Public/Wap/js/base.js'></script>
<script>
$(document).ready(function(){
//	$(".topnav").click(function(){
//		$(".header a")[0].click();
//	});
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
	




<div class="geren" style="margin-top:0;">
 <ul class="list1" id="myTab" role="tabpanel" style="margin-top:0;">
       <li class="dropdown  li_li">
          <a href="#" class="dropdown-toggle" data-toggle="dropdown">维修订单<b class="caret"></b></a>
          <ul class="dropdown-menu menu">
              <li><a class="menu_li" href="#home" data-toggle="tab">维修订单</a></li>
              <li><a class="menu_li" href="<?php echo U('Fans/order_maintain');?>">保养订单</a></li>
              <li><a class="menu_li" href="<?php echo U('Fans/order_charge');?>">充电订单</a></li>
              <li><a class="menu_li" href="<?php echo U('Fans/order_rent');?>">租车订单</a></li>
              <li><a class="menu_li" href="<?php echo U('Fans/order_mall');?>">精品订单</a></li>
           </ul>
        </li>
        
        <?php if(is_array(C("ORDER_STATUS_REPAIR"))): $i = 0; $__LIST__ = C("ORDER_STATUS_REPAIR");if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$type): $mod = ($i % 2 );++$i;?><li class="li_li <?php if(($_GET['s'] != '') and ($_GET['s'] == $key)): ?>active<?php endif; ?>"><a href="<?php echo U('Fans/order_repair',array('s'=>$key));?>" data-toggle="tabpanel"><?php echo ($type); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
         
  </ul>
  <div  id="myTabContent" class="tab-content">
   <ul role="tabpanel"  class="tab-pane active order" id="home" >
   <?php if(isset($lstOrder)): if(is_array($lstOrder)): $i = 0; $__LIST__ = $lstOrder;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i; if($list["status"] == 1): ?><!-- 等待支付 -->
       
        <li><a href="#" style="color:#333; text-decoration:none;">
       <div class="biaotid">
       <p>订单号：<?php echo ($list["transid"]); ?> <span><?php echo (get_repairorder_astatus($list["status"])); ?></span></p>
       <div class="clearfix"></div>
       </div>
       
          <div id="parts_info_<?php echo ($list["id"]); ?>" class="dingaw">
              <div class="yikuai">滴啊滴-<?php echo (get_shop_id($list["shopid"])); ?></div>
              	<div id="parts_in_<?php echo ($list["id"]); ?>"></div>
              	
              
          </div>
          <script>
          		if('<?php echo ($list["parts_in"]); ?>' != '')
          		{
	          		if('<?php echo ($list["parts_in"]); ?>'.indexOf('[') >= 0)
	              	{
		          		var parts_in = eval('<?php echo ($list["parts_in"]); ?>');
		              	for(var i=0;i<parts_in.length;i++)
		              	{
		              		$("#parts_info_<?php echo ($list["id"]); ?>").append("<div class='neirong'><div class='shangrong'>"+parts_in[i]['p']+"</div>" + 
		              				"<div class='shangmig'></div>" + "<div class='shuzhi'>数量："+parts_in[i]['n']+"</div>" + 
		              				"<div class='shujia'></div></div>");
		              	}
		              	
	              	}
          		}
          		
          		if('<?php echo ($list["parts_out"]); ?>' != '')
          		{
          			if('<?php echo ($list["parts_out"]); ?>'.indexOf('[') >= 0)
	              	{
		              	var parts_out = eval('<?php echo ($list["parts_out"]); ?>');
		              	for(var i=0;i<parts_out.length;i++)
		              	{
		              		$("#parts_info_<?php echo ($list["id"]); ?>").append("<div class='neirong'><div class='shangrong'>"+parts_out[i]['p']+"</div>" + 
		              				"<div class='shangmig'></div>" + "<div class='shuzhi'>数量："+parts_out[i]['n']+"</div>" + 
		              				"<div class='shujia'></div></div>");
		              	}
	              	}
          		}

              	$("#parts_info_<?php echo ($list["id"]); ?>").append("<div class='clearfix'></div>");
          </script>
          <div class="anjia">工时费：￥<?php echo ($list["work_price"]); ?>    合计：￥<?php echo ($list["price"]); ?></div>
          </a>
          <div class="ann">
            <a href="<?php echo U('Fans/order_repair_details',array('oid'=>$list['id']));?>"> <div class="sc_ann">详情</div></a>
            <a href="/wxpay/wap.php?s=/Repair/pay/oid/<?php echo ($list['id']); ?>"><div class="pj_ann">支付</div></a>
          </div>
       </li>
        <?php else: ?> 
       <!-- 等待检查 -->
			<li><a href="#" style="color: #333; text-decoration: none;">
					<div class="biaotid">
						<p>
							订单号：<?php echo ($list["transid"]); ?> <span><?php echo (get_repairorder_astatus($list["status"])); ?></span>
						</p>
						<div class="clearfix"></div>
					</div>

					<div class="dingaw">
						<div class="yikuai">维修地址：滴啊滴-<?php echo (get_shop_id($list["shopid"])); ?></div>
						<div class="neirong">车辆状况：<?php echo ($list["problem"]); ?></div>
						<div class="neirong">下单时间：<?php echo (date("Y年m月d日H时i分",$list["create_time"])); ?></div>
						<div class="neirong">联系电话：<?php echo ($list["mobile"]); ?></div>
						<div class="clearfix"></div>
					</div>
			    </a>
				<div class="ann">
            		<a href="<?php echo U('Fans/order_repair_details',array('oid'=>$list['id']));?>"> <div class="sc_ann">详情</div></a>
            		
		             <?php if(($list["status"] == 2)): if($list["isremark"] == 0): ?><a href="#"><div class="pj_ann pingjia" id="<?php echo ($list["id"]); ?>">评价</div></a><?php endif; ?>
            	
		            	<a href="<?php echo U('Fans/complain',array('transid'=>$list['transid']));?>"><div class="pj_ann">投诉</div></a><?php endif; ?>
				</div></li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
       <?php else: ?>
       <div class="noorder">
          <div class="tb"></div>
          <p>您还没有相关订单</p>
          <span>去看看您需要什么</span>
          <a href="<?php echo U('Repair/index');?>">去看看</a>
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
		$("body").attr('style','background:#f1f1ef;');
		
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
		
		$(".pingjia").click(function(){
			var oid = $(this).attr('id');
			top.location.href = "/wap.php/Fans/remark/flag/WX/oid/"+oid+'.html';
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