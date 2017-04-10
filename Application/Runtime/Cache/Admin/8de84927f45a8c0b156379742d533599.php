<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta name="renderer" content="webkit" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
	<title>登录-<?php echo ($title); ?></title>
	<meta name="keywords" content="<?php echo ($keywords); ?>" />
	<meta name="description" content="<?php echo ($description); ?>" />
	<link rel="stylesheet" type="text/css" href="/Public/css/bootstrap.min.css" /><link rel="stylesheet" type="text/css" href="/Public/css/dashboard.css" />
	<script type="text/javascript" src="/Public/js/jquery.min.js"></script><script type="text/javascript" src="/Public/js/bootstrap.min.js"></script>
</head>
<body class="admin-login">
<div class="container">
<form class="form-signin" role="form" method="post" action="<?php echo U('login');?>">
	<h2 class="form-signin-heading">请登录</h2>
	<div class="admin-name">
		<input type="text" name="name" class="form-control" placeholder="帐号" required="" autofocus="" value="<?php echo ($post["name"]); ?>" />
	</div>
	<div class="admin-pass">
		<input type="password" name="password" class="form-control" placeholder="密码" required="" />
	</div>
	<div class="checkbox">
	  <label>
		<input type="checkbox" value="remember-me"> 记住
	  </label>
	</div>
	<?php if($err): ?><div class="alert-danger alert" role="alert"><?php echo ($err); ?></div><?php endif; ?>
	<button class="btn btn-lg btn-primary btn-block" type="submit">确定</button>
</form>
</div>
</body>
</html>