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



    <form action="" method="post">
        <div class="cont">
            <div class="xian1"></div>
            <div style="clear: both;"></div>
            <div id="myTabContent" class="tab-content">
                <ul role="tabpanel" class="tab-pane listing" id="home">
                     <div class="screen">
                        <a class="a_up order-new" href="#" data-value="id">默认</a>

                        <div class="xian"></div>
                        <a href="#" class="order-new" data-value="create_time desc">最新</a>

                        <div class="xian"></div>
                        <a href="#" class="order-new" data-value="sales desc">销量</a>

                        <div class="xian"></div>
                        <a href="#" class="order-new" data-value="member_price desc">价格</a>
                    </div>
                    <div id="divlist">

                    </div>


                </ul>
                <ul role="tabpanel" class="tab-pane active listing" id="profile">
                    <div class="screen">
                        <a class="a_up order" href="#" data-value="id">默认</a>

                        <div class="xian"></div>
                        <a href="#" class="order" data-value="create_time desc">最新</a>

                        <div class="xian"></div>
                        <a href="#" class="order" data-value="sales desc">销量</a>

                        <div class="xian"></div>
                        <a href="#" class="order" data-value="member_price desc">价格</a>
                    </div>
                    <div id="div_accessory">
                    </div>
                    <div class="clearfix"></div>
                    <div class="more" style="display:none;">+ 关注店铺 获取更多门店相关信息 +</div>
                </ul>

            </div>
        </div>
        <script>
            $('#myTab a').click(function (e) {
                e.preventDefault();
                $(this).tab('show');
            })
        </script>
    </form>
    <a href="<?php echo U('Mall/shopping_cart');?>" ><img src="/Public/Wap/images/shopping-cart.png" class="shopping_cart_pic"></a>

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
        $(document).ready(function () {
            load_accessory_list(2,"","");
            load_accessory_list(1,"","newCar");
            $('.order').click(function(){
                $('.order').removeClass('a_up');
                $(this).addClass('a_up');
                var orderstr = $(this).attr('data-value');
                load_accessory_list(2,orderstr,"");
            });
            $('.order-new').click(function(){
                $('.order-new').removeClass('a_up');
                $(this).addClass('a_up');
                var orderstr = $(this).attr('data-value');
                load_accessory_list(1,orderstr,"newCar");
            });
        });
        function load_accessory_list(type,strorder,cla)
        {
            $.ajax({
                type: "GET",
                url: "<?php echo U('Mall/accessory_list');?>",
                data: {order:strorder,type:type},
                success: function(data){
                    if (data != null) {
                        var html = "";
                        for (var i = 0; i < data.length; i++) {
                            var url = "/index.php?s=/Wap/Mall/detail_accessory/id/" + data[i].id + ".html";
                            html += "<li><div class='tup'><a href='" + url + "'><img src='" + data[i].pic + "' width='100%'></a></div><div class='wz'><p><a href='" + url + "'>"+data[i].title+"_"+data[i].subtitle+"</a></p><h3><em>￥" + data[i].member_price + "</em>/份<br/><strong>   数量:  "+data[i].quantity+"</strong></h3></div></li>";
                        }
                        if(cla=="newCar"){
                            $("#divlist").html(html);
                        }else{
                            $("#div_accessory").html(html);
                        }
                    }
                    else {
                        if(cla=="newCar"){

                        $("#divlist").html("");
                        }else{
                            
                        $("#div_accessory").html("");
                        }
                    }
                }
            });
        }

        function fsubmit() {
            document.forms[0].submit();
        }

        function search() {
            var a = $(".wenza:eq(0)").find("input:checked").val();
            var b = $(".wenza:eq(1)").find("input:checked").val();
            var c = $(".wenza:eq(2)").find("input:checked").val();
            $.get("<?php echo U('Mall/indexlist');?>",{type: "GET", data: "aa=" + a + "&bb=" + b + "&cc=" + c,
                url: "{:U('Mall/indexlist')}"
            },function(data){
                if (data != null) {
                    var html = "";
                    for (var i = 0; i < data.length; i++) {
                        //document.write(data[i].brand+",");
                        var url = "/index.php?s=/Wap/Mall/detail/id/" + data[i].id + ".html";
                        html += "<li><div class='tup'><a href='" + url + "'><img src='" + data[i].pic + "' width='100%'></a></div><div class='wz'><p><a href='" + url + "'>"+data[i].title+"_"+data[i].subtitle+"</a></p><h3><em>￥" + data[i].member_price + "</em>/份<br/><strong>   数量:  "+data[i].quantity+"</strong></h3></div></li>";
                    }
                    $("#divlist").html(html);
                }
                else {
                    $("#divlist").html("");
                }
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