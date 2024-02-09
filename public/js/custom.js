/* --------------------------------------
		CUSTOM FUNCTION WRITE HERE
-------------------------------------- */
"use strict";
$(document).ready(function($){
	// Sidebar Open/Close
	$(".mobile-humberger").on("click", function(){
		$("body").addClass("sidebar-open");
	});
	$(".sidebar-close>span, .sidebar_overlay").on("click", function(){
		$("body").removeClass("sidebar-open");
	});
	// Custom select box
	$('.select-styled').on('click', function() {
		$('.select-options').not($(this).next('.select-options')).slideUp();
		$(this).next('.select-options').slideToggle();
	});
	$('.select-option').on('click', function() {
		var value = $(this).text();
		$(this).parents(".select-options").prev('.select-styled').text(value);
		$(this).parents(".select-options").slideUp();
	});
	$(document).on('click', function(event) {
		if (!$(event.target).closest('.custom-select').length) {
			$('.select-options').slideUp();
		}
	});
	// Client Admin Table
        $('#client-admin-table').DataTable();
	// Shift page Table
        var myTable = $('#shift-table').DataTable();
        $('th .custom-checkbox input').on('click', function() {
            var isChecked = $(this).prop('checked');
            $('input[type="checkbox"]', myTable.rows().nodes()).prop('checked', isChecked);
        });
        $('#shift-table tbody').on('change', 'input[type="checkbox"]', function() {
            if (!this.checked) {
                var el = $('th .custom-checkbox input').get(0);
                if (el && el.checked && ('indeterminate' in el)) {
                    el.indeterminate = true;
                }
            }
        });
        $('#shift-table').on('change', 'tbody input[type="checkbox"]', function() {
            var allChecked = $('input[type="checkbox"]', myTable.rows().nodes()).length === $('input[type="checkbox"]:checked', myTable.rows().nodes()).length;
            $('th .custom-checkbox input').prop('checked', allChecked);
        });
	// Staff page Table
        var myTableStaff = $('#staff-table').DataTable();
        $('th .custom-checkbox input').on('click', function() {
            var isChecked = $(this).prop('checked');
            $('input[type="checkbox"]', myTableStaff.rows().nodes()).prop('checked', isChecked);
        });
        $('#staff-table tbody').on('change', 'input[type="checkbox"]', function() {
            if (!this.checked) {
                var el = $('th .custom-checkbox input').get(0);
                if (el && el.checked && ('indeterminate' in el)) {
                    el.indeterminate = true;
                }
            }
        });
        $('#staff-table').on('change', 'tbody input[type="checkbox"]', function() {
            var allChecked = $('input[type="checkbox"]', myTableStaff.rows().nodes()).length === $('input[type="checkbox"]:checked', myTableStaff.rows().nodes()).length;
            $('th .custom-checkbox input').prop('checked', allChecked);
        });
});
