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
	 
<link rel="stylesheet" type="text/css" href="/Public/Wap/css/jd.css">
<link rel="stylesheet" type="text/css" href="/Public/Wap/css/normalize.css">



<div class="cont">
    <div class="member1">
        <div class="icon"></div>
        <div class="grade">滴啊滴租车<?php echo ($fansgrade["name"]); ?></div>
        <div class="phone">18757655658</div>
    </div>
    <div class="insider">
       <div class="tb"></div>
       <p>升级做滴啊滴租车的VIP<a href="#">如何升级></a></p>
    </div>
    <div class="leaguer">
        <div class="title">您的会员进度</div>
        <div class="pace"><span id="score1"></span>/10000</div>
        <section id="sample-pb">
          <article>
            <div class="number-pb">
              <div class="number-pb-shown"></div>
              <div class="number-pb-num "></div>
           </div>
        </article>
      </section>
      <div class="progress">
          <div class="por1">银卡</div>
          <div class="xian"></div>
          <div class="por">金卡</div>
          <div class="xian"></div>
          <div class="por">钻石</div>
          <div class="xian"></div>
          <div class="por">皇冠</div>
      </div>
    </div>
    <div class="insider">
     <p>再有<span id="score2">100</span>积分即可以升级为<span></span></p>
    </div>
    <?php if($fansgrade["id"] == 1): ?><div class="members">
       <div class="title">继续升级，您还可以享受</div>

       <div class="mem">
          <div class="tup1"></div>
          <div class="wz">
             <div class="ka">金卡会员</div>
             <p>升级为金卡会员，你可以享受到更多服务！</p>
          </div>
       </div>
       <div class="mem">
          <div class="tup2"></div>
          <div class="wz">
             <div class="ka">钻石会员</div>
             <p>升级为钻石会员，你可以享受到更多服务！</p>
          </div>
       </div>
       <div class="mem">
          <div class="tup3"></div>
          <div class="wz">
             <div class="ka">皇冠卡会员</div>
             <p>升级为皇冠卡会员，你可以享受到更多服务！</p>
          </div>
       </div>
    </div><?php endif; ?>
<?php if($fansgrade["id"] == 2): ?><div class="members">
     <div class="title">继续升级，您还可以享受</div>
     <div class="mem">
        <div class="tup2"></div>
        <div class="wz">
           <div class="ka">钻石会员</div>
           <p>升级为钻石会员，你可以享受到更多服务！</p>
        </div>
     </div>
     <div class="mem">
        <div class="tup3"></div>
        <div class="wz">
           <div class="ka">皇冠卡会员</div>
           <p>升级为皇冠卡会员，你可以享受到更多服务！</p>
        </div>
     </div>
  </div><?php endif; ?>
<?php if($fansgrade["id"] == 3): ?><div class="members">
     <div class="title">继续升级，您还可以享受</div>
     <div class="mem">
        <div class="tup3"></div>
        <div class="wz">
           <div class="ka">皇冠卡会员</div>
           <p>升级为皇冠卡会员，你可以享受到更多服务！</p>
        </div>
     </div>
  </div><?php endif; ?>
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

<script src="/Public/Wap/js/number-pb.js"></script>
<script src="/Public/Wap/js/jquery.velocity.min.js"></script>
<script>
  $(function() {
    var score=Math.floor(<?php echo ($fans["score"]); ?>);
    var max_score=Math.floor(<?php echo ($fansgrade["max_score"]); ?>);
    var lack_score=max_score-score;
    var vipname="<?php echo ($fansgrade2["name"]); ?>";
    $("#score1").text(score);
    $("#score2").text(lack_score);
    $("#score2").next().append(vipname);
    
    function randomPercentage() {
      return (score/8000)*200;
    }
    var loopBars = $('#random .number-pb').NumberProgressBar().each(function() {
      $(this).reach(randomPercentage());
    });
    window.setInterval(function () {
      loopBars.each(function() {
        $(this).reach(randomPercentage()); 
      })
    }, 12000);

    var num = randomPercentage();
    var title = $('#sample-pb .title').text('@' + num);
    var controlBar = $('#sample-pb .number-pb').NumberProgressBar({
      duration: 12000,
      percentage: num
    });

    var $controls = $('#sample-pb .control');
    
    function animate(val) {
      if (val < 0) {
        num = 0;
      } else if (val > 100) {
        num = 100;
      } else {
        num = val
      }
      controlBar.reach(num);
      title.text('@' + num);
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