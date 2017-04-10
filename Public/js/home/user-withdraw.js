/**
 * 提现
 **/
(function(){
	var verImg = $('.vercode img');
	
	//切换验证码
	function changeVercode(img){
		img.attr('src', img.attr('url') + '?t=' + time() );
	}
	
	verImg.on('click', function(){ changeVercode(verImg) });
	
	//表单提交事件
	$('.user-withdraw form').on('ajaxCallback', function(e,info){
		if(info.error)
			return changeVercode(verImg)
		location.href = '/User';
	});
})();