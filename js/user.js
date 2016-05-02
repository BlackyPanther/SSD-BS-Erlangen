function action_switch() {
	switch ($('select[name=action]').val()) {
		case 'new':
			$('#user-user').hide();
			$('#user-name').show();
			$('#user-email').show();
			$('#user-password').show();
			$('#user-password label').addClass('required');
			$('#user-class').show();
			$('#user-mobile').show();
			$('#user-permission').show();
			
			$('input[name=name]').val('');
			$('input[name=email]').val('');
			$('input[name=class]').val('');
			$('input[name=room]').val('');
			$('input[name=mobile]').val('');
			$('input[name=qualification]').val('');
			break;
		case 'edit':
			user_switch();
		case 'delete':
			$('#user-user').show();
			$('#user-name').hide();
			$('#user-email').hide();
			$('#user-password').hide();
			$('#user-password label').removeClass('required');
			$('#user-class').hide();
			$('#user-mobile').hide();
			$('#user-permission').hide();
			break;
		default:
			$('#user-user').hide();
			$('#user-name').hide();
			$('#user-email').hide();
			$('#user-password').hide();
			$('#user-class').hide();
			$('#user-mobile').hide();
			$('#user-permission').hide();
			break;
	}
}

function user_switch() {
	if ($('select[name=action]').val() == 'delete')
		return;

	if ($('select[name=user]').val() === '') {
		$('#user-name').hide();
		$('#user-email').hide();
		$('#user-password').hide();
		$('#user-class').hide();
		$('#user-mobile').hide();
		$('#user-permission').hide();
	} else {
		api('user_getUser', $('select[name=user]').val(), function(res) {
			if (res.error === '') {
				$('#user-name').show();
				$('#user-email').show();
				$('#user-password').show();
				$('#user-class').show();
				$('#user-mobile').show();
				$('#user-permission').show();
				
				$('input[name=name]').val(res.data.name);
				$('input[name=email]').val(res.data.email);
				$('input[name=class]').val(res.data.class);
				$('input[name=room]').val(res.data.room);
				$('input[name=mobile]').val(res.data.mobile);
				$('input[name=qualification]').val(res.data.qualification);
				
				$.each($('input[type=checkbox]'), function(idx, box) {
					$(box).prop('checked', false);
				});
				
				$.each(res.data.permissions, function(idx, perm) {
					$('input[value="' + perm + '"]').prop('checked', true);
				});
			} else {
				console.log("Error:\n" + res.error);
			}
		});
	}
}

$(function() {
	action_switch();
	
	$('select[name=action]').change(function() {
		action_switch();
	});
	
	$('select[name=user]').change(function() {
		user_switch();
	});
});