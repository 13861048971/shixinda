/**
 * 运营中心
 */
(function run(){
	//演出单管理
	$('.show-order-list td').on('click', function(){
		var tr = $(this).parent();
		var url = tr.attr('url');
		url && (location.href= url);
	});
	
	//发送消息选择框
	$('.select-type-id .trans').click(function(){
		var th = $(this);
		var div = th.next();
		if(!div.is(':hidden'))
			return div.hide();
		
		div.show();
	});
	
	$('.select-type-id .btn-default').click(function(){
		var div = $(this).parents('.select');
		var op = $(div.parents('form').find('select option')[0]);
		op.text('所有人');
		console.log(op);
		div.find('input').each(function(){
			$(this).prop('checked', false);
		});
		div.hide();
	})
	//确定
	$('.select-type-id .btn-info').click(function(){
		var div = $(this).parents('.select');
		var op = $(div.parents('form').find('select option')[0]);
		var str = [];
		div.find('input:checked').each(function(){
			str.push($(this).parent().text());
		});
		
		str.length > 0 && op.text(str.join(','));
		div.hide();
	});
	var evTimeStamp = 0;
	$('.select-type-id input').click(function(){
		var now = +new Date();
		console.log(now);
        if (now - evTimeStamp < 100) {
           return;
        }
        evTimeStamp = now;
		
		var th = $(this);
		var name = th.attr('name');
		var div = th.parents('.select');
		var inputs = div.find('input');
		var rowInp = th.parent().parent().next().find('input');
		if('type' == name){
			console.log(rowInp)
			if(th.prop('checked') == true && 1 == th.val()){
				inputs.prop('checked', true);
			}
			if(th.prop('checked') == true && th.val() > 1){
				inputs.prop('checked', false);
				rowInp.prop('checked', true);
				th.prop('checked',true);
			}
			
			if(th.prop('checked') == false && 1 == th.val()){
				inputs.prop('checked', false);
			}
			if(th.prop('checked') == false && th.val() > 1){
				rowInp.prop('checked', false);
				th.prop('checked',false);
			}
		}
		
	});
})();