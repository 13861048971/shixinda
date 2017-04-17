(function(){
	window.win = new smWin(); //状态小弹窗
	window.floatWin = new smWin();   //浮动消息弹窗
	//ajax 提交表单
	ajaxSubmit($('form.ajaxSubmit'));
	ajaxDel('.ajaxDel');
	album($('.album'));
	selectRegion(); //选择地区
	uploadFile('.upload-file');
	
	//a标签href=#
	$(document).on('mouseover', 'a', function(){
		var th = $(this);
		th.attr('href') == '#' && th.attr('href', 'javascript:void(0)');
	});
	//全选事件
	$(document).on('change', '.select-all', function(){
		var th = $(this);
		var table = th.parents('table');
		var checkboxs = table.find(':checkbox');
		if(th.is(':checked')){
			checkboxs.prop('checked', true);
			return true;
		}
		checkboxs.prop('checked', false);
	});
	$(function(){
		//弹出框
		$('[data-toggle="popover"]').popover();
	});
	
	//ajax post提交
	$('.ajaxPost').on('click', function(){
		var th = $(this);
		var url = th.attr('url');
		var success = th.attr('success');
		var data = th.attr('data');
		var isConfirm = th.attr('confirm');

		var call = function(){
			th.prop('disabled', true).addClass('disabled');
			$.post(url, data, function(d){
				th.prop('disabled', false).removeClass('disabled');
				if(d.error){
					return floatWin.alert(d.info);
				}
				th.trigger('change', th);
				if(success)
					return th.text(success).prop('disabled', true).addClass('disabled');
				
				floatWin.alert(d.info, 'success');
			},'json');
		}
		
		if(!isConfirm){
			return call();
		}
		var act = th.val();
		!act && (act = th.text()) || (act = '这样做');
		var t = layer.confirm("确定要" + act +  "吗?",function(){  
			call(); layer.close(t);
		});
	});
	$('.daterange')[0] && initDaterange('.daterange');
	//日期选择回调
	$('.daterange').on('daterangeCallback', function(e, data){
		var th = $(this);
		var form = th.parents('form');
		setTimeout(function(){
			if(th.val())
				form.submit();
		},200);
	});
	$('.multi-level-select')[0] && initMultiSelect();
})();
$(function(){
	renderEditor();
});
$('.post-cate-table').on('click',function(e){
	var _this=$(e.target);
	var pid=_this.data('id');
	if(_this[0].className=='icon-add'){
		var url="/admin/user/postcatechildren/pid/"+_this.data('id');
		$.ajax({
			url:url,
			type:'get',
			dataType:'json',
			success:function(data){
				var sub=data.data.list;
				_this.removeClass('icon-add').addClass('icon-sub');

				for(k in sub){
					var html='<tr class="'+_this.parents('tr')[0].className+'pid'+sub[k].pid+' "><td>';
					if(sub[k].num > 0){
						html+='<span class="icon-add" data-id="'+sub[k].id+'" data-level="'+(_this.data('level')+1)+'"></span>';
					}

					html+='</td><td class="post-cate-name">';

					for(var i=0;i<_this.data('level');i++){
						html+='&nbsp;&nbsp;&nbsp;&nbsp;';
					}

					html+=sub[k].name+'</td><td>'+sub[k].rank+'</td>';

					if(sub[k].status > 0){
						html+='<td class="green">显示</td>';
					}else{
						html+='<td>不显示</td>';
					}
					
					html+='<td>'+sub[k].addTime+'</td><td>'+sub[k].updateTime+'</td><td width=200 class="handle"><a class="dialog add" dialog-lg="true" href="#" url="/admin/user/postCateEdit/pid/'+sub[k].id+'">添加子类</a> <a class="dialog edit" dialog-lg="true" href="#" url="/admin/user/postCateEdit/id/'+sub[k].id+'">编辑</a> <a class="ajaxDel del" href="#" url="/admin/user/postCateDel/id/'+sub[k].id+'">删除</a></td></tr>';
					
					_this.parents('tr').after(html);
				}
			}
		})
	}else if(_this[0].className=='icon-sub'){
		_this.removeClass('icon-sub').addClass('icon-add');
		selector='.pid'+pid;
		$(selector).remove();
	}
});
$('.content-cate-table').on('click',function(e){
	var _this=$(e.target);
	var pid=_this.data('id');
	if(_this[0].className=='icon-add'){
		var url="/admin/content/contentCateChildren/pid/"+_this.data('id');
		$.ajax({
			url:url,
			type:'get',
			dataType:'json',
			success:function(data){
				var sub=data.data.list;
				_this.removeClass('icon-add').addClass('icon-sub');

				for(k in sub){
					var html='<tr class="'+_this.parents('tr')[0].className+'pid'+sub[k].pid+' "><td>';
					if(sub[k].number > 0){
						html+='<span class="icon-add" data-id="'+sub[k].id+'" data-level="'+(_this.data('level')+1)+'"></span>';
					}

					html+='</td><td class="post-cate-name">';

					for(var i=0;i<_this.data('level');i++){
						html+='&nbsp;&nbsp;&nbsp;&nbsp;';
					}

					html+=sub[k].name+'</td><td>'+sub[k].rank+'</td>';

					if(sub[k].status > 0){
						html+='<td class="green">'+sub[k].statusName+'</td>';
					}else{
						html+='<td>'+sub[k].statusName+'</td>';
					}
					
					html+='<td>'+sub[k].addTime+'</td><td>'+sub[k].updateTime+'</td><td width=200 class="handle"><a class="dialog add" dialog-lg="true" href="#" url="/admin/content/addContentChildren/id/'+sub[k].id+'/name/'+sub[k].name+'">添加子类</a>| <a class="dialog edit" dialog-lg="true" href="#" url="/admin/content/contentCateEdit/id/'+sub[k].id+'">编辑</a>| <a class="ajaxDel del" href="#" url="/admin/content/contentCateDel/id/'+sub[k].id+'">删除</a></td></tr>';
					_this.parents('tr').after(html);
				}
			}
		})
	}else if(_this[0].className=='icon-sub'){
		_this.removeClass('icon-sub').addClass('icon-add');
		selector='.pid'+pid;
		$(selector).remove();
	}
});
$('.navigation-table').on('click',function(e){
	var _this=$(e.target);
	var pid=_this.data('id');
	if(_this[0].className=='icon-add'){
		var url="/admin/content/navigationChildren/pid/"+_this.data('id');
		$.ajax({
			url:url,
			type:'get',
			dataType:'json',
			success:function(data){
				var sub=data.data.list;
				_this.removeClass('icon-add').addClass('icon-sub');
				for(k in sub){
					var html='<tr class="'+_this.parents('tr')[0].className+'pid'+sub[k].pid+' "><td>';
					if(sub[k].num > 0){
						html+='<span class="icon-add" data-id="'+sub[k].id+'" data-level="'+(_this.data('level')+1)+'"></span>';
					}

					html+='</td><td class="post-cate-name">';

					for(var i=0;i<_this.data('level');i++){
						html+='&nbsp;&nbsp;&nbsp;&nbsp;';
					}
					html+=sub[k].name+'</td><td><img src="'+sub[k].logo+'" width=25></td><td><a href="'+sub[k].url+'" target="_blank">'+sub[k].url+'</a></td>';
					if(sub[k].status > 0){
						html+='<td class="green">'+sub[k].statusName+'</td>';
					}else{
						html+='<td>'+sub[k].statusName+'</td>';
					}
					
					html+='<td>'+sub[k].rank+'</td><td width=200 class="handle"><a class="dialog add" dialog-lg="true" href="#" url="/admin/content/navigationChildrenEdit/name/'+sub[k].name+'/pid/'+sub[k].id+'">添加子类</a>| <a class="dialog edit" dialog-lg="true" href="#" url="/admin/content/navigationEdit/id/'+sub[k].id+'">编辑</a>| <a class="ajaxDel del" href="#" url="/admin/content/navigationDel/id/'+sub[k].id+'">删除</a></td></tr>';
					_this.parents('tr').after(html);
				}
			}
		})
	}else if(_this[0].className=='icon-sub'){
		_this.removeClass('icon-sub').addClass('icon-add');
		selector='.pid'+pid;
		$(selector).remove();
	}
});
// $('.multi-level-select').on('change',function(e){
// 	multiLevel($(e.target));
// });