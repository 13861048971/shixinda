/**
 * 轮播图 
 */
(function slide(){
	$(document).on('change', '.select-type select', function(){
		var th = $(this);
		var form = th.parents('form');
		var nodeRow = form.find('.node-row');
		var linkRow = form.find('.link-row');
		
		if(th.val() == 4){
			nodeRow.hide();
			linkRow.show();
			return;
		}
		nodeRow.show();
		linkRow.hide();
		
	});
})();