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
	
    <style type="text/css">
        body, html,#allmap {
            width: 100%;
            height: 100%;
            overflow: hidden;margin:0;
            margin:0;
        }
    </style>



    <div id="allmap">
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

    var map;
    //$(document).ready(function() {
        map = new BMap.Map("allmap");
        var geolocation = new BMap.Geolocation();
        var point = new BMap.Point(119.661223,29.089421);
        map.centerAndZoom(point, 13);
        
        geolocation.getCurrentPosition(function(r){
            if(this.getStatus() == BMAP_STATUS_SUCCESS){
                showPosition(r.point);
            }
            else {
                alert('failed'+this.getStatus());
            }
        },{enableHighAccuracy: true})

    //}) ;


        //异常处理
        function showError(error)
        {
            switch(error.code)
            {
                case error.PERMISSION_DENIED:
                    alert("用户不允许地理定位");
                    break;
                case error.POSITION_UNAVAILABLE:
                    alert("无法获取当前位置");
                    break;
                case error.TIMEOUT:
                    alert("操作超时");
                    break;
                case error.UNKNOWN_ERROR:
                    alert("无法获取当前位置");
                    break;
            }
        }

        //显示地图
        function showPosition(point)
        {
            // 百度地图API功能
            var point = new BMap.Point(point.lng,point.lat);
			map.panTo(point);

            var myIcon = new BMap.Icon("/Public/Wap/images/marker3.png", new BMap.Size(262/8,262/8));
            myIcon.setImageSize(new BMap.Size(262/8,262/8));
            var marker = new BMap.Marker(point,{icon:myIcon});  // 创建标注
            map.addOverlay(marker);
            
            var data = '<?php echo (json_encode($lstShop)); ?>';
            var arr = eval(data);
            addMarker(arr);
        }

        //将充电桩以标注样式加入到地图中
        function addMarker(arr) {
            for(var i =0 ; i < arr.length; i ++) {
                var point = new BMap.Point(arr[i].longitude,arr[i].latitude);
                var marker;  // 创建标注
                marker = new BMap.Marker(point);  // 创建标注
                		
                map.addOverlay(marker);
                marker.id = arr[i].id;
                marker.title = arr[i].name;
                marker.point = point;
                if('' == arr[i].mobile)
                    marker.mobile = arr[i].tel;
                else
                	marker.mobile = arr[i].mobile;
                marker.address = arr[i].address;
                marker.orders = arr[i].orders;
                var tel = arr[i].tel; 

                marker.addEventListener("click", function(){
                    var opts = {
                        width : 200,     // 信息窗口宽度
                        height: 100,     // 信息窗口高度
                        title : this.title, // 信息窗口标题
                        enableMessage:true,//设置允许信息窗发送短息
                        message:"地址"
                    }

                    var info = "<div style=\"font-size:12px; margin:10px 10px 0 10px;\">"
					     +"<div style=\"clear:both;\"></div>"
					     +"</div>"
					     +"<div style=\"font-size:12px; margin:10px 10px 0 10px;\">地址："+this.address+"</div>"
					     +"<div style=\"font-size:12px; margin:10px 10px 0 10px;\">电话："+this.mobile+"</div>";
					
                    infoWindow = new BMap.InfoWindow(info, opts);  // 创建信息窗口对象
                    map.openInfoWindow(infoWindow,this.point); //开启信息窗口
                });
            }
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