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
	
    <style type="text/css">
        .divm{
            background:url(/Public/Wap/imagesRed/monkey1.png) no-repeat ;
            height:600px;
            weight:600px;
            background-position: center center;
            margin: auto;
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
        }
    </style>




    <form id="form1" method="post" >
        <div class="cont">
            <div class="pj_assess">
                <div class="tup">
                    <div class="tp"><img src="/Public/Wap/images/tup1.jpg"></div>
                </div>
                <div class="wz">
                    <div><?php echo ($order["title"]); ?></div>
                    <p><?php echo (date("Y-m-d H:i:s",$order["create_time"])); ?></p>
                    <span><?php if($_GET['code'] == 'CD'): ?>￥<?php echo C('CHARGE_PRICE'); else: ?>￥<?php echo ($order["price"]); endif; ?></span> 
                </div>
                <div class="box_163css"> 
                    <span class="s_name">总体评价：</span>
                    <ul id="ul_star_total" class="star_ul fl">
                        <li><a class="one-star active-star" title="1" href="#"></a></li>
                        <li><a class="two-star active-star" title="2" href="#"></a></li>
                        <li><a class="three-star active-star" title="3" href="#"></a></li>
                        <li><a class="four-star active-star" title="4" href="#"></a></li>
                        <li><a class="five-star active-star" title="5" href="#"></a></li>
                    </ul>
                    <span class="s_result fl"></span> 
                </div>
                <div class="appraise">
                    <textarea name="memo" id="memo" rows="3" class="kuang"></textarea>
                    <div class="tp"></div>
                </div>
            </div>
            <div class="pj_assess">
                <div class="title">门店评价</div>
                <div class="box_163css"> 
                    <span class="s_name">服务态度：</span>
                    <ul id="ul_star_service"  class="star_ul fl">
                        <li><a class="one-star active-star" title="1" href="#"></a></li>
                        <li><a class="two-star active-star" title="2" href="#"></a></li>
                        <li><a class="three-star active-star" title="3" href="#"></a></li>
                        <li><a class="four-star active-star" title="4" href="#"></a></li>
                        <li><a class="five-star active-star" title="5" href="#"></a></li>
                    </ul>
                    <span class="s_result fl"></span> 
                </div>
                <div class="box_163css"> 
                    <span class="s_name">操作正规：</span>
                    <ul id="ul_star_oper" class="star_ul fl">
                        <li><a class="one-star active-star" title="1" href="#"></a></li>
                        <li><a class="two-star active-star" title="2" href="#"></a></li>
                        <li><a class="three-star active-star" title="3" href="#"></a></li>
                        <li><a class="four-star active-star" title="4" href="#"></a></li>
                        <li><a class="five-star active-star" title="5" href="#"></a></li>
                    </ul>
                    <span class="s_result fl"></span> 
                </div>
            </div>
        </div>
        <div class="pj_foot">
            <input type="hidden" name="star_total" id="star_total" value="5" />
            <input type="hidden" name="star_service" id="star_service" value="5" />
            <input type="hidden" name="star_oper" id="star_oper" value="5" />
            <input type="hidden" name="shopid" id="shopid" value="<?php echo ($order["shopid"]); ?>" />
            <input type="hidden" name="service_type" id="service_type" value="<?php echo ($order["service_type"]); ?>" />
            <input type="hidden" name="oid" id="oid" value="<?php echo ($order["oid"]); ?>" />
            <input type="hidden" name="pid" id="pid" value="<?php echo ($order["pid"]); ?>" />
            <input type="hidden" name="obj" id="obj" value="<?php echo ($order["obj"]); ?>" />
            <input type="hidden" name="url_details" value="<?php echo ($order["url_details"]); ?>" />
            <a href="#"><div class="pj_ann">发表评论</div></a>

            <pre>
                <div id="shade" style=" height:10px;display: none;background:none;"> 
                    layer.open({
                    type: 1,
                    content: html,
                    style: 'width:100%; height:100%;  background:url() no-repeat ;            -moz-background-size:contain;            -webkit-background-size:contain;            -o-background-size:contain;            background-size:contain'
                         });
                </div>
            </pre>
            <textarea id="shadea" style="display: none;background:none;">
                <div id="shadeb" href="javascript:" class="divm" ></div>
            </textarea>

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

    <script src="/wxpay/Public/Wap/js/layer.m/layer.m.js" type="text/javascript"></script>
    <!--<script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>-->    
    <script src="/Public/Wap/js/jweixin-1.0.0.js"></script>
    <script type="text/javascript">

        wx.config({
            appId: '<?php echo ($jsapi_sign_package["appId"]); ?>',
            timestamp: '<?php echo ($jsapi_sign_package["timestamp"]); ?>',
            nonceStr: '<?php echo ($jsapi_sign_package["nonceStr"]); ?>',
            signature: '<?php echo ($jsapi_sign_package["signature"]); ?>',
            jsApiList: [
                'onMenuShareTimeline',
                'onMenuShareAppMessage',
                'onMenuShareQQ',
                'onMenuShareWeibo',
                'hideOptionMenu',
                'showOptionMenu',
                'chooseWXPay'
                        // 所有要调用的 API 都要加到这个列表中
            ]
        });
        var orderoid = "<?php echo ($order["oid"]); ?>";
        var paid = "<?php echo ($order["paid"]); ?>";
        $(document).ready(function () {
            var isShade = request('isShade');
            if (isShade != 1 && paid != 'undefined' && paid != '' && paid != null && paid != 'null') {
                new Function('var html = shadea.value;' + shade.innerHTML)();
                $('#shadeb').click(function () {
                    top.window.location = '/wap.php/hongbao/share.html?paid=' + paid + '&oid=' + orderoid;
                });
            }
            if ('1' == '<?php echo ($order["isremark"]); ?>')
            {
                galert_url("该订单已评价，请勿重复评价!", "<?php echo ($order["url_details"]); ?>");
            }

            $('#ul_star_total a').click(function () {
                $('#ul_star_total li a').removeClass('active-star');
                $(this).addClass('active-star');

                $("#star_total").val($(this).attr('title'));
            });


            $('#ul_star_service a').click(function () {
                $('#ul_star_service li a').removeClass('active-star');
                $(this).addClass('active-star');

                $("#star_service").val($(this).attr('title'));
            });

            $('#ul_star_oper a').click(function () {
                $('#ul_star_oper li a').removeClass('active-star');
                $(this).addClass('active-star');

                $("#star_oper").val($(this).attr('title'));
            });

            $(".pj_ann").click(function () {
                if ('' == $("#star_total").val())
                {
                    galert('请为本次服务总体评价打分');
                    return false;
                }
                if ('' == $("#memo").val())
                {
                    galert('请输入评论内容');
                    return false;
                }
                if ('' == $("#star_service").val())
                {
                    galert('请为本次门店服务态度打分');
                    return false;
                }
                if ('' == $("#star_oper").val())
                {
                    galert('请为本次操作正规打分');
                    return false;
                }

                $("#form1").submit();
            });

        });

        //微信分享接口监听,防止点击分享过快而没有获取到要分享的内容.
        if (typeof WeixinJSBridge == "undefined") {
            if (document.addEventListener) {
                document.addEventListener('WeixinJSBridgeReady', onBridgeReady, false);
            } else if (document.attachEvent) {
                document.attachEvent('WeixinJSBridgeReady', onBridgeReady);
                document.attachEvent('onWeixinJSBridgeReady', onBridgeReady);
            }
        } else {
            onBridgeReady();
        }

        //自定义右上的分享开始---------------------------------------------------------------------------------------------------------------
        // document.addEventListener('WeixinJSBridgeReady', 

        //var link = 'http://longhongxuan.luyuan.cn';
        var link='http://www.lulingzhijia.com';
        function onBridgeReady() {
            WeixinJSBridge.on('menu:share:appmessage', function (argv)
            {
                WeixinJSBridge.invoke('sendAppMessage', {
                    "img_url": 'https://mmbiz.qlogo.cn/mmbiz/pRFSZ4ZYm6p1Ch5GP2ghZW1YplceicxfB2KgAic9WGy0WbJ0D2jTv6PdvLt2CGAsOYia8MMeHqJicVOUktZO0Jl8EA/0?wx_fmt=jpeg',
                    "img_width": "200",
                    "img_height": "200",
                    "link": link + '/wap.php/hongbao/index.html?Pid=' + paid,
                    "desc": '关注公众号,百万红包等你拿',
                    "title": ''
                }, function (res) {
                    // eixinJSBridge.log(res.err_msg);
                });
            });
            WeixinJSBridge.on('menu:share:timeline', function (argv)
            {
                WeixinJSBridge.invoke("shareTimeline", {
                    "img_url": 'https://mmbiz.qlogo.cn/mmbiz/pRFSZ4ZYm6p1Ch5GP2ghZW1YplceicxfB2KgAic9WGy0WbJ0D2jTv6PdvLt2CGAsOYia8MMeHqJicVOUktZO0Jl8EA/0?wx_fmt=jpeg',
                    "img_width": "200",
                    "img_height": "200",
                    "link": link + '/wap.php/hongbao/index.html?Pid=' + paid,
                    "desc": '关注公众号,百万红包等你拿',
                    "title": ''
                },
                        function (e) {
                            //  alert(e.err_msg);
                        })
            });
        }
        //自定义右上的分享结束---------------------------------------------------------------------------------------------------------------

        function galert(msg)
        {
            layer.open({
                content: msg,
                style: 'background-color:#00af89; color:#fff; border:none;',
                time: 2
            });
        }

        //style="background: rgb(241, 241, 239);"
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