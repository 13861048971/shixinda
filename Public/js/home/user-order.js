/**
 * 订单
 */
(function userOrder(){
	//确认收货
	$('.order-list .handle .order-receive').on('click', function(){
		var url =$(this).attr('url');
		var t = layer.confirm('你确定已经收货了吗?', function(){
			$.get(url, function(info){
				if(!info.error){
					floatWin.alert(info.info, 'success');
					location.reload();
					return;
				}
				return floatWin.alert(info.info);
			}, 'json');
			layer.close(t);
		});
	});
	//申请退货
	$('.order-list .handle .order-complete').on('click', function(){
		var url =$(this).attr('url');
		$.get(url, function(info){
			if(!info.error){
				floatWin.alert(info.info, 'success');
				location.reload();
				return;
			}
			return floatWin.alert(info.info);
		}, 'json');
	});
	
	//评论 打星
	$(document).on('click', '.rating .star', function(){
		var th = $(this);
		var i = th.index();
		var input = th.parent().find('input');
		th.parent().find('.star').each(function(k, v){
			if($(v).index() <= i)
				$(v).addClass('cur');
			else
				$(v).removeClass('cur');
		});
		input.val(th.html());
	});
	
})();