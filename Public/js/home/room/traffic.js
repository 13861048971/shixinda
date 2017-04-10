var liver = JSON.parse($('#liverData').val());
var user = JSON.parse($('#userData').val());
var socket = new WebSocket("wss://"+ document.domain +"/wss");
socket.onclose = function(){
	floatWin.alert('糟糕,聊天服务器断开了!','error');
	chatUsers.clear();
}
socket.onerror = function(){
	console.error('socket error!');
}
var peer = {}

if(true){
	navigator.getUserMedia({"audio": true, "video": true}, function(stream){
			q('#myVideo').src = window.URL.createObjectURL(stream);
			window.stream = stream;
			peer = new peerConn({socket:socket,stream:stream});
	}, function(error){ 
		console.warn("开启摄像头失败!", error);
		peer = new peerConn({socket:socket});
	});
}

//处理到来的信令
socket.onmessage = function(event){
	if(!event.data) return;
	var info = JSON.parse(event.data);
	var msg 	= info.msg;
	var type 	= info.type;
	if('pong' == type){
		win.hide();
		return;
	}
	console.log(event.data);
	var callback = {
		//初始化
		init:function(){
			var sid = msg.sid;
			var name= msg.username;
			socket.sid = sid;
			user.name = name;
			user.sid  = sid;
			chatUsers.add(msg, true);
			return;
		},
		online:function(){
			var users = msg.users;
			
			foreach(users, function(v){ 
				var name = v.username;
				if(v.sid !== socket.sid) 
					chatUsers.add(v); 
			});
			return ;
		},
		close:function(){
			chatUsers.remove(msg);
			return ;
		},
		
		//呼叫信息
		call:function(){ peer.responseCall(msg)},
		//被叫 相应
		called:function(){ peer.responseCalled(msg)},
		//被叫人
		candidate:function(){ peer.responseCandidate(msg)},
		//聊天数据处理
		chat:function(){
			if(msg.id && msg.user == user.name){
				$('#'+msg.id).removeClass('sending');
				return;
			}
			addChatContent(msg.content,msg.user);
		}
	};
	if(!callback[type])
		return false;
	callback[type]();
};


setInterval(function(){
	if(socket.OPEN == socket.readyState ){
		win.loading();
		socket.send(JSON.stringify({event:'ping'}));
		return;
	}
	console.log('socket closed');
}, 20000)
/**
 * 打开摄像头
 */
function openCamera(flag){
	if(false!==flag){
		flag = true;
	}
}



/**
 * 显示信息
 * @param object chatMsg;
 */
function sendChatMsg(chatMsg){
	if(!window.socket) return console.warn('socket lost!');

	socket.send(JSON.stringify(chatMsg));
}

/**
 * 添加聊天类容
 * @param string msg
 * @param string user 用户名称
 * @param bool isSending 是否是发送中 默认true
 *
 */
function addChatContent(msg, user, isSending){
	var isSendClass = '';
	if(isSending)
		isSendClass = ' class="sending" ';
	var d = new Date();
	var time = d.getHours() + ':' + d.getMinutes();
	var msg  = faceObj.parseContent(msg);
	var msgId = 'msg'+d.getTime();
	var str = '<li id="'+msgId+'" '+ isSendClass +'><i>'+ time +'</i><a href="javascript:void(0);">'+ user +'</a><span>'+ msg +'</span></li>';
	$('#chat .content').append(str);
	return msgId;
}

//点击发送聊天内容
$('#sendMsgBtn').on('click', function(){
	var input = $('#msgInput');
	var msg = $.trim(input.val());
	if(!msg) return input.focus();
	if(!user.name) return floatWin.alert('你还没有登录!', 'warn');
	
	var msgId   = addChatContent(msg, user.name, true);
	var chatMsg = {event:'chat',content:msg,user:user.name,id:msgId};
	sendChatMsg(chatMsg);
	input.val('');
});

//呼叫某人事件
$('#userList').on('click','li.isLiver' , function(e){
	if(user.isLiver) return;
	
	//openCamera();
	call(user.sid, liver.sid, liver.username);
	
	//呼叫某人
	/**
	 * @param int callerSid 呼叫人sid
	 * @param int calledSid 被呼叫人sid
	 * @param string name 被呼叫人名称
	 */
	function call(callerSid, calledSid, name){
		//var node = $('#calling');
		//node.find('label').text(name);
		//node.show();
		//console.log('call');
		peer.call(calledSid);
	}
});
//创建请求
var liveHandle = {
	btn:$('#live-handle a'),
	statusNode:$('.liver .base b'),
	on:function(){
		$.get('/Room/on',function(data){
			if(!data.error){
				liveHandle.btn.text('关闭').attr('class', 'on');
				liveHandle.statusNode.text('(正在直播)');
				liver.living = true;
				openCamera();
				return;
			}
			liveHandle.btn.text('开启').attr('class', 'off');
			floatWin.alert('开启直播失败!');
		},'json');
		
	},
	off:function(){
		$.get('/room/off',function(data){
			if(!data.error){
				liveHandle.btn.text('开启').attr('class', 'off');
				liveHandle.statusNode.text('(休息中)');
				liver.living = true;
				return;
			}
			liveHandle.btn.text('关闭').attr('class', 'on');
			floatWin.alert('关闭直播失败!');
		},'json');
	}
}
/**
 * 开启关闭直播事件
 */
$('#live-handle a').click(function(){
	if($(this).hasClass('off')){
		liveHandle.on();
		$(this).addClass('process');
		return;
	}
	if( $(this).hasClass('on')){
		liveHandle.off();
		$(this).addClass('process');
		return;
	}
});
if(user.isLiver)
$(window).on('beforeunload',function(){
	if(!liver.living) return;
	liveHandle.off();
	//return '关闭窗口或者刷新页面会关闭直播,你确定要关闭直播吗?';
});
