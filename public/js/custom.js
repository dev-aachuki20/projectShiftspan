/* --------------------------------------
		CUSTOM FUNCTION WRITE HERE
-------------------------------------- */
"use strict";
$(document).ready(function($){
    $('table').on('draw.dt', function() {
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))
    })
	// Sidebar Open/Close
	$(".mobile-humberger").on("click", function(){
		$("body").addClass("sidebar-open");
	});
	$(".sidebar-close>span, .sidebar_overlay").on("click", function(){
		$("body").removeClass("sidebar-open");
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
        // $('.right-sidebox-small.modal-dropdown').close();
        $('.select-options,.options').slideUp();

    });
    function updateSelectedOptions() {
        var selected = [];
        $('.options input[type="checkbox"]:checked').each(function() {
        selected.push($(this).closest(".select-option").find('span').text());
    });
        $('.selected-options').text(selected.length > 0 ? selected.join(', ') : 'Select...');
    }

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