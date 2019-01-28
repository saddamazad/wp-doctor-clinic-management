<?php
function dac_member_registration_form( $atts, $content = null ) {
	extract(shortcode_atts(array(
		'product_id' => ''
	), $atts));	

	if ( isset($_POST['register']) ) {
		// process form data
		
		if( isset($_GET['clid']) ) {
			$_SESSION['claim_page_id'] = $_POST['claim_page_id'];
			$claim_post = get_post( $_POST['claim_page_id'] );
			$title = explode(" ", $claim_post->post_title);
			$_SESSION['first_name'] = $title[0];
			$_SESSION['last_name'] = end($title);
			$_SESSION['clinic_name'] = $claim_post->post_title;
			$_SESSION['title'] = get_post_meta($_POST['claim_page_id'], '_dac_title', true);
			$_SESSION['user_profession'] = get_post_meta($_POST['claim_page_id'], '_dac_profession', true);
			
			if( has_term( 'professional', 'category', $_POST['claim_page_id'] ) ) {
				$_SESSION['account_type'] = 'Professional';
			} elseif( has_term( 'clinic', 'category', $_POST['claim_page_id'] ) ) {
				$_SESSION['account_type'] = 'Clinic';
			}
		} else {
			$_SESSION['first_name'] = $_POST['first_name'];
			$_SESSION['last_name'] = $_POST['last_name'];
			$_SESSION['title'] = $_POST['title'];
			$_SESSION['user_profession'] = $_POST['user_profession'];
			$_SESSION['birth_date'] = $_POST['birth_date'];
			$_SESSION['account_type'] = $_POST['account_type'];
			$_SESSION['clinic_name'] = $_POST['clinic_name'];
			$_SESSION['regulatory_body'] = $_POST['regulatory_body'];
			$_SESSION['registration_number'] = $_POST['registration_number'];
			/*$_SESSION['membership_type_professional'] = $_POST['membership_type_professional'];
			$_SESSION['membership_type_clinic'] = $_POST['membership_type_clinic'];*/
			if( isset($_POST['clinic_id']) ) {
				$_SESSION['clinic_id'] = $_POST['clinic_id'];
			}
		}

		global $woocommerce;
		$checkout_url = $woocommerce->cart->get_checkout_url();
		
		/*if( $_POST['account_type'] == 'Professional' ) {
			$product_id = $_POST['membership_type_professional'];
		} elseif( $_POST['account_type'] == 'Clinic' ) {
			$product_id = $_POST['membership_type_clinic'];
		}*/
		
		//wp_redirect( $checkout_url."?add-to-cart=$product_id" );
		//exit;
		echo '<script type="text/javascript">
				window.location = "'.$checkout_url.'?add-to-cart='.$product_id.'";
			</script>';
	}
	
	ob_start();

	if ( is_user_logged_in() ) {
		echo 'Welcome!!';
	} else {
	?>
	<form class="dac_register clearfix" action="" method="post">
    	<?php if( !isset($_GET['clid']) ) { ?>
    	<div class="form_left">
        	<h3>Enter Your Information Below</h3>
            <div>
                <select name="title" id="title">
                    <option value="">Title*</option>
                    <option value="Dr">Dr</option>
                    <option value="Prof">Prof</option>
                    <option value="Mr">Mr</option>
                    <option value="Mrs">Mrs</option>
                    <option value="Ms">Ms</option>
                    <option value="Miss">Miss</option>
                </select>
            </div>
    
            <div>
                <select name="user_profession" id="user_profession">
                    <option value="">Profession*</option>
                    <option value="Doctor">Doctor</option>
                    <option value="Surgeon">Surgeon</option>
                    <option value="Dentist">Dentist</option>
                    <option value="Nurse">Nurse</option>
                </select>
            </div>

            <div>
                <select name="account_type" id="account_type">
                    <option value="">Who are You*</option>
                    <option value="Professional">Professional</option>
                    <option value="Clinic">Clinic</option>
                </select>
            </div>

            <div style="position:relative;">
            	<input type="text" name="clinic_name" id="clinic_name" placeholder="Practice's Name" />
                <div class="clinics_container"></div>
            </div>
    
            <div><input type="text" name="first_name" value="" id="first_name" placeholder="First Name*" /></div>
    
            <div><input type="text" name="last_name" value="" id="last_name" placeholder="Surname*" /></div>
            
            <div><input type="text" name="birth_date" id="birth_date" placeholder="DOB" /></div>
    
            <div>
                <select name="regulatory_body" id="regulatory_body">
                    <option value="">Regulatory body</option>
                    <option value="GMC">GMC</option>
                    <option value="GDC">GDC</option>
                    <option value="NMC">NMC</option>
                </select>
            </div>
    
            <div><input type="text" name="registration_number" id="registration_number" placeholder="Registration number*" /></div>
        </div>
        <?php } ?>
        
        <div class="form_right">
        	<?php
				/*$product = get_post( $product_id );
				$title = $product->post_title;*/
			?>
            <h3>Membership Plan Details</h3>
            
            <div class="membership_plan_details">
            	<?php
					$sign_up_fee_arr = explode(".", get_post_meta($product_id, '_subscription_sign_up_fee', true));
					if( sizeof($sign_up_fee_arr) > 0 ) {
						$sign_up_fee = $sign_up_fee_arr[1];
					} else {
						$sign_up_fee = get_post_meta($product_id, '_subscription_sign_up_fee', true);
					}
				?>
            	<!--<h3>First 3 Months only <span><?php //echo $sign_up_fee; ?>p</span></h3>-->
                <h3>First 3 Months <span>Free</span></h3>
                <span class="plan_top_border">&nbsp;</span>
                <div class="mpd_left_info">No Contracts</div>
                <div class="mpd_right_info" style="margin-top: 8px;">Renews at only £<?php echo get_post_meta($product_id, '_subscription_price', true); ?>/yr</div>
            </div>
			<?php do_action('register_form'); ?>
            <?php wp_nonce_field( 'dac_register_action', 'dac_register_submit' ); ?>
            <?php
				if( isset($_GET['clid']) ) {
					echo '<input type="hidden" name="claim_page_id" value="'.$_GET['clid'].'" />';
				}
			?>
            <div class="register_btn"><input type="submit" value="Create Account" id="register" name="register" /></div>
        </div>
	</form>		
	
	<?php } ?>
    <script type="text/javascript">
		jQuery( document ).ready(function() {
			jQuery( ".clinics_container" ).hide();
			jQuery( ".dac_register" ).on( "keyup", "#clinic_name", function( event ) {
				event.preventDefault();
				var search_this = jQuery(this).val();
				jQuery.ajax({
					type : "post",
					url : ajaxObj.ajaxurl,
					data : {action : 'get_practices', clinic: search_this},
					beforeSend: function() {
						jQuery(".clinics_container").html('Loading...').show();
					}, 
					success: function(response) {
						jQuery(".clinics_container").html(response).show();
					}
				});
			});
			jQuery( ".clinics_container" ).on( "click", "span", function() {
				var inputValue = jQuery(this).text();
				var clinic_id = jQuery(this).attr('data-clinic');
				jQuery(this).parent('div').siblings("input").attr("value", inputValue);
				jQuery(this).parent().empty();
				jQuery(".clinics_container").append('<input type="hidden" name="clinic_id" value="'+clinic_id+'" />').hide();
			});

			jQuery( ".dac_register" ).on( "focus", "#first_name", function( event ) {
				jQuery( ".clinics_container" ).hide();
			});

			jQuery(".dac_register").submit(function() {
				//var check = true;
				if( jQuery("#title").val() == '' ) {
					alert("Please select a title");
					return false;
				} else if( jQuery("#user_profession").val() == '' ) {
					alert("Please select your profession");
					return false;
				} else if( jQuery("#account_type").val() == '' ) {
					alert("Please select who are you");
					return false;
				} else if( jQuery("#first_name").val() == '' ) {
					alert("Please enter your first name");
					return false;
				} else if( jQuery("#last_name").val() == '' ) {
					alert("Please enter your surname");
					return false;
				} else if( jQuery("#registration_number").val() == '' ) {
					alert("Please enter your registration number");
					return false;
				}
			}); 
		});
	</script>
<?php
	$registration_form = ob_get_contents();
	ob_end_clean();	
	return $registration_form;	
	
}
add_shortcode('dac_user_register', 'dac_member_registration_form');

add_action( 'wp_ajax_get_practices', 'get_practices' );
add_action( 'wp_ajax_nopriv_get_practices', 'get_practices' );
function get_practices() {
	$term = strip_tags($_POST['clinic']);
	$term = mysql_real_escape_string($term); // Attack Prevention
	if($term != "") {

		/*global $wpdb;
		$table = $wpdb->base_prefix . 'rmdh_products';
		$sql = "SELECT * FROM $table WHERE company_id = $company_id AND product_name LIKE('$term%') ORDER BY product_name";
		$results = $wpdb->get_results($sql,ARRAY_A);*/
		
		//print_r($result_1);
		global $wpdb;
		$results = $wpdb->get_results("SELECT ID, post_title FROM $wpdb->posts WHERE post_type='clinic' AND post_status='draft' AND post_title LIKE '$term%'", ARRAY_A);

		$string = '';
		//$string .= $wpdb->last_query;
		
		if( sizeof($results) > 0 ) {
			foreach($results as $result){
				$string .= '<span style="cursor:pointer;" data-clinic="'.$result['ID'].'">'.$result['post_title'].'</span>';
			}
		} else {
			$string .= "No clinic found!";
		}

		echo $string;
		die(); 

	} else {
		echo $string = '';
		die();
	}
}
?>