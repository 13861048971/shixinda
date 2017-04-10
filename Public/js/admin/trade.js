(function trade(){
	var orderListNode = $('#order-list');
	orderListNode.on('ajaxCallback','.remark form', function(event, info){
		if(info.status < 1) return;
		var form = $(event.target);
		var eles = event.target.elements;
		var flag = eles['flag'].value;
		var note = eles['note'].value;
		var orderRemarkNode = form.parents('tr').prev().find('.order-remark');
		orderRemarkNode.attr('class', 'order-remark flag'+flag).attr('title', note);
		form.parents('.dropdown').removeClass('open');
	});
})();