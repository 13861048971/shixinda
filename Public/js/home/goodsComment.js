/**
 * 产品评论||产品咨询
 * @param string selector 评论节点选择器
 */
function goodsComment(selector){
	var s = this;
	s.url = 'Public/Goods/comment';
	s.node = $(selector);
	s.goods_id = s.node.attr('goods_id');
	
	//tabs
	s.node.on('click', '.tab_comments li', function(){
		var th = $(this);
		var url = th.attr('url');
		$.get(url, function(info){
			s.node.find('.comment-list').html(info.data.content);
			console.log(s.node);
		}, 'json');
		
		th.addClass('cur').siblings().removeClass('cur');
	});
	
	//pager
	s.node.on('click', '.pager a', function(){
		var th = $(this);
		var url = th.attr('href');
		$.get(url, function(info){
			s.node.find('.comment-list').html(info.data.content);
			console.log(s.node);
		}, 'json');
		return false;
	});
}
(function(){
	new goodsComment('#goods_comment');
	new goodsComment('#goods_consult');
})();