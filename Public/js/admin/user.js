/**
 * 用户列表
 */
(function user(){
	//添加教育情况 or 工作情况
	$(document).on('click', '.user .addwork .addrow', function(){
		var th = $(this);
		var pa = th.parents('.addwork');
		var checked = true;
		var lastspan = pa.find('span:last');
		lastspan.find('input').each(function(){
			if( !$(this).val() ){
				$(this).focus();
				checked = false;
				return false;
			}
		});
		if(!checked) return;
		
		 var row = lastspan.clone();
		 row.find('input').each(function(){
			var name = $(this).attr('name');
			var k = name.match(/\w+\[([0-9]+)/);
			$(this).val('');
			if(k && k[1]){
				name = name.replace(/[0-9]+/, parseInt(k[1]) + 1);
				$(this).attr('name', name);
			}
		 });
		 row.find('.addrow').removeClass('addrow').addClass('delrow').text('-');
		pa.append(row);
	});
	$(document).on('click', '.user .addwork .delrow', function(){
		$(this).parent().remove();
	});
})();