<?php if (!defined('THINK_PATH')) exit();?><form class="form-horizontal ajaxSubmit role-form" action="<?php echo u();?>" method="post">
  <div class="form-group">
    <label class="col-sm-2 control-label"><b class="red">*</b>角色名称:</label>
    <div class="col-sm-5">
      <input name="name" class="form-control" value="<?php echo ($row["name"]); ?>">
    </div>
  </div>
  <div class="form-group">
    <label class="col-sm-2 control-label"><b class="red">*</b>设置权限:</label>
    <div class="col-sm-10 actions">
      <?php if(is_array($actions)): foreach($actions as $k=>$v): ?><dl>
			<dt>
				<label><input type="checkbox" <?php if($v['checked']): ?>checked<?php endif; ?>  /> <?php echo ($v["name"]); ?></label>
			</dt>
			<dd >
		  <?php if(is_array($v["actionList"])): foreach($v["actionList"] as $key=>$v2): ?><label class="checkbox-inline"><input type="checkbox" name="actions[<?php echo ($v2[4]); ?>]" value="<?php echo ($v2[3]); ?>" <?php if($v2[2]): ?>checked<?php endif; ?> ><?php echo ($v2[1]); ?></label><?php endforeach; endif; ?>
			</dd>
		</dl><?php endforeach; endif; ?>
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