<?php if (!defined('THINK_PATH')) exit();?><html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta  charset="UTF-8">
<meta  name="viewport"  content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<link rel="stylesheet" href="/Public/Wap/css/style.css" type="text/css" />
<title>滴啊滴租车</title>
</head>

<body>
<div class="header">
  <div class="topnav"></div>
  <div class="logo">出错啦！</div>
</div>
 
<div class="cont">
     <div class="jump">
        <div class="tup"></div>
        <p><?php echo($error); ?></p>
        <?php if($stop == false): ?><p><b id="wait"><?php echo($waitSecond); ?></b> 秒后页面将自动跳转</p>
        <?php else: ?>
		<p>返回首页</p><?php endif; ?>
     </div>
</div>

<script type="text/javascript">
(function(){
 var wait = document.getElementById('wait'),href = document.getElementById('href').href;
 var interval = setInterval(function(){
     	var time = --wait.innerHTML;
     	if(time <= 0) {
     		location.href = href;
     		clearInterval(interval);
     	};
     }, 1000);
  window.stop = function (){ 
            clearInterval(interval);
 }
 })();
</script>
</body>
</html>