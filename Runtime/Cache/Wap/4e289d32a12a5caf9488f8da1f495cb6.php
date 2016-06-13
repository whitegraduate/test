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



<div id="shopping-cart">
<?php if(is_array($shopping_cart)): $i = 0; $__LIST__ = $shopping_cart;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="shopping-cart-cont" name="<?php echo ($vo["title"]); ?>">
	<div class="shopping">
		<div class="nr" >
			<input type="checkbox" name="checkbox" class="shopping-cart-checkbox" product-id="<?php echo ($vo["id"]); ?>" <?php if($vo["checked"] == 1): ?>checked<?php endif; ?> >
			<div class="tup">
				<img src="<?php echo ($vo["pic"]); ?>" ></div>
			<div class="wz">
				<p>
					<?php echo ($vo["title"]); ?>
					<a href="#" class="deleteDiv" name="<?php echo ($vo["title"]); ?>" product-id="<?php echo ($vo["id"]); ?>">删除</a>
				</p>
				<span>颜色：<?php echo (get_color_id($vo["color"])); ?></span>
				<string >￥<lebel class="shopping-cart-price"><?php echo ($vo["member_price"]); ?></lebel>
					
					<div class="sjia">
						<input class="min" type="button" product-id="<?php echo ($vo["id"]); ?>"  value="-">
						<input class="text_box quantity-<?php echo ($vo["id"]); ?>" type="text" value="<?php echo ($vo["quantity"]); ?>">
						<input class="aded"  type="button"  product-id="<?php echo ($vo["id"]); ?>" value="+"></div>
				</string>
			</div>
		</div>
	</div>
</div><?php endforeach; endif; else: echo "" ;endif; ?>
</div>
<div class="sh_foot">
	<!-- <input type="checkbox" id="select-all"> -->
   <div class="wz"> <p>合计：￥<span id="total_price" >0</span></p></div>
   <button class="price1">结算</button>
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
		"APP"    : "/index.php?s=", //当前项目地址
		"PUBLIC" : "/Public", //项目公共目录地址
		"DEEP"   : "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
		"MODEL"  : ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
		"VAR"    : ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"]
	}
})();
</script>

<script>
	$(function(){	
		calculatePrice();
		$(".min").click(function(){
			var id=$(this).attr('product-id');
			if($(".quantity-"+id).val()==1){
				return false;
			}
			$.post("/wap.php?s=/Mall/shopping_cart/from/x",{id:id,quantity:-1},function(result){
				$(".quantity-"+id).val(result[id]['quantity']);
				calculatePrice();
			});

		});
		$(".aded").click(function(){
			var id=$(this).attr('product-id');
			$.post("/wap.php?s=/Mall/shopping_cart/from/x",{id:id,quantity:1},function(result){
				$(".quantity-"+id).val(result[id]['quantity']);
				calculatePrice();
			});
		});
		$(".deleteDiv").click(function(){
			var element=$(this).attr("name");
			var id=$(this).attr('product-id');
			$.post("/wap.php?s=/Mall/shopping_product_delete/from/x",{id:id,checked:0},function(result){
				$("#shopping-cart>div[name="+element+"]").remove();
				calculatePrice();
			});
		});
		$(".nr .shopping-cart-checkbox").click(function(){
			var id=$(this).attr("product-id");
			if(true==$(this).prop("checked")){
				var checked=1;
			}else{
				var checked=0;
			}
			calculatePrice();
			$.post("/wap.php?s=/Mall/shopping_cart/from/x",{id:id,checked:checked},function(result){

			});
		});
		$(".price1").click(function(){
			var flag=0;
			$("input[name='checkbox']").each(function(){
				if(true==$(this).prop("checked"))
				flag=1;
			});
			
			if (flag==0){
				alert("您还没有勾选产品哦~");
				return false;
			} 
			top.window.location = "/wap.php?s=/Mall/shopping_cart_order";
		});
		
		// $("#select-all").click(function(){
		// 	$("input[name='checkbox']").prop("checked",$("#select-all").prop("checked"));
		// });

		function calculatePrice(){
			var p = new Array();
			var q = new Array();
			var total_price=0;
			$(".shopping-cart-price").each(function(i){
				if($("parent"))
				p[i]=$(this).text();
				i++;
			});
			$(".text_box").each(function(i){
				q[i]=$(this).val();
				i++;
			});
			$(":checked").each(function(){
				var key=$(".shopping-cart-checkbox").index($(this));
				total_price+=q[key]*p[key];
			});
			$("#total_price").html(total_price);
		}

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
</script>
<!-- 页面footer钩子，一般用于加载插件JS文件和JS代码 -->
<?php echo hook('pageFooter', 'widget');?>
<div class="hidden"><!-- 用于加载统计代码等隐藏元素 -->
    
</div>

	<!-- /底部 -->
</body>
</html>