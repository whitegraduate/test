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
	
<link  href="/Public/Wap/css/swipe.css"  rel="stylesheet">



    <div  id="slider"  class="swipe"  style="visibility: visible;">
    <div  class="swipe-wrap"  style="width: 4269px;">
    <figure  data-index="0"  style="width: 1423px; left: 0px; transition: 300ms; -webkit-transition: 300ms; -webkit-transform: translate(0px, 0px) translateZ(0px);">
      <div  class="wrap"> <a  href=" http://mp.weixin.qq.com/s?__biz=MzAwMzUxODQzMA==&mid=401896139&idx=1&sn=f534a79860e7b6bfbef3a8c7190ee934#rd"> <img  src="/Public/Wap/images/banner4.jpg"  width="100%"  height="100%">
        <div  class="text">滴啊滴租车</div>
        </a> </div>
    </figure>
    <figure  style="left: -1423px; width: 1423px; transition: 0ms; -webkit-transition: 0ms; -webkit-transform: translate(1423px, 0px) translateZ(0px);"  data-index="1">
      <div  class="wrap"> <a  href="#"> <img  src="/Public/Wap/images/banner5.jpg"  width="100%"  height="100%">
        <div  class="text">滴啊滴租车</div>
        </a> </div>
    </figure>
    <figure  style="left: -2846px; width: 1423px; transition: 300ms; -webkit-transition: 300ms; -webkit-transform: translate(1423px, 0px) translateZ(0px);"  data-index="2">
      <div  class="wrap"> <a  href="#"> <img  src="/Public/Wap/images/banner6.jpg"  width="100%"  height="100%">
        <div  class="text">滴啊滴租车</div>
        </a> </div>
    </figure>
  </div>
  <nav style="right:0; bottom:5px;">
    <ul  id="position">
      <li  class="on"></li>
      <li  class=" "></li>
      <li  class=" "></li>
    </ul>
  </nav>
</div>
     <div class="module">
        <ul>
           <li> <a href="<?php echo U('Charge/index');?>">
                 <div class="yuan1">
                     <div class="yuantu1"></div>
                 </div>
                 <div class="yuanzi">充电</div>
                 </a>
           </li>
           <li><a href="<?php echo U('Rent/index');?>">
                 <div class="yuan2">
                     <div class="yuantu2"></div>
                 </div>
                 <div class="yuanzi">租车</div>
                 </a>
           </li>
           <li><a href="<?php echo U('Maintain/index');?>">
                 <div class="yuan3">
                     <div class="yuantu3"></div>
                 </div>
                 <div class="yuanzi">保养</div>
                 </a>
           </li>
           <li><a href="<?php echo U('Mall/index');?>">
                 <div class="yuan4">
                     <div class="yuantu4"></div>
                 </div>
                 <div class="yuanzi">精品</div>
                 </a>
           </li>
           <li><a href="<?php echo U('Repair/index');?>">
                 <div class="yuan5">
                     <div class="yuantu5"></div>
                 </div>
                 <div class="yuanzi">维修</div>
                 </a>
           </li>
           <li><a href="<?php echo U('Sechand/index');?>">
                 <div class="yuan6">
                     <div class="yuantu6"></div>
                 </div>
                 <div class="yuanzi">二手车</div>
                 </a>
           </li>
           <li><a href="#">
                 <div class="yuan7">
                     <div class="yuantu7"></div>
                 </div>
                 <div class="yuanzi">更多</div>
                 </a>
           </li>
        </ul>
     </div>
     <div class="clearflx"></div>
     <div class="function">
        <div class="funleft">
           <a href="http://mp.weixin.qq.com/s?__biz=MzAwMzUxODQzMA==&mid=402018136&idx=1&sn=4a33d4a8919e0b2fc5cd69d9749a888a#rd">
           <div class="tibao jiaxian">
                 <div class="tubiao"></div>
                 <div class="tuzi">热门活动</div>
            </div>
            <div class="titu"><img src="/Public/Wap/images/banner5.jpg" width="100%" height="100" /></div>
            </a>
        </div>
        <div class="funright">
           <a href="<?php echo U('Fans/coupons');?>">
           <div class="tibao jiaxian">
                 <div class="tubiao"></div>
                 <div class="tuzi">优惠券</div>
            </div>
            <div class="titu"><img src="/Public/Wap/images/jifentu.jpg" width="100%" height="100" /></div>
            </a>
        </div>
     </div>
     <div class="clearflx"></div>
     <div class="kong" style="display:none;"></div>
     <div class="dibu" style="display:none;">
          <div class="dikuai">
              <a href="#">
              <div class="ditu"></div>
              <div class="dizi">加入滴啊滴</div>
              </a>
          </div>
          <div class="dikuai">
              <a href="#">
              <div class="ditu2"></div>
              <div class="dizi">滴啊滴文化</div>
              </a>
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
 
<script  type="text/javascript"  src="/Public/Wap/js/jquery.lazyload.min.js"></script> 
<script  src="/Public/Wap/js/swipe.js"></script>
    <style>
        .citychange a{margin: 5px; color: #477fc0  }
    </style>
<script>
        $("img.lazy").lazyload({
            effect : "fadeIn"
        });

//        顶部幻灯片效果
        var bullets = document.getElementById('position').getElementsByTagName('li');
        var slider =
                Swipe(document.getElementById('slider'), {
                    auto: 3000,
                    continuous: true,
                    disableScroll: false,
                    callback: function(pos) {
                        var i = bullets.length;
                        while (i--) {
                            bullets[i].className = ' ';
                        }
                        bullets[pos].className = 'on';
                    }
                });
        function choose_city(){
            layer.open({
                type:4, //page层
                content: '<?php echo ($allcity); ?>'
            });
        }

        var cityname;
        function setthiscity(id)
        {
            cityname =$("div.citychange a[id="+id+"]").html();
            console.log(cityname);
            $.post("/wap.php/Location/setCityid/from/x",{"cityid": id},function(page){
                layer.closeAll();
                var message = layer.open({
                    content:page.info,
                    time:2,
                    end:function(){
                        //console.log('end');
                        $("#spancity").html("("+cityname+")").show();
                    }
                });
                //layer.closeAll();
            },"json");
        }
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