/**
 * This file is just to make bootstrap navbar's dropdown menu active on hover.
 */


// jquery run on page loaded ----------------------------------------------------------------------------------------------
$(function() {
	$(".navbar .dropdown").hover(
		function () {
			$('.dropdown-menu', this).stop(true, true).fadeIn("fast");
			$(this).toggleClass('open');
		},
		function () {
			$('.dropdown-menu', this).stop(true, true).fadeOut("fast");
			$(this).toggleClass('open');
		}
	);
});