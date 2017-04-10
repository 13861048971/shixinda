/**
 * 首页
 */
(function index(){
	$('.right-tabs .tab-dt li').on('mouseover',function(){
		var th = $(this);
		var i = th.index();
		var tabContent = th.parents('.right-tabs').find('.tab-content');
		tabContent.find('ul').hide();
		
		th.addClass('cur').siblings().removeClass('cur');
		tabContent.find('ul:eq('+ i +')').show();
		
	});
})();
