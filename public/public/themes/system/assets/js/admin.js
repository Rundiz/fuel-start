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


/**
 * table with floating header
 * to use this, call tableWithFloatingheader() function in the page you want and add tableWithFloatingHeader class to table
 * 
 * @returns {undefined}
 */
function tableWithFloatingheader() {
	function UpdateTableHeaders() {
		$("div.divTableWithFloatingHeader").each(function() {
			var originalHeaderRow = $(".tableFloatingHeaderOriginal", this);
			var floatingHeaderRow = $(".tableFloatingHeader", this);
			var offset = $(this).offset();
			var scrollTop = $(window).scrollTop();
			if ((scrollTop > offset.top) && (scrollTop < offset.top + $(this).height())) {
				floatingHeaderRow.css("visibility", "visible");
				floatingHeaderRow.css("top", Math.min(scrollTop - offset.top, $(this).height() - floatingHeaderRow.height()) + "px");

				// Copy cell widths from original header
				$("th", floatingHeaderRow).each(function(index) {
					var cellWidth = $("th", originalHeaderRow).eq(index).css('width');
					$(this).css('width', cellWidth);
				});

				// Copy row width from whole table
				floatingHeaderRow.css("width", $(this).css("width"));
			}
			else {
				floatingHeaderRow.css("visibility", "hidden");
				floatingHeaderRow.css("top", "0px");
			}
		});
	}// UpdateTableHeaders
	
	// floating header
	$("table.tableWithFloatingHeader").each(function() {
		$(this).wrap("<div class=\"divTableWithFloatingHeader\" style=\"position:relative\"></div>");
		var originalHeaderRow = $("tr:first", this);
		originalHeaderRow.before(originalHeaderRow.clone());
		var clonedHeaderRow = $("tr:first", this);
		clonedHeaderRow.addClass("tableFloatingHeader");
		clonedHeaderRow.css("position", "absolute");
		clonedHeaderRow.css("top", "0px");
		clonedHeaderRow.css("left", $(this).css("margin-left"));
		clonedHeaderRow.css("visibility", "hidden");
		originalHeaderRow.addClass("tableFloatingHeaderOriginal");
	});
	UpdateTableHeaders();
	$(window).scroll(UpdateTableHeaders);
	$(window).resize(UpdateTableHeaders);
	// end floating header
}// tableWithFloatingheader


$(function() {
	// fix bootstrap 3 navbar dropdown use hover
	$('.navbar .dropdown.pc_device').hover(function() {
		$(this).addClass('open');
	}, function() {
		$(this).removeClass('open');
	});
	
	// activate SmartMenus
	$('.navbar-smart-menu').smartmenus({
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
	
	// custom checkbox
	$('.custom-checkbox, .custom-radio').iCheck({
		checkboxClass: 'custom-checkbox-radio-icheck icheckbox_minimal-grey',
		radioClass: 'custom-checkbox-radio-icheck iradio_minimal-grey'
	});
});// jquery start
