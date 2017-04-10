(function address(){
	//弹出框
	$('[data-toggle="popover"]').popover();
	selectRegion();
	var node = $('.address');
	
	//默认
	node.on('click', '.set-default', function(){
		var th = $(this);
		var url = th.attr('url');
		var row = th.parents('tr');
		!row[0] && (row = th.parents('li'));
		
		if(th.hasClass('cur')) return;
		
		$.get(url, function(info){
			if(info.error) return;
			row.siblings().find('.set-default').removeClass('cur');
			th.addClass('cur');
			floatWin.alert(info.info, 'success');
		}, 'json');
	});
	//删除
	node.on('click', '.del', function(){
		var th = $(this);
		var url = th.attr('url');
		var row = th.parents('tr');
		!row[0] && (row = th.parents('li'));
		
		var t = layer.confirm('确定要删除地址', function(){
			$.get(url, function(info){
				if(info.error) return floatWin.alert(info.info);
				row.remove();
				floatWin.alert(info.info, 'success');
			}, 'json');
			layer.close(t);
		});
	});
	
	$(document).on('click', '.address-form .attr label', function(){
		var th = $(this);
		var addressNameNode = th.parent().prev().find('input');
		addressNameNode.val(th.text());
		th.addClass('cur').siblings().removeClass('cur');
	});
	
	
})();