(function(){
	$(document).on('click', '.role-form .actions dt input', function(){
		var th = $(this);
		var dd = th.parents('dt').next('dd');

		if(th.is(':checked')){
			dd.find(':checkbox').prop('checked', true);
			return;
		}
		dd.find(':checkbox').prop('checked', false);
	});
})();