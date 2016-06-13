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
	


<form id="form1" name="form1" method="post">
    <script src="/Public/Wap/js/layer.m/layer.m.js" type="text/javascript"></script>
    <div class="cont">
        <div class="list">
            <div class="timo">手机号码：</div>
            <?php if(empty($fans["mobile"])): ?><input type="tel" id="mobile" name="mobile" class="input_box select2" value="<?php echo ($fans["mobile"]); ?>" >
                <?php else: ?>
                <input type="tel" id="mobile" name="mobile" class="input_box select2" value="<?php echo ($fans["mobile"]); ?>" readOnly="true" ><?php endif; ?>

        </div>
        <?php if(empty($fans["mobile"])): ?><div class="list">
            <div class="timo">验证码：</div>
            <input type="tel" id="code" name="code"  class="input_box select3">
            <div id="y_code" data-status="1" class="y_code y_code_active">获取验证码</div>
        </div>
        <div class="clear"></div>
        <div class="ann ann3" onclick="doSubmit()">提交</div>
        <?php else: ?>
            <div class="cont">
                <div class="list"><div class="timo">状态:</div><input type="tel" id="mobile" name="mobile" class="input_box select2" value="已验证" readOnly="true" autocomplete="off">
                    </div><div class="ann ann3" onclick="doUpdate()">修改</div></div><?php endif; ?>
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

<script type="text/javascript">
    //设置验证码按钮状态
    function setZcodeStatus(isactive){
        var ycode = $("#y_code");
        if(isactive){
            ycode.data('status', 1);
            ycode.addClass('y_code_active');
        }else{
            ycode.data('status', 0);
            ycode.removeClass('y_code_active');
        }
    }
    //显示密码
    var myPassTime=60;    //已用时间，单位为秒
    function myTimer() {
        var ycode = $("#y_code");
        if(myPassTime>1){    //已用时间是否小于可用时间
            myPassTime-=1;    //保存客户端已用时间
            ycode.html(myPassTime+"秒后重发");
        }else{
            setZcodeStatus(true);
            ycode.html("重新获取");
            return false;
        }
        window.setTimeout("myTimer()",1000);//一秒循环一次
    }

    $("#y_code").on('click', function(){
        var status = $(this).data('status');
        if (status == 0) {
            return;
        }
        var mobile = $('#mobile').val();
        var exp_phone = /^((\(\d{2,3}\))|(\d{3}\-))?(13|15|17|18|14)\d{9}$/;
        if (!exp_phone.test(mobile)){
            layer.open({
                content: '请输入正确的手机号码',
                time: 2
            });
            return false;
        }

        setZcodeStatus(false);
        myTimer();
        $.post(
                "<?php echo U('Fans/sendSMS');?>",
                {mobile: mobile},
                function(data){
                    layer.open({
                        content: data.msg,
                        time: 2
                    });
                },
                "json"
        );
    });

    function doUpdate() {
        window.location.href = "<?php echo U('Fans/auth_mobile?update=1');?>";
    }
    //提交表单
    function doSubmit() {
        var mobile = $('#mobile').val();
        var exp_phone = /^((\(\d{2,3}\))|(\d{3}\-))?(13|15|17|18|14)\d{9}$/;
        if (!exp_phone.test(mobile)){
            layer.open({
                content: '请输入正确的手机号码',
                time: 2
            });
            return false;
        }

        var code = $('#code').val();
        if (code.length != 4){
            layer.open({
                content: '验证码必须是4位数字',
                time: 2
            });
            return false;
        }

        form1.submit();
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
</script>
<!-- 页面footer钩子，一般用于加载插件JS文件和JS代码 -->
<?php echo hook('pageFooter', 'widget');?>
<div class="hidden"><!-- 用于加载统计代码等隐藏元素 -->
    
</div>

	<!-- /底部 -->
</body>
</html>