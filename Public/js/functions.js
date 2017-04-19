function q(s,p){ return (p || document).querySelector(s); }
function qA(s,p){ return (p || document).querySelectorAll(s); }
function foreach(arr,func){
	var re = true;
	if(!arr) return;
	for(var i in arr){ if(false===func(arr[i], i)) return false; }
	return re;
}
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

//判断变量是否申明
function isDefined(a){
	if('undefined' == typeof a)
		return false;
	return true;
}

//整除
function Div(exp1, exp2) {
	var n1 = Math.round(exp1); //四舍五入   
	var n2 = Math.round(exp2); //四舍五入  
	var rslt = n1 / n2; //除  

	if (rslt >= 0)
		return Math.floor(rslt); //返回小于等于原rslt的最大整数。   

	return Math.ceil(rslt); //返回大于等于原rslt的最小整数。   
}

/**
 * 日志方法
 */
function log(str){
	var logNode = '<style>' +
			'#logNode{border:1px solid #ccc; position:fixed; bottom:0px;height:40px; overflow-y:auto;'+
			  'width:994px;height:90px;background:#fff;padding:2px;bottom:1px;}'+
			'#logNode.hidden{width:40px;height:23px;overflow:hidden;position:static;'+
			  'float:right;}'+
			'#logNode span{padding-right:10px;}' + 
			'#logNode .close{float:right; padding:2px 2px;cursor:pointer;}'+
		'</style>'+
		'<div id="logNode" class="hidden"><b class="close">logbar</b><input /></div>';
	if(!$('#logNode')[0]){
		$('body').append(logNode);
		$('#logNode input').keyup(function(e){
			//回车符
			if(13 == e.keyCode){
				var re = eval($(this).val());
				log( 'eval:"'+ $(this).val() +'" :' + re);
				$(this).val('');
			}
		});
		$('#logNode .close').click(function(){
			var p = $(this).parent();
			if(p.hasClass('hidden')){
				p.removeClass('hidden');
				return;
			}		
			p.addClass('hidden');
		});
	}
	
	var node = $('#logNode');
	node.append('<span>' + str + '</span>');
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

/**
 * 设置jQuery 对象位置, 默认居中
 */
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
 * 节点移动动画
 * @param  	jNode			jqueryObject
 * @param	target_jNode 	jqueryObject
 * @param 	callback		function  
 * */
function moveTo (jNode, target_jNode, callback){	
	$('body').append(jNode.clone().addClass("new-clone").css({
		"position":"absolute","left":jNode.offset().left,"top":jNode.offset().top,"z-index":999,
		"height":jNode.height(),"width":jNode.width()
	}));
	$("body>.new-clone").animate({
		left:target_jNode.offset().left, 
		top:target_jNode.offset().top, 
		"height":jNode.height()*0.1, 
		"width":jNode.width()*0.1
	}, 1000 ,function(){
		$("body>.new-clone").remove();
		if( 'function' == typeof callback)
		callback();
	});	
}

/**
 * 滚动到
 * @param object jNode
 * @param object scrollNode 要滚动的jquery 对象
 * @param int  addHeight 附加的高度
 **/
function scrollTo2(jNode, scrollNode, addHeight){
	!addHeight && (addHeight = 0);
	if(!scrollNode) scrollNode = $('body,html');
	var top = jNode.offset().top - 10 + addHeight; 
	scrollNode.animate({scrollTop: top},500);
}

/**
 * 获取客户端cookie
 * @param 	string name
 * @return 	string
 */
function getCookie(name){
  var str=document.cookie.split("; ")
  for(var i=0;i<str.length;i++){
	var str2=str[i].split("=");
	if(str2[0] == name){
		var v  = unescape(str2[1]);
		if('deleted' == v || 'null' == v)
			return null;
		return v;
	}
  }
}

/**
 * 设置cookie
 * @param	string	name
 * @param	string	value
 * @param	int		expSec -过期秒数
 */
function setCookie(name, value, expSec, path){
	!path && (path = '/');
	if(!Number(expSec)){
		var Days = 30; //此 cookie 将被保存 30 天 
		var expSec = Days*24*60*60;
	}
	var exp  = new Date();
	exp.setTime(exp.getTime() + expSec*1000); 
	document.cookie = name + "="+ escape(value) + 
		";expires="+ exp.toGMTString() + ';path=' + path; 
}

/**
 * 换行符换成br
 */
function nl2br(str){
	if(str = String(str))
		return str.replace('\n','<br/>');
}

//轮询函数，返回false 停止
function poll(func, msec){
	if(!msec) msec = 5000;
	var flag = true;
	var n = 1;
	var t = setInterval(function(){
		if(flag)
			flag = func();
		else
			clearInterval(t);
	}, msec); 
}

function isInt(str) {
	patten = /^-?[1-9]\d*$/;
	if (null == str.match(patten)) {
		return false;
	} else {
		return true;
	}
}

function isDecimal(str) {
	patten = /^-?\d+(?:\.\d+)?$/;
	if (null == str.match(patten)) {
		return false;
	} else {
		return true;
	}
}
/**
 * 数组排序
 */
function objSort(obj, desc){
	var keyArr = [], obj2 = {};
	for(var x in obj){
		keyArr.push(x);
	}
	var n = 0;
	keyArr.sort(function(a,b){
		if(desc)
			return a < b ? 1 : -1;
		 return a>b ? 1 : -1;
	});
	
	for(var i=0,l=keyArr.length; i< l; i++ )
		obj2[keyArr[i]] = obj[keyArr[i]];

	delete obj;
	return obj2;
}

/**
 *
 * 查找元素
 */
function findHtml(str, find, tag){
	var i = str.search(find);
	if(i < 0) return false;
	
	var str1 = str.substr(0, i);
	var str2 = str.substr(i);
	var tag1 = '<' + tag;
	var tag2 = '</' + tag + '>';
	
	var n = 1;
	var j = j1 = j2 = t = 0;
	
	var m = 0;
	var max = 1000;
	
	//查找初始标签
	while(-1 != (t = str1.indexOf(tag1, j))){
		j = t + tag1.length;
	}
	t = 0;
	//查找闭合标签
	while(n>0){
		t = str2.indexOf(tag2, j2);
		if( t>-1 ){
			j2 = t + tag2.length;
			n--;
		}
		
		t = str2.substring(j1,j2).indexOf(tag1, j1);
		if(t > -1){
			 j1 = t + tag1.length;
			 n++;
		}
		
		m++;
		if(m > max) break;
	}
	
	str2 = str.substring(j-tag1.length, i+j2);
	if(-1 != str.indexOf('<meta charset="gbk" />')){
		str2 = utf8(str2);
	}
	return str2;
}

/**
 * 获取表单中所有表单元素值
 * @param	HTMLFormElement form
 * @return	Object 
 * @author	wind
 * @date	2014-3-24
 */
function getFormData(form){
	var eles = form.elements;
	var data = {};
	for(var x=0;x < eles.length; x++){ 
		if(eles[x].disabled)
			continue;
		if(eles[x].type == 'radio' || eles[x].type == 'checkbox'){
			eles[x].checked && (data[eles[x].name] = eles[x].value);
		}else if(eles[x].type == 'select'){
			eles[x].selected && (data[eles[x].name] = eles[x].value);
		}else{
			eles[x].name && (data[eles[x].name] = $(eles[x]).val());
		}
	}
	return data;
}

/**
 * 设置为禁用
 * @param jNode button 
 * @param boolean v 是否设置为禁用
 */
function setDisabled(button, v){
	//启用
	if(false === v){
		return button.removeClass('active').prop('disabled', false);
	}
	return button.addClass('active').prop('disabled', true);
}

/**
 * 滚动到节点
 * @param jNode node
 * @param int speed
 * @param function callBack
 */
function scrollToNode(node, speed, callBack){
	if(!node[0]) return;
	!speed ? speed = 1000 : null
	var top = node.offset().top;
	top>150 ? top = top - 100 : null;
	$('body').animate({scrollTop:top}, speed, callBack);
}
/**
 * 随机挑选
 * @param int max 随机数范围
 * @param int m 挑选的个数
 * @param int value 排除的值
 * @return array arr2 
 */
function randSelect(max, m, value, repeat){
	var i = n = 0;
	var arr = [],arr2 = [];
	m > max ? m = max : null;
	for(i=0;i < max;i++){
		if(value && i == value-1)
			continue;
		
		arr[i] = i+1;
	}
	for(i=0;i < m;i++){
		n = Math.floor(Math.random() * arr.length + 1)-1;
		arr2[i] = arr[n];
		if(!repeat){
			arr.splice(n,1);
		}
	}
	return arr2;
}

/**
 * 随机数
 */
function randomNum(Min,Max){
	Min = parseInt(Min);
	Max = parseInt(Max);
	var Range = Max - Min;
	var Rand = Math.random();  
	return(Min + Math.floor(Math.round(Rand * Range) + 1)-1);  
}

//转码
 function utf8(wide){
	 var c, s;
	 var enc = "";
	 var i = 0;
	 while(i<wide.length) {
		 c= wide.charCodeAt(i++);
		 // handle UTF-16 surrogates
		 if (c>=0xDC00 && c<0xE000) continue;
		 if (c>=0xD800 && c<0xDC00) {
			 if (i>=wide.length) continue;
			 s= wide.charCodeAt(i++);
			 if (s<0xDC00 || c>=0xDE00) continue;
			 c= ((c-0xD800)<<10)+(s-0xDC00)+0x10000;
		 }
		 // output value
		 if (c<0x80) enc += String.fromCharCode(c);
		 else if (c<0x800) enc += String.fromCharCode(0xC0+(c>>6),0x80+(c&0x3F));
		 else if (c<0x10000) enc += String.fromCharCode(0xE0+(c>>12),0x80+(c>>6&0x3F),0x80+(c&0x3F));
		 else enc += String.fromCharCode(0xF0+(c>>18),0x80+(c>>12&0x3F),0x80+(c>>6&0x3F),0x80+(c&0x3F));
	 }
	 return enc;
 }
 
 var hexchars = "0123456789ABCDEF";
 function toHex(n) {
	return hexchars.charAt(n>>4)+hexchars.charAt(n & 0xF);
 }
 
 var okURIchars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789_-";
 function encodeURIComponentNew(s) {
	 var s = utf8(s);
	 var c;
	 var enc = "";
	 for (var i= 0; i<s.length; i++) {
	 if (okURIchars.indexOf(s.charAt(i))==-1)
	 enc += "%"+toHex(s.charCodeAt(i));
	 else
	 enc += s.charAt(i);
	 }
	 return enc;
 }

//js实现的get
function jsGet(url, callBack){
	var callbackName = 'jsonp' + time() + randomNum(1000,9999);
	url += '&callback=' + callbackName;
	window[callbackName] = function(data){
		callBack(data);
		delete window[callbackName];
	};
	if('undefined' == typeof httpGet){
		$.ajax({
			url: '/api/proxy?url=' + encodeURIComponent(url), 
			dataType: 'jsonp',
			jsonp: callbackName,
			error:function(){ log('jsGet Error'); }
		});
		return ;
	}
	var row = 'Accept:text/javascript, application/javascript, application/ecmascript, application/x-ecmascript, */*; q=0.01';
	httpGet(url, row, callbackName);
}
 
/**
 * 渲染表单ajax 提交
 */
function ajaxSubmit(jForm){
	if(!jForm[0]) return;
	var targetContainer = jForm.attr('targetContainer');
	jForm[0].ajaxSubmitBinded = true;
	jForm.submit(function(){
		var submit = $(this).find(':submit');
		var form = $(this);
		var data = getFormData(this);
		var type = $(this).attr('method') == 'post' ? 'post' : 'get';
		var url = $(this).attr('url');
		!url ? url = $(this).attr('action') : null;
		$.ajax({
			url : url,
			type: type,
			data: data,
			dataType:'json',
			error:function(data, r){
				submit.prop('disabled', false);
				floatWin.alert(data.status+' '+data.statusText);
			},
			success:function(data){
				submit.prop('disabled', false);
				//如果绑定了ajaxCallback事件，促发
				form.trigger('ajaxCallback', data);
				//如果设置 targetContainer 属性
				if($(targetContainer).get(0) && isDefined(data.content)){
					var c = $(data.content);
					var n = c.find('.ajaxLoad');
					if(n.get(0)) ajaxLoad(n);
					var b = $(targetContainer).html(c);
				}
				
				var type = 'error';
				if(data.status || data.error == 0)
					type = 'success';
				if(data.info)
					floatWin.alert(data.info, type);
			}
		});
		submit.prop('disabled', true);
		return false;
	});
}

/**
 * a标签点击改为ajax 载入
 * @attr string url||href  ajax 		载入地址
 * @attr String targetContainer 载入后内容填充到的节点“选择器名称”
 * @param jObject jNode	a标签的父类
 * @param string 		 节点名称
 */
function ajaxLoad(jNode, nodeName){
	if(!nodeName) nodeName = 'a';
	var targetContainer = jNode.attr('targetContainer');
	
	jNode.find(nodeName).click(function(){
		var node = $(this);
		var type = node.attr('type') == 'post' ? 'post' : 'get';
		var url  = node.attr('href');
		var data =  
		$.ajax({
			url : url,
			type: type,
			data: data,
			dataType:'json',
			success:function(data){
				node.removeClass('disabled');
				//如果绑定了ajaxCallback事件，促发
				node.trigger('ajaxCallback', data);
				//如果设置 targetContainer 属性
				if($(targetContainer).get(0) && isDefined(data.content)){
					var c = $(data.content)
					var n = c.find('.ajaxLoad');
					if(n.get(0))
						ajaxLoad(n);
					$(targetContainer).html(c);
					scrollTo($(targetContainer));
				}
				
				var type = 'error';
				if(data.status)
					type = 'success';
				if(data.info)
					floatWin.alert(data.info, type);
			}
		});
		node.addClass('disabled');
		return false;
	});
};

/**
 * ajax 删除
 * @param string selector   
 */
 function ajaxDel(selector){
	$(document).on('click', selector,function(){
	  var url = $(this).attr('url');
	  var row = $(this).parents('tr');
	  !row[0] && (row = $(this).parents('.tr'));
	  console.log(row);
	  var del = function(){
		  $.ajax({
			url:url,
			dataType:'json',
			success:function(info){
				if(info.error){
					if(info.info)
						return floatWin.alert(info.info);
					return floatWin.alert('删除失败!','error');
				}
				row.addClass('deleted');
			}
		  })
	  };
	  var t = layer.confirm("确定要删除吗?",function(){  
		del(); layer.close(t);
	  });
	  
	  return false;
	});
 }
 
/**
 * 新窗口中打开
 */
function openInNewWindow(url, title) {
  (url.indexOf('http') !== 0) && (url = location.origin + url); 
  
  var html = $('html')[0];
  var left = html.clientWidth*0.1;
  var top = html.clientHeight*0.1;
  var width = html.clientWidth*0.8 + 'px';
  var height = html.clientHeight*0.8 + 'px';
  !title && (title = '新的窗口');
  
  var body = $('body');
  
  if(url.match(/(.png)|(.jpg)|(.gif)|(.jpeg)/)){
	
	!body.find('#imgview-node')[0] && body.append('<img id="imgview-node">');
	var imgviewNode = $('#imgview-node');
	imgviewNode.attr('src', url);
	layer.open({
	  type: 1,
	  title: false,
	  closeBtn: 1,
	  area: 'auto',
	  maxWidth:800,
	  skin: 'layui-layer-nobg', //没有背景色
	  shadeClose: true,
	  content: imgviewNode
	});
	return;
  }
  
	layer.open({
	  title: title,
	  type: 2,
	  area: [width, height],
	  fix: false, //不固定
	  maxmin: true,
	  content: url
	});
	return;
  
  var newWin = window.open(url, 'filePreview', 'height='+ height +',width='+ width +',top='+ top +',left='+ left +';toolbar=0,scrollbars=1,location=yes,titlebar=0,statusbar=1,menubar=0,resizable=1');
  newWin.document = title;
}

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
		var edit = KindEditor.create(node[0], {width:"100%",height:350,afterChange:function(){
			if(!edit) return;
			edit.sync();
		}});
	}, 200);
}

/**
 * 相册类
 * @param jNode node
 *
 */
function album(node, inputName){
	!inputName && (inputName = 'image');
	var inputs = node.find('input.image');
	var addBtn = $('<a class="btn btn-default">+</a>');
	var progressNode = $('<li class="progress">0%</li>')
	var fileBtn = $('<input type="file" name="file" class="hidden" />');
	var imgDefalt = $('<input type="hidden" name="image-default" value="0" />');
	var len = node.find('li').length;
	var max = parseInt(node.attr('max')); 				//最多上传的张数
	var single = node.attr('single') ? true : false;
	var imageName = node.attr('image-name');
	!imageName && (imageName = 'image');
	(max < 1) && (max = 20);
	single && node.addClass('single');
	imgDefalt.val(node.find('li.default').attr('rank'));
	node.append(progressNode.hide());
	node.append(addBtn);
	node.append(imgDefalt);
	$('body').prepend(fileBtn);

	fileBtn.change(function(){ upload(this.files[0]); });
	addBtn.click(function(){
		if(node.find('li').length > max) 
			return floatWin.alert('只能上传'+ max +'张图片!');
		
		fileBtn.trigger('click'); 
	});
	/**
	 * 上传
	 * @param file file
	 */
	function upload(file){
		var data = new FormData();
		var read = new FileReader();
		data.append(fileBtn[0].name, fileBtn[0].files[0]);
		data.append('type', node.attr('type'));
		if(single){
			node.find('.img-thumbnail').remove();
		}
		progressNode.show().html('0%');
		send(data);
	}
	
	/**
	 * 发送
	 * @param FormData data
 	 */
	function send(data){
		$.ajax({
			url:'/file/upload',
			type:'post',
			data:data,
			dataType: 'JSON',  
			cache: false,
			xhr: function(){
				var xhr = new window.XMLHttpRequest();
				xhr.upload.addEventListener("progress", function(evt){
				  if (!evt.lengthComputable) return;
				  var percentComplete = parseFloat(evt.loaded * 100/ evt.total).toFixed(2);
				  progressNode.html(percentComplete + '%');
				}, false);
				return xhr;
			},
			processData: false,  
			contentType: false,
			complete : function(){ progressNode.hide(); },
			success:function(info){
				var src = info.src;
				if(!src) return console.warn('need src!');
				add(src, info.thumb);
			}
		});
	}
	
	//添加
	function add(src, thumb){
		!thumb && (thumb = src);
		if(single){
			var li = $('<li class="img-thumbnail"><img src="'+ thumb +'">'+
			'<input type="hidden" name="'+ imageName +'" value="'+ src +'">');
			progressNode.before(li);return;
		}
		
		var li = $('<li class="img-thumbnail" rank="'+ len +'"><img src="'+ thumb +'">'+
			'<input type="hidden" name="'+ imageName +'[0]['+ len +']" value="'+ src +'">'+
			'<span class="del"></span><span class="set-default">设为默认</span></li>');
		
		!node.find('.default')[0] && li.addClass('default') && imgDefalt.val(len);
		progressNode.before(li);
		len++;
	}
	
	//删除
	node.on('click', '.del', function(){
		var li = $(this).parent('li');
		var t = layer.confirm('你确定要删除吗?',function(){
			if(li.hasClass('default')){
				var lisib = li.siblings('li')[0];
				lisib && $(lisib).addClass('default') && imgDefalt.val($(lisib).attr('rank'));
			}
			li.remove();
			layer.close(t);
		});
	});
	//设置为默认
	node.on('click', '.set-default', function(){
		var li = $(this).parent('li');
		li.addClass('default').siblings().removeClass('default');
		imgDefalt.val(li.attr('rank'));
	});
}

/**
 * 上传文件
 * @param string btnSelector
 **/
function uploadFile(btnSelector){
	var btn;
	var options;
	var progressNode = $('<span class="process"></span>');
	var fileBtn = $('<input type="file" name="file" class="hidden" />');
	$('body').append(fileBtn);
	
	var input, preview;
	
	fileBtn.change(function(){ 
		if(!this.files[0]) return;
		upload(this.name, this.files[0]); 
	});
	
	$(document).on('click', btnSelector, function(){
		btn = $(this);
		options = eval('('+ btn.attr('data-option') + ')');
		input = $(options.urlContainer);
		preview = $(options.preview);
		if(!preview.parent().find('.process')[0]){
			preview.parent().prepend(progressNode.hide());
		}
		fileBtn.trigger('click'); 
	});
	
	/**
	 * 上传成功
	 */
	function add(src, thumb){
		input[0] && input.val(src);
		preview[0] && preview.attr('src', src) && preview.show();
	}
	
	/**
	 * 上传
	 * @param file file
	 */
	function upload(name, file){
		var data = new FormData();
		var read = new FileReader();
		data.append(name, file);
		data.append('type', options.type);
		progressNode.width(0).show().html('0%');
		preview.hide();
		$.ajax({
			url:'/file/upload',
			type:'post',
			data:data,
			dataType: 'JSON',  
			cache: false,
			xhr: function(){
				var xhr = new window.XMLHttpRequest();
				xhr.upload.addEventListener("progress", function(evt){
				  if (!evt.lengthComputable) return;
				  var percentComplete = parseFloat(evt.loaded * 100/ evt.total).toFixed(0);
				  progressNode.html(percentComplete + '%');
				  progressNode.width(percentComplete);
				}, false);
				return xhr;
			},
			processData: false,  
			contentType: false,
			complete : function(){ progressNode.hide(); },
			success:function(info){
				var src = info.src;
				if(!src) return console.warn('need src!');
				add(src, info.thumb);
			}
		});
	}
}

/**
 * 选择地区
 * @param 
 */
function selectRegion(){
	$(document).on('change', '.select-region select', function(){
		var node = $(this);
		var pid = node.val();
		var i   = node.index();
		if(pid < 1 || i > 2) return;

		$.ajax({
			url:'/index/getRegion?pid=' + pid,
			dataType:'json',
			error:function(x,t,e){ console.log(e); },
			success:function(info){
				if(!info.error)
					addData(node, info.data);
			}
		});
		node.siblings('select').each(function(){
			var i2 = $(this).index();
			if(i2 > i)
				$(this).html('');
		});
	});
	
	$(document).on('click', '.select-region .add-region', function(){
		var selects = $('.select-region select');
		var list = selects.find('option:selected');
		addSelected(list);
	});
	/**
	 * 添加选中项
	 */
	function addSelected(list){
		var node = $('.select-region .selected-region');
		var checkboxs = node.find(':checkbox');
		var ids = [];
		checkboxs.each(function(){
			ids.push(this.value);
		});
		
		var str = '';
		list.each(function(){
			var v = this.value;
			if(ids.indexOf(v) > -1) return;
			str += '<label class="checkbox-inline"><input name="region_ids['+v+']" value="'+ v +'" checked type="checkbox">'+ $(this).text() +'</label>'
		});
		node.append(str);
	}
	/**
	 * @param jNode selectNode
	 * @param array list
	 */
	function addData(selectNode, list){
		var optionStr = '<option value="">-选择-</option>';
		for(var i=0,l=list.length; i<l;i++){
			optionStr += '<option value="' + list[i].id + '">' +
				list[i].region_name + '</option>';
		}
		selectNode.next('select').html(optionStr);
	}
}

/**
 * 标记关键词
 * @param string keywords
 * @param string selector 选择器
 */
function markKeywords(keywords, selector){
	$(selector).each(function(){
		var str = $(this).html();
		str = str.replace(keywords, '<b class="mark">'+keywords+'</b>');
		$(this).html(str);
	});
}

/**
 * 自定义number控件
 * @param string
 */
function numberCtr(selector){
	var ctr = $(selector);

	ctr.on('click', '.plus', function(){
		var input = $(this).parent().find('input');
		var v = parseInt(input.val());
		var min = parseInt(input.attr('min'));
		if(typeof min != 'undefined' && v==min)
			return false;
		input.val(v-1);
		input.change();
		return true;
	});
	
	ctr.on('click', '.add',function(){
		var input = $(this).parent().find('input');
		var v = parseInt(input.val());
		var max = parseInt(input.attr('max'));
		if(typeof max != 'undefined' && v==max){
			floatWin.alert('超出最大值!');
			return false;
		}
		input.val(v+1);
		input.change();
		return true;
	});
	
	ctr.on('blur', 'input', function(){
		var th = $(this);
		var min = parseInt(th.attr('min'));
		var max = parseInt(th.attr('max'));
		
		if(th.val() < min){
			th.val(min);
			th.change();
			return
		} 
		
		if(th.val() > max){
			floatWin.alert('超出最大值!');
			th.val(max);
			th.change();
			return;
		} 
	});
}

//自定义的comfirm 弹窗
var mycomfirmBinded = false;
function mycomfirm(msg, callback, title){
	var node = $('.my-comfirm');
	var contentNode = node.find('.content');
	var titleNode = node.find('.title');
	var shadow = node.find('.my-comfirm-shadow');
	var btnNo = node.find('.btn-cancel');
	var btnOk = node.find('.btn-ok');
	var body = $('body');
	body.addClass('my-comfirm-bodycss');
	if(!mycomfirmBinded){
		mycomfirmBinded = true;
		
		btnOk.click(function(){
			body.removeClass('my-comfirm-bodycss')
			if(callback && false === callback()){
				return;
			}
			node.hide();
		});
		btnNo.click(function(){ node.hide(); body.removeClass('my-comfirm-bodycss'); });
		shadow.click(function(){ node.hide(); body.removeClass('my-comfirm-bodycss'); });
	}
	!title && (title = '信息')
	titleNode.text(title);
	contentNode.text(msg);
	node.show();
}

function isMobile(mobile){
	var pat = /^1\d{10}$/;
	if (pat.test(mobile)) 
		return true;
	return false;
}

// Element Attribute Helper
function attrDefault($el, data_var, default_val)
{
  if(typeof $el.attr(data_var) != 'undefined')
  {
	return $el.attr(data_var);
  }
  
  return default_val;
}

//初始化 daterange
function initDaterange(selector) {
	if(!$(selector)[0]){
		return false;
	}
  $(selector).each(function(i, el){
	  var ranges = {
		  '1天': [moment(), moment().add('days', 1)],
		  '5天': [moment(), moment().add('days', 5)],
		  '7天': [moment(), moment().add('days', 7)],
		  '10天': [moment(), moment().add('days', 10)],
		  '15天': [moment(), moment().add('days', 15)],
		  '30天': [moment(), moment().add('days', 30)],
		  '半年': [moment(), moment().add('months', 6)],
		  '1年': [moment(), moment().add('years', 1)],
		  '2年': [moment(), moment().add('years', 2)],
	  };
	  var $this = $(el),
	  opts = {
		  format: attrDefault($this, 'format', 'YYYY-MM-DD HH:mm:ss'),
		  timePicker: attrDefault($this, 'timePicker', true),
		  timePickerIncrement: attrDefault($this, 'timePickerIncrement', false),
		  separator: attrDefault($this, 'separator', ' - '),
		  locale: {
			applyLabel: '确定',
			cancelLabel: '取消',
			fromLabel: '从',
			toLabel: '到',
			weekLabel: '周',
			customRangeLabel: '自定义范围',
			daysOfWeek: moment()._lang._weekdaysMin.slice(),
			monthNames: moment()._lang._monthsShort.slice(),
			firstDay: 0
		  },
	  	  opens:attrDefault($this, 'opens', 'left'),
	  },
	  min_date = attrDefault($this, 'minDate', ''),
	  max_date = attrDefault($this, 'maxDate', ''),
	  start_date = attrDefault($this, 'startDate', ''),
	  end_date = attrDefault($this, 'endDate', '');
	  if ($this.hasClass('add-ranges'))
	  {
		  opts['ranges'] = ranges;
	  }
	  if (min_date.length)
	  {
		  opts['minDate'] = min_date;
	  }
	  if (max_date.length)
	  {
		  opts['maxDate'] = max_date;
	  }
	  if (start_date.length)
	  {
		  opts['startDate'] = start_date;
	  }
	  if (end_date.length)
	  {
		  opts['endDate'] = end_date;
	  }
	  $this.daterangepicker(opts, function(start, end){
		  var drp = $this.data('daterangepicker');
		  $this.trigger('daterangeCallback',[start,end]);
	  });
  });
}
// date2timestamp
function date2timestamp(time){
	timestamp = Date.parse(new Date(time.replace(/-/g, "/")));
	timestamp = timestamp/1000;
	$('.lay-date').val(timestamp);
}
// 初始化日期控件，特定用于时间为时间戳格式的时间填写
function initLayDate(node){
	node.hide();
	var time = node.val();
	if(time){
		time = date('Y-m-d', time);
	}
	var html = '<input type="text" onclick="window.laydate({choose:date2timestamp});" required value="'+ time +'" class="form-control">';
	node.after(html);
}
// 多级下拉菜单
function initMultiSelect(){
/*	if($('.multi-level-select select').val()){
		multiLevel($('.multi-level-select select'));
	}*/
	$('.multi-level-select').on('change',function(e){
		multiLevel($(e.target));
	});
}
function multiLevel(node){
	node.nextAll().remove();
	if(!node.val()){
		return false;
	}
	node.attr('name',node.parents('.multi-level-select').data('name'));
	node.siblings().removeAttr('name');
	url = node.parents('.multi-level-select').data('url')+node.val();
	$.ajax({
		url:url,
		type:'get',
		dataType:'json',
		success:function(data){
			var sub = data.data.list;
			if(sub){
				var html = '<select class="form-control"><option value="">请选择</option>';
				for(k in sub){
					html+='<option value="'+sub[k].id+'">'+sub[k].name+'</option>';
				}
				html+='</select>';
				node.after(html);
			}
		}
	});
}