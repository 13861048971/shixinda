function makeFaces(id){
	var imgs = {"baidu_bear":["small_icon.png","airkiss.gif","angry.gif","beauty.gif","bow.gif","clap.gif","console.gif","cool.gif","corner.gif","cry.gif","dance.gif","dizzy.gif","groupdancing.gif","handshake.gif","hit.gif","hitthewall.gif","hopelessly.gif","interrogative.gif","Intimacy.gif","kiss.gif","longing.gif","laughter.gif","love.gif","lovely.gif","malicious.gif","money-obsessed.gif","shyness.gif","sleep.gif","smart.gif","smile.gif","stickout.gif","superman.gif","surprise.gif","sweat.gif","TAT.gif","thinking.gif","tired.gif","wronged.gif"],"bo":["small_icon.png","cry.gif","applause.gif","shock.gif","bling.gif","lightning.gif","what.gif","despair.gif","stiff.gif","dance.gif","hi.gif","crash.gif","proud.gif","404.gif","buy.gif","TAT.gif","yeah.gif","bath.gif","flower.gif"],"weeklystar":["small_icon.png","222.gif","221.gif","220.gif","219.gif","218.gif","217.gif","216.gif","215.gif","image001.gif","image002.gif","image005.gif","image007.gif","image010.gif","image012.gif","image014.gif","image016.gif","214.gif","213.gif","212.gif","211.gif"],"lengtu":["small_icon.png","lengtu1.gif","lengtu2.gif","lengtu3.gif","lengtu4.gif","lengtu5.gif","lengtu6.gif","lengtu7.gif","lengtu8.gif","lengtu9.gif","lengtu10.gif","lengtu11.gif","lengtu12.gif","lengtu13.gif","lengtu14.gif","lengtu15.gif","lengtu16.gif","lengtu17.gif","lengtu18.gif","lengtu19.gif","lengtu20.gif","lengtu21.gif","lengtu22.gif","lengtu23.gif","lengtu24.gif","lengtu25.gif","lengtu26.gif","lengtu27.gif","lengtu28.gif","lengtu29.gif","lengtu30.gif","lengtu31.gif","lengtu32.gif","lengtu33.gif","lengtu34.gif","lengtu35.gif","lengtu36.gif","lengtu37.gif","lengtu38.gif","lengtu39.gif","lengtu40.gif"],"boy_girl_uncle":["small_icon.png","boy_face.gif","boy_halo.gif","boy_kiss.gif","boy_sweat.gif","girl_bi.gif","girl_body.gif","girl_cry.gif","girl_hurry.gif","girl_kiss.gif","girl_sword.gif","girl_up.gif","girl_whip.gif","girl_fart.gif","uncle_flower.gif","uncle_good.gif","uncle_hit.gif","uncle_love.gif","uncle_sign.gif","uncle_yeah.gif"]};
	
	var style = '<style>'+
			'#faces{width:400px; position:absolute;background:#fff;box-shadow:0 0 4px rgba(127,127,127,.7); top:-328px; left:5px;display:none;}' +
			'#faces .tab{height:30px; border-bottom:1px solid #d7d7d7; background:#f8f8f8;}'+
			'#faces .tab span{width:50px;height:30px;border-right:1px solid #d7d7d7;background:#f8f8f8; float:left; text-align:center; display:block;}'+
			'#faces .tab .cur{background:#fff;padding:0 0 1px 0;}'+
			'#faces .tab span img{max-width:50px;max-height:29px;vertical-align:middle;}'+
			'#faces li{width:40px; height:40px; float:left; padding:2px; border:1px solid #fff;}' +
			'#faces li img{width:40px; height:40px;cursor:pointer; }' +
			'#faces li:hover{border:1px solid #ff4d88;}' +
			'#faces ul{height:270px;overflow-y:auto;padding:15px;}' +
			'#faces em{position:absolute;height:0;border:7px solid #f8f8f8;border-top:#ccc 7px solid;left:7px;border-bottom:none;}' +
			'#faces em i{position: absolute;height: 0;border: 3px solid #ccc;border-bottom: none;border-top: 4px solid #fff;left: -3px;bottom:3px;}'+
	'</style>';
	
	var facesNode;
	var imgPre = 'Public/css/faces/';
	var _this = this;
	
	//解析表情文本为图文混排
	this.parseContent = function(str){
		
		var arr = str.match(/\[\d+\:\d+\]/g);
		if(!arr) return str;
		foreach(arr, function(v){
			var img = _this.parse(v);
			if(!img) return;
			
			str = str.replace(v, img);
		});
		return str;
	}
	
	//编码图片为文本
	this.encode = function(jImg){
		var no = jImg.parent().attr('no');
		return '['+ no +']';
	}
	
	
	init();
	
	//解析faces代码为图片
	this.parse = function(faceCode){
		var i = 0;
		var imgStr = '';
		foreach(imgs, function(v, k){
			 i++;var j=0;
			foreach(v, function(v2, k2){
				j++;
				var no = '['+ i +':' + j + ']';
				if(no != faceCode) return;
				var imgName = imgPre + k + '-' + v2;
				imgStr = '<img class="faceImg" src="' + imgName+'" />';
				return false;
			});
			if(imgStr) return false;
		});
		return imgStr;
	};
	
	function init(){
		if($('#faces')[0]) return;
		var html = style + '<div id="faces">'+ createStr() +'<em><i></i></em></div>';
		$(id).html(html);
		facesNode = $('#faces')
		eventBind();
	}
	
	function createStr(){
		var tab = '<div class="tab">';
		var faceList = '';
		var i = 0;
		foreach(imgs, function(v, k){
			var j=0;
			i++;
			var idName = k + '_faces';
			var tabC = '';
			var ulC = 'class="hide"';
			if('baidu_bear' == k){
				tabC = 'class="cur"';
				ulC = '';
			}
			tab += '<span '+tabC+' target="'+ idName +'"><img src="'+imgPre+ k+'-'+v[0] +'" /></span>';
			
			faceList += '<ul '+ ulC +' id='+ idName +'>';
			foreach(v, function(v2, k2){
				j++;
				if(k2<1) return;
				var imgName = imgPre + k + '-' + v2;
				var no = i+':'+j;
				faceList += '<li no='+ no +'><img src="'+ imgName +'"></li>';
			});
			faceList += '</ul>';
		});
		tab += '</div>';
		return tab+faceList;
	}
	
	function eventBind(){
		//点击表情图片
		facesNode.on('click', 'ul img',function(){
			var msgInp = $('#msgInput'); 
			var no = '[' + $(this).parent().attr('no') +']';
			msgInp.val(msgInp.val()+ no);
			facesNode.hide();
		});
		//显示表情弹窗
		$(document).on('click',function(e){
			if($(e.target).hasClass('face')) {
				if(facesNode.is(':visible')) 
					return facesNode.hide();
				facesNode.show();
				return;
			}
			if(!facesNode.is(':visible')) return;
			if(facesNode.find($(e.target))[0]) return;
			facesNode.hide();
		});
	}
}
var faceObj = new makeFaces('#faceSec');

//视屏列表方法
var videoList = {
	node : $('#myVideos'),
	bigNode : $('#myVideos .big-video'),
	list : $('#myVideos .video-li'), 
	add:function(sid, src){
		var bigVideo = this.bigNode.find('video');
		var v = $('<video>');
		v.attr('id', 'video' + sid);
		v.attr('src', src);
		v.attr('autoplay', true);
		if(!bigVideo[0]){
			this.bigNode.append(v);
			return v;
		}
		this.list.append(v);
		return v;
	},
	init:function(){
		//点击视频事件
		this.node.on('click', 'video',function(){
			var bigVideo = videoList.bigNode.find('video');
			console.log(bigVideo);
			if(bigVideo[0]){
				videoList.list.prepend(bigVideo);
				bigVideo[0].play();
				return;
			}
			videoList.bigNode.append($(this));
			this.play();
		});
		
		//移动
		this.node.on('click', 'li', function(){
			var list = videoList.list;
			if($(this).hasClass('left')){
				var one = list.find('video:first');
				list.append(one);
			}
			
			if($(this).hasClass('right')){
				var one = list.find('video:last');
				list.prepend(one);
			}
		});
		
		this.node.on('mouseover','ul',function(){
			var list = videoList.list;
			var len = list.find('video').length;
			if(len > 3) {
				list.siblings().show();
				return;
			}
			list.siblings().hide();
		});
		
		this.node.on('mouseout','ul',function(){
			videoList.list.siblings().hide();
		});
	}
};

videoList.init();