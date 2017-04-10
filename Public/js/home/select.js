/**
 * 选择用
 */
(function selectPanel(){
	//显示选择页面
	$('.select-show').on('click', function(){
		var th = $(this);
		var selectPanel = th.next();
		selectPanel.show();
		
	});
	
	//隐藏显示页面
	$('.select-hide,.select-bg').click(function(){ 
		var th = $(this);
		var panel = $(th.parents('.select-panel')[0]);
		
		updateVal(panel);
	});
	
	function updateVal(panel){
		var str = [];
		var valueNode = panel.prev().find('.selected-values');
		panel.find('.select-value-list .cur,.select-nodes .cur,[type=text]').each(function(){
			var s = $(this).text();
			!s && (s = $(this).val());
			str.push(s);
		});
		str && valueNode.text(str.join(','));
		panel.hide(); 
	}
	
	//选择地区
	$('.select-value-group li').click(function(){
		var th = $(this);
		var panel = $(th.parents('.select-panel')[0]);
		var pid = th.attr('region');
		th.addClass('cur').siblings().removeClass('cur');
		panel.find('.select-value-list ul').hide();
		panel.find('.select-value-list .region'+ pid).show();
	});
	//选择值
	$('.select-value-list li,.select-nodes .label').click(function(){
		var th = $(this);
		var pid = th.attr('region');
		var input = th.find('input');
		var panel = $(th.parents('.select-panel')[0]);
		var isRadio = th.find('[type=radio]').get(0);

		if(!isRadio){
			if(th.hasClass('cur')){
				input.prop('checked', false);
				th.removeClass('cur');
			}else{
				input.prop('checked', true);
				th.addClass('cur');
			}
		}
		
		if(isRadio){
			th.addClass('cur').siblings().removeClass('cur');
			input.prop('checked', true);
			!panel.find('.select-nodes').get(0) && panel.hide() && updateVal(panel);;
		}
		
		//星星
		var pa = th.parents('.con_star');
		var i = pa.find('.label.cur').index();
		if(pa[0]){
			th.siblings().removeClass('cur2');
			pa.find('.label:lt('+ i +')').addClass('cur2');
		}
	});
	
	
})();

$(function(){
	$('#select-time').date();
});

/**
 * 选择标签
 */
//显示
function tagSelect(containerNode, max){
	var node = $('#tags-select');
	node.data('container', containerNode);
	max && node.attr('max', max);
	node.show();
}
 
(function tags(){
	var node = $('#tags-select');
	var selectedRow = $('.tag-list-selected');
	var tagList = node.find('.tag-list');
	
	//选择标签
	$('#tags-select').on('click', '.tag-list span', function(){
		var th = $(this);
		var row = selectedRow.find('span');
		var max = node.attr('max');
		th.find('input').prop('checked', true);
		if(row.length >= max)
			return;
		
		th.appendTo('.tag-list-selected');
	});
	//取消选择标签
	node.on('click', '.tag-list-selected span', function(){
		var th = $(this); 
		th.find('input').prop('checked', false);
		$('.tag-list').prepend(th);
	});
	//保存
	node.on('click', '.profile-edit', function(){
		var container = node.data('container');
		var spans = selectedRow.find('span');
		
		if(!spans[0])
			return floatWin.alert('请选择标签!');
		
		if(container){
			$(container).find('span').remove().end().prepend(spans.clone())
		}
			
		node.hide()
	});
	
	//添加标签
	$('#tag-form').on('ajaxCallback', function(e, info){
		if(info.status<1 || !info.data || !info.data.list) return;
		var li = info.data.list;
		var str = '';
		li.forEach(function(v){
			str += '<span>'+ v.name +
				'<input type="hidden" name="tag['+ v.id +']" value="'+ v.id +'"></span>';
		});
		
		tagList.prepend(str);
		$('#tag-form').hide();
	});
})();