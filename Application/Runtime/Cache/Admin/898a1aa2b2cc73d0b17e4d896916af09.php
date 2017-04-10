<?php if (!defined('THINK_PATH')) exit();?><form class="form-horizontal ajaxSubmit user-block-form" action="<?php echo u();?>" method="post">
	<div class="form-group">
	  <div class="col-sm-offset-1 col-sm-10">
		<div class="alert alert-warning">
			封号后用户将无法登陆到APP<br>用户也将收到一条通知。
		</div>
		<h4>封号原因:</h4>
		<?php if(is_array($blockArr)): foreach($blockArr as $k=>$v): ?><div class="checkbox">
			<label>
			  <input type="checkbox" value="block_type[<?php echo ($k); ?>]">
			  <?php echo ($v); ?>
			</label>
		</div><?php endforeach; endif; ?>
	  </div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-1 col-sm-10">
			<textarea style="padding-left:0;" class="form-control" rows="5" name="block_note"></textarea>
		</div>
	</div>
	<div class="form hide">
		<div class="col-sm-2 ">状态</div>
		<div class="col-sm-6"><?php echo w('select/radio', $statusList);?></div>
	</div>
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<input type="hidden" name="id" value="<?php echo ($row["id"]); ?>" />
			<button type="submit" class="btn btn-primary">提交</button>
		</div>
	</div>
</form>