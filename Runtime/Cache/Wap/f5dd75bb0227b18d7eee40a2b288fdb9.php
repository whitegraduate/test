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
	
  
<link rel="stylesheet" type="text/css" href="/Public/Wap/css/ll.css" />
  



<div class="dian"><img src="<?php echo ($shop["picture1"]); ?>" width="100%" /></div>
     <div class="jiashao">
       <div class="taitou">
            <div class="xingji">星级：<img src="/Public/Wap/images/xing<?php echo ($shop["star"]); ?>.png" height="16" /></div>
            <div class="dingdan">订单数：<?php echo ($shop["orders"]); ?>单</div>
            <div class="clearflx"></div>
         </div>
         <div class="dianhua">电话：<?php if($shop["tel"] != ''): echo ($shop["tel"]); else: echo ($shop["mobile"]); endif; ?></div>
         <div class="dizhi">地址：<?php echo ($shop["address"]); ?></div>
         
         
     </div>
     <div class="anhui" style="display:none;">
          <ul>
              <li>
                 <div class="antu1"></div>
                 <div class="anzi">我要维修</div>
              </li>
              <li>
                 <div class="antu2"></div>
                 <div class="anzi">我要保养</div>
              </li>
              <li>
                 <div class="antu3"></div>
                 <div class="anzi">我要租车</div>
              </li>
              <li>
                 <div class="antu4"></div>
                 <div class="anzi">精选产品</div>
              </li>
          </ul>
     </div>
     <?php if($shop["shop_type"] == 1): ?><div class="module">
        <ul>
           <li id="liRepair"><a href="#">
                 <div class="yuan5">
                     <div class="yuantu5"></div>
                 </div>
                 <div class="yuanzi">维修</div>
                 </a>
           </li>
           <li id="liMaintain"><a href="#">
                 <div class="yuan3">
                     <div class="yuantu3"></div>
                 </div>
                 <div class="yuanzi">保养</div>
                 </a>
           </li>
           <li id="liRent"><a href="#">
                 <div class="yuan2">
                     <div class="yuantu2"></div>
                 </div>
                 <div class="yuanzi">租车</div>
                 </a>
           </li>
           
           <li id="liMall"><a href="#">
                 <div class="yuan4">
                     <div class="yuantu4"></div>
                 </div>
                 <div class="yuanzi">精品</div>
                 </a>
           </li>
           <div class="clearflx"></div>
           <div id="divRoute" class="ann">去门店</div>
        </ul>
     </div> 
     <?php else: ?>
      <div class="module">
        <ul>
        	<div id="divHelp" class="ann">求助</div>
        </ul>
     </div><?php endif; ?>
     

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
    	 $("#liRepair").click(function(){
    		 top.location.href = "<?php echo U('Repair/index');?>";
    	 });
    	 
    	 $("#liMaintain").click(function(){
    		 top.location.href = "<?php echo U('Maintain/index');?>";
    	 });
    	 
    	 $("#liRent").click(function(){
    		 top.location.href = "<?php echo U('Rent/show',array('sid'=>$shop['id']));?>";
    	 });
    	 
    	 $("#liMall").click(function(){
    		 top.location.href = "<?php echo U('Mall/index');?>";
    	 });
    	 
    	 $("#divRoute").click(function(){
    		 top.location.href = "<?php echo U('Index/route',array('code'=>'MD','id'=>$shop['id']));?>";
    	 });
    	 
    	 $("#divHelp").click(function(){
    		 layer.open({
    			 type:1,
    			 content:
    			    '<div class="list">'+
    			      '<div class="tb1"></div>'+
    			      '<div class="timo">车辆症状：</div>'+
    			      '<dl id="selProblem"  class="select">'+
    			        '<dt></dt>'+
    			        '<dd>'+
    			          '<ul>'+
    			            '<li><a href="#">轮胎漏气</a></li>'+
    			            '<li><a href="#">碰撞损坏</a></li>'+
    			            '<li><a href="#">刹车故障</a></li>'+
    			            '<li><a href="#">里程跑不远</a></li>'+
    			            '<li><a href="#">更换电池</a></li>'+
    			            '<li><a href="#">车有异响</a></li>'+
    			            '<li><a href="#">车灯不亮</a></li>'+
    			            '<li><a href="#">灯亮不走</a></li>'+
    			            '<li><a href="#">其他故障</a></li>'+
    			          '</ul>'+
    			        '</dd>'+
    			      '</dl>'+
    			    '</div>'+
    			    '<div class="list">'+
    			      '<div class="tb4"></div>'+
    			      '<div class="timo">手机号码：</div>'+
    			      '<input type="tel" id="mobile" style="margin:0" name="mobile" class="input_box" placeholder=""/>'+
    			    '</div>'+
    			    
    			    '<div class="ann" id="divSubmit">提交</div>'+
    			    
    				'<input type="hidden" name="problem" id="problem" value="" />'+
    			    '',
    			 style:'width:100%;height:200px;border:none;',
    			 success:function(olayer){
    				 
    			 }
    		 });
    		 
    		 $("#selProblem").each(function(){
    		  		var s=$(this);
    				var z=parseInt(s.css("z-index"));
    				var dt=$(this).children("dt");
    				var dd=$(this).children("dd");
    				var _show=function(){dd.slideDown(200);dt.addClass("cur");s.css("z-index",z+1);};   //展开效果
    				var _hide=function(){dd.slideUp(200);dt.removeClass("cur");s.css("z-index",z);};    //关闭效果
    				dt.click(function(){dd.is(":hidden")?_show():_hide();});
    				dd.find("a").click(function(){dt.html($(this).html());$("#problem").val($(this).html());_hide();});     //选择效果（如需要传值，可自定义参数，在此处返回对应的“value”值 ）
    				$("body").click(function(i){ !$(i.target).parents(".select").first().is(s) ? _hide():"";});
    			});
    		 
    		 $("#divSubmit").click(function(){
    			 
    			 if('' == $("#problem").val())
   				{
   					galert('请选择车辆症状');
   					return;
   				}

   				if('' == $("#mobile").val())
   				{
 					galert('请输入您的手机号码');
   					return;
   				}
   				else if(!validatemobile($("#mobile").val()))
   				{
 					galert('请输入正确的手机号码');
 					return;
   				}
    			 
    			 $.ajax({
     		        type: 'POST',
     		        dataType: 'json',
     		        async: false,
     		        url: "<?php echo C('SITE_URL');?>/wap.php/Shop/help/from/x",
     		        data: {
     		            sid: "<?php echo $_GET['sid'];?>",
     		            problem:$("#problem").val(),
     		            mobile:$("#mobile").val()
     		        },
     		        success: function(data){
     		            //galert(data);  
     		        	layer.closeAll();
     		                if (data.flag == 1) {
     		                	galert('提交成功,服务商稍候会联系您！');
     		                }  
     		                else
     		                    galert('提交失败');
     		           
     		        },
     		        error: function(){
     		            galert('系统繁忙！');
     		        }
     		    });
    		 });
    	 });
    	 
    	 localStorage.shopid = "<?php echo ($shop["id"]); ?>";
    	 localStorage.shopname = "<?php echo ($shop["name"]); ?>";
     });

     function validatemobile(mobile)
     {
         if(mobile.length!=11)
         {
             return false;
         }
         
         var myreg = /^0?1[3|4|5|8][0-9]\d{8}$/;
          
         if(!myreg.exec(mobile))
         {
             return false;
         }
         
         
         return true;
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