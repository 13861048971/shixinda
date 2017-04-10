<?php if (!defined('THINK_PATH')) exit();?><form class="form-horizontal ajaxSubmit role-form" action="<?php echo u();?>" method="post">
  <div class="form-group">
    <label class="col-sm-2 control-label"><b class="red">*</b>名称:</label>
    <div class="col-sm-5">
      <input name="name" required class="form-control" value="<?php echo ($row["name"]); ?>">
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label"><b class="red">*</b>密码:</label>
    <div class="col-sm-5">
      <input name="password" type="password" required class="form-control" value="">
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label"><b class="red">*</b>角色:</label>
    <div class="col-sm-5">
		<?php echo w('select/index', $roleList);?>
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label">备注:</label>
    <div class="col-sm-8">
       <textarea rows="3" class="form-control" name="note"></textarea>
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
		<input type="hidden" name="id" value="<?php echo ($row["id"]); ?>" />
		<button type="submit" class="btn btn-primary">提交</button>
    </div>
  </div>
</form>