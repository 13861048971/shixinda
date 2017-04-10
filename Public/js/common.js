/**
 * @param object jUlNode 幻灯片节点
 * @param int width 幻灯片最大宽度
 * @param int height
 * @param int model 1:banner幻灯片 2:图册幻灯片
 */
function slide(jUlNode, width, height, model){
	var ul = jUlNode;
	var len = ul.children('li').length;
	var ulMarginLeft = 0;
	var bodyWidth = $('body').width();
	var imgMargeLeft = 0;
	bodyWidth < width ?  width = bodyWidth : null;
	if(!ul[0]) return;
	
	!model ? model = 1 : '';
	var stop = false;
	if(1 == model){
		var cssStr = '<style>'+
			'.slideUl{overflow:hidden;width:' + len*width+'px;height:'+ height+'px;}'+
			'.slideUl li{float:left; height:'+height+'px;width:'+width+'px;overflow:hidden;}'+
			'.slideUl li img{height:'+ height +'px;}' + 
			'.paging{text-align:center; clear:both;margin:-31px 0 0 0;padding:0 0 10px 0}' +
			'.paging a{font-size:0px;background:#ccc;color:#ccc;display:inline-block;'+
				'width:12px;height:12px;margin:0px 5px;vertical-align:middle;border-radius:6px;}' +
			'.paging .cur{background:#f84477; color:#E45652}' + 
			'</style>';
		ul.before($(cssStr));
		ul.addClass('slideUl');
		ul.parent().css({width : width+'px' , overflow : "hidden"});
		ul.find('img').each(function(){
			var w = $(this).width();
			if(!w){
				$(this).load(function(){ 
					var w = $(this).width();
					w > width ? $(this).css({ marginLeft: (width-w)/2 + 'px'}): null;
					w < width ? $(this).css({ width: width + 'px'}): null;
				});
				return true;
			}
			w > width ? $(this).css({ marginLeft: (width-w)/2 + 'px'}): null;
			w < width ? $(this).css({ width: width + 'px'}): null;
		});
		//pagingHtml
		var pagingHtml = '<div class="paging">';
		ul.find('li').each(function(){
			var i = $(this).index() + 1;
			$(this).attr('paging', i);
			pagingHtml += '<a href="#" paging="' + i + '" ' + (1==i ? 'class="cur"' : '') + '>' + i + '</a>';
		});
		pagingHtml += '</div>';
		ul.after(pagingHtml);
		
		//paging 事件
		$('.paging a').mouseover(function(){ stop = true; }).mouseout(function(){ stop = false; }).click(function(){
			$(this).parent().find('.cur').removeClass('cur');
			$(this).addClass('cur');
			
			var i = $(this).text();
			var selector = 'li[paging="'+ i + '"]';
			var curLi = ul.find(selector);
			var offsetLeft = curLi.offset().left - ul.offset().left;
			if(offsetLeft){
				ul.animate({marginLeft: - offsetLeft}, 1500,function(){
					ul.find('li').each(function(){					
						if($(this)[0] == curLi[0]) return false;
						$(this).appendTo(ul);
					});
					ul.css('marginLeft', 0);
				});
			}
			return false;
		});
		var donghua = function(){
			ul.find('li:eq(0)').animate({ marginLeft: -width}, 1500, function(){
				$(this).appendTo(ul).css('marginLeft', '0px');
				var i = ul.find('li:eq(0)').attr('paging');
				$('.paging .cur').removeClass('cur');
				$('.paging a[paging='+i+']').addClass('cur');
			});
		}
	}
	
	if(2 == model){
		ul.find('li').hide(); 
		var first = ul.find('li:first');
		first.show();
		ul.css({'width' : width+'px', overflow : 'hidden'});
		ul.find('li').each(function(){
			var i = $(this).index();
			$(this).find('p').prepend('<span style="font-size:25px;font-weight:700;padding:0 20px 0 0;">'+ (i+1) + '/'+ len +'</span>');
		})
		var donghua = function(){
			var node = ul.find('li:visible');
			var nextNode = node.next('li');
			!nextNode[0] ? nextNode = ul.find('li:first') : '';
			node.fadeOut(1000);
			nextNode.fadeIn(800);
		}
		ul.find('>a').click(function(){
			stop = true;
			var className = $(this).attr('class');
			var node = ul.find('li:visible');
			var last = ul.find('li:last');
			if('pre' == className){
				var prevNode = node.prev('li');
				!prevNode[0] ? prevNode = last : '';
				node.fadeOut(1000);
				prevNode.fadeIn(800); 
				return false;
			}
			donghua();
			return false;
		}).mouseover(function(){ stop = true; }).mouseout(function(){ stop = false; });
	}
	
	var t = setInterval(function(){
			if(stop) return;
			donghua();
	}, 5000);
	
	ul.find('li').mouseover(function(){ stop = true; }).mouseout(function(){ stop = false; });
}

//是否登录
function isLogin(){
	var username = getCookie('username');
	var pwd = getCookie('password');
	if(username && pwd) 
		return true;
	return false;
}

$(function(){
	window.win = new smWin();
	//状态小弹窗
	window.floatWin = new smWin();   //浮动消息弹窗
	//首页幻灯片
	slide($('.banner-slide ul'), 1200, 400);
	//ajax 提交表单
	ajaxSubmit($('form.ajaxSubmit'));
	ajaxDel('.ajaxDel');
	
	//tab标签页
	$('.tab .li').on('click', function(){
		
		var target = $('#' + $(this).attr('target'));
		if(!target[0]) return;
		if($(this).hasClass('cur')) return;
		console.log('sdsd');
		$(this).addClass('cur').siblings().removeClass('cur');
		target.siblings('.ul').hide();
		target.show();
	});
	
	//弹出框
	$('[data-toggle="popover"]').popover();
	
	/*
	 * 支持轮播图滑动
	 */
	var isTouch=('ontouchstart' in window);
	if(isTouch){
		$(".carousel").on('touchstart', function(e){
			var that=$(this);
			var touch = e.originalEvent.changedTouches[0];
			var startX = touch.pageX;
			var startY = touch.pageY;
			$(document).on('touchmove',function(e){
				touch = e.originalEvent.touches[0] ||e.originalEvent.changedTouches[0];
				var endX=touch.pageX - startX;
				var endY=touch.pageY - startY;
				if(Math.abs(endY)<Math.abs(endX)){
					if(endX > 10){
						$(this).off('touchmove');
						that.carousel('prev');  
					}else if (endX < -10){
						$(this).off('touchmove');
						that.carousel('next');
					}
					return false;
				}
			});
		});
		$(document).on('touchend',function(){
			$(this).off('touchmove');
		}); 
	}
});


//ajax 加载分页内容
$(document).on('scroll', function(){
	var body = $('body')[0];
	var bottom = body.scrollHeight - body.clientHeight - body.scrollTop;
	var pager = $('#pager');
	var curr = pager.find('.current');
	var loaded = pager.attr('loaded');
	var len = pager.find('.num').length;
	var container = pager.attr('container');
	
	if(body.scrollTop < 1 || bottom > 3) return;
	if(!pager[0]) return;
	if(!curr.next('.num')[0]) return;
	if(pager.hasClass('loading')) return;
	
	var url = curr.next('a').attr('href');
	$.get(url,function(html){
		if(!html) return;
		var h = $(html);
		var rowNode = $(container, h);
		!rowNode[0] && (rowNode = h.filter(container));
		$(container).append(rowNode.html());
		pager.removeClass('loading');
		curr.removeClass('current').next('.num').addClass('current');
	});
	pager.addClass('loading');
});
layer.config({
    extend: ['skin/myskin/style.css'], //加载您的扩展样式
    skin: 'layer-ext-myskin'
});




