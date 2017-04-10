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

<div class="table-responsive user shrink">
	<form class="form-inline search clearfix">
		<div class="btn-group pull-left">
		<?php echo w('select/index', $typeArr);?>
		<?php echo w('select/index', $statusArr);?>
		
		<input  class="form-control" name="keywords" type="text" placeholder="手机号或者昵称搜索" value="<?php echo ($search["keywords"]); ?>" >
		<button  class="form-control btn-info" >搜索</button>
		</div>
	</form>
	<style> .act-identify .type, .act-identify .imageccie{ display:none; } </style>
	<table class="table table-striped artist-list act-<?php echo (ACTION_NAME); ?>">
		<tr>
		  <th>#</th>
		  <th>申请时间</th>
		  <th width=80>用户</th>
		  <th>注册手机</th>
		  <th>认证人</th> 
		  <th width="145">身份证号</th>
		  <th>支付宝</th>
		  <th>作品</th>
		  <th width="70">服务城市</th>
		  <th>类型</th>
		  <th width="180">认证图片</th>
		  <th width="75">状态</th>
		  <th>操作</th>
		</tr>
		<?php if(is_array($list)): foreach($list as $key=>$v): ?><tr>
			<td><?php echo ($v["id"]); ?></td>
			<td><?php echo ($v["addTime"]); ?></td>
			<td><?php if($v['user']['avatar']): ?><a href="javascript:openInNewWindow('<?php echo ($v["user"]["avatar"]); ?>');"><img  src="<?php echo ($v["user"]["avatar"]); ?>" width=40 /></a><?php endif; ?>
				<br><?php echo ($v["user"]["nickname"]); ?>
			</td>
			<td><?php echo ($v["user"]["mobile"]); ?></td>
			<td><?php echo ($v["realname"]); ?></td>
			<td><?php echo ($v["idno"]); ?></td>
			<td><?php echo ($v["alipay"]); ?></td>
			<td>
				<?php if($v['worklink']): ?><a class="btn-link" target="_blank" href="<?php echo ($v["worklink"]); ?>">查看</a>
				<?php else: endif; ?>
			</td>
			<td><?php echo ($v["cityName"]); ?></td>
			<td><?php if($v['type']): ?><abbr title="名称:<?php echo ($v["orgname"]); ?> &#10;地址:<?php echo ($v["orgaddress"]); ?>"><?php echo ($v["typeName"]); ?></abbr><?php else: echo ($v["typeName"]); endif; ?></td>
			
			<td>
				<a href="javascript:;"><img src="<?php echo ($v["image1"]); ?>" onclick="openInNewWindow('<?php echo ($v["image1"]); ?>')" width="50"  /></a>
				<a href="javascript:;"><img src="<?php echo ($v["image2"]); ?>" onclick="openInNewWindow('<?php echo ($v["image2"]); ?>')" width="50"  /></a>
				<?php if($v['type'] < 1 && $v['image3']): ?><a href="javascript:;"><img src="<?php echo ($v["image3"]); ?>" onclick="openInNewWindow('<?php echo ($v["image3"]); ?>')" width="50"  /></a><?php endif; ?>
			</td>
			<td>
				<?php if($v['status'] == 2): ?><abbr title="<?php echo ($v["verify_note"]); ?>"><?php echo ($v["statusName"]); ?></abbr>
				<?php else: ?>
				<?php echo ($v["statusName"]); endif; ?>
				<br><a class="btn-link" href="javascript:openInNewWindow('/Admin/user/report?pho_id=<?php echo ($v["id"]); ?>');">举报(<?php echo ($v["reportNum"]); ?>)</a>
			</td>
			<td width=150 class="handle">
				<?php if($v['status'] < 1 or $v['status'] == 2): ?><a class="btn btn-warning btn-sm dialog" url="/Admin/user/phoChange/id/<?php echo ($v["id"]); ?>">不通过</a>
					<button class="btn btn-default btn-sm ajaxPost" url="/Admin/user/phoChange/" data="id=<?php echo ($v["id"]); ?>&status=1" onchange="(function(th){th.text('审批通过');th.prop('disabled',true).prev().hide();})($(this))">通过</button>
				<?php elseif($v['status'] == 1): ?>
					<button class="btn btn-danger btn-sm ajaxPost" url="/Admin/user/phoChange/" data="id=<?php echo ($v["id"]); ?>&status=3" confirm="1" onchange="location.reload();">取消摄影师资格</button>
				<?php elseif($v['status'] == 2): ?>
					<button class="btn btn-success btn-sm ajaxPost" url="/Admin/user/phoChange/" data="id=<?php echo ($v["id"]); ?>&status=1" confirm="1" onchange="location.reload();">通过审批</button>
				<?php elseif($v['status'] == 3): ?>
					<button class="btn btn-success btn-sm ajaxPost" url="/Admin/user/phoChange/" data="id=<?php echo ($v["id"]); ?>&status=1" confirm="1" onchange="location.reload();">恢复摄影师资格</button><?php endif; ?>
			
			</td>
		</tr><?php endforeach; endif; ?>
	</table>
	<div class="pager"><?php echo ($pageVar); ?></div>
</div>
<link rel="stylesheet" type="text/css" href="/Public/js/lib/daterangepicker/daterangepicker-bs3.css" />
<script type="text/javascript" src="/Public/js/admin/user.js"></script><script type="text/javascript" src="/Public/js/lib/daterangepicker/moment.min.js"></script><script type="text/javascript" src="/Public/js/lib/daterangepicker/daterangepicker.js"></script>
		</div>
	</div>
</div>
<div class="footer">
	<p>footer</p>
</div>
<script type="text/javascript" src="/Public/js/functions.js"></script><script type="text/javascript" src="/Public/js/dialog.js"></script><script type="text/javascript" src="/Public/js/admin/admin.js"></script>
</body>
</html>