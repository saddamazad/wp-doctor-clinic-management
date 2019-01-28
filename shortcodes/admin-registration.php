<?php
function dac_clinic_admin_registration_form( $atts, $content = null ) {
	extract(shortcode_atts(array(
		'first_name' => 'yes',
		'last_name' => 'yes',
		'password' => 'yes'
	), $atts));	

	if ( isset($_POST['register']) && isset($_GET['cid']) ) {
		// process form data
		$first_name = $_POST['first_name'];
		$last_name = $_POST['last_name'];
		$email = $_POST['email'];
		//$password = $_POST['password'];
		//$address = $_POST['address'];

		$refferer_id = $_GET['cid'];
		$user_id = email_exists( $email );
		
		if ( !$user_id && email_exists($email) == false ) {
			$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
			$user_id = wp_create_user( $email, $random_password, $email );
			$wp_user_object = new WP_User($user_id);
			$wp_user_object->set_role('account_manager');
			//send mail
			wp_new_user_notification($user_id, $random_password, $first_name, $last_name, $email);
			update_user_meta($user_id, 'first_name', $first_name );
			update_user_meta($user_id, 'last_name', $last_name );
			update_user_meta($user_id, '_dac_parent_clinic_user_id', $refferer_id );
			//update_user_meta($user_id, '_dac_parent_clinic_user_id', $refferer_id );
			if( get_user_meta($refferer_id, '_dac_clinic_admins', true ) ) {
				$admin_connected = get_user_meta($refferer_id, '_dac_clinic_admins', true );
				update_user_meta($refferer_id, '_dac_clinic_admins', $admin_connected++ );
			} else {
				update_user_meta($refferer_id, '_dac_clinic_admins', 1 );
			}
			echo 'Registration Completed.';
		}else{
			echo 'This user already exist.';
		}		
	}
	
	ob_start();

	/*if ( is_user_logged_in() ) {
		echo 'Welcome!!';
	} else {*/
	?>
	<form class="dac_admin_register" action="" method="post">
		<p><label for="first_name">First Name:</label> <input type="text" name="first_name" value="" id="first_name" class="input"  /></p>

		<p><label for="last_name">Last Name:</label> <input type="text" name="last_name" value="" id="last_name" class="input"  /></p>

		<p><label for="last_name">Email:</label> <input type="text" name="email" value="" id="email" class="input"  /></p>

		<!--<p><label for="last_name">Password:</label> <input type="text" name="password" value="" id="password" class="input"  /></p>-->

		<!--<p>
        	<label for="address">Address:</label>
            <textarea id="address" name="address"></textarea>
        </p>-->
		<?php wp_nonce_field( 'dac_admin_register_action', 'dac_admin_register_submit' ); ?>
		<input type="submit" value="Register" id="register" name="register" />
	</form>		
	
	<?php	
	/*}*/
	
	$registration_form = ob_get_contents();
	ob_end_clean();	
	return $registration_form;	
	
}
add_shortcode('clinic_admin_registration', 'dac_clinic_admin_registration_form');
?>