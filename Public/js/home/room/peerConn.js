/**
 * 新建点对点连接
 * @param object config; 
 *	{	socket:,stream:, remoteVideoId:, iceServer:,
 *		callerSid:,
 *		calledSid:,
 *	}
 */
function peerConn(config){
	if(!config.socket){
		throw "config.socket needed!";
	}
	
	this.config = config;
	var socket = config.socket;
	var stream = config.stream;
	var remoteVideoId0 = '#myVideo2';
	var iceServer0 = {"iceServers": [{ "url": "stun:stun.iptel.org" }]};
	iceServer0 = {"iceServers": []};
	var options = {optional:[{'DtlsSrtpKeyAgreement': 'true'}]};
	var dataChannelName = 'dc';
	this.dc = null;
	
	this.pcList = [];
	//新建
	this.peerAdd = function(sid, iceServer){
		if(!iceServer) iceServer = iceServer0;
		console.log(iceServer);
		var pc = new RTCPeerConnection(iceServer0, options);
		this.pcList[sid] = pc;
		
		if(stream){
			pc.addStream(stream);
		}
		
		this.dc = pc.createDataChannel(dataChannelName, {});
		
		this.dc.onopen = function(){
			console.log('dc opened!');
		};
		this.dc.onmessage = function(e){
			console.log("dc Message:",e);
		};
		
		
		//协商发送
		pc.onnegotiationneeded = function(){
			
		};
		
		pc.onaddstream = function(event){
		  console.log('onaddstream');
		  var src = URL.createObjectURL(event.stream);
		  videoList.add(sid, src);
		  console.log(event.stream);
		};
		
		var isSend = false;
		pc.onicecandidate = function(event){
		  if(!event.candidate) return;
		  socket.send(JSON.stringify({
			sid:socket.sid,
			"event": "candidate",
			"candidate": event.candidate
		  }));
		  isSend = true;
		};
		return pc;
	}
	
	//呼叫
	this.call = function(sid){
		var pc = this.peerAdd(sid);
		//create 
		console.log('offer');
		pc.createOffer(function(desc){
			pc.setLocalDescription(desc);
			var data = JSON.stringify({
				event: "call", 
				caller:socket.sid,
				called:sid,
				sdp:desc
			});
			socket.send(data);
		},function(e){
			console.log(e)
		});
		
	}
	
	//响应呼叫信息
	this.responseCall = function(msg){	
		var calledSid = msg.called;
		var caller = msg.caller;
		if(calledSid != socket.sid) return;
		
		var pc = this.peerAdd(caller);
		pc.setRemoteDescription(new RTCSessionDescription(msg.sdp),function(){
			answer();
			console.log('setRemoteDescription success!');
		}, function(errorMsg){
			console.warn(errorMsg);
		});
		function answer(){
			pc.createAnswer(function(desc){
				pc.setLocalDescription(desc);
				var data = JSON.stringify({"event": "called","sdp": desc, called:calledSid, caller:caller});
				socket.send(data);
				console.log('called recive');
			}, function(desc){ console.warn(desc); });
		}
		return;
	}
	
	//响应被呼叫信息
	this.responseCalled = function(msg){
		if(socket.sid != msg.caller) return;
		var pc = this.getPc(msg.called);
		
		pc.setRemoteDescription(new RTCSessionDescription(msg.sdp),function(){
			console.log('setRemoteDescription success!');
		},function(errorMsg){
			console.warn(errorMsg);
		});
		
		return;
	}
	
	//响应 ice 信息
	this.responseCandidate = function(msg){
		if(msg.sid == socket.sid) return;
		
		var pc = this.getPc(msg.sid);
		pc.addIceCandidate(new RTCIceCandidate(msg.candidate),function(){
			console.log('addIceCandidate success', 'sid:'+msg.sid);
		},function(errorMsg){
			console.warn(errorMsg);
		});
	}
	
	this.getPc = function(sid){
		var pc = this.pcList[sid];
		if(!pc){
			throw "pc not exists!";
		}
		return pc;
	}
}