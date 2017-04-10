// var socket = new WebSocket("ws://"+document.domain+":8080");
var back = document.getElementById('output');
var backcontext = back.getContext('2d');
var video = document.getElementsByTagName('video')[0];
		var image = new Image();
		var canvas = document.getElementById('output2');
		var canText = canvas.getContext('2d');
		
var success = function(stream){
	video.src = window.URL.createObjectURL(stream);
				window.stream = stream;
}

//socket.onopen = function(){ draw(); }

var draw = function(){
	try{
		backcontext.drawImage(video,0,0, back.width, back.height);
	}catch(e){
		if (e.name == "NS_ERROR_NOT_AVAILABLE") {
			return setTimeout(draw, 100);
		} else {
			throw e;
		}
	}
	if(video.src){
					//image.src=back.toDataURL("image/jpeg", 0.5);
					//canText.drawImage(image, 0, 0);
					//console.log(image.src);
					//canvas.getContext("2d").drawImage(image,0,0);
	  //socket.send(back.toDataURL("image/jpeg", 0.5));
	}
	setTimeout(draw, 100);
}
navigator.getUserMedia = navigator.getUserMedia || navigator.webkitGetUserMedia ||
navigator.mozGetUserMedia || navigator.msGetUserMedia;
navigator.getUserMedia({video:true, audio:false}, success, console.log);
draw();