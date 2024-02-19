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

function fireSuccessSwal(title,message){
	Swal.fire({
        title: title, 
        text: message, 
        type: "success",
        icon: "success",
        confirmButtonText: "Okay",
        confirmButtonColor: "#04a9f5"
    });
}

function fireWarningSwal(title,message){
  Swal.fire({
        title: title, 
        text: message, 
        type: "warning",
        icon: "warning",
        confirmButtonText: "Okay",
        confirmButtonColor: "#04a9f5"
    });
}

function fireErrorSwal(title,message){
	Swal.fire({
        title: title, 
        text: message, 
        type: "error",
        icon: "error",
        confirmButtonText: "Okay",
        confirmButtonColor: "#04a9f5"
    });
}


$(document).on('change', '#dt_cb_all', function(e){
    var t = $(this);
    if(t.prop('checked') === true){
        $('.dt_cb').prop('checked', true);
    } else {
        $('.dt_cb').prop('checked', false);
    }
});

$(document).on('change', '.dt_cb', function(e){    
    if ($('.dt_cb:checked').length == $('.dt_cb').length) {
        $('#dt_cb_all').prop('checked', true);
    } else {
        $('#dt_cb_all').prop('checked', false);
    }
});

function updateHeaderProfile(profile_image, user_name){
    $('#header_profile_image').attr('src', profile_image);
    $('#header_auth_name').text(user_name);
}