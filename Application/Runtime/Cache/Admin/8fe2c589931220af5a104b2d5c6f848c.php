<?php if (!defined('THINK_PATH')) exit();?><form class="form-horizontal ajaxSubmit slide-edit" action="<?php echo u();?>" method="post">
  <div class="form-group">
    <label class="col-sm-2 control-label"><i class="red">*</i>类型</label>
    <div class="col-sm-6 select-type">
      <?php echo w('form/node', $typeNode);?>
    </div>
  </div>
  
  <div class="form-group node-row <?php if($row['type'] == 4): ?>hide2<?php endif; ?>">
    <label class="col-sm-2 control-label"><i class="red">*</i>节点id</label>
    <div class="col-sm-2">
      <input class="form-control" name="node_id" value="<?php echo ($row["id"]); ?>" />
    </div>
  </div>
  
  <div class="form-group link-row <?php if($row['type'] < 4): ?>hide2<?php endif; ?>">
    <label class="col-sm-2 control-label"><i class="red">*</i>链接地址</label>
    <div class="col-sm-8">
      <input class="form-control" name="link" value="<?php echo ($row["link"]); ?>" placeholder="http://..." />
    </div>
  </div>
      <?php echo w('form/row', $imgNode);?>

 <div class="form-group">
    <label class="col-sm-2 control-label">排序</label>
    <div class="col-sm-2">
      <input class="form-control" name="rank" required=1 value="<?php echo ($row["rank"]); ?>" />
    </div>
  </div>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
		<input type="hidden" name="id" value="<?php echo ($row["id"]); ?>" />
		<input type="submit" class="btn btn-primary" value="提交" />
    </div>
  </div>
</form>