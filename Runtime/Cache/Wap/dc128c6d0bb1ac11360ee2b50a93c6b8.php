<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE HTML>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta  charset="UTF-8">
<meta  name="viewport"  content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<link rel="stylesheet" href="/Public/Wap/css/bootstrap.min.css" type="text/css" />
<link rel="stylesheet" href="/Public/Wap/css/ll.css" type="text/css" />
<script type='text/javascript' src='/Public/Wap/js/jquery-2.0.3.min.js'></script>
<script type='text/javascript' src='/Public/Wap/js/base.js'></script>
<script>
$(document).ready(function(){
//	$(".topnav").click(function(){
//		$(".header a")[0].click();
//	});
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
	


 

<div class="geren" style="margin-top:0px;">
  
  <ul class="list3" id="myTab" role="tabpanel" style="margin-top:0px;">
    <li <?php if($_GET['s'] == ''): ?>class="active"<?php endif; ?>><a href="<?php echo U('Fans/coupons');?>" data-toggle="tabpanel">全部优惠券</a></li>
    
        <?php if(is_array(C("COUPON_STATUS"))): $i = 0; $__LIST__ = C("COUPON_STATUS");if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$type): $mod = ($i % 2 );++$i;?><li <?php if($_GET['s'] == $key): ?>class="active"<?php endif; ?>><a href="<?php echo U('Fans/coupons',array('s'=>$key));?>" data-toggle="tabpanel"><?php echo ($type); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
  </ul>
  <div  id="myTabContent" class="tab-content">
    <ul role="tabpanel"  class="tab-pane active order" id="profile1">
      <div class="container-fluid jianju">
      <?php if(is_array($lstCoupon)): $i = 0; $__LIST__ = $lstCoupon;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><div class="quan">
          <div class="ceng"> 
          <?php if($list["status"] == 1): if($list["service_type"] == 1): ?><img src="/Public/Wap/images/bj1.png" width="100%" />
          <?php elseif($list["service_type"] == 2): ?>
          <img src="/Public/Wap/images/bj2.png" width="100%" />
          <?php elseif($list["service_type"] == 3): ?>
          <img src="/Public/Wap/images/bj3.png" width="100%" />
          <?php elseif($list["service_type"] == 4): ?>
          <img src="/Public/Wap/images/bj4.png" width="100%" />
          <?php else: ?>
          <img src="/Public/Wap/images/bj3.png" width="100%" /><?php endif; ?>
          <?php else: ?>
          <img src="/Public/Wap/images/bj5.png" width="100%" /><?php endif; ?>
            <div class="ceng_tu">
              <div class="tuer">
              	<?php if($list["service_type"] == 1): ?><img src="/Public/Wap/images/ico_wx.jpg" width="100%" />
          		<?php elseif($list["service_type"] == 2): ?>
              	<img src="/Public/Wap/images/ico_by.jpg" width="100%" />
          		<?php elseif($list["service_type"] == 3): ?>
              	<img src="/Public/Wap/images/ico_cd.jpg" width="100%" />
          		<?php elseif($list["service_type"] == 4): ?>
              	<img src="/Public/Wap/images/ico_zc.jpg" width="100%" />
          		<?php elseif($list["service_type"] == 5): ?>
              	<img src="/Public/Wap/images/ico_xj.jpg" width="100%" />
              	 <?php else: ?>
              	<img src="/Public/Wap/images/ico_by.jpg" width="100%" /><?php endif; ?>
              </div>
              <div class="tuzi">
                 <p><em><?php echo (get_coupon_status($list["status"])); ?></em></p>
                 <p><?php echo ($list["cname"]); ?></p>
                 <p style="font-size:12px;">卡券号：<?php echo ($list["code"]); ?></p>
              </div>
            </div>
          </div>
          <div class="cengdi">
            <div class="ceng_zuo"></div>
            <div class="ceng_time">有效期至：<?php echo (date("Y-m-d",$list["end_time"])); ?></div>
            <div class="clearfix"></div>
          </div>
        </div><?php endforeach; endif; else: echo "" ;endif; ?>
      </div>
    </ul>
    <ul role="tabpanel"  class="tab-pane order" id="profile2">
      <div class="container-fluid jianju">
        <div class="quan">
          <div class="ceng"> <img src="/Public/Wap/images/bj3.png" width="100%" />
            <div class="ceng_tu">
              <div class="tuer"><img src="/Public/Wap/images/tou.jpg" width="100%" /></div>
              <div class="tuzi">
                 <p><em>已过期</em></p>
                 <p>北京首星美电影院星美电星美电</p>
                 <span>卡券号：kfhg5254456654521d</span>
              </div>
            </div>
          </div>
          <div class="cengdi">
            <div class="ceng_zuo"></div>
            <div class="ceng_time">有效期至：2015.01.05</div>
            <div class="clearfix"></div>
          </div>
        </div>
        <div class="quan">
          <div class="ceng"> <img src="/Public/Wap/images/bj4.png" width="100%" />
            <div class="ceng_tu">
              <div class="tuer"><img src="/Public/Wap/images/tou.jpg" width="100%" /></div>
               <div class="tuzi">
                 <p><em>已过期</em></p>
                 <p>北京首星美电影院星美电星美电</p>
                 <span>卡券号：kfhg5254456654521d</span>
              </div>
            </div>
          </div>
          <div class="cengdi">
            <div class="ceng_zuo"></div>
            <div class="ceng_time">有效期至：2015.01.05</div>
            <div class="clearfix"></div>
          </div>
        </div>
      </div>
    </ul>
    <ul role="tabpanel"  class="tab-pane order" id="profile2">
      <div class="container-fluid jianju">
        <div class="quan">
          <div class="ceng"> <img src="/Public/Wap/images/bj5.png" width="100%" />
            <div class="ceng_tu">
              <div class="tuer"><img src="/Public/Wap/images/tou.jpg" width="100%" /></div>
              <div class="tuzi">
                 <p><em>已过期</em></p>
                 <p>北京首星美电影院星美电星美电</p>
                 <span>卡券号：kfhg5254456654521d</span>
              </div>
            </div>
          </div>
          <div class="cengdi">
            <div class="ceng_zuo"></div>
            <div class="ceng_time">有效期至：2015.01.05</div>
            <div class="clearfix"></div>
          </div>
        </div>
        <div class="quan">
          <div class="ceng"> <img src="/Public/Wap/images/bj5.png" width="100%" />
            <div class="ceng_tu">
              <div class="tuer"><img src="/Public/Wap/images/tou.jpg" width="100%" /></div>
               <div class="tuzi">
                 <p><em>已过期</em></p>
                 <p>北京首星美电影院星美电星美电</p>
                 <span>卡券号：kfhg5254456654521d</span>
              </div>
            </div>
          </div>
          <div class="cengdi">
            <div class="ceng_zuo"></div>
            <div class="ceng_time">有效期至：2015.01.05</div>
            <div class="clearfix"></div>
          </div>
        </div>
      </div>
    </ul>
    <ul role="tabpanel"  class="tab-pane order" id="profile3">
      <div class="container-fluid jianju">
        <div class="quan">
          <div class="ceng"> <img src="/Public/Wap/images/bj5.png" width="100%" />
            <div class="ceng_tu">
              <div class="tuer"><img src="/Public/Wap/images/tou.jpg" width="100%" /></div>
              <div class="tuzi">
                 <p><em>已过期</em></p>
                 <p>北京首星美电影院星美电星美电</p>
                 <span>卡券号：kfhg5254456654521d</span>
              </div>
            </div>
          </div>
          <div class="cengdi">
            <div class="ceng_zuo"></div>
            <div class="ceng_time">有效期至：2015.01.05</div>
            <div class="clearfix"></div>
          </div>
        </div>
        <div class="quan">
          <div class="ceng"> <img src="/Public/Wap/images/bj5.png" width="100%" />
            <div class="ceng_tu">
              <div class="tuer"><img src="/Public/Wap/images/tou.jpg" width="100%" /></div>
               <div class="tuzi">
                 <p><em>已过期</em></p>
                 <p>北京首星美电影院星美电星美电</p>
                 <span>卡券号：kfhg5254456654521d</span>
              </div>
            </div>
          </div>
          <div class="cengdi">
            <div class="ceng_zuo"></div>
            <div class="ceng_time">有效期至：2015.01.05</div>
            <div class="clearfix"></div>
          </div>
        </div>
      </div>
    </ul>
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

 
	<script type="text/javascript">
	$(document).ready(function(){
		 
		
		$("body").attr('style','background:#f1f1ef;');
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