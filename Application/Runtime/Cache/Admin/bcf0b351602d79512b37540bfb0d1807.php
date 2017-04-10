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


<div class="document" style="position:relative;">
<div class="pull-right navbar-fixed-top" style="">
	<ul class="nav nav-pills nav-stacked right-nav">
		<li><a href="">sdsdsd</a></li>
	</ul>
</div >
<div class="form-list pull-left" style="width:74%">
  <?php if(is_array($forms)): foreach($forms as $k=>$v): ?><div class="panel panel-default" <?php if($v[0]['group']): ?>group="<?php echo ($v[0]['group']); ?>"<?php endif; ?>>
	  <div class="panel-heading"><?php echo ($k); ?>.<?php echo ($v[0]['title']); ?></div>
	  <div class="panel-body">
		<?php echo w('form/index', $v);?>
	  </div>
	</div><?php endforeach; endif; ?>
</div>

</div>
<script type="text/javascript" src="/Public/js/admin/index/index.js"></script>
		</div>
	</div>
</div>
<div class="footer">
	<p>footer</p>
</div>
<script type="text/javascript" src="/Public/js/functions.js"></script><script type="text/javascript" src="/Public/js/dialog.js"></script><script type="text/javascript" src="/Public/js/admin/admin.js"></script>
</body>
</html>
<script>
var li = $('.form-list .panel .panel-heading');
var rightNav = $('.right-nav');

var i = 0;
var str = '';
var group = '';
li.each(function(){
	var th = $(this);
	var id = 'hash_'+i
	var liGroup = th.parent().attr('group');
	
	liGroup && (group=liGroup) && (str += '<li class="group" group="'+ group	 +'"><a style="font-weight:700;" href="#' +id+ '"><b>--</b> '+ group + '</a></li>');
	str += '<li group="'+ group +'"><a style="text-indent:20px;" href="#' +id+ '">'+ th.text().replace(/[A-z\/?=]+/,'')+'</a></li>';
	th.parent().attr('id', id);
	i++;
});
rightNav.html(str);
rightNav.on('click', 'a', function(){
	var th = $(this);
	var node = $(th.attr('href'));
	scrollTo2(node,'', -42);
	return false;
});

$('#hash_3').on('ajaxCallback',function(e, info){
	$('form input[name=vercode]').val(info.code);
});

rightNav.on('click', '.group b', function(e){
	console.log('==');
	var th = $(this);
	var li = th.parent().parent();
	var gr = li.attr('group');
	var str = $.trim(th.text());
	lis = li.siblings('[group=' + gr + ']');
	if(str == '+')
		lis.show() && (th.text('--'));
	else
		lis.hide() && (th.text('+'));
	
	e.stopPropagation();
});
</script>