/* 
 * admin javascripts
 * 
 * @author Vee W.
 * @license http://opensource.org/licenses/MIT
 * 
 */


function change_redirect(obj) {
	window.location = $(obj).val();
}// change_redirect


function checkAll(pForm, boxName, parent) {
	for (i = 0; i < pForm.elements.length; i++)
		if (pForm.elements[i].name == boxName)
			pForm.elements[i].checked = parent;
}// checkAll


/**
 * make bootstrap 3 tabs.
 * 
 * @requires Bootstrap 3, jQuery. These files must be loaded first.
 * @returns {undefined}
 */
function makeBs3Tabs() {
	// active first tab
	$('.bootstrap3-tabs a:first').tab('show');
	
	// set last clicked tab to local storage
	 $('.bootstrap3-tabs a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		 amplify.store(window.location.href+'last_used_tab', $(this).attr('href'));
	 });
	 
	 // store last click tab and set it to active.
	 var lastTab = amplify.store(window.location.href+'last_used_tab');
	 if (lastTab) {
		 $(".bootstrap3-tabs a[href="+ lastTab +"]").tab('show');
	 }
}// makeBs3Tabs


function noEnter(e) {
	var code = e.keyCode || e.which; 
	if (code == 13) {
		e.preventDefault();
		return false;
	}
}// noEnter


/**
 * table with floating header
 * to use this, call tableWithFloatingheader() function in the page you want and add tableWithFloatingHeader class to table
 * 
 * @requires  jquery.floatThead.min.js
 * @returns {undefined}
 */
function tableWithFloatingheader() {
	// the table with floating header use this awesome script: http://mkoryak.github.io/floatThead/
	$("table.tableWithFloatingHeader").floatThead({
		scrollingTop: $('.navbar').height(),
		zIndex: 1
	});
}// tableWithFloatingheader


$(function() {
	// fix bootstrap 3 navbar dropdown use hover. @requires Bootstrap 3.
	$('.navbar .dropdown.pc_device').hover(function() {
		$(this).addClass('open');
	}, function() {
		$(this).removeClass('open');
	});
	
	// activate SmartMenus. @requires SmartMenus
	$('.navbar-smart-menu').smartmenus({
		markCurrentItem: true,
		markCurrentTree: true,
		mainMenuSubOffsetY: 1,
		subMenusSubOffsetX: 1,
		subMenusSubOffsetY: -2
	});
	
	// chosen custom styled select box. @requires Chosen
	$('.chosen-select').chosen({
		allow_single_deselect: true,
		disable_search: true,
		display_disabled_options: true
	});
	
	// custom checkbox. @requires iCheck
	$('.custom-checkbox, .custom-radio').iCheck({
		checkboxClass: 'custom-checkbox-radio-icheck icheckbox_minimal-grey',
		radioClass: 'custom-checkbox-radio-icheck iradio_minimal-grey'
	});
});// jquery start
