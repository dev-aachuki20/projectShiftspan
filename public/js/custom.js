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
		if (!$(event.target).closest('.custom-select,.modal-dropdown').length) {
			$('.select-options,.options').slideUp();
		}
	});
    //multiple select and button click all select
    $('.select-box').click(function(event) {
        event.stopPropagation();
        $(this).next('.options').slideToggle();
    });
    $('.options input[type="checkbox').change(function() {
        updateSelectedOptions();
    });
    $('.selectAll').click(function() {
        var $checkboxes = $('.options input[type="checkbox"]');
        var allChecked = $checkboxes.filter(':checked').length === $checkboxes.length;
        $checkboxes.prop('checked', !allChecked);
        updateSelectedOptions();

    });
    function updateSelectedOptions() {
        var selected = [];
        $('.options input[type="checkbox"]:checked').each(function() {
        selected.push($(this).val());
    });
        $('.selected-options').text(selected.length > 0 ? selected.join(', ') : 'Select...');
    }

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
    // Client Detail page Table
    var myTableCdetail = $('#client-detail-table').DataTable();
    $('th .custom-checkbox input').on('click', function() {
        var isChecked = $(this).prop('checked');
        $('input[type="checkbox"]', myTableCdetail.rows().nodes()).prop('checked', isChecked);
    });
    $('#client-detail-table tbody').on('change', 'input[type="checkbox"]', function() {
        if (!this.checked) {
            var el = $('th .custom-checkbox input').get(0);
            if (el && el.checked && ('indeterminate' in el)) {
                el.indeterminate = true;
            }
        }
    });
    $('#client-detail-table').on('change', 'tbody input[type="checkbox"]', function() {
        var allChecked = $('input[type="checkbox"]', myTableCdetail.rows().nodes()).length === $('input[type="checkbox"]:checked', myTableCdetail.rows().nodes()).length;
        $('th .custom-checkbox input').prop('checked', allChecked);
    });
    // Location page Table
    var myTableLocation = $('#location-table').DataTable();
    $('th .custom-checkbox input').on('click', function() {
        var isChecked = $(this).prop('checked');
        $('input[type="checkbox"]', myTableLocation.rows().nodes()).prop('checked', isChecked);
    });
    $('#location-table tbody').on('change', 'input[type="checkbox"]', function() {
        if (!this.checked) {
            var el = $('th .custom-checkbox input').get(0);
            if (el && el.checked && ('indeterminate' in el)) {
                el.indeterminate = true;
            }
        }
    });
    $('#location-table').on('change', 'tbody input[type="checkbox"]', function() {
        var allChecked = $('input[type="checkbox"]', myTableLocation.rows().nodes()).length === $('input[type="checkbox"]:checked', myTableLocation.rows().nodes()).length;
        $('th .custom-checkbox input').prop('checked', allChecked);
    });
    // occupation page Table
    var myTableOccupation = $('#occupation-table').DataTable();
    $('th .custom-checkbox input').on('click', function() {
        var isChecked = $(this).prop('checked');
        $('input[type="checkbox"]', myTableOccupation.rows().nodes()).prop('checked', isChecked);
    });
    $('#occupation-table tbody').on('change', 'input[type="checkbox"]', function() {
        if (!this.checked) {
            var el = $('th .custom-checkbox input').get(0);
            if (el && el.checked && ('indeterminate' in el)) {
                el.indeterminate = true;
            }
        }
    });
    $('#occupation-table').on('change', 'tbody input[type="checkbox"]', function() {
        var allChecked = $('input[type="checkbox"]', myTableOccupation.rows().nodes()).length === $('input[type="checkbox"]:checked', myTableOccupation.rows().nodes()).length;
        $('th .custom-checkbox input').prop('checked', allChecked);
    });

    // Multiple Input type file select for add staff modal
    $('.fileInput').on('change', function(e) {
        var files = e.target.files;
        var sectionNumber = $(this).closest('.msg-form').data('section');
        var previewContainerId = 'imagePreviewContainer' + sectionNumber + '_' + $(this).attr('id').slice(-1);
        $('#' + previewContainerId).empty();
        for (var i = 0; i < files.length; i++) {
            var reader = new FileReader();
            reader.onload = function(e) {
                var image = $('<img>').addClass('previewImage').attr('src', e.target.result);
                $('#' + previewContainerId).append(image);
            };
            reader.readAsDataURL(files[i]);
        }
    });
});

// Image show in profile page
$(document).on('change', ".fileInputBoth",function(e){
	var files = e.target.files;
	for (var i = 0; i < files.length; i++) {
		var reader2 = new FileReader();
		reader2.onload = function(e) {
			$('.img-prePro img').attr('src', e.target.result);
		};
		reader2.readAsDataURL(files[i]);
	}
});


