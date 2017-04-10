<?php if (!defined('THINK_PATH')) exit();?><div class="form-group">
	<div class="col-sm-<?php echo ($imgL); ?> control-label">
	<?php if($img['required']): ?><i class="required-star">*</i><?php endif; ?>
	<?php echo ($img["label"]); ?>
	</div>
	<div class="col-sm-<?php echo ($imgR); ?>">
		<div class="input-group">
		  <input type="text" name="<?php echo ($img["name"]); ?>" id="<?php echo ($img["idName"]); ?>" value="<?php echo ($img["path"]); ?>" class="form-control" placeholder="填写图片URL或者选择本地文件上传"  <?php if($img['required']): ?>required<?php endif; ?>>
		  <span class="input-group-btn">
			<button class="btn btn-default upload-file" data-option="{urlContainer:'#<?php echo ($img["idName"]); ?>',preview:'.<?php echo ($img["preview"]); ?>', accept:{extensions: 'jpeg,jpg,png,gif,bmp', mimeTypes:'image/*'},maxSize:500}" type="button"><i class="entypo-popup"></i> 本地文件</button>
		  </span>
		</div>
	</div>

	<div class="col-sm-1">
	  <?php if($img['path']): ?><a target="_blank" href="javascript:openInNewWindow('<?php echo ($img["path"]); ?>')"  type="image/jpeg;"><img class="<?php echo ($img["preview"]); ?>" src="<?php echo ($img["path"]); ?>"  style="height:60px"></a>
	  <?php else: ?>
	    <img class="<?php echo ($img["preview"]); ?>"  style="height:60px" /><?php endif; ?>
	</div>
</div>