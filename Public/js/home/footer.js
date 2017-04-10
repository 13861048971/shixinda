//登陆窗口
(function(){
	var layIndex = 0;
	$('.tab')[0] ? tab($('.tab')) : null;
	var ele = $('#loginForm')[0].elements;
	var verimg = $('#registerForm .verimg');
	//自动登陆
	var autoLogin = {
		//保存登陆信息
		setInfo : function(){
			var d = getFormData($('#loginForm')[0]);
			if(d.username && d.password && d.rememberPwd){
				localStorage.setItem('username', d.username);
				localStorage.setItem('password', d.password);
				return true;
			}
		},
		//自动登陆动作
		toLogin:function(){
			var d = getFormData($('#loginForm')[0]);
			if(d.rememberPwd && d.autoLogin){
				$('#loginPost').click();
				return;
			}
		},
		
		//取登陆信息填充表单
		getInfo:function(){
			var d = getFormData($('#loginForm')[0]);
			var u = localStorage.getItem('username');
			var p = localStorage.getItem('password');
			
			if(d.rememberPwd && u && p){
				ele['username'].value = u;
				ele['password'].value = p;
				return true;
			}
			return false
		}
	}
	/**
	 * @param bool auto 是否自动登录
	 */
	function loginWin(auto){
		if(!$('#login')[0]) return;
		//初始换登陆窗口
		if(isLogin()) 
			return setLogin(1);
		var autoLoginMsgNode = $('#autoLoginMsg');
		autoLoginMsgNode.hide();
		$('#login').show();
		layIndex = layer.open({
			type:1,
			//closeBtn:false,
			title:false,
			area: 'auto',
			maxWidth:800,
			content:$('#login')
		});
		
		if(!auto) return;
		autoLoginMsgNode.show();
		var b = autoLoginMsgNode.find('b');
		
		//没有取得信息
		if(!autoLogin.getInfo()) return autoLoginMsgNode.hide() && false;
		
		//5秒后登陆
		var f = setInterval(function(){
			var i = parseInt(b.text());
			console.log(i);
			if(i > 1)
				return b.text(--i);

			clearInterval(f);
			if(!isLogin())
				autoLogin.toLogin();	
		}, 1000);
	}
	//登陆事件
	$('#loginPost').click(function(){
		var d = getFormData($('#loginForm')[0]);
		var btn = $(this);
		setDisabled(btn);
		$.post('/User/login', d,function(data){
			setDisabled(btn, false);
			var type = data.error ? 'error' : null; 
			floatWin.alert(data.info, type);
			if(!data.error){
				setLogin(1);
				autoLogin.setInfo();
				$('#loginHeader').html(data.content);
			}
		});
	});
	//注册事件
	$('#registerPost').click(function(){
		var d = getFormData($('#registerForm')[0]);
		var btn = $(this);
		setDisabled(btn);
		$.post('/User/register', d, function(data){
			setDisabled(btn, false);
			var type = data.error ? 'error' : null; 
			floatWin.alert(data.info, type);
			if(!data.error){
				$('#loginHeader').html(data.content);
				return setLogin(1);
			}
			
			reloadVer();
		});
	});
	
	$('#logoutBtn').click(function(){
		setLogin();
		$('.tab ul li:eq(0)').click();
		loginWin();
	});
	
	$('#registerBtn').click(function(){
		loginWin();
		$('.tab [target=regist-region]').click();
	});
	//显示注册
	$('#login .tab span').on('click', function(){
		var target = $(this).attr('target');
		if('regist-region' == target){
			verimg.attr('src', verimg.attr('url'));
			return;
		}
		verimg.attr('');
	});
	
	$('#loginBtn').click(function(){
		$('.tab [target=login-region]').click();
		loginWin();
	});
	
	//验证码更新
	function reloadVer(){
		$('#registerForm .vercode').val('');
		verimg.attr('src', verimg.attr('url') + '?t=' + (new Date).getTime());
	}
	verimg.on('click', function(){
		reloadVer();
	});
	
	/**
	  * 设置登陆状态
	  * @param bool flag 
 	  */
	function setLogin(flag){
		if(flag){
			layIndex ? layer.close(layIndex) : null;
			$('#loginBtn, #registerBtn').hide();
			$('#earingBtn').show();
			$('#logoutBtn').show();
			return ;
		}
		$('#loginBtn, #registerBtn').show();
		$('#earingBtn').hide();
		$('#logoutBtn').hide();
		console.log('--');
		setCookie('username', '', -1);
		setCookie('password', '', -1);
	}
	
	//loginWin(false);
})();

/**
 * 选项卡	
 * @param object jNode 选择标签节点
 */
function tab(jNode){
	jNode.find('li').click(function(){
		var i = $(this).index();
		$(this).addClass('cur').siblings().removeClass('cur');
		var li = jNode.find('.tabContent').hide();
		li.eq(i).show();
	});
	jNode.find('.tabContent:gt(0)').hide();
}