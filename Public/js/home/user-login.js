/**
 * 用户登录
 */
(function userLogin(){
	//注册登录成功跳转
	$('#login-region form, #regist-region form').on('ajaxCallback', function(e, info){
		var th = $(this);
		var isRegist = th.parents('#region-region');
		
		if(info.status > 0){
			if(isRegist){
				$('#regist-success').show();
				return;
			}
			
			var url = info.data.redirect;
			!url && (url = '/User');
			location.href = url;
		}
	});
	
	
	//数秒
	function countSec(node){
		var str = node.text();
		var sec = 60;
		node.prop('disabled', true);
		var t = setInterval(function(){
			node.text(sec + '秒获取');
			sec--;
			if(sec < 1){
				node.text(str);
				clearInterval(t);
			}
		}, 1000);
		
	}
	//获取验证码
	$('.getvercode').on('click', function(){
		var th = $(this);
		var tel = $('#registerForm [name=tel]').val();
		var codeNode = th.prev().find('input');
		if(!isMobile(tel)){
			return floatWin.alert('手机号码格式错误!');
		}
		var url = '/User/getvercode?tel='+tel;
		codeNode.val('');
		console.log(url);
		$.get(url, function(info){
			if(info.status < 1){
				return floatWin.alert(info.info);
			}
			countSec(th);
			
		}, 'json');
	});
})();

