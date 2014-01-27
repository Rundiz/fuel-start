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


$(function() {
	// fix bootstrap 3 navbar dropdown use hover
	$('.navbar .dropdown.pc_device').hover(function() {
		$(this).addClass('open');
	}, function() {
		$(this).removeClass('open');
	});
	
	// activate SmartMenus
	$('#admin-nav-menu').smartmenus({
		mainMenuSubOffsetY: 1,
		subMenusSubOffsetX: 1,
		subMenusSubOffsetY: -2
	});
	
	// chosen custom styled select box
	$('.chosen-select').chosen({
		allow_single_deselect: true,
		disable_search: true,
		display_disabled_options: true
	});
});// jquery start
