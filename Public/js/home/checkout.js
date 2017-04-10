(function checkout(){
	var node = $('#checkout');
	var addressNode = node.find('.address');
	var payNode = node.find('payment');
	var shippingNode = node.find('.shipping');
	var totalNode = node.find('.total-panel .price');
	
	node.on('click', '.attr label', function(){
		var th = $(this);
		var row = th.parents('.attr');
		row.find('label').removeClass('cur');
		th.addClass('cur');
		
		//重新加载配送方式
		if(th.parents('.payment').get(0)){
			var addressId = addressNode.find('label.cur').attr('address_id');
			var paymentId = payNode.find('label.cur').attr('payment_id');
			var data = {address_id : addressId, payment_id : paymentId };
			$.get('/Order/shipps', data, function(info){
				shippingNode.html(info.data.content);
			}, 'json')
			shippingNode.html('');
			return;
		}
		
		if(th.parents('.shipping').get(0)){
			var insure = parseFloat(th.attr('insure'));
			var shipFee = parseFloat(th.attr('fee'));
			var goodsTotal = parseFloat($('#goods-total').attr('price'));
			var total =insure + shipFee + goodsTotal;
			node.find('.shipping-fee .price').html('￥' + shipFee);
			node.find('.insure-fee .price').html('￥'+ insure);
			totalNode.html('￥' + total);
			return;
		}
		
		shippingNode.html('');
	});
	
	
})();