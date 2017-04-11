<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Insert title here</title>
</head>
<body>
    <form class="form-horizontal ajaxSubmit role-form" action="<?php echo u();?>" method="post">
  <div class="form-group">
    <label class="col-sm-2 control-label"><b class="red">*</b>标题:</label>
    <div class="col-sm-8">
      <input name="title" required class="form-control" value="<?php echo ($row["title"]); ?>">
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label"><b class="red">*</b>作者:</label>
    <div class="col-sm-5">
	   <input name="author" type="text" required class="form-control" value="<?php echo ($row["author"]); ?>">	
    </div>
  </div>
   <div class="form-group">
    <label class="col-sm-2 control-label">状态:</label>
    <div class="col-sm-5">

	<?php if(is_array($statusArr)): foreach($statusArr as $k=>$v): ?><label><input type="radio" value="<?php echo ($k); ?>"  <?php if($k == $row['status']): ?>checked<?php endif; ?>  name="status" /><?php echo ($v); ?></label><?php endforeach; endif; ?>
    
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">发布时间:</label>
    <div class="col-sm-5">
	   <input name="publish_time" type="password" required class="form-control" value="<?php echo ($row["publish_time"]); ?>">	
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">添加时间:</label>
    <div class="col-sm-5">
	   <input name="add_time" type="password" required class="form-control" value="<?php echo ($row["add_time"]); ?>">	
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">更新时间:</label>
    <div class="col-sm-5">
	   <input name="update_time" type="password" required class="form-control" value="<?php echo ($row["update_time"]); ?>">	
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label"><b class="red">*</b>内容:</label>
    <div class="col-sm-8">
       <textarea rows="3" class="form-control" name="content"></textarea>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
		<input type="hidden" name="id" value="<?php echo ($row["id"]); ?>" />
		<button type="submit" class="btn btn-primary">提交</button>
    </div>
  </div>
</form> 
</body>
</html>