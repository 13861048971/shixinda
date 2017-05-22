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
//日期格式化
function date(format, UnixTime) {
	this_Date = new Date(UnixTime?(parseInt(UnixTime)*1000):(new Date().getTime()));
	var weekday = new Array('Sunday', 
	'Monday', 
	'Tuesday', 
	'Wednesday', 
	'Thursday', 
	'Friday', 
	'Saturday');
	return format.replace(/y/i, this_Date.getFullYear()).
	replace('m', parseInt(this_Date.getMonth()+1) < 10 ? 
	'0'+parseInt(this_Date.getMonth()+1) : parseInt(this_Date.getMonth()+1)).
	replace('d', parseInt(this_Date.getDate()) < 10 ? 
	'0'+this_Date.getDate() : this_Date.getDate()).
	replace(/h/i, this_Date.getHours() < 10 ? 
	'0' + this_Date.getHours() : this_Date.getHours()).
	replace('i', this_Date.getMinutes() < 10 ? 
	'0' + this_Date.getMinutes() : this_Date.getMinutes()).
	replace('s', this_Date.getSeconds() < 10 ? 
	'0' + this_Date.getSeconds() : this_Date.getSeconds()).
	replace('w', this_Date.getDay()).
	replace('W', weekday[this_Date.getDay()]);
}
//时间戳函数
function time() {
	return parseInt(new Date().getTime()/1000);
}
/**
 * 小弹窗
 **/
function smWin(info){
	if(!info) info = '';
	var style='<style id="sm-win-style">'+
				'.sm-win{position:fixed;border:1px solid #faebcc;color:#8a6d3b;background:#fcf8e3;padding:8px 10px; '+
				' z-index:19991015; display:none;opacity:0.9}'+
				'.sm-win.error{background:#f2dede;border:1px solid #ebccd1;color:#a94442}'+
				'.sm-win.success{background:#dff0d8;border:1px solid #d6e9c6;color:#3c763d}'+
				'.sm-win.left-bottom{display:block; left:0; bottom:10px;}'+
				'.sm-win.center{width: auto;display: table;margin-left: auto; margin-right: auto;}'+
			  '</style>';
	var html = '<div class="sm-win"><span>' + info + '</span></div>';
	var node = $(html);
	this.add = function(){
		if(!$('#sm-win-style').get(0))
			$('head').append(style);
		$('body').append(node); return node; 
	};
	this.del = function(){ node.remove(); };
	this.show= function(str,pos){
		if(str) node.find('span').text(str);
		node.show();
		this.setPosition('center');
		return this;
	}
	this.hide = function(sec){ node.fadeOut(sec); }
	this.alert = function(info, type){ 
		this.show(info, 'center');
		if(!type) type = 'error';
		node.removeClass('error').removeClass('success').addClass(type);
		setTimeout(function(){
			node.fadeOut(300); return;
			node.animate({left:0},800,function(){ node.hide(); });
		}, 2500);
	}
	this.getWin = function(){ return node; };
	this.loading = function(){ 
		node.show().find('span').text('加载数据中...');	
		node.addClass('left-bottom'); 
	}
	//@param string   "lb", 'center'
	this.setPosition = function(pos){
		if('lb' == pos)
			node.removeClass('center').addClass('left-bottom');
		if('center' == pos)
			setPos(node);	
	}
	this.add();
}
function setPos(jNode, pos){
	if(!pos) pos = 'center';
	
	var top = ($('html').get(0).clientHeight - jNode.height()) / 2;
	var left = ($('html').get(0).clientWidth - jNode.width()) / 2;
	left = left>0 ? left : 0;
	top  = top > 0 ? top : 0;
	top = top > 40 ? (top - 40) : top;
	
	var scrollTop = $('html').scrollTop() || $('body').scrollTop();
	top = jNode.css('position') == 'absolute' ?  scrollTop + top : top;
	jNode.css({"top":top,"left":left});
}
/**
 * 图片上传裁剪，依赖jQuery,Bootstrap
 * @param string src 初始图片地址
 * @param NaN(num/num) aspectRario 裁剪比例
 * @param function callback 回调函数
 */
function imgUploadClip(src,aspectRatio,onclick,callback){
	var html = '<button class="btn btn-default upload-file" type="button">上传图片</button><input type="file" id="file0" multiple="multiple" style="display:none"><div class="img-area"><div class="img-operate"><img src="'+src+'" id="img0"></div><div class="img-handle" style="display:none;"><div class="img-preview" style="overflow:hidden;"><img src="" alt=""></div><button class="btn btn-default save" type="button">裁剪</button></div></div>';
	$('.img-upload-clip').append(html);
	$('.upload-file').on('click',function(){
		$("#file0").val('');
		$("#file0").click();
		$.fn.cropper;
		if(onclick){
			onclick();
		}
	});
	var fileName = '';
	$("#file0").change(function(){
		if(this.files[0]){
			var objUrl = getObjectURL(this.files[0]) ;
			if($('.img-upload-clip').data('type')){
				var imgType = $('.img-upload-clip').data('type');
			}else{
				var imgType = 'images';
			}
			fileName = imgType+'/';
			fileName += date('y-m-d',time())+'/';
			fileName += time().toString()+Math.round(Math.random()*8999+1000+1).toString()+this.files[0].name.match(/\.[a-z]+$/)[0];
			if (objUrl) {
				$("#img0").attr("src", objUrl) ;
			}
			$('.img-upload-clip .img-handle').show();
			$('.img-upload-clip .img-operate>img').cropper({
				aspectRatio: aspectRatio,
				crop: function() {},
				preview:'.img-upload-clip .img-preview'
			}); 
			$('.img-upload-clip .img-operate>img').cropper('replace', objUrl);
		}
	});
	//建立一个可存取到该file的url
	function getObjectURL(file) {
		var url = null ; 
		if (window.createObjectURL!=undefined) { // basic
			url = window.createObjectURL(file) ;
		} else if (window.URL!=undefined) { // mozilla(firefox)
			url = window.URL.createObjectURL(file) ;
		} else if (window.webkitURL!=undefined) { // webkit or chrome
			url = window.webkitURL.createObjectURL(file) ;
		}
		return url ;
	}
	$('.save').on('click', function(){
		if(!$('.cropper-container')[0])
			return;
		// token获取
		var token = '';
		var url = '/File/getQiNiuToken';
		$.ajax({
			url:url,
			type:'post',
			data:{'imageName':fileName},
			dataType:'json',
			success:function(data){
				if(!data.error){
					token = data.data.token;
					uploadQiniu(fileName,token);
				}
			}
		});
		function uploadQiniu(fileName,token){
				$('.img-upload-clip .img-operate>img').cropper("getCroppedCanvas").toBlob(function(blob) {
				var url = getObjectURL(blob);
				var form = new FormData();
				form.append("file", blob);
				form.append('key', fileName);
				form.append("token", token);
				win.alert('图片上传中', 'info');
				$.ajax({
					processData: false,
					contentType: false,
					data:form,
					url:'http://up-z2.qiniu.com/',
					dataType:'json',
					type:'post',
					success:function(data){
						if(callback){
							callback(data);
						}
						$('.img-upload-clip .img-handle').hide();
						$('.img-upload-clip .img-operate>img').cropper('destroy');
						$("#img0").attr("src", url);
					}
				});
			},'image/jpeg',0.8);
		}
	});
};
$(function(){
	window.win = new smWin();
	//状态小弹窗
	window.floatWin = new smWin(); 
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
function loginOut(){
	$.ajax({
		url:'/user/loginOut',
		type:'get',
		dataType:'json',
		success:function(data){
			if(data.error == '0'){
				win.alert(data.info,'success');
				window.location.href = '/';
			}else{
				win.alert(data.info,'error');
			}
		}
	});
}
$('.login-out').on('click', loginOut);
// 渲染编辑器
function renderEditor(parentNode){
	var node = $('.kind-editor');
	parentNode && (node = parentNode.find('.kind-editor'));
	
	if(!node[0]) return;
	if('undefined' == typeof KindEditor)
		return console.warn('less kindEditor!');

	setTimeout(function(){
		for(var k=0;k< node.length;k++){
			var edit = KindEditor.create(node[k], {width:"100%",height:350,afterChange:function(){
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
				window.location.href = '/User/postList';
			}else{
				win.alert(data.info, 'error');
			}
		}
	});
});
// 删除帖子
$('.user-container .post-manage-list').on('click',function(e){
	if($(e.target)[0].className == 'post-del'){
		var url = '/User/postDel/id/'+$(e.target).data('id');
		$.ajax({
			url:url,
			type:'get',
			dataType:'json',
			success:function(data){
				if(data.error == '0'){
					win.alert(data.info, 'success');
					$(e.target).parents('dd').remove();
				}else{
					win.alert(data.info, 'error');
				}
			}
		});
	}
});
//账号修改
$('.account-info-edit .commit-account').on('click',function(){
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
(function(){
	if($('.img-upload-clip')[0]){
		var src = $('.img-upload-clip').data('src');
		var aspectRatio = 1/1;
		function callback(data){
			if(!data.error){
				$('form .avatar-url').attr('value',  data.key);
			}else{
				win.alert(data.error, 'error');
			}
		}
		imgUploadClip(src,aspectRatio,'',callback);
	}
}());
//密码修改
$('.modify-pass .commit-pass').on('click',function(){
	var newPass = $('.new-pass').val();
	var confirmPass = $('.confirm-pass').val();
	if(newPass == confirmPass){
		var form = $('.modify-pass').serialize();
		$.ajax({
			data:form,
			url:'/User/changePwd',
			dataType:'json',
			type:'post',
			success:function(data){
				if(data.data){
					win.alert(data.info, 'success');
					window.location.href = '/user/login';
				}else{
					win.alert(data.info, 'error');
				}
			}
		});
	}else{
		win.alert('新密码不一致','error');
	}
	
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
// 关于我们导航选中状态
(function(){
	if($('.about-nav')[0]){
		var url = window.location.href;
		var nav = $('.about-nav').find('a');
		for(var k=0;k<nav.length;k++){
			if(nav[k].href == url){
				nav[k].parentNode.style.backgroundColor='#eee';
			}
		}
	}
}());
// 帖子列表过滤选中状态
(function(){
	if($('.post-list-filter')[0]){
		var url = window.location.href;
		var nav = $('.post-list-filter dd').find('a');
		for(var k=0;k<nav.length;k++){
			if(nav[k].href == url){
				nav[k].style.color='#1C86EE';
			}
		}
	}
}());
// 帖子详情收藏
$('.post-handle .post-collect').on('click',function(){
	var url = '/post/postCollect/id/'+$('.post-detail-page').data('id');
	$.ajax({
		url:url,
		dataType:'json',
		type:'get',
		success:function(data){
			if(!data.error){
				win.alert(data.info, 'success');
				var num = $('.post-collect').find('span').text();
				num = num.substr(1, num.length-2);
				if(data.status){
					num = parseInt(num)+1;
					$('.post-collect')[0].innerHTML='已收藏<span>('+num+')</span>';
				}else{
					num = parseInt(num)-1;
					$('.post-collect')[0].innerHTML='收藏<span>('+num+')</span>';
				}
			}else{
				win.alert(data.info, 'error');
			}
		}	
	})
});
// 帖子首页轮播图
function changeImg(index){
	var rightImg = (index*514.8)+"px";
	var rightTitle = (index*308)+"px";
	$('.banner-img').animate({right:rightImg}, 1000);
	$('.banner-title ul').animate({right:rightTitle}, 1000);
	$('.banner-control span').removeClass();
	if($('.banner-control span')[index]){
		$('.banner-control span')[index].className = 'banner-cur';
	}
}
var curIndex = 0;
var imgLen = $(".banner-img li").length;
var autoChange = setInterval(function(){
	if(!$('.post-index-banner')[0])
		return false;
	if(curIndex < imgLen-1){ 
		curIndex ++; 
	}else{ 
		curIndex = 0;
	}
	changeImg(curIndex); 
},2500);
function autoChangeAgain(curIndex){
	autoChange = setInterval(function(){ 
	if(curIndex < imgLen-1){ 
		curIndex ++;
	}else{ 
		curIndex = 0;
	}
	changeImg(curIndex); 
	},2500);
}
$('.banner-control span').hover(function(){
	var index = $(this).index();
	$(this).siblings().removeClass();
	$(this).addClass('banner-cur');
	window.clearInterval(autoChange);
	$('.banner-img').stop();
	$('.banner-title ul').stop();
	var rightImg = (index*514.8)+"px";
	var rightTitle = (index*308)+"px";
	$('.banner-img').css('right',rightImg);
	$('.banner-title ul').css('right',rightTitle);
},function(){
	var index = $(this).index();
	autoChangeAgain(index);
});
// 帖子评论
$('.btn-pub-comment').on('click',function(){
	var comment = $('.pub-comment textarea').val();
	var post_id = $('.post-detail-page').data('id');

	if(comment){
		$.ajax({
			url:'/post/comment',
			type:'post',
			data:{post_id:post_id,content:comment},
			success:function(data){
				data = JSON.parse(data);
				if(!data.error){
					win.alert(data.info, 'success');
					$('.pub-comment textarea').val('');
					var avatar = $('.nav-avatar img').attr('src');
					var nickname = $('.nav-avatar+a').text();
					var id = $('.nav-avatar').data('id');

					var html = '<div class="row post-comment-item"><div class="col-md-2"><a href="/post/personInfo'+id+'"><img src="'+avatar+'" alt=""></a></div><div class="col-md-10"><ul><li><a href="/post/personInfo'+id+'">'+nickname+'</a> 刚刚 发表</li><li><div class="comment-content">'+comment+'</div></li><li class="comment-handle" data-id="'+data.data.id+'"><a href="javascript:void(0);" class="comment-reply" data-id="'+data.data.id+'">回复</a> <a href="javascript:void(0);" class="comment-support">赞<span>(0)</span></a> <a href="javascript:void(0);" class="comment-oppose">踩<span>(0)</span></a><span><a href="javascript:void(0);" class="post-tip">举报<span>(0)</span></a></span></li></ul></div></div>';

					$('.post-pager').before(html);
				}else{
					win.alert(data.info, 'error');
				}
			}
		});
	}else{
		win.alert('请填写评论内容！','error')
	}
});
// 帖子赞和踩
function postSupport(act, _this){
	if(_this){
		var post_id = $('.post-detail-page').data('id');
		var id = _this.parents('.comment-handle').data('id');
		var url = '/post/postCommentSupport/post_id/'+post_id+'/act/'+act+'/id/'+id;
	}else{
		var id = $('.post-detail-page').data('id');
		var url = '/post/postSupport/act/'+act+'/id/'+id;
	}
	
	$.ajax({
		url:url,
		type:'get',
		dataType:'json',
		success:function(data){
			if(!data.error){
				if(_this){
					if(data.status == 3){
						win.alert(data.info, 'success');
						var num = _this.find('span').text();
						console.log(num);
						num = num.substr(1, num.length-2);
						num = parseInt(num)+1;
						_this[0].innerHTML='已赞<span>('+num+')</span>';
					}
					if(data.status == 4){
						win.alert(data.info, 'success');
						var num = _this.find('span').text();
						num = num.substr(1, num.length-2);
						num = parseInt(num)+1;
						_this[0].innerHTML='已踩<span>('+num+')</span>';
					}
				}else{
					if(data.status == 3){
						win.alert(data.info, 'success');
						var num = $('.post-support').find('span').text();
						num = num.substr(1, num.length-2);
						num = parseInt(num)+1;
						$('.post-support')[0].innerHTML='已赞<span>('+num+')</span>';
					}
					if(data.status == 4){
						win.alert(data.info, 'success');
						var num = $('.post-oppose').find('span').text();
						num = num.substr(1, num.length-2);
						num = parseInt(num)+1;
						$('.post-oppose')[0].innerHTML='已踩<span>('+num+')</span>';
					}
				}
			}else{
				win.alert(data.info, 'error');
			}
		}
	});
};
$('.post-detail-page .post-support').on('click', function(){
	postSupport('zan');
});
$('.post-detail-page .post-oppose').on('click', function(){
	postSupport('cai');
});
// 评论支持和反对
$('.post-detail-page').on('click','.comment-support', function(){
	var _this = $(this);
	postSupport('zan', _this);
});
$('.post-detail-page').on('click','.comment-oppose', function(){
	var _this = $(this);
	postSupport('cai', _this);
});
//举报弹窗
var thisReport = '';
$('.post-detail-page').on('click','.post-tip', function(){
	$('.win-tip-container').show();
	thisReport = $(this);
	return thisReport;
});
$('.win-tip-bg').on('click', function(){
	$(this).parent().hide();
});
$('.win-tip span').on('click', function(){
	$('.win-tip-container').hide();
});
$('.win-tip button').on('click', function(){
	var content = $('.win-tip textarea').val();
	if(!content){
		win.alert('请填写举报内容！', 'error');
		return false;
	}
	if(!thisReport.parents('.comment-handle').data('id')){
		var post_report_id = $('.post-detail-page').data('id');
	}else{
		var post_report_id = thisReport.parents('.comment-handle').data('id');
	}
	$.ajax({
		url:'/post/postReport',
		type:'post',
		dataType:'json',
		data:{content:content,post_id:post_report_id},
		success:function(data){
			if(!data.error){
				win.alert(data.info, 'success');
				var num = thisReport.find('span').text();
				num = num.substr(1, num.length-2);
				num = parseInt(num)+1;
				thisReport[0].innerHTML='已举报<span>('+num+')</span>';
				$('.win-tip textarea').val('');
				$('.win-tip-container').hide();
			}else{
				win.alert(data.info, 'error');
			}
		}
	});
});
// 其他用户主页显示控制
$('.person-index .tab-data').on('click', function(){
	$(this).addClass('tab-actived');
	$('.person-index .tab-subject').removeClass('tab-actived');
	$('.person-index .person-data').show();
	$('.person-index .person-post').hide();
});
$('.person-index .tab-subject').on('click', function(){
	$(this).addClass('tab-actived');
	$('.person-index .tab-data').removeClass('tab-actived');
	$('.person-index .person-post').show();
	$('.person-index .person-data').hide();
});
$('.person-index .switch-subject').on('click', function(){
	$(this).addClass('actived');
	$('.person-index .switch-reply').removeClass('actived');
	$('.person-index .post-subject').show();
	$('.person-index .post-reply').hide();
})
$('.person-index .switch-reply').on('click', function(){
	$(this).addClass('actived');
	$('.person-index .switch-subject').removeClass('actived');
	$('.person-index .post-reply').show();
	$('.person-index .post-subject').hide();
});
if($('.person-index')[0]){
	var url = window.location.href;
	if(url.match(/.+\/([a-z]+)/)[1]=='original'){
		$('.person-index .tab-subject').addClass('tab-actived');
		$('.person-index .tab-data').removeClass('tab-actived');
		$('.person-index .switch-subject').addClass('actived');
		$('.person-index .switch-reply').removeClass('actived');
		$('.person-index .person-post').show();
		$('.person-index .person-data').hide();
		$('.person-index .post-subject').show();
		$('.person-index .post-reply').hide();
	}
	if(url.match(/.+\/([a-z]+)/)[1]=='reply'){
		$('.person-index .tab-subject').addClass('tab-actived');
		$('.person-index .tab-data').removeClass('tab-actived');
		$('.person-index .switch-reply').addClass('actived');
		$('.person-index .switch-subject').removeClass('actived');
		$('.person-index .person-post').show();
		$('.person-index .person-data').hide();
		$('.person-index .post-reply').show();
		$('.person-index .post-subject').hide();
	}
}
// 帖子评论回复
var thisReply = '';
$('.win-reply span').on('click', function(){
	$('.win-reply-container').hide();
});
$('.post-detail-page').on('click','.comment-reply', function(){
	$('.win-reply-container').show();
	thisReply = $(this);
	return thisReply;
});
$('.win-reply button').on('click', function(){
	var content = $('.win-reply textarea').val();
	if(!content){
		win.alert('请填写回复内容！', 'error');
		return false;
	}
	var reply_id = thisReply.data('id');
	$.ajax({
		url:'/post/personReplay',
		type:'post',
		dataType:'json',
		data:{content:content,reply_id:reply_id},
		success:function(data){
			if(!data.error){
				win.alert(data.info, 'success');
				$('.win-reply textarea').val('');
				$('.win-reply-container').hide();

				var avatar = $('.nav-avatar img').attr('src');
				var nickname = $('.nav-avatar+a').text();
				var id = $('.nav-avatar').data('id');

				var html = '<div class="row post-comment-item"><div class="col-md-2"><a href="/post/personInfo'+id+'"><img src="'+avatar+'" alt=""></a></div><div class="col-md-10"><ul><li><a href="/post/personInfo'+id+'">'+nickname+'</a> 刚刚 发表</li><li><div class="comment-content"><blockquote cite=""><span>'+data.data.reply.replyUserName+' 发表于'+data.data.reply.replyAddTime+'</span>'+data.data.reply.replyContent+'</blockquote>'+content+'</div></li><li class="comment-handle" data-id="'+data.data.reply.id+'"><a href="javascript:void(0);" class="comment-reply" data-id="'+data.data.reply.id+'">回复</a> <a href="javascript:void(0);" class="comment-support">赞<span>(0)</span></a> <a href="javascript:void(0);" class="comment-oppose">踩<span>(0)</span></a><span><a href="javascript:void(0);" class="post-tip">举报<span>(0)</span></a></span></li></ul></div></div>';

				$('.post-pager').before(html);
			}else{
				win.alert(data.info, 'error');
			}
		}
	});
});
// 站内信查看详情
$('.user-section .message-list').on('click','.show-complete', function(){
	var node = $(this).parents('.message-thumb');
	node.hide();
	node.siblings('.message-complete').show();
})
$('.user-section .message-list').on('click','.hide-complete', function(){
	var node = $(this).parents('.message-complete');
	node.hide();
	node.siblings('.message-thumb').show();
})
// 站内信删除
$('.user-section .message-list').on('click', '.post-del', function(){
	var _this = $(this);
	var url = '/user/messageDel/id/'+_this.data('id');
	$.ajax({
		url:url,
		dataType:'json',
		type:'get',
		success:function(data){
			if(!data.error){
				win.alert(data.info, 'success');
				_this.parents('dd').remove();
			}else{
				win.alert(data.info, 'error');
			}
		}
	});
});
// 消息未读变已读
$('.user-section .message-list').on('click','.show-complete', function(){
	var unread = $(this).siblings('.icon-unread');
	if(!unread[0]){
		return;
	}
	var url = '/user/messageRead/id/'+$(this).data('id');
	$.ajax({
		url:url,
		dataType:'json',
		type:'get',
		success:function(data){
			if(!data.error){
				unread.remove();
			}
		}
	})
});
// 消息通知tab选中状态
(function(){
	if($('.user-section .tab-control')[0]){
		var url = window.location.href;
		var nav = $('.tab-control').find('a');
		for(var k=0;k<nav.length;k++){
			if(nav[k].href == url){
				nav[k].className+=' tab-actived';
			}
		}
	}
}());
// 站内信回复
var msgReply = '';
$('.user-section .message-list').on('click', '.msg-reply', function(){
	$('.win-reply-container').show();
	msgReply = $(this);
});
$('.win-msg-reply span').on('click', function(){
	$('.win-reply-container').hide();
});
$('.win-msg-reply button').on('click', function(){
	var content = $('.win-msg-reply textarea').val();
	if(!content){
		win.alert('请填写回复内容！', 'error');
		return false;
	}
	var user_id = msgReply.data('user-id');
	$.ajax({
		url:'/user/messageSiteNew',
		type:'post',
		dataType:'json',
		data:{content:content,user_id:user_id},
		success:function(data){
			if(!data.error){
				win.alert(data.info, 'success');
				$('.win-msg-reply textarea').val('');
				$('.win-reply-container').hide();
			}else{
				win.alert(data.info, 'error');
			}
		}
	});
});