/* 
 * login javascript.
 * This js used in admin/login page
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */


function ajaxAdminLogin(obj) {
	var serialize_val = obj.serialize();
	
	// disable button to prevent double click
	$('.admin-page-login-btn').attr('disabled', 'disabled');
	
	$('.ajax-admin-login-processing').addClass('fa fa-spinner fa-spin');
	
	$.ajax({
		url: obj.attr('action'),
		type: 'POST',
		data: serialize_val,
		dataType: 'json',
		success: function(data) {
			if (data.login_status === true) {
				window.location = data.go_to;
			} else {
				$('.admin-page-login-btn').removeAttr('disabled');
				$('.ajax-admin-login-processing').removeClass('fa fa-spinner fa-spin');
				
				if (data.form_status == 'error') {
					$('.form-status-placeholder').html(
						'<div class="alert alert-danger">'+data.form_status_message+'</div>'
					);
				}
				
				if (data.input_csrf_token != '') {
					$('.form-csrf-protection-placeholder').html(data.input_csrf_token);
				}
				
				$('.captcha').attr('src', base_url+'assets/img/securimage_show.php?' + Math.random());
				$('.login-page-input-username').focus();
				
				if (data.show_captcha == true) {
					$('.captcha-form-group').show('fade', {}, 'fast');
				} else {
					$('.captcha-form-group').hide('fade', {}, 'fast');
				}
				
				return false;
			}
		},
		error: function(data, status, e) {
			alert('Login error '+e);
			$('.admin-page-login-btn').removeAttr('disabled');
			$('.ajax-admin-login-processing').removeClass('fa fa-spinner fa-spin');
			$('.form-status-placeholder').html('');
			
			return false;
		}
	});
	
	return false;
}// ajaxAdminLogin


$(function() {
	// javascript check at login page
	$('#login-page-js-check').removeClass('glyphicon-remove').addClass('glyphicon-ok');// jquery checked javascript requirement at login page
}); // jquery start
