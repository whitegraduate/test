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
	 
    <script src="/Public/Wap/js/date/mobiscroll_002.js" type="text/javascript"></script>
	<script src="/Public/Wap/js/date/mobiscroll_004.js" type="text/javascript"></script>
	<link href="/Public/Wap/css/date/mobiscroll_002.css" rel="stylesheet" type="text/css">
	<link href="/Public/Wap/css/date/mobiscroll.css" rel="stylesheet" type="text/css">
	<script src="/Public/Wap/js/date/mobiscroll.js" type="text/javascript"></script>
	<script src="/Public/Wap/js/date/mobiscroll_003.js" type="text/javascript"></script>
	<script src="/Public/Wap/js/date/mobiscroll_005.js" type="text/javascript"></script>
	<link href="/Public/Wap/css/date/mobiscroll_003.css" rel="stylesheet" type="text/css">
    <script src="/Public/Wap/js/layer.m/layer.m.js" type="text/javascript"></script>



    <form id="form1" name="form1" action="/wap.php?s=/Fans/auth_idcard.html" method="post">
        <div class="cont">
            <div class="list">
                <div class="timo">认证状态：</div>
                <input type="text"  class="input_box select2" name="idcard_status_desc" value="<?php echo (get_dic_desc('AUTH_STATUS',$fans["idcard_status"])); ?>" readonly>
            </div>
            <div class="list">
                <div class="timo">真实姓名：</div>
                <input type="text" id="realname"  class="input_box select2" name="realname" value="<?php echo ($fans["realname"]); ?>" <?php if($fans["idcard_status"] == 2): ?>readonly<?php endif; ?>>
            </div>
            <div class="list">
                <div class="timo">身份证号：</div>
                <input type="text" name="idcard" id="idcard" class="input_box select2" value="<?php echo ($fans["idcard"]); ?>" <?php if($fans["idcard_status"] == 2): ?>readonly<?php endif; ?>>
            </div>
            <div class="list3" <?php if($fans["idcard_status"] == 2): ?>readonly<?php endif; ?>>
                <div class="timo">上传图片：</div>
                <div class="tup" onclick="chooseImage()">
                    <input type="hidden" id="media_id" name="media_id" />
                    <img id="upload_img" src="<?php echo ($fans["idcard_img"]); ?>" width="100%" />
                </div>
            </div>
            <div style="clear: both"></div>
            <?php if($fans["idcard_status"] != 2): ?><input type="hidden" name="idcard_status" value="1">
            <div id="ann" class="ann">提交</div><?php endif; ?>
            </body>
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

    <script src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
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
                'chooseImage',
                'previewImage',
                'uploadImage'
                // 所有要调用的 API 都要加到这个列表中
            ]
        });

        var images = {
            localId: [],
            serverId: []
        };

        //选择照片
        function chooseImage(){
            var isflag = true;
            wx.checkJsApi({
                jsApiList: ['chooseImage'], // 需要检测的JS接口列表，所有JS接口列表见附录2,
                success: function(res) {
                    isflag = res['checkResult']['chooseImage'];
                }
            });
            if(isflag){
                wx.chooseImage({
                    success: function (res) {
                        images.localId = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
                        if (images.localId.length > 0) {
                            $('#upload_img').attr('src', images.localId[0]);
                        }
                    }
                });
            }
            else{
                layer.open({
                    content: '请将微信升级到最新版本！',
                    time: 2
                });
            }
        }

        $('#ann').click(function(){

            if($("#realname").val() == ''){
                layer.open({
                    content: '真实姓名不能为空',
                    time: 2
                });
                return false;
            }

            if($("#idcard").val() == ''){
                layer.open({
                    content: '请填写正确的身份证号',
                    time: 2
                });
                return false;
            }
            // if ('<?php echo ($fans["idcard_status"]); ?>' == '0' && images.localId.length == 0) {
            if (0) {
                layer.open({
                    content: '请先选择身份证照片',
                    time: 2
                });
                return false;
            }

            wx.uploadImage({
                localId: images.localId[0], // 需要上传的图片的本地ID，由chooseImage接口获得
                isShowProgressTips: 1, // 默认为1，显示进度提示
                success: function (res) {
                    images.serverId = [];
                    images.serverId.push(res.serverId); // 返回图片的服务器端ID
                    if (images.serverId.length == 0) {
                        showTip('照片上传失败');
                        return false;
                    }
                    $('#media_id').val(images.serverId[0]);
                    form1.submit();
                    return true;
                }
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