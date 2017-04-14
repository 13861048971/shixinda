<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Insert title here</title>
</head>
<body>
    <form class="form-horizontal ajaxSubmit role-form" action="<?php echo u('navigationEdit');?>" method="post">
  <div class="form-group">
    <label class="col-sm-2 control-label"><b class="red">*</b>连接名:</label>
    <div class="col-sm-8">
      <input name="name" required class="form-control" value="<?php echo ($row["name"]); ?>">
    </div>
  </div>
   <div class="form-group">
    <label class="col-sm-2 control-label"><b class="red">*</b>logo:</label>
    <div class="col-sm-8">
      <input name="logo" required class="form-control" value="<?php echo ($row["logo"]); ?>">
    </div>
  </div>
   <div class="form-group">
    <label class="col-sm-2 control-label"><b class="red">*</b>连接地址:</label>
    <div class="col-sm-8">
      <input name="url" required class="form-control" value="<?php echo ($row["url"]); ?>">
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label"><b class="red">*</b>状态:</label>
    <div class="col-sm-5">

	<?php echo w('select/index', $statusList);?>
    
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">父id:</label>
    <div class="col-sm-5">
	   <input name="parent_id" type="text" required class="form-control" value="<?php echo ($row["parent_id"]); ?>">	
    </div>
  </div>
    <div class="col-sm-offset-2 col-sm-10">
		<input type="hidden" name="id" value="<?php echo ($row["id"]); ?>" />
		<button type="submit" class="btn btn-primary">提交</button>
    </div>
  </div>
</form> 
</body>
</html>