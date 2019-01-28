jQuery( document ).ready(function() {
	jQuery( ".dac_register" ).on( "change", "#account_type", function() {
		if( jQuery(this).val() == 'Professional' ) {
			jQuery( ".dac_register .professional" ).show();
			jQuery( ".dac_register .clinic" ).hide();
		} else if( jQuery(this).val() == 'Clinic' ) {
			jQuery( ".dac_register .clinic" ).show();
			jQuery( ".dac_register .professional" ).hide();
		}
	});

	var timer;
	jQuery( ".dac_search_form" ).on( "keyup", "#treatment", function( event ) {
		event.preventDefault();
		var search_this = jQuery(this).val();
		clearTimeout(timer);
		timer = setTimeout(function(){
			jQuery.ajax({
				type : "post",
				url : ajaxObj.ajaxurl,
				data : {action : 'get_treatments', treatment: search_this},
				beforeSend: function() {
					jQuery(".treatments_container").html('Loading...');
				}, 
				success: function(response) {
					jQuery(".treatments_container").html(response);
				}
			});
		}, 500);
	});

	jQuery( ".dac_search_form" ).on( "keyup", "#location", function( event ) {
		event.preventDefault();
		var search_this = jQuery(this).val();
		clearTimeout(timer);
		timer = setTimeout(function(){
			jQuery.ajax({
				type : "post",
				url : ajaxObj.ajaxurl,
				data : {action : 'get_locations', location: search_this},
				beforeSend: function() {
					jQuery(".locations_container").html('Loading...');
				}, 
				success: function(response) {
					jQuery(".locations_container").html(response);
				}
			});
		}, 500);
	});

	jQuery( ".dac_search_form" ).on( "click", "span", function() {
		var inputValue = jQuery(this).text();
		jQuery(this).parent('div').siblings("input").attr("value", inputValue);
		jQuery(this).parent().empty();
	});

	jQuery( ".Search_calint" ).on( "click", ".view_profile", function() {
		var post_id = jQuery(this).attr('data-id');
		jQuery( "form[name=profile_"+post_id+"_form]" ).submit();
	});

	/*jQuery( ".dac_search_form" ).on( "click", "#search_submit", function( event ) {
		event.preventDefault();
		var check = true;
		if( jQuery("#treatment").val() == '' ) {
			alert("Please enter a treatment");
			check = false;
		} else if( jQuery("#location").val() == '' ) {
			alert("Please enter a location");
			check = false;
		}

		if(check) {
			jQuery(this).closest('form').submit();
		}
	});*/

	jQuery(".dac_search_form form").submit(function() {
		//var check = true;
		if( jQuery("#treatment").val() == '' ) {
			alert("Please enter a treatment");
			return false;
		} else if( jQuery("#location").val() == '' ) {
			alert("Please enter a location");
			return false;
		}
	}); 

	//jQuery("#leave_review").click(function( event ) {
	jQuery( "#review_form" ).on( "click", "#leave_review", function( event ) {
		event.preventDefault();
		var review_code = jQuery("#review_code").val();
		var name = jQuery("#name").val();
		var email = jQuery("#email").val();
		var phone = jQuery("#phone").val();
		var rating_count = jQuery("#rating_count").val();
		var review_message = jQuery("#review_message").val();
		if(email == '') {
			alert("Please enter your email");
			return false;
		} else if(review_code == '') {
			alert("Please enter secret code");
			return false;
		} else if(phone == '') {
			alert("Please enter phone number");
			return false;
		} else {
			var dataContainer = {
				profile_code: review_code,
				email: email,
				name: name,
				phone: phone,
				rating_count: rating_count,
				review_message: review_message,
				action: "get_review_profile"
			};
			jQuery.ajax({
				action: "get_review_profile",
				type : "POST",
				dataType: "json",
				url : ajaxObj.ajaxurl,
				data : dataContainer,
				beforeSend: function() {
					// do something
				}, 
				success: function(data) {
					alert(data.msg);
					//alert(response);
				}
			});
		}
	});
});

/*
// create an array of days which need to be disabled 
var disabledDays = ["2-21-2010","2-24-2010","2-27-2010","2-28-2010","3-3-2010","3-17-2010","4-2-2010","4-3-2010","4-4-2010","4-5-2010"];

// utility functions 
function nationalDays(date) {
	var m = date.getMonth(), d = date.getDate(), y = date.getFullYear();
	//console.log('Checking (raw): ' + m + '-' + d + '-' + y);
	for (i = 0; i < disabledDays.length; i++) {
		if($.inArray((m+1) + '-' + d + '-' + y,disabledDays) != -1 || new Date() > date) {
			//console.log('bad:  ' + (m+1) + '-' + d + '-' + y + ' / ' + disabledDays[i]);
			return [false];
		}
	}
	//console.log('good:  ' + (m+1) + '-' + d + '-' + y);
	return [true];
}
function noWeekendsOrHolidays(date) {
	var noWeekend = jQuery.datepicker.noWeekends(date);
	return noWeekend[0] ? nationalDays(date) : noWeekend;
}

// create datepicker 
jQuery(document).ready(function() {
	jQuery('#date').datepicker({
		minDate: new Date(2010, 0, 1),
		maxDate: new Date(2010, 5, 31),
		dateFormat: 'DD, MM, d, yy',
		constrainInput: true,
		beforeShowDay: noWeekendsOrHolidays
	});
});*/