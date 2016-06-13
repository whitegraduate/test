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
	



<form id="form1" method="post">

    <div class="geren" style="margin-top:0px;">
        <div class="kuai" style="margin-top:0px;">
            <div class="kas"> <a href="#">
                <div class="kabiao">昵称</div>
                <div class="kago"><?php echo ($fans["nickname"]); ?></div>
            </a>
                <div class="clearflx"></div>
            </div>
            <div class="kas"> <a href="#myModa2" role="button" data-toggle="modal">
                <div class="kabiao">头像</div>
                <div class="tp"></div>
            </a>
                <div class="clearflx"></div>
            </div>
            <a href="#myModal" role="button" data-toggle="modal">
                <div class="kas">
                    <div class="kabiao">性别</div>
                    <div class="kajian"></div>
                    <div id="div_sex" class="kago"><?php if(($fans["sex"]) == "1"): ?>男<?php else: ?>女<?php endif; ?></div>
                    <input type="hidden" id="sex" name="sex" value="<?php echo ($fans["sex"]); ?>">
                    <div class="clearflx"></div>
                </div>
            </a>
            <a href="#myModal3" role="button" data-toggle="modal">
                <div class="kas">
                    <div class="kabiao">年龄</div>
                    <div class="kajian"></div>
                    <div id="div_age" class="kago"><?php if($fans["age"] == 0): ?>待完善<?php else: echo ($fans["age"]); endif; ?></div>
                    <div class="clearflx"></div>
                </div>
            </a>
            <div class="kas"> <a href="<?php echo U('Fans/member');?>">
                <div class="kabiao">等级</div>
                <div class="kajian"></div>
                <div class="kago"><?php echo (get_gradename_id($fans["grade"])); ?></div>
            </a>
                <div class="clearflx"></div>

            </div>
            <a href="#myModal2" role="button" data-toggle="modal">
                <div class="kas">
                    <div class="kabiao">职业</div>
                    <div class="kajian"></div>
                    <div id="div_job" class="kago"><?php if($fans["job"] == ''): ?>待完善<?php else: echo ($fans["job"]); endif; ?></div>
                    <input type="hidden" id="job" name="job" value="<?php echo ($fans["job"]); ?>">
                    <div class="clearflx"></div>
                </div>
            </a>
            <a href="<?php echo U('Fans/fans_address');?>">
                <div class="kas">
                    <div class="kabiao">我的收货地址</div>
                    <div class="kajian"></div>
                    <div class="clearflx"></div>
                </div>
            </a>

        </div>
        <div class="ann" onclick="form1.submit()" style="background: #00af89;color: #FFFFFF;width: 90%;text-align: center;">保 存</div>
    </div>
    <div id="myModal" class="modal modal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <div class="title">性别</div>
        <div class="xuan">
            <div class="wz" onclick="setSex(1)">男</div>
            <div class="wz" onclick="setSex(2)">女</div>
        </div>
    </div>
    <div id="myModal2" class="modal modal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="height: 250px">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <div class="title">职业</div>
        <div class="xuan">
            <div class="wz" onclick="setJob(this)">白领</div>
            <div class="wz" onclick="setJob(this)">工人</div>
            <div class="wz" onclick="setJob(this)">农民</div>
            <div class="wz" onclick="setJob(this)">学生</div>
            <div class="wz" onclick="setJob(this)">其他</div>
        </div>
    </div>
    <div id="myModal3" class="modal modal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <div class="title">年龄</div>
        <div class="xuan">
            <div class="xian"></div>
            <input name="age" id="age" type="tel" class="input_box" value="<?php echo ($fans["age"]); ?>">
            <div class="ann" onclick="setAge()" style="background: #00af89;">确定</div>
        </div>
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
        $(document).ready(function(){
            $("body").attr('style','background:#f1f1ef;');
        });

        function setSex(value) {
            if (value == 1) {
                $('#div_sex').html('男');
                $('#sex').val('1');
            } else if (value == 2) {
                $('#div_sex').html('女');
                $('#sex').val('2');
            }
            $('#myModal').modal('hide');
        }

        function setAge() {
            var age = $('#age').val();
            $('#div_age').html(age);
            $('#myModal3').modal('hide');
        }

        function setJob(obj) {
            var job = $(obj).html();
            $('#div_job').html(job);
            $('#job').val(job);
            $('#myModal2').modal('hide');
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