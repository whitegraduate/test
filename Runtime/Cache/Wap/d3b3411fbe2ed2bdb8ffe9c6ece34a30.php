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
	<!-- /头部 -->
	
	<!-- 主体 -->
	
    <link href="/Public/Wap/css/style.css" rel="stylesheet">



    <form action="<?php echo U('Mall/order');?>" method="post">
        <div class="header">
            <a href=""><div class="topnav"></div></a>
            <div class="logo"><?php echo ($wap_title); ?></div>
        </div>
        <input type="hidden" name="id" value="<?php echo ($info['id']); ?>">
        <img src="<?php echo ($info['pic']); ?>" width="100%">
        <div class="cont">
            <div class="cp_title"><?php echo ($info['title']); ?></div>
            <div class="price">
                ￥
                <span><?php echo ($info['member_price']); ?></span>
                <em>会员价</em>
            </div>

            <ul class="pattern">
                <?php if(is_array($infoattr)): $k = 0; $__LIST__ = $infoattr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($k % 2 );++$k;?><li>
                        <div class="wz"><?php echo ($vo["id"]); echo ($vo["attrname"]); ?></div>
                        <div class="manner">
                            <!--class="up"-->
                            <?php if(is_array($vo["attr"])): $k2 = 0; $__LIST__ = $vo["attr"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo2): $mod = ($k2 % 2 );++$k2;?><span data-value="<?php echo ($k2); ?>" class="spanclass"><?php echo ($vo2); ?></span><?php endforeach; endif; else: echo "" ;endif; ?>
                            <input type="hidden" id="attr_<?php echo ($vo["id"]); ?>" name="attr_<?php echo ($vo["id"]); ?>" value="">
                        </div>
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
                <li>
                    <div class="wz">颜色:</div>
                    <div class="manner">
                        <?php if(is_array($colorlist)): $i = 0; $__LIST__ = $colorlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><span data-value="<?php echo ($vo["id"]); ?>" class="spanclass"><?php echo ($vo["color_name"]); ?></span><?php endforeach; endif; else: echo "" ;endif; ?>

                        <input type="hidden" id="hdcolor" name="hdcolor" value="">
                    </div>
                </li>
            </ul>
            <div class="suggest">
                <div class="sug">
                    <div class="tb"></div>
                    <span>卖家承诺25小时内发货</span>
                </div>
                <div class="sug">
                    <div class="tb"></div>
                    <span>信用卡支付</span>
                </div>
                <div class="sug">
                    <div class="tb"></div>
                    <span>如实描述</span>
                </div>
            </div>
            <div class="xian1"></div>
            <div class="details">
                <div class="title">
                    <a class="a_up" href="#">产品参数</a>
                </div>
                <!---图文详情----->
                <!--  <div class="tup">
                        <img  src="images/tp1.jpg"  width="100%">
                        <p>图片详情图片详情图片详情图片详情图片详情图片详情图片详情图片详情图片详情图片详情图片详情图片详情图片详情图片详情图片详情图片详情图片详情图片详情图片详情图片详情图片详情图片详情图片详情</p>
                     </div>-->
                <!---产品参数----->
                <div class="wz">
                    <!--<p>上市时间: 2011年冬季</p>-->
                    <!--<p>品牌: 绿源</p>-->
                    <!--<p>是否商场同款: 是</p>-->
                    <!--<p>颜色分类: 豪迈红 宝石蓝 银白</p>-->
                    <!--<p>配置等级: 其他</p>-->
                    <?php echo ($info['content']); ?>
                </div>
            </div>

        </div>
        <!--<input type="submit" value="购买" name="buy">-->
        <div>
        <div class="ann1" style="float:left;width:50%">立即购买</div>
        <div class="ann2" style=" width:50%; height:40px; line-height:35px; font-size:18px; color:#fff; position:fixed;bottom:0;right:0;border:1px solid #9b030d; background:#ff4f51;text-align:center; cursor:pointer; font-size:14px;">加入购物车</div>
    </div>
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
		"APP"    : "/index.php?s=", //当前项目地址
		"PUBLIC" : "/Public", //项目公共目录地址
		"DEEP"   : "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
		"MODEL"  : ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
		"VAR"    : ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"]
	}
})();
</script>

<script src="/Public/Wap/js/layer.m/layer.m.js" type="text/javascript"></script>
    <script type="text/javascript">
        str = '';
        $(document).ready(function () {
            if($("body").height()<$(window).height()){$("body").height($(window).height());}//用于下拉框全屏可点
            $(".select").each(function () {
                var s = $(this);
                var z = parseInt(s.css("z-index"));
                var dt = $(this).children("dt");
                var dd = $(this).children("dd");
                var _show = function () {
                    dd.slideDown(200);
                    dt.addClass("cur");
                    s.css("z-index", z + 1);
                };   //展开效果
                var _hide = function () {
                    dd.slideUp(200);
                    dt.removeClass("cur");
                    s.css("z-index", z);
                };    //关闭效果
                dt.click(function () {
                    dd.is(":hidden") ? _show() : _hide();
                });
                dd.find("a").click(function () {
                    dt.html($(this).html());
                    _hide();
                });     //选择效果（如需要传值，可自定义参数，在此处返回对应的“value”值 ）
                $("body").click(function (i) {
                    !$(i.target).parents(".select").first().is(s) ? _hide() : "";
                });
            })

            $('label').click(function () {
                var radioId = $(this).attr('name');
                $('label').removeAttr('class') && $(this).attr('class', 'checked');
                $('input[type="radio"]').removeAttr('checked') && $('#' + radioId).attr('checked', 'checked');
            });

            $('.spanclass').click(function(){
                var sval = $(this).attr('data-value');
                $(this).closest('div.manner').find('input').val(sval);
                $(this).closest('div.manner').find('span').removeClass('up');
                $(this).addClass('up');
            });

            $("div.wz").find("img").attr("width","100%");
            
            $(".ann1").click(function(){
            	
//            	if('' == $("#hdks").val())
//            	{
//            		galert('请选择款式');
//            		return false;
//            	}
        	   if(<?php echo ($info["quantity"]); ?> == ''){
                 alert("亲，该商品卖完了哦");
                 return false;
               }
               
            	// if('' == $("#hdcolor").val())
            	// {
            	// 	galert('请选择颜色');
            	// 	return false;
            	// }

                <?php if(is_array($infoattr)): $i = 0; $__LIST__ = $infoattr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>if(!$("#attr_<?php echo ($vo["id"]); ?>").val()){
                            galert('请选择<?php echo ($vo["attrname"]); ?>');
                            return false;
                        }
                        str += '/attr_<?php echo ($vo["id"]); ?>/'+ $("#attr_<?php echo ($vo["id"]); ?>").val();<?php endforeach; endif; else: echo "" ;endif; ?>
             
            	top.window.location = "/wap.php?s=/Mall/order/id/<?php echo $_GET['id'];?>/cl/"+$("#hdcolor").val()+str+".html";
               
            	//document.forms[0].submit();
            });
             //添加购物车
            $(".ann2").click(function(){
                
//              if('' == $("#hdks").val())
//              {
//                  galert('请选择款式');
//                  return false;
//              }
               if(<?php echo ($info["quantity"]); ?> == ''){
                 alert("亲，该商品卖完了哦");
                 return false;
               }
               
                // if('' == $("#hdcolor").val())
                // {
                //  galert('请选择颜色');
                //  return false;
                // }

                <?php if(is_array($infoattr)): $i = 0; $__LIST__ = $infoattr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>if(!$("#attr_<?php echo ($vo["id"]); ?>").val()){
                            galert('请选择<?php echo ($vo["attrname"]); ?>');
                            return false;
                        }
                        str += '/attr_<?php echo ($vo["id"]); ?>/'+ $("#attr_<?php echo ($vo["id"]); ?>").val();<?php endforeach; endif; else: echo "" ;endif; ?>
                var id=<?php echo $_GET['id'];?>;
                var cl=$("#hdcolor").val()+str;
                $.post("/wap.php?s=/Mall/shopping_cart/",{id:id,quantity:1,cl:cl,checked:0},function(result){
                    alert("添加成功,请在购物车中查看");
                });
                //document.forms[0].submit();
            });
        });

        function fsubmit() {
            document.forms[0].submit();
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