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
	




    <div class="content">
        <ul>
            <li <?php if(($_GET['type'] == 1) or ($_GET['type'] == 0)): ?>class="xuanzhong"<?php endif; ?>>保养</li>
            <li <?php if($_GET['type'] == 2): ?>class="xuanzhong"<?php endif; ?>>洗车</li>
            <li <?php if(($_GET['type'] == 2)): ?>class="xuanzhong"<?php endif; ?>>配件</li>

            <div class="clearflx"></div>
        </ul>
    </div>
<?php if(!empty($lstMaintain)): if(is_array($lstMaintain)): $i = 0; $__LIST__ = $lstMaintain;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i; switch($list["type"]): case "1": ?><div class="shangpin li0">
                <a href="<?php echo U('Maintain/details',array('pid'=>$list['id']));?>">
                    <div class="shantu"><img src="<?php echo ($list["picture1"]); ?>" width="100%"></div>
                    <div class="shanwen"><?php echo ($list["name"]); ?></div>
                    <div class="shanjia">￥<?php echo ($list["price"]); ?></div>
                </a>
            </div><?php break;?>

        <?php case "2": ?><div class="shangpin li1">
                <a href="<?php echo U('Maintain/details',array('pid'=>$list['id']));?>">
                    <div class="shantu"><img src="<?php echo ($list["picture1"]); ?>" width="100%"></div>
                    <div class="shanwen"><?php echo ($list["name"]); ?></div>
                    <div class="shanjia">￥<?php echo ($list["price"]); ?></div>
                </a>
            </div><?php break;?>

        <?php case "3": ?><div class="shangpin li2">
                <a href="<?php echo U('Maintain/details',array('pid'=>$list['id']));?>">
                    <div class="shantu"><img src="<?php echo ($list["picture1"]); ?>" width="100%"></div>
                    <div class="shanwen"><?php echo ($list["name"]); ?></div>
                    <div class="shanjia">￥<?php echo ($list["price"]); ?></div>
                </a>
            </div><?php break; endswitch; endforeach; endif; else: echo "" ;endif; ?>
    <?php else: ?>
    <div style="text-align:center;font-size:18px;color:#787878;margin-top:30%;">未获取到您的定位或您已不在服务区~</div>
    <div style="text-align: center;font-size:12px;color:#949494;margin-top: 20px;color: rgba(186, 110, 46, 0.53)">请查看公众号主体信息页面<p>“提供地理位置信息”选项是否为开启状态</p></div><?php endif; ?>

<script>
    $(document).ready(function(){
        show(0);
        $(".content ul li").click(function(){
            console.log($(this).index());
            $(".content ul li").attr('class','');
            $(this).attr('class','xuanzhong');

            show($(this).index());
        });
    });

    function show(i)
    {
        $(".shangpin").hide();
        $(".li"+i).show();
    }
</script>

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