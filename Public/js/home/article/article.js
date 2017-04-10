(function article(){
	var str = '没有权限查看？你未开通vip会员资格！点击确定按钮可购买开通';
	$('#news-list a, .homepage-slide a').click(function(){
		var th = $(this);
		if(th.attr('vip') < 1)
			return location.href = th.attr('url');
		mycomfirm(str, function(){
			location.href='/User/vip';
		}, '开通会员');
		return false;
	});
})();