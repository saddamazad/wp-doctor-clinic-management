<?php
	require_once("../../../../wp-load.php");
	$post_id = $_GET['pid'];
	//$profile_views = $wpdb->get_row( $wpdb->prepare( "SELECT * from $table_profile_views WHERE post_id=$post_id"), ARRAY_A );
	$submitted = false;
	if( isset($_POST['appointment_submit']) ) {
		$recaptcha_response = $_POST['g-recaptcha-response'];
		$user_ip = $_SERVER['REMOTE_ADDR'];
		// google recaptcha validation api url
		$url = "https://www.google.com/recaptcha/api/siteverify?secret=6LcvHRgTAAAAAA67al37ANbP31oAjlPo4eCLRFze&response=$recaptcha_response&remoteip=$user_ip";
	 
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, ''.$url.'');
		curl_setopt($ch, CURLOPT_HTTPGET, 1);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		//curl_setopt ($ch, CURLOPT_USERAGENT, sprintf("Mozilla/%d.0",rand(4,5)));
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$curl_response_res = curl_exec ($ch);
		curl_close ($ch);
		 
		// decode the json
		$resp = json_decode($curl_response_res, true);
	 
		// response status will be 'OK', if able to geocode given address 
		if($resp['success'] !== true) {
			$captcha_error = true;
		} else {
			global $wpdb;
	
			$post_id = $_POST['appointment_profile_id'];
			$first_name = $_POST['first_name'];
			$last_name = $_POST['last_name'];
			$email_address = $_POST['email_address'];
			$phone_number = $_POST['phone_number'];
			$location = $_POST['location'];
			$appointment_date = $_POST['appointment_date'];
			$patient_name = $first_name . ' ' . $last_name;
	
			$table_appointments = $wpdb->base_prefix . "dac_appointments";
			if( $wpdb->insert( $table_appointments, array("appt_doctor_id" => $post_id, "appt_address" => $location, "appt_date" => $appointment_date, "appt_patient_name" => $patient_name, "appt_patient_phone" => $phone_number, "appt_patient_email" => $email_address, "appt_month" => date("m"), "appt_week" => date("W"), "appt_year" => date("Y"), "appt_status" => "Unread", "created_datetime" => current_time( 'mysql' ), "updated_datetime" => current_time( 'mysql' ) )) ) {
				$user_id = get_post_meta( $post_id, '_dac_user_id', true );
				$user = get_user_by( 'id', $user_id );
				$user_email = $user->user_email;

				$message = 'Hi ' . $user->first_name . ", <br>";
				$message .= "You have a new enquiry! <br>";
				$message .= "<br>";
				$message .= "Please <a href='".wp_login_url()."'>login</a> to access your new message. <br>";
				$message .= "<br>";
				$message .= "Give the Following Code: <strong>".get_post_meta( $post_id, '_dac_pin_code', true )."</strong> to any Client who makes an Appointment with You through TrustedTreatment.co.uk - so that they can Leave You Feedback on Our Website (give the code to them at the beginning of the Appointment).<br>";
				$message .= "<br>";
				$message .= "If you have any questions please <a href='".home_url()."/contact-us/'>contact us</a> <br>";
				$message .= "<br>";
				$message .= "Regards, <br>";
				$message .= "TrustedTreatment Support Team";
				/*$message .= "Appointment Details: \r\n";
				$message .= "----------------------------- \r\n";
				$message .= "Name: ".$patient_name."\r\n";
				$message .= "Email: ".$email_address."\r\n";
				$message .= "Phone Number: ".$phone_number."\r\n";
				$message .= "Appointment Date: ".$appointment_date."\r\n";
				$message .= "Location: ".$location."\r\n";
				$message .= "\r\n";
				$message .= "~Thanks";*/
				$headers = array('Content-Type: text/html; charset=UTF-8');
				if(	wp_mail( $user_email, 'Appointment Request', $message, $headers ) ) {
					$message = 'Hi ' . $first_name . ", <br>";
					$message .= "Your enquiry has been sent! <br>";
					$message .= "<br>";
					$message .= "Please allow up to 24hours for a response as your health profession may currently be in clinic. <br>";
					$message .= "<br>";
					$message .= "If you have any questions please <a href='".home_url()."/contact-us/'>contact us</a> <br>";
					$message .= "<br>";
					$message .= "Regards, <br>";
					$message .= "TrustedTreatment Support Team";
					wp_mail( $email_address, 'Appointment Request Sent', $message, $headers );
				}
			}
			$submitted = true;
			//echo $wpdb->last_query;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Appointment Form</title>
		<link type="text/css" href="<?php echo get_stylesheet_directory_uri().'/style.css' ?>" rel="stylesheet" />
        <link type="text/css" rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css" />
        <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
        <script src="<?php echo home_url(); ?>/wp-includes/js/jquery/jquery.js" type="text/javascript"></script>
		<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
        <script type="text/javascript">
			jQuery( document ).ready(function() {
				var profile_id = jQuery('input[name=profile_id]', window.parent.document).val();
				jQuery( '<input type="hidden" name="appointment_profile_id" value="'+profile_id+'" />' ).insertBefore( "#appointment_form input[name=appointment_submit]" );
				
				//var full_days = jQuery('input[name=days_full]', window.parent.document).val();
				
				/** Days to be disabled as an array */
				//var disableddates = ["2016-2-3", "2016-2-21", "2016-2-25"];
				var disableddates = parent.profileObj.disableddates;
				var weekEnds = parent.profileObj.week_ends;
				function DisableSpecificDates(date) {
					var m = date.getMonth();
					var d = date.getDate();
					var y = date.getFullYear();
					
					// First convert the date in to the mm-dd-yyyy format 
					// Take note that we will increment the month count by 1 
					var currentdate = y + '-' + (m + 1) + '-' + d ;
					/*var currentdate = (m + 1) + '-' + d + '-' + y ;*/
					
					// We will now check if the date belongs to disableddates array 
					for (var i = 0; i < disableddates.length; i++) {
						// Now check if the current date is in disabled dates array. 
						if (jQuery.inArray(currentdate, disableddates) != -1 ) {
							return [false];
						} 
					}
					
					var day = date.getDay();
					if( weekEnds.length == 1 ) {
						return [(day != weekEnds[0])];
					}
					if( weekEnds.length == 2 ) {
						return [(day != weekEnds[0] && day != weekEnds[1])];
					}
					if( weekEnds.length == 3 ) {
						return [(day != weekEnds[0] && day != weekEnds[1] && day != weekEnds[2])];
					}
					if( weekEnds.length == 4 ) {
						return [(day != weekEnds[0] && day != weekEnds[1] && day != weekEnds[2] && day != weekEnds[3])];
					}
					
					// In case the date is not present in disabled array, we will now check if it is a weekend. 
					// We will use the noWeekends function
					var weekenddate = jQuery.datepicker.noWeekends(date);
					return weekenddate; 
				}
				
				jQuery( "#datepicker" ).datepicker({
					dateFormat: "yy-mm-dd",
					//firstDay: 6,
					beforeShowDay: DisableSpecificDates
				});

				jQuery("#appointment_form").submit(function() {
					//var check = true;
					if( jQuery("#datepicker").val() == '' ) {
						alert("Please select a date");
						return false;
					} else if( jQuery(".email_address").val() == '' ) {
						alert("Please enter your email address");
						return false;
					} else if( jQuery(".first_name").val() == '' ) {
						alert("Please enter your name");
						return false;
					}
				}); 
			});
		</script>
        <script src='https://www.google.com/recaptcha/api.js'></script>
    </head>
    
    <body>
		<?php if( !$submitted ) { ?>
		<div class="image_top_section">
        <?php
			$user_type = get_post_type( $post_id );
			
			if ( has_post_thumbnail($post_id) ) {
				$image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'profile-size');
				echo '<img src="' . $image_url[0] . '" alt="" />';
			} else {
				echo '<img src="' . DAC_URL . 'images/unknown_user.png" width="200" height="200" />';
			}
		?>
		<!--<img src="http://www.sitenex.com/wpr/preview/lwh/wp-content/uploads/serch_calint_im.png" />-->
        <?php if($user_type == 'professional') { ?>
		<h2><?php echo get_post_meta($post_id, '_dac_title', true).' '.get_the_title($post_id).' - <span>'.get_post_meta($post_id, '_dac_profession', true).'</span>'; ?></h2>
        <?php } elseif($user_type == 'clinic') { ?>
        <h2><?php echo get_the_title($post_id); ?></h2>
        <?php
        }
		if(($user_type == 'professional') && get_post_meta($post_id, '_dac_associated_clinic', true)) { ?>
		<h3><?php echo get_the_title(get_post_meta($post_id, '_dac_associated_clinic', true)); ?></h3>
        <?php } ?>
		</div><!--image_top_section-->
		<h2 class="hadeing_form">Request An Appointment</h2>
        <form action="" method="post" name="appointment_form" id="appointment_form">
            <div class="form_appointment_pop">
                <input class="first_name" type="text" name="first_name" placeholder="First Name*" />
                <input class="last_name" type="text" name="last_name" placeholder="Last Name*" />
                <input class="email_address" type="text" name="email_address" placeholder="Email Address*" />
                <input class="phone_number" type="text" name="phone_number" placeholder="Phone Number*" />
                <select class="dropdown_loc" name="location">
                    <!--<option value="">Select Location</option>-->
                    <?php
						if(get_post_meta($post_id, '_dac_location', true)) {
							echo '<option value="'.get_post_meta($post_id, '_dac_location', true).'">'.get_post_meta($post_id, '_dac_location', true).'</option>';
						}
						if(get_post_meta($post_id, '_dac_clinic_location', true)) {
							echo '<option value="'.get_post_meta($post_id, '_dac_clinic_location', true).'">'.get_post_meta($post_id, '_dac_clinic_location', true).'</option>';
						}
						if(get_post_meta($post_id, '_dac_additional_location', true)) {
							echo '<option value="'.get_post_meta($post_id, '_dac_additional_location', true).'">'.get_post_meta($post_id, '_dac_additional_location', true).'</option>';
						}
					?>
                </select>
                <div class="location clearfix"></div>
                <div class="checkbox_robot"><div class="g-recaptcha" data-sitekey="6LcvHRgTAAAAAAYb5gLl8rbLu88ERsAFsGtrsYXO"></div></div>
					
                <p><span class="view_profile_con"><input type="submit" class="view_profile" value="REQUEST AN APPOINTMENT" name="appointment_submit" /></span> <input class="cancel_appointment" value="Cancel Request" type="reset" name="cancel_appointment" /></p>
            </div>
				
            <div class="calenda_date">
                
                <input type="text" id="datepicker" name="appointment_date" placeholder="Select Date" />
                <p>Select Desired Date Above for Your Appointment</p>
            </div>
        </form>
				
        <div class="full_content clearfix">
            <p>By submitting this contact form, this Specialist wiill be informed about your request for an appointment. To enable the specialist to reach out 
    to you we will share your contact details. If you do not wish to submit this contact request, please press Cancel.</p>
        </div>

        <?php if( isset($captcha_error) ) { ?>
        <script type="text/javascript">
			setTimeout(function(){ alert('Please verify the captcha'); }, 1800);
		</script>
        <?php } ?>
        
        <?php } else { ?>
        
        <div class="confirmation_message">
        <div class="image_top_section">
            <!--<img src="http://www.sitenex.com/wpr/preview/lwh/wp-content/uploads/serch_calint_im.png" />
            <h2>Dr Martin Wade - <span>Dermatologist</span></h2>
            <h3>Clinic's Name if Entered</h3>-->
			<?php
                $user_type = get_post_type( $post_id );
                
                if ( has_post_thumbnail($post_id) ) {
                    $image_url = wp_get_attachment_image_src( get_post_thumbnail_id($post_id), 'profile-size');
                    echo '<img src="' . $image_url[0] . '" alt="" />';
                } else {
                    echo '<img src="' . DAC_URL . 'images/unknown_user.png" width="200" height="200" />';
                }
            ?>
            <!--<img src="http://www.sitenex.com/wpr/preview/lwh/wp-content/uploads/serch_calint_im.png" />-->
            <?php if($user_type == 'professional') { ?>
            <h2><?php echo get_post_meta($post_id, '_dac_title', true).' '.get_the_title($post_id).' - <span>'.get_post_meta($post_id, '_dac_profession', true).'</span>'; ?></h2>
            <?php } elseif($user_type == 'clinic') { ?>
            <h2><?php echo get_the_title($post_id); ?></h2>
            <?php
            }
            if(($user_type == 'professional') && get_post_meta($post_id, '_dac_associated_clinic', true)) { ?>
            <h3><?php echo get_the_title(get_post_meta($post_id, '_dac_associated_clinic', true)); ?></h3>
            <?php } ?>
        </div>
				
				<div class="thanks">
				<h3><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/thanks-icon.png" />Your Appointment Request has Been Submitted</h3>
				<p>Thank you for Requesting an Appointment. We will send your information to your selected Specialist and they will be in contact with you either via Email or Phone very soon!</p>
				</div>
        
        <?php } ?>
        
        <style type="text/css">
			.thanks{ text-align:center;}
			.thanks img{ margin-right:15px;}
			.thanks h3{ font-size:30px; line-height:32px; color:#666; background:url(thanks-icon.png) no-repeat;}
		  	.image_top_section img{ max-width:13%; }
			.hadeing_form{ text-align:center; font-size:30px; line-height:32px; color:#666666; padding-bottom:15px;}
			.calenda_date input {border: medium none;margin-bottom: 10px;padding: 10px;width: 100%;}
			.form_appointment_pop{ float:left; width:57%; margin-right:3%;}
			.calenda_date{ float:right; width:40%; padding:10px; background:url(<?php echo get_stylesheet_directory_uri(); ?>/images/calendar-bg.jpg) no-repeat; background-size:cover !important;}
			.calenda_date{ text-align:center; padding-top:20px; margin-bottom:28px;}
			.first_name, .email_address{ width:225px; float:left; border:1px solid #afc4ce; border-radius:5px;padding:6px !important;min-height:34px; color:#666666; margin-bottom:5px !important;font-size:16px;font-family:'FuturaTOT-Book';}
			.last_name, .phone_number{ width:225px; float:right;border-radius:5px; padding:6px !important; min-height:34px; color:#666666;margin-bottom:5px !important; font-size:16px;font-family:'FuturaTOT-Book';}
			.dropdown_loc{ padding:7px 6px; border-radius:5px; background-repeat: no-repeat; -webkit-appearance: none;-moz-appearance: none;appearance: none; width:100%;}
		  	.checkbox_robot{ min-height:50px; margin: 5px 0 12px; }
			.cancel_appointment{ background:#e6e6e6; padding:5px 20px; text-transform:uppercase; border:none; font-size:16px; color:#999999; cursor:pointer;}
			.full_content{ float:left; width:100%; margin-top:8px;}
			.calenda_date > p{ color:#316c80; text-align: center; padding-top:45px;}
			.image_top_section img{ float:left; margin-right:10px;}
			.image_top_section{ width:100%; float:left;border-bottom:1px solid #edf1f3; padding-bottom:8px; margin-bottom:5px;}
			.image_top_section h2{ color:#44a798;font-family:'FuturaTOT-Book'; padding-bottom:3px; font-size:24px; padding-top:33px;}
			.image_top_section span{ color:#40859c; }
			.image_top_section h3{ color:#666;font-family:'FuturaTOT-Book'; font-size:16px;}
			.full_content p{ font-size:14px; text-align:center;line-height:16px;}
			.view_profile_con:before{ background:url(<?php echo get_stylesheet_directory_uri(); ?>/images/req_icon.png) no-repeat; content:""; width:27px; height:24px; display:block; margin-bottom:-30px; position:relative; z-index:999; margin-left:5px;}
			.form_appointment_pop .view_profile{ padding: 6px 10px 6px 34px !important; font-size:16px;}
			.ui-widget-content { font-size: 14px;}
			#datepicker{ background: rgba(130,207,195,0.8); color: #FFF; font-weight: 700; }
			
			 
			@media (max-width: 768px) {
				.form_appointment_pop{ float:none; width:100%;}
				.calenda_date{float:none; width:100%;}
				.first_name, .email_address{ width:100%; float:none;}
				.first_name, .email_address{width:100%; float:none;}
				.view_profile_con::before{ font-size:10px; padding:0;}
				.last_name, .phone_number{width:100%; float:none;}
			}
			
        </style>

		<script type="text/javascript">
            /*jQuery( document ).ready(function() {
                jQuery('.form_appointment_pop input[name=appointment_submit]').on('click', function(e) {
                    e.preventDefault();
                    var post_id = jQuery('input[name=appointment_profile_id]').val();
                    var first_name = jQuery('input[name=first_name]').val();
                    var last_name = jQuery('input[name=last_name]').val();
                    var email_address = jQuery('input[name=email_address]').val();
                    var phone_number = jQuery('input[name=phone_number]').val();
                    var location = jQuery('input[name=location]').val();
                    var appointment_date = jQuery('input[name=appointment_date]').val();
                    jQuery("#appointment_form").ajaxForm( {
                        dataType: 'json',
                        type: 'post',
                        data: { 'post_id' : post_id, 'first_name' : first_name, 'last_name' : last_name, 'email_address' : email_address, 'phone_number' : phone_number, 'location' : location, 'appointment_date' : appointment_date },
                        beforeSubmit: function() {
                            //jQuery(".photo_holder").append('<div id="loadingIcon"><img src="<?php //echo get_stylesheet_directory_uri(); ?>/images/loading.gif" alt="Uploading...."/></div>');
                            jQuery("body").text('Sending....');
                        },
                        success: function(data) {
                            jQuery("body").html(data.builtHTML);
                        }
                    }).submit();
                });
            });*/
        </script>
    </body>
</html>