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

<div class="table-responsive shrink message">
	<h2 class="sub-header" style="padding-bottom:3px;margin-bottom:20px;">
		<div class="tabs clearfix">
			<?php if(is_array($tabs)): foreach($tabs as $key=>$v): ?><a href="<?php echo ($v[1]); ?>" class="<?php echo ($v[2]?"cur":""); ?>" ><?php echo ($v[0]); ?></a><?php endforeach; endif; ?>
		</div>
	</h2>
	<?php if($tab < 1): ?><form class="form-horizontal ajaxSubmit message-form" action="<?php echo u('messageEdit');?>" method="post">
		<div class="select-type-id">
			<span class="trans"></span>
			<div class="select">
				<h4>选择推送人群</h4>
				<?php if(is_array($typeArr)): foreach($typeArr as $key=>$v): ?><dl>
					<dt><label>
						<input name="type" checked type="checkbox" value="<?php echo ($v["value"]); ?>"><?php echo ($v["name"]); ?>
					</label>
					</dt>
				<?php if($v['ids']): ?><dd>
					<?php if(is_array($v["ids"])): foreach($v["ids"] as $k2=>$v2): ?><label>
						<input name="type_id[<?php echo ($k2); ?>]" checked type="checkbox" value="<?php echo ($v2); ?>"><?php echo ($v2); ?>
					</label><?php endforeach; endif; ?>
					</dd><?php endif; ?>
				 </dl><?php endforeach; endif; ?>
				<div class="div-btn pull-right">
					<button type="button" class="btn btn-default btn-sm">取消</button>
					<button type="button" class="btn btn-info btn-sm">确定</button>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-1 control-label"><i class="red">*</i>推送人群</div>
			<div class="col-sm-4">
				<?php echo w('select/index', $typeList);?>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-1 control-label"><i class="red">*</i>推送标题</div>
			<div class="col-sm-5">
				<input type="text" required class="form-control" name="title" value="">
			</div>
		</div>	 
		<div class="form-group">
			<div class="col-sm-1 control-label">相关链接</div>
			<div class="col-sm-5">
				<input type="text" class="form-control" name="link" value="">
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-1 control-label"><i class="red">*</i>内容</div>
			<div class="col-sm-8">
				<textarea name="content" required class="form-control" rows="5"></textarea>
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-1 col-sm-10">
				<input type="hidden" name="name" value="SMS">
				<button type="submit" class="btn btn-primary">提交</button>
			</div>
		</div>
	</form>

 <?php else: ?>
	<table class="table table-striped show-order-list tab<?php echo ($tab); ?>">
		<tr>  
		  <th width="150">推送时间</th>
		  <th width="150">推送人群</th>
		  <th width="250">推送标题</th>
		  <th width="150">推送链接</th>
		  <th>推送内容</th>
		  <th>发送备注</th>
		</tr>
		<?php if(is_array($list)): foreach($list as $key=>$v): ?><tr>
			<td><?php echo ($v["addTime"]); ?></td>
			<td><?php echo ($v["typeName"]); ?></td>
			<td><?php echo ($v["title"]); ?></td>
			<td><?php echo ($v["link"]); ?></td>
			<td><?php echo ($v["content"]); ?></td>
			<td><?php echo ($v["note"]); ?></td>
		</tr><?php endforeach; endif; ?>
	</table>
	<div class="pager"><?php echo ($pageVar); ?></div><?php endif; ?>
</div>
<script type="text/javascript" src="/Public/js/lib/laydate/laydate.js"></script><script type="text/javascript" src="/Public/js/admin/run.js"></script>
		</div>
	</div>
</div>
<div class="footer">
	<p>footer</p>
</div>
<script type="text/javascript" src="/Public/js/functions.js"></script><script type="text/javascript" src="/Public/js/dialog.js"></script><script type="text/javascript" src="/Public/js/admin/admin.js"></script>
</body>
</html>