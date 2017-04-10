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
			<?php echo w('select/index', $payArr);?>
			<?php echo w('select/index', $typeArr);?>
		</div>
		<div class="btn-group pull-left">
			&nbsp;<input  class="form-control" name="contact" type="text" placeholder="联系人" value="<?php echo ($search["contact"]); ?>" > 
			<button  class="form-control btn-info" >搜索</button>
		</div>
	</form>
	<table class="table table-striped artist-list">
		<tr>  
		  <th>#</th>
		  <th>订单号</th>
		  <th>用户</th>
		  <th>联系人</th>
		  <th>类型</th>
		  <th>摄影师</th>
		  <th>金额</th>
		  <th>备注</th>
		  <th>状态</th>
		  <th>下单时间</th>
		  <th class="">操作</th>
		</tr>
		<?php if(is_array($list)): foreach($list as $key=>$v): ?><tr>
			<td><?php echo ($v["id"]); ?></td>
			<td><a class="btn-link2" url="/Admin/Run/orderDetail/id/<?php echo ($v["id"]); ?>"><?php echo ($v["order_sn"]); ?></a></td>
			<td><?php if($v['user']['avatar']): ?><img src="<?php echo ($v["user"]["avatar"]); ?>" width=40 /><?php endif; echo ($v["user"]["nickname"]); ?></td>
			<td><?php echo ($v["contact"]); ?></br><?php echo ($v["mobile"]); ?></td>
			<td><?php if($v['type'] == 1): ?><a class="btn-link" href="/Admin/run/meal?id=<?php echo ($v["node_id"]); ?>"><?php echo ($v["typeName"]); ?></a>
				<?php else: ?>
					<a  class="btn-link" href="/Admin/run/index?id=<?php echo ($v["node_id"]); ?>"><?php echo ($v["typeName"]); ?></a><?php endif; ?>
			</td>
			<td><a href="/Admin/run/index"><?php echo ($v["pho"]["realname"]); ?></a></td>
			<td><span class="price"><?php echo ($v["total"]); ?></span><br><?php echo ($v["price"]); ?>*<?php echo ($v["num"]); ?></td>
			<td><?php echo ($v["desc"]); ?></td>
			<td><?php echo ($v["statusName"]); ?>
				<?php if($v['report_type']): ?><br><abbr class="red" title="<?php echo ($v["reportTypeName"]); ?> <?php echo ($v["report_note"]); ?>">已举报</abbr><?php endif; ?>
				<?php if($v['cancel_note']): ?><br><abbr class="red" title="<?php echo ($v["cancel_note"]); ?> <?php echo ($v["report_note"]); ?>">退单原因</abbr><?php endif; ?>
			</td>
			<td><?php echo ($v["addTime"]); ?></td>
			<td width=120 class="handle">
				<?php if($v['status'] < 1): ?><a class="ajaxPost btn btn-warning btn-sm" href="#" url="<?php echo u('orderCancel');?>" data="id=<?php echo ($v["id"]); ?>" success="已被系统撤销" confirm="true">撤销订单</a><?php endif; ?>
				
				<?php if($v['status'] == 1 or $v['status'] == 2): ?><a class="ajaxPost btn btn-warning btn-sm" style="margin-bottom:5px;" href="#" url="<?php echo u('orderCancel');?>" data="id=<?php echo ($v["id"]); ?>" success="已关闭" confirm="1">退款并撤销订单</a><?php endif; ?>
				
				<?php if($v['status'] == 2): ?><a class="dialog btn btn-primary btn-sm" href="#" url="<?php echo u('orderDone',['id'=>$v['id']]);?>">确认完工</a><?php endif; ?>
				<?php if($v['status'] == 3): ?><a class="dialog btn btn-success btn-sm" href="#" url="<?php echo u('orderPay',['id'=>$v['id']]);?>">结款</a><?php endif; ?>
				<?php if($v['report_type'] and $v['pho']['status'] == 1): ?><a style="margin-top:5px;" class="ajaxPost btn btn-danger btn-sm" href="#" url="/Admin/user/phoChange/" data="id=<?php echo ($v["pho_id"]); ?>&status=3" success="已取消资格" confirm="1">取消摄影师资格</a><?php endif; ?>
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