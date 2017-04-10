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
	
    //屏蔽用户,审核不通过,撤销订单原因选中
	$(document).on('click','.order-cancel-form input:checkbox,.user-block-form input:checkbox',function(){
		var th = $(this);
		var textarea = th.parents('form').find('textarea');
		var v = textarea.val();
		var s = $.trim(th.parent().text());
		if(th.prop('checked')){
			if(v && v.match(s))
				return ;
			textarea.val( (v ? v+"\n":"")+ s); 
			return;
		}
		
		if(v && v.match(s)){
			v.indexOf(s) > 0 && (s = "\n" +s);
			textarea.val( (v.replace(s,'') ) );
		}
		
	});
	
	//日期选择初始化
	$('.daterange')[0] && initDaterange('.daterange');
	//日期选择回调
	$('.search .daterange').on('daterangeCallback', function(e, data){
		var th = $(this);
		var form = th.parents('form');
		setTimeout(function(){
			if(th.val())
				form.submit();
		},200);
	});
	
	
})();
$(function(){
	renderEditor();
});

