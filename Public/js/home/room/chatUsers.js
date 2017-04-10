var chatUsers = {
	node:$('#userList'),
	visitorNoNode : q('#visitorNo'),
	adminNoNode : q('#adminNo'),
	list:{},
	//生成用户id
	getId:function(user){
		if(user.id) return 'user' + user.sid;
		if(!user.sid) return;
		return 'user' + user.sid;
	},
	
	remove:function(sid){
		$('#'+this.getId({sid:sid})).remove();
		var user = this.list[sid];
		if(!user) return;
		this.visitorNoNode.innerHTML = parseInt(this.visitorNoNode.innerHTML) - 1;
		if(user.isAdmin){
			this.adminNoNode.innerHTML = parseInt(this.adminNoNode.innerHTML) -1;
		}
		
		delete this.list[sid];
	},
	
	clear:function(){
		this.node.find('li').remove();
		this.visitorNoNode.innerHTML = 0;
		this.adminNoNode.innerHTML = 0;
	},
	/**
	 * 添加上线的用户
	 * @param object user  #[username:'', sid:'',id:]
	 */
	add:function(user, isMe, isAdmin){
		var ul = q('#visitor');
		
		var idStr    = this.getId(user);
		var liExistsNode =  q('#'+idStr);
		this.list[user.sid] = user;
		//已存在用户
		if(liExistsNode){
			//不是直播者
			if(liver.userId != user.id){
				liExistsNode.title = 'sid:'+user.sid;
				return;
			}
			liver.sid = user.id;
			return;
		}

		var	li = document.createElement('li');
		li.id    = idStr;
		li.title = 'sid:'+ user.sid;
		li.className = 'online';
		isMe && (li.className = 'isMe') && (user.username +="(我)");
		li.innerHTML = '<img><span>'+ user.username +'</span>';
		//直播者
		if(liver.userId == user.id){
			liver.sid = user.sid
			li.className = 'isLiver';
			$(ul).prepend($(li));
			this.list[user.sid].isLiver = true;
			isAdmin = true;
		}else{
			ul.appendChild(li);
		}
		this.visitorNoNode.innerHTML = parseInt(this.visitorNoNode.innerHTML)+1;
		if(!isAdmin) return;
		this.addAdmin(li);
	},
	/**
	 * @param htmlElement li 
	 */
	addAdmin:function(li){
		var adminId = 'admin' + user.id;
		if(q('#'+adminId)) return;
		var adminLi = $(li).clone();
		var adminUl = q('#adminList');
		this.list[user.sid].isAdmin = true;
		adminLi.attr('id', adminId);
		$(adminUl).append(adminLi);
		this.adminNoNode.innerHTML = parseInt(this.adminNoNode.innerHTML) + 1;
	}
}