(function(){
	$('#update_menu_form').on('ajaxCallback', function(e, info){
		console.log(info);
		if(info.data && info.data.content){
			$('#update_button').hide().html(info.data.content).show(600);
		}
		return true;
	});
})();