<!DOCTYPE html>
<!-- saved from url=(0054)http://miniappstg.mcdonalds.com.cn/adm/video/addbanner -->
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	
	<title>小程序后台管理</title>
	<meta name="renderer" content="webkit">	
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">	
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">	
	<meta name="apple-mobile-web-app-status-bar-style" content="black">	
	<meta name="apple-mobile-web-app-capable" content="yes">	
	<meta name="format-detection" content="telephone=no">
	<link rel="icon" href="/images/.jpg" type="image/x-icon">
	<script type="text/javascript" src="/admin/js/jquery-1.10.1.min.js"></script>
</head>
<style>

/**20170519**/
*{padding:0px; margin:0px;}
.entrancebody { background:#1d1d1d}
.layui-layout-admin .layui-header {background:#1d1d1d; width:1000px; margin:0 auto}
.entrancebox { text-align:center; color:#fff; padding:50px;}
.entranceh1 { margin-bottom:50px; font-size:40px; }
.entranbtn {background:#bd0017; color:#fff; box-shadow: 0 0 3px #fff;display:block;width:150px; margin:0px auto; padding:10px 15px; border-radius:100px;text-decoration:none;}
.entranbtn:hover { color:#fff;}
.enterpic { display:block; margin:0 auto;width:400px;}
</style>
<script>
	$(function(){
		var winval = $(window).height();
		var contval = $(".entrancebox").height();
		var topval = (winval-contval-200)/2;
		console.log(contval);
		$(".entrancebox").css("margin-top",topval);
		
	})
	$(window).resize(function() {
		var winval = $(window).height();
		var contval = $(".entrancebox").height();
		var topval = (winval-contval-200)/2;
		$(".entrancebox").css("margin-top",topval);
	});
</script>
<body class="entrancebody">
	<div class="entrancebox">
		<img src="/images/.png" class="enterpic" />
		<h1 class="text-center entranceh1">小程序后台管理</h1>
		<a href="/adm/login" class="entranbtn">进入管理员后台</a>
	</div>
</div>
</body>
</html>