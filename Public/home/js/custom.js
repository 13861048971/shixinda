(function ($) {
    "use strict";
    var mainApp = {
        slide_fun: function () {
            $('#carousel-div').carousel({
                interval: 4000 //TIME IN MILLI SECONDS
            });
        },
        wow_fun: function () {
            new WOW().init();
        }
    }
    $(document).ready(function () {
        mainApp.slide_fun();
        mainApp.wow_fun();       
    });
}(jQuery));

//CLIENTS SECTION SCRIPTS
$(window).load(function () {
    $('.flexslider').flexslider({
        animation: "slide",
        animationLoop: false,
        itemWidth: 200,
        itemMargin: 15,
        pausePlay: false,
        start: function (slider) {
            $('body').removeClass('loading');
        }
    });
});
// 产品详情
$('.product-detail').on('click',function(e){
    var node = $(e.target);
    if(node[0].className == 'product-intro'){
        return false;
    }
    if(node[0].nodeName == 'SPAN'){
        node = node.parents('dt');
    }
    if(node[0].nodeName == 'A'){
        node = node.parent('dd');
    }
    if(node.find('span')[0]){
        var icon = node.find('span');
        if(icon.css('transform') == 'matrix(6.12323e-17, 1, -1, 6.12323e-17, 0, 0)'){
            icon.css('transform','rotate(0deg)');
        }else{
            icon.css('transform','rotate(90deg)');
        }
    }

    if(node[0].nodeName == 'DT'){
        node.siblings('dd').slideToggle(200);
    }
    if(node[0].nodeName == 'DD'){
        var url = node.find('a').attr('href');

        node.parents('.product-detail').find('dd').css('background-color','#fafafa');
        node.css('background-color','#eee');

        $.ajax({
            url:url,
            type:'get',
            dataType:'json',
            success:function(data){
                $('.product-title').empty().append(data.data.title);
                $('.product-content').empty().append(data.data.content);
            }
        });
    }
});
// 新闻评论功能
$('.commit-comment').on('click',function(){
    var comment = $('.news-comment textarea').val();
    var node_id = $('.news-detail').data('id');
    var user_id = $('.news-comment-user').data('id');
    var avatar = $('.news-comment-user').data('avatar');
    var nickname = $('.news-comment-user').find('a').text();

    console.log(comment);
    if(comment){
        $.ajax({
            url:'/index/comment',
            type:'post',
            data:{node_id:node_id,content:comment,user_id:user_id,type:'news'},
            success:function(){
                $('.news-comment textarea').val('');
                var html = '<dd><dl><dt><div><a href=""><img src="'+avatar+'"></a></div></dt><dd><a href="">'+nickname+'</a> · 刚刚</dd><dd>'+comment+'</dd></dl></dd>';
                $('.news-comment-list').prepend(html);
            }
        });
    }else{
        win.alert('请填写评论内容！','error')
    }
});
// 验证码
$('.btn-vercode').on('click',function(){
    var mobile = $('.mobile').val();
    $.ajax({
        url:'/User/getVercode',
        type:'post',
        data:{mobile:mobile},
        dataType:'json',
        success:function(data){
            if(data.error == '0'){
                var wait=60;
                function time(btn) {
                    if (wait == 0) {
                        btn.removeAttr("disabled");
                        btn.css('background-color','#1C86EE');
                        btn.text('获取验证码');
                        wait = 60;
                    } else { 
                        btn.attr("disabled", true);
                        btn.text('重新发送' + wait + 's');
                        btn.css('background-color','#ddd');
                        wait--;
                        setTimeout(function() {
                            time(btn)
                        }, 1000)
                    }
                }
                time($('.btn-vercode'));
            }else{
                win.alert(data.info,'error');
            }
        }
    });
});
// 登录
$('.login .btn-login').on('click',function(){
    var form = $('form').serialize();
    $.ajax({
        url:'/User/login',
        type:'post',
        data:form,
        dataType:'json',
        success:function(data){
            if(data.error != '0'){
                win.alert(data.info,'error');
            }else{
                window.location.href='/index';
            }
        }
    });
});
// 注册
$('.regist .btn-regist').on('click',function(){
    var form = $('form').serialize();
    $.ajax({
        url:'/User/regist',
        type:'post',
        data:form,
        dataType:'json',
        success:function(data){
            if(data.error != '0'){
                win.alert(data.info,'error');
            }else{
                win.alert(data.info,'success');
                window.location.href='/index';
            }
        }
    });
});
// 重置密码
$('.regist .pass-reset').on('click',function(){
    var form = $('form').serialize();
    $.ajax({
        url:'/User/passReset',
        type:'post',
        data:form,
        dataType:'json',
        success:function(data){
            console.log(data);
            if(data.error != '0'){
                win.alert(data.info,'error');
            }else{
                win.alert(data.info,'success');
                window.location.href='/index';
            }
        }
    });
});
//退出
$('.menu-section .exit').on('click',function(e){
    var url = $(e.target).attr('href');
    $.ajax({
        url:url,
        type:'get',
        dataType:'json',
        success:function(data){
            if(data.error == '0'){
                win.alert(data.info,'success');
                var href = window.location.href;
                window.location.href = href;
            }else{
                win.alert(data.info,'error');
                var href = window.location.href;
                window.location.href = href;
            }
        }
    });
});
/**
 * 渲染编辑器
 */
function renderEditor(parentNode){
	var node = $('.kind-editor');
	parentNode && (node = parentNode.find('.kind-editor'));
	
	if(!node[0]) return;
	if('undefined' == typeof KindEditor)
		return console.warn('less kindEditor!');

	setTimeout(function(){
		for(var k=0;k< node.length;k++){
			var edit = KindEditor.create(node[k], {width:"82%",height:350,afterChange:function(){
				if(!edit) return;
				edit.sync();
			}});
		}
	}, 200);
};
(function(){
    if($('.user-container .post-edit')[0]){    
        renderEditor($('.user-container .post-edit'));
    }
}());
// 编辑帖子
$('.user-container .post-edit .commit-post').on('click',function(){
    var url = '/User/postEdit/id/'+$('.post-edit .commit-post').data('id');
    var form = $('.post-edit').serialize();
    $.ajax({
        url:url,
        data:form,
        type:'post',
        dataType:'json',
        success:function(data){
            if(data.data){
                win.alert(data.info, 'success');
            }else{
                win.alert(data.info, 'error');
            }
        }
    });
});
//账号修改
$('.account-info-edit .commit-account').on('click',function(){
    var avatar = $('#img-input-id-1').val();
    $('.avatar-url').val(avatar);
    var form = $('.account-info-edit').serialize();
    $.ajax({
        data:form,
        url:'/User/userEdit',
        dataType:'json',
        type:'post',
        success:function(data){
            if(data.data){
                win.alert(data.info, 'success');
            }else{
                win.alert(data.info, 'error');
            }
        }
    });
});
//密码修改
$('.modify-pass .commit-pass').on('click',function(){
    var form = $('.modify-pass').serialize();
    $.ajax({
        data:form,
        url:'/User/userEdit',
        dataType:'json',
        type:'post',
        success:function(data){
            if(data.data){
                win.alert(data.info, 'success');
            }else{
                win.alert(data.info, 'error');
            }
        }
    });
});
// 多级下拉菜单
function initMulSel(){
	$('.mul-select').on('change',function(e){
		multiLevelFun($(e.target));
	});
}
function multiLevelFun(node){
	node.nextAll().remove();
	if(!node.val()){
		return false;
	}
	node.attr('name',node.parents('.mul-select').data('name'));
	node.siblings().removeAttr('name');
	url = node.parents('.mul-select').data('url')+node.val();
	$.ajax({
		url:url,
		type:'get',
		dataType:'json',
		success:function(data){
			var sub = data.data.list;
			if(sub){
				var html = '<select class="col-md-2"><option value="">请选择</option>';
				for(k in sub){
					html+='<option value="'+sub[k].id+'">'+sub[k].name+'</option>';
				}
				html+='</select>';
				node.after(html);
			}
		}
	});
};
initMulSel();