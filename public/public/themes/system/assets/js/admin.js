/* 
 * admin javascripts
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */


$(function() {
	// fix bootstrap 3 navbar dropdown use hover
	$('.navbar .dropdown.pc_device').hover(function() {
		$(this).addClass('open');
	}, function() {
		$(this).removeClass('open');
	});
});// jquery start
