/*!
 * FuelStart theme sys2 sidebar menu
 * 
 * @license MIT
 * @author Vee W.
 * @version 2
 */


function sidebarMenuSticky(selector) {
	if (typeof(selector) === 'undefined') {
		console.log('Selector for sidebar menu sticky is missing.');
		return false;
	}
	
	if ($(window).width() < 768) {
		// it is not good for mini screen.
		console.log('Do not active sticky menu on mini screen.');
		$(window).off('scroll', window);
		$(selector).css({
			bottom: '',
			position: '',
			top: '',
		});
		return false;
	}

	sidebarMenuStickyActive(selector);
	$(window).trigger('scroll');// trigger scroll on page load.
}// sidebarMenuSticky


function sidebarMenuStickyActive(selector) {
	// clear all previous var and listening event before begin again.
	delete box_at_bottom;
	delete box_at_top;
	delete current_scroll_box_offset_top;
	delete current_scroll_box_top;
	delete current_scrolling_box_up_top;
	delete document_height;
	delete diff_scroll_box_bottom_to_window_height;
	delete last_scroll_top;
	delete scroll_box_bottom;
	delete scroll_box_height;
	delete scroll_box_top;
	delete window_height;
	delete window_y_bottom;
	delete window_y_top;
	$(window).off('scroll', window);
	
	// check to make sure that this page has sidebar menu. if not, return false and don't do anything.
	if (typeof($(selector).height()) == 'undefined') {
		return false;
	}
	
	// set vars for check.
	document_height = parseInt($(document).height());
	scroll_box_height = parseInt($(selector).height());
	scroll_box_top = parseInt($('.navbar-top-page').height());
	if (isNaN(scroll_box_top)) {
		scroll_box_top = parseInt($('.navbar').height());
	}
	scroll_box_bottom = (parseInt(scroll_box_height)+parseInt(scroll_box_top));
	window_height = parseInt($(window).height());
	diff_scroll_box_bottom_to_window_height = (parseInt(scroll_box_bottom)-parseInt(window_height));
	last_scroll_top = 0;// for check that is scrolling up or down.
	box_at_bottom = false;// for help not set bottom position again when it is already at bottom.
	box_at_top = false;// for help not set top position again when it is already at top.
	// debug
	//$('.scroll-debug').html('scroll box top: '+scroll_box_top+' scroll box bottom: '+scroll_box_bottom+'<br>');
	//$('.scroll-debug').append('scroll box height: '+scroll_box_height+'<br>');
	//$('.scroll-debug').append('diff box bottom to win height: '+diff_scroll_box_bottom_to_window_height+'<br>');
	//$('.scroll-debug').append('win height: '+window_height+'<br>');
	//$('.scroll-debug').append('document height: '+document_height+'<br>');
	// window scrolling event listening.
	$(window).on('scroll', window, function() {
		// check too small screen
		if ($(window).width() < 768) {
			return false;
		}

		// check that menu exists.
		if (typeof($(selector).offset()) == 'undefined') {
			return false;
		}

		window_y_top = parseInt($(this).scrollTop());
		current_scroll_box_offset_top = parseInt($(selector).offset().top);
		current_scroll_box_top = (parseInt(scroll_box_top)-parseInt(window_y_top));
		window_y_bottom = (parseInt(window_height)+parseInt(window_y_top));
		if (scroll_box_bottom <= window_height) {
			// bottom of scroll box is less than window height. fixed at top.
			box_at_bottom = false;
			box_at_top = false;
			$(selector).css({
				bottom: 'auto',
				position: 'fixed',
				top: scroll_box_top
			});
		} else if (window_y_top > last_scroll_top && box_at_bottom === false) {
			// scroll down.
			box_at_top = false;
			if (window_y_bottom >= (parseInt(current_scroll_box_offset_top)+parseInt(scroll_box_height))) {
				box_at_bottom = true;
				$(selector).css({
					bottom: '0',
					position: 'fixed',
					top: 'auto'
				});
			} else {
				$(selector).css({
					bottom: 'auto',
					position: 'absolute',
					top: (parseInt($(selector).offset().top)-parseInt(scroll_box_top))+'px'
				});
			}
			// debug
			//console.log('scrolling down');
		} else if (window_y_top < last_scroll_top && box_at_top === false) {
			// scroll up.
			box_at_bottom = false;
			current_scrolling_box_up_top = ((parseInt($(selector).offset().top)-parseInt(scroll_box_top))+parseInt(current_scroll_box_top));// current box that is scrolling up. top offset.
			if (current_scrolling_box_up_top > scroll_box_top) {
				box_at_top = true;
				$(selector).css({
					bottom: 'auto',
					position: 'fixed',
					top: 'auto',
				});
			} else {
				up_top_value = (parseInt($(selector).offset().top)-parseInt(scroll_box_top));
				if (up_top_value < 0) {
					up_top_value = 0;
				}
				$(selector).css({
					bottom: 'auto',
					position: 'absolute',
					top: up_top_value+'px'
				});
				delete up_top_value;
			}
			// debug
			//console.log('scrolling up');
		}
		last_scroll_top = window_y_top;
		// debug
		//console.log('current scroll box top: '+current_scroll_box_top+' current box offset top: '+current_scroll_box_offset_top);
		if (typeof(current_scrolling_box_up_top) != 'undefined') {
			//console.log('current scrolling box up top: '+current_scrolling_box_up_top);
		}
		//console.log('win y top: '+window_y_top+' win y bottom: '+window_y_bottom);
	});
}// sidebarMenuStickyActive


// jquery loaded required --------------------------------------------------------------------------------------------------
$(function() {
	// on mobile screen, click on toggle menu button. (menu burger icon)
	$('.sidebar-toggle').on('click', function() {
		// show or hide sidebar menu.
		$('.page-wrapper').toggleClass('mini-screen-sidebar-menu');
	});
	
	
	// on small screen (larger than mobile), click on expand/collapse sidebar button.
	$('.expand-collapse-sidebar-menu-btn').on('click', function(e) {
		e.preventDefault();
		$('.page-wrapper').toggleClass('expanded-sidebar-menu collapsed-sidebar-menu');
		$('.expand-collapse-sidebar-menu-icon').toggleClass('glyphicon-chevron-left glyphicon-chevron-right');
		sidebarMenuSticky('.page-sidebar');
	});
	
	
	// sidebar menu using smart menus
	$('#sidebar-menu').smartmenus({
		markCurrentItem: true,
		markCurrentTree: true,
		mainMenuSubOffsetY: -6,
		subMenusSubOffsetY: -6
	});
	
	
	// sidebar menu always stick on scroll the page.
	setTimeout(function() {
		// needs to set timeout because if smartmenus generating and it will be wrong value of height() in sidebar.
		sidebarMenuSticky('.page-sidebar');
		$(window).on('resize', function() {
			sidebarMenuSticky('.page-sidebar');
		});
	}, 200);
});