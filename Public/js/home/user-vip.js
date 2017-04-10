/**
 * 订单
 */
(function userVip(){
	var btn = $('.step2 button'); //确认支付按钮
	var step1 = $('.step1');
	var step2 = $('.step2');
	//购买
	$('.step1 button').on('click', function(){
		step1.hide();
		step2.show();
	});
	
	/**
	 * 微信支付
	 */
	(function(){
		var d = $('#wx-data').val()
		if(!d) return console.warn('缺少weixinData!');
		
		var wxData = JSON.parse(d);
		var wxIsready = false;
		wx.config({
			debug: true, // 
			appId    : 	wxData.appId, 			// 必填，公众号的唯一标识
			timestamp: 	wxData.timestamp, 	// 必填，生成签名的时间戳
			nonceStr : 	wxData.nonceStr, 		// 必填，生成签名的随机串
			signature: 	wxData.signature,	// 必填，签名，见附录1
			jsApiList: 	['chooseWXPay']
		});
		
		wx.ready(function(){
			wxIsready = true;
		});
		wx.error(function(res){
			console.warn(res);
		})
		//支付成功回调
		function paySucc(res){
			console.log("支付成功:", res);
		}
		
		$('#place-order').on('click', function(){
			if(!wxIsready) floatWin.alert('网络超时!');
			btn.prop('disabled', true);
			var url = '/User/vip';
			var data = {msg:$('#apply_msg').val(), id: $('#apply_id').val()};
			$.post(url, data,function(info){
				btn.prop('disabled', false);
				if(!info.success || !info.data) 
					return floatWin.alert('拉取支付参数出错!');
				var data = info.data;
				data.success = paySucc
				wx.chooseWXPay(data);
			}, 'json');
		});
	})();
})();