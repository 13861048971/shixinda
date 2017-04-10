<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta name="renderer" content="webkit" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge;chrome=1">
	<title><?php echo ($title); ?></title>
	<meta name="keywords" content="<?php echo ($keywords); ?>" />
	<meta name="description" content="<?php echo ($description); ?>" />
	<link rel="shortcut icon" href="Public/admin.ico" type="image/x-icon"/>
	<script> var controller = '<?php echo (CONTROLLER_NAME); ?>'; </script>
	<link rel="stylesheet" type="text/css" href="/Public/css/bootstrap.min.css" /><link rel="stylesheet" type="text/css" href="/Public/css/dashboard.css" />
	<script type="text/javascript" src="/Public/js/lib/jquery.min.js"></script><script type="text/javascript" src="/Public/js/lib/bootstrap.min.js"></script><script type="text/javascript" src="/Public/js/lib/layer/layer.js"></script>
</head>
<body>
<div class="logo"></div>
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <!-- Brand and toggle get grouped for better mobile display -->
	<div class="container-fluid">
		<!-- Collect the nav links, forms, and other content for toggling -->
		<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		  <ul class="nav navbar-nav top-nav">
			<?php if(is_array($nav)): foreach($nav as $key=>$v): ?><li <?php if(($v["active"]) == "1"): ?>class="active"<?php endif; ?> ><a href="<?php echo ($v["url"]); ?>"><?php echo ($v["name"]); ?></a></li><?php endforeach; endif; ?>
		  </ul>
		  
		  <ul class="nav navbar-nav navbar-right admin-nav">
			<?php if(!$admin): ?><li><a href="login.html">登录</a></li>
			<?php else: ?>
			<li><a target="_blank" href="/">前台</a></li>
			<li class="dropdown">
			  <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo ($admin["name"]); ?> <span class="caret"></span></a>
			  <ul class="dropdown-menu" role="menu">
				<li><a href="<?php echo U('/Admin');?>">概况</a></li>
				<li class="divider"></li>
				<li><a href="<?php echo U('Index/Logout');?>">退出</a></li>
			  </ul>
			</li><?php endif; ?>
		  </ul>
		</div><!-- /.navbar-collapse -->
	</div>
</nav>

<div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar">
			<ul class="nav nav-sidebar">
			<?php if(is_array($leftNav)): foreach($leftNav as $key=>$v): ?><li <?php if(($v["active"]) == "1"): ?>class="active"<?php endif; ?> >
				  <?php if(!$v['list']): ?><a href="<?php echo ($v["url"]); ?>"><?php echo ($v["name"]); ?></a>
				  <?php else: ?>
					<a class="has-list"><?php echo ($v["name"]); ?></a>
					<ul>
					<?php if(is_array($v["list"])): foreach($v["list"] as $key=>$vl): ?><li class="<?php echo ($vl['active'] ? 'active':''); ?>"><a href="<?php echo ($vl["url"]); ?>"><?php echo ($vl["name"]); ?></a></li><?php endforeach; endif; ?>
					</ul><?php endif; ?>
				</li><?php endforeach; endif; ?>
			</ul>
		</div>
		<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
			<h2 class="sub-header">
				<span class="right-action pull-right navbar-right">
				<?php if(is_array($rightAction)): foreach($rightAction as $key=>$v): if($v['dialog'] < 1): ?><a class="btn btn-primary" href="<?php echo ($v["url"]); ?>"  ><?php echo ($v["name"]); ?></a>
				  <?php else: ?>
				   <?php if($v['list']): ?><div class="btn-group">
					  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<?php echo ($v["name"]); ?> <span class="caret"></span>
					  </button>
					  <ul class="dropdown-menu">
						<?php if(is_array($v["list"])): foreach($v["list"] as $key=>$v2): ?><li><a href="#" class="dialog" <?php if($v['dialog-lg']): ?>dialog-lg="true"<?php endif; ?> url="<?php echo ($v2["url"]); ?>" ><?php echo ($v2["name"]); ?></a></li><?php endforeach; endif; ?>
					  </ul>
					</div>
				   <?php else: ?>
					<a class="btn btn-primary dialog" <?php if($v['dialog-lg']): ?>dialog-lg="true"<?php endif; ?> url="<?php echo ($v["url"]); ?>" ><?php echo ($v["name"]); ?></a><?php endif; endif; endforeach; endif; ?>
				</span>
				<?php echo ($main_title); ?>
			</h2>

<div class="table-responsive shrink">
	<form class="form-inline search clearfix">
		<div class="pull-left time-buttons">
			<?php echo w('select/index', $statusArr);?>
		</div>
		<div class="btn-group pull-left">
			<input  class="form-control" name="nickname" type="text" placeholder="任务标题" value="<?php echo ($search["nickname"]); ?>" > 
			<button  class="form-control btn-info" >搜索</button>
		</div>
	</form>
	<table class="table table-striped artist-list">
		<tr>  
		  <th>#</th>
		  <th>头像</th>
		  <th>用户昵称</th>
		  <th>标题</th>
		  <th>拍摄类型</th>
		  <th>场景</th>
		  <th>后期</th>
		  <th>所在城市</th>
		  <th>价格</th>
		  <th>模特</th>
		  <th>起拍数</th>
		  <th>特色说明</th>
		  <th>状态</th>
		  <th class="">操作</th>
		</tr>
		<?php if(is_array($list)): foreach($list as $key=>$v): ?><tr>
			<td><?php echo ($v["id"]); ?></td>
			<td><?php if($v['pho']['avatar']): ?><img src="<?php echo ($v["pho"]["avatar"]); ?>" width=40 /><?php endif; ?></td>
			<td><?php echo ($v["pho"]["user"]["nickname"]); ?></th>
			<td><?php echo ($v["title"]); ?></td>
			<td><?php echo ($v["typeName"]); ?></td>
			<td><?php echo ($v["sceneName"]); ?></td>
			<td><?php echo ($v["afterName"]); ?></td>
			<td><?php echo ($v["cityName"]); ?></td>
			<td class="price"><?php echo ($v["price"]); ?></td>
			<td><?php echo ($v["model"]); ?></td>
			<td><?php echo ($v["num"]); ?></td>
			<td><?php echo ($v["note"]); ?></td>
			<td><?php echo ($v["statusName"]); ?></td>
			<td width=120 class="handle">
				<?php if(1 > $v['status']): ?><a class="ajaxPost btn btn-warning btn-sm" href="#" url="<?php echo u('mealChange');?>" data="id=<?php echo ($v["id"]); ?>&status=1" success="已屏蔽" confirm="1">屏蔽</a><?php endif; ?>
				<?php if(1 == $v['status']): ?><a class="ajaxPost btn btn-success btn-sm" href="#" url="<?php echo u('mealChange');?>" data="id=<?php echo ($v["id"]); ?>&status=0" success="已取消" confirm="1">取消屏蔽</a><?php endif; ?>
			</td>
		</tr><?php endforeach; endif; ?>
	</table>
	<div class="pager"><?php echo ($pageVar); ?></div>
</div>
<script type="text/javascript" src="/Public/js/lib/laydate/laydate.js"></script><script type="text/javascript" src="/Public/js/admin/user.js"></script>
		</div>
	</div>
</div>
<div class="footer">
	<p>footer</p>
</div>
<script type="text/javascript" src="/Public/js/functions.js"></script><script type="text/javascript" src="/Public/js/dialog.js"></script><script type="text/javascript" src="/Public/js/admin/admin.js"></script>
</body>
</html>