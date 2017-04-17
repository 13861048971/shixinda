/**
 * 模态对话框
 */
function dialog(idName, callback){
	!idName && (idName = 'myModal')
	var th = this;
	var modelHtml = '<div class="modal fade" id="'+ idName +'" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button><h4 class="modal-title" id="myModalLabel">Modal title</h4></div><div class="modal-body"></div><div class="modal-footer"><button type="button" class="btn btn-default btn-cancel" data-dismiss="modal">取消</button> <button class="btn btn-primary btn-submit"> 确定 </botton></div></div></div></div>';
	var modalNode = $('#' + idName);
	if(!modalNode.get(0))
	  modalNode = $(modelHtml);
	var cancelBtn = modalNode.find('.btn-cancel');
	var submitBtn = modalNode.find('.btn-submit');
	th.modalNode = modalNode;
	th.form = null;
	// $('body').append(th.modalNode);
	
	//执行弹窗的节点
	this.triggerNode = {};
	this.show = function(content, title, lg, triggerNode){
		th.modalNode.find('.modal-body').html(content);
		th.modalNode.find('.modal-title').html(title);
		th.triggerNode = triggerNode;
		if(lg){
			th.modalNode.find('.modal-dialog').addClass('modal-lg');
		}
		th.modalNode.modal('show');
		th.form = th.modalNode.find('form');
		if(!th.form[0] || !th.form.find(':submit')[0]) {
			th.modalNode.find('.modal-footer button').hide();
			return;
		}
		th.form.find(':submit').hide();
		//绑定 ajaxSubmit 到表单
		ajaxSubmit(th.form);
		renderEditor(th.modalNode);			//编辑器
		album(th.modalNode.find('.album'));	//相册
		initLayDate(th.modalNode.find('.lay-date'));
		setTimeout(initMultiSelect,500);
	};
	this.hide = function(){
		modalNode.modal('hide');
	}
	
	//取消按钮click事件
	modalNode.on('click','.btn-cancel', function(){
		if(!th.form[0]) return;
		th.form.off('ajaxCallback');
	});
	//确定按钮
	modalNode.on('click','.btn-submit', function(){
		if(callback) callback();
		if(!th.form[0]) return;
		//绑定ajax回调事件
		th.form.on('ajaxCallback', function(e,info){
			if(info.error) return;
			modalNode.hide();
			setTimeout(function(){ location.reload(); }, 800);
		});
		th.form.find(':submit').trigger('click');
	});
	//绑定dialog 属性
	
	$(document).on('click', '.dialog', function(){
		var url = $(this).attr('url');
		var title = $(this).attr('dialog-title');
		var lg    = $(this).attr('dialog-lg');
		var triggerNode = $(this); 
		!title && (title = triggerNode.attr('title'));
		!title && (title = triggerNode.html());
		
		$.get(url, function(info){
			if(info.status){
				var content = $(info.data.content);
				th.show(content, title, lg, triggerNode);
				return;
			}
			floatWin.alert(info.info);
		}, 'json');
		return void(0);
	});
	th.modalNode.off('shown.bs.modal').on('shown.bs.modal', function (e) {
		$(document).off('focusin.modal');//解决编辑器弹出层文本框不能输入的问题
	});
}
(new dialog());

