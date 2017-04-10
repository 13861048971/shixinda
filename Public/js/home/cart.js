(function cart(){
	var node = $('#cart');
	//移除
	node.on('click', '.delete,.clear', function(){
		var url = $(this).attr('href');
		var t = layer.confirm('你确定要移除商品?', function(){
			location.href=url;
			layer.close(t);
		});
		return false;
	});

	//全选
	node.on('change', '.select-all input', function(){
		var th = $(this);
		var inputs = node.find('.select input');
		var rows = node.find('tbody tr');
		
		if(th.is(':checked')){
			inputs.prop('checked', true);
			rows.addClass('selected');
			calculateTotal();
			return ;
		}
		
		inputs.prop('checked', false);
		rows.removeClass('selected');
		calculateTotal();
	});
	
	//选中产品
	node.on('change', 'td.select input', function(){
		var th = $(this);
		var row = th.parents('tr');
		if(th.is(':checked')){
			row.addClass('selected');
			calculateTotal();
			return ;
		}
		node.find('.select-all input').prop('checked', false);
		row.removeClass('selected');
		calculateTotal();
	});
	
	//改变购买数量
	node.on('change', '.number input', function(){
		var th = $(this);
		var row = th.parents('tr');
		var price = row.attr('price');
		var subtotalNode = row.find('.subtotal');
		var number = th.attr('number');
		
		if(number == th.val()) return ;
		var url = '/Order/cartChange';
		var data = {id: row.attr('row_id'), number: th.val()};
		$.post(url, data, function(info){
			if(info.error){
				floatWin.alert(info.info);
				th.val(number);
				return ;
			}
			subtotalNode.html('￥' + parseFloat(price * th.val()).toFixed(2));
			calculateTotal();
			th.attr('number', th.val());
		}, 'json');
		console.log('change');
	});
	
	//计算总价
	function calculateTotal(){
		var totalNode = node.find('.total-panel .price');
		var total = 0;
		node.find('tr.selected').each(function(){
			var num = $(this).find('.number input').val();
			var price = $(this).attr('price');
			total += parseFloat(price * num);
		});
		totalNode.html('￥' + total.toFixed(2));
	}
	
})();