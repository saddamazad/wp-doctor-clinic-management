<?php
	add_submenu_page( 'edit.php?post_type=professional', 'Calendar', 'Calendar', 'edit_posts', 'appointment-calendar', 'appointment_calendar_settings_page_callback' );
	//add_submenu_page( 'edit.php?post_type=professional', 'Appointments', 'Appointments', 'edit_posts', 'appointments', 'appointments_settings_page_callback' );
	add_menu_page( 'Appointments', 'Appointments', 'edit_posts', 'appointments', 'appointments_settings_page_callback', '', 39 );
	add_submenu_page( 'edit.php?post_type=clinic', 'Calendar', 'Calendar', 'edit_posts', 'clinic-appointment-calendar', 'clinic_appointment_calendar_settings_page_callback' );
	add_submenu_page( 'edit.php?post_type=clinic', 'Professionals', 'Professionals', 'edit_posts', 'clinic-professionals', 'clinic_professionals_page_callback' );
	add_submenu_page( 'edit.php?post_type=clinic', 'Administrators', 'Administrators', 'edit_posts', 'clinic-administrators', 'clinic_administrators_page_callback' );
	//add_submenu_page( 'edit.php?post_type=clinic', 'Appointments', 'Appointments', 'edit_posts', 'appointments', 'appointments_settings_page_callback' );
	add_submenu_page( 'edit.php?post_type=claims', 'Email Claim Page', 'Email Claim Page', 'manage_options', 'email-claim', 'email_claim_page_callback' );
	add_submenu_page( 'options-general.php', 'Review', 'Review', 'manage_options', 'review-page', 'review_page_callback' );
	
	function appointment_calendar_settings_page_callback() {
		if( isset($_POST['day_full_submit']) ) {
			$day_full = $_POST['day_full'];

			global $current_user;
			get_currentuserinfo();
			$user_id = $current_user->ID;
			
			$post_id = get_user_meta( $user_id, '_dac_post_id', true );
			$days_full = get_post_meta( $post_id, '_dac_days_full', true );
			if( $days_full ) {
				$days_full_arr = explode(",", $days_full);
				if( ! in_array($day_full, $days_full_arr) ) {
					$days_full .= ','.$day_full;
				}
			} else {
				$days_full .= $day_full;
			}
			update_post_meta( $post_id, '_dac_days_full', $days_full );
		}
?>
	<style type="text/css">
        table.calendar{ border-left:1px solid #999; }
        tr.calendar-row	{}
        td.calendar-day	{ min-height:80px; font-size:11px; position:relative; } * html div.calendar-day { height:80px; }
        td.calendar-day:hover { background:#eceff5; }
        td.calendar-day-np { background:#eee; min-height:80px; } * html div.calendar-day-np { height:80px; }
        td.calendar-day-head { background:#ccc; font-weight:bold; text-align:center; width:120px; padding:5px; border-bottom:1px solid #999; border-top:1px solid #999; border-right:1px solid #999; }
        div.day-number { background:#999; padding:5px; color:#fff; font-weight:bold; float:right; margin:-5px -5px 0 0; width:30px; text-align:center; }
		div.day-number a { color:#fff; text-decoration: none; }
		div.day-number.week-end { background: #F69; }
        td.calendar-day, td.calendar-day-np { width:120px; padding:5px; border-bottom:1px solid #999; border-right:1px solid #999; }
		.color-indicator { width:12px; height:11px; display:inline-block; vertical-align:middle; margin-right:5px; }
		.color-indicator.week-end { background: #F69; }
		div.day-number.day_full, .color-indicator.day_full { background:#F90; }
		.color-indicator.day_available { background:#999; }
    </style>

    <div class="wrap">
    	<h2><?php echo __('Calendar'); ?></h2>
        <h3><?php echo date("F").' '.date("Y"); ?></h3>
        <?php
			if( isset($_GET['cal_day']) ) {
				echo '<h3 style="color:#0073aa;">'.$_GET['cal_day'].'-'.$_GET['cal_month'].'-'.$_GET['cal_year'].'</h3>';
				$dateFull = $_GET['cal_year'].'-'.$_GET['cal_month'].'-'.$_GET['cal_day'];
		?>
        	<form action="<?php echo admin_url('/edit.php?post_type=professional&page=appointment-calendar'); ?>" method="post">
            	<p><input type="checkbox" value="<?php echo $dateFull; ?>" name="day_full" id="day_full" /> <label for="day_full">Mark this Day as Full / UnAvailable</label></p>
                <input type="submit" name="day_full_submit" class="button button-primary" value="Submit" />
            </form>
            
            <p></p>
            
            <!--<h3>Set Appointment</h3>
            <form action="<?php //echo admin_url('/edit.php?post_type=professional&page=appointment-calendar'); ?>" method="post">
            	<p>
                	<label>Appointee Name </label>
                    <br />
                    <input type="text" name="appointee_name" />
                </p>
            	<p>
                	<label>Appointee Email </label>
                    <br />
                    <input type="text" name="appointee_email" />
                </p>
            	<p>
                	<label>Appointee Phone </label>
                    <br />
                    <input type="text" name="appointee_phone" />
                </p>
            	<p>
                	<label>Appointment Location </label>
                    <br />
                    <textarea name="appointment_location"></textarea>
                </p>
            	<p>
                	<label>Appointment Time </label>
                    <br />
                    <input type="text"  name="appointment_time" />
                    <small>Ex: (11:00 AM - 11:30 AM)</small>
                </p>
                <p>
                	<input type="hidden" name="appointment_date" value="<?php //echo $dateFull; ?>" />
                	<input type="submit" name="appointment_submit" class="button button-primary" value="Submit" />
                </p>
            </form>-->
        <?php
			} else {
				echo '<span class="color-indicator week-end">&nbsp;</span> Weekly Off Day';
				echo '&nbsp;&nbsp;&nbsp;';
				echo '<span class="color-indicator day_full">&nbsp;</span> Day Full';
				echo '&nbsp;&nbsp;&nbsp;';
				echo '<span class="color-indicator day_available">&nbsp;</span> Available';
				echo '<p></p>';
				echo draw_calendar(date("m"), date("Y"));
			}
		?>
	</div>

<?php
	}
	
	
	function clinic_appointment_calendar_settings_page_callback() {
		if( isset($_POST['day_full_submit']) ) {
			$day_full = $_POST['day_full'];

			global $current_user;
			get_currentuserinfo();
			$user_id = $current_user->ID;
			
			$post_id = get_user_meta( $user_id, '_dac_post_id', true );
			$days_full = get_post_meta( $post_id, '_dac_days_full', true );
			if( $days_full ) {
				$days_full_arr = explode(",", $days_full);
				if( ! in_array($day_full, $days_full_arr) ) {
					$days_full .= ','.$day_full;
				}
			} else {
				$days_full .= $day_full;
			}
			update_post_meta( $post_id, '_dac_days_full', $days_full );
		}
?>
	<style type="text/css">
        table.calendar{ border-left:1px solid #999; }
        tr.calendar-row	{}
        td.calendar-day	{ min-height:80px; font-size:11px; position:relative; } * html div.calendar-day { height:80px; }
        td.calendar-day:hover { background:#eceff5; }
        td.calendar-day-np { background:#eee; min-height:80px; } * html div.calendar-day-np { height:80px; }
        td.calendar-day-head { background:#ccc; font-weight:bold; text-align:center; width:120px; padding:5px; border-bottom:1px solid #999; border-top:1px solid #999; border-right:1px solid #999; }
        div.day-number { background:#999; padding:5px; color:#fff; font-weight:bold; float:right; margin:-5px -5px 0 0; width:30px; text-align:center; }
		div.day-number a { color:#fff; text-decoration: none; }
		div.day-number.week-end { background: #F69; }
        td.calendar-day, td.calendar-day-np { width:120px; padding:5px; border-bottom:1px solid #999; border-right:1px solid #999; }
		.color-indicator { width:12px; height:11px; display:inline-block; vertical-align:middle; margin-right:5px; }
		.color-indicator.week-end { background: #F69; }
		div.day-number.day_full, .color-indicator.day_full { background:#F90; }
		.color-indicator.day_available { background:#999; }
    </style>

    <div class="wrap">
    	<h2><?php echo __('Calendar'); ?></h2>
        <h3><?php echo date("F").' '.date("Y"); ?></h3>
        <?php
			if( isset($_GET['cal_day']) ) {
				echo '<h3 style="color:#0073aa;">'.$_GET['cal_day'].'-'.$_GET['cal_month'].'-'.$_GET['cal_year'].'</h3>';
				$dateFull = $_GET['cal_year'].'-'.$_GET['cal_month'].'-'.$_GET['cal_day'];
		?>
        	<form action="<?php echo admin_url('/edit.php?post_type=clinic&page=clinic-appointment-calendar'); ?>" method="post">
            	<p><input type="checkbox" value="<?php echo $dateFull; ?>" name="day_full" id="day_full" /> <label for="day_full">Mark this Day as Full / UnAvailable</label></p>
                <input type="submit" name="day_full_submit" class="button button-primary" value="Submit" />
            </form>
            
            <p></p>
            
            <!--<h3>Set Appointment</h3>
            <form action="<?php //echo admin_url('/edit.php?post_type=professional&page=appointment-calendar'); ?>" method="post">
            	<p>
                	<label>Appointee Name </label>
                    <br />
                    <input type="text" name="appointee_name" />
                </p>
            	<p>
                	<label>Appointee Email </label>
                    <br />
                    <input type="text" name="appointee_email" />
                </p>
            	<p>
                	<label>Appointee Phone </label>
                    <br />
                    <input type="text" name="appointee_phone" />
                </p>
            	<p>
                	<label>Appointment Location </label>
                    <br />
                    <textarea name="appointment_location"></textarea>
                </p>
            	<p>
                	<label>Appointment Time </label>
                    <br />
                    <input type="text"  name="appointment_time" />
                    <small>Ex: (11:00 AM - 11:30 AM)</small>
                </p>
                <p>
                	<input type="hidden" name="appointment_date" value="<?php //echo $dateFull; ?>" />
                	<input type="submit" name="appointment_submit" class="button button-primary" value="Submit" />
                </p>
            </form>-->
        <?php
			} else {
				echo '<span class="color-indicator week-end">&nbsp;</span> Weekly Off Day';
				echo '&nbsp;&nbsp;&nbsp;';
				echo '<span class="color-indicator day_full">&nbsp;</span> Day Full';
				echo '&nbsp;&nbsp;&nbsp;';
				echo '<span class="color-indicator day_available">&nbsp;</span> Available';
				echo '<p></p>';
				echo draw_calendar(date("m"), date("Y"), true);
			}
		?>
	</div>

<?php
	}
	
	
	function clinic_professionals_page_callback() {
		if( isset($_POST['day_full_submit']) ) {
			$day_full = $_POST['day_full'];
			
			$post_id = $_GET['prid'];
			$days_full = get_post_meta( $post_id, '_dac_days_full', true );
			if( $days_full ) {
				$days_full_arr = explode(",", $days_full);
				if( ! in_array($day_full, $days_full_arr) ) {
					$days_full .= ','.$day_full;
				}
			} else {
				$days_full .= $day_full;
			}
			update_post_meta( $post_id, '_dac_days_full', $days_full );
		}
?>
    <div class="wrap">
    	<?php
			if( isset($_GET['cal_day']) ) {
				echo '<h3 style="color:#0073aa;">'.$_GET['cal_day'].'-'.$_GET['cal_month'].'-'.$_GET['cal_year'].'</h3>';
				$dateFull = $_GET['cal_year'].'-'.$_GET['cal_month'].'-'.$_GET['cal_day'];
		?>
        	<form action="<?php echo admin_url('/edit.php?post_type=clinic&page=clinic-professionals&prid='.$_GET['prid']); ?>" method="post">
            	<p><input type="checkbox" value="<?php echo $dateFull; ?>" name="day_full" id="day_full" /> <label for="day_full">Mark this Day as Full / UnAvailable</label></p>
                <input type="submit" name="day_full_submit" class="button button-primary" value="Submit" />
            </form>
            
            <p></p>
            
            <!--<h3>Set Appointment</h3>
            <form action="<?php //echo admin_url('/edit.php?post_type=professional&page=appointment-calendar'); ?>" method="post">
            	<p>
                	<label>Appointee Name </label>
                    <br />
                    <input type="text" name="appointee_name" />
                </p>
            	<p>
                	<label>Appointee Email </label>
                    <br />
                    <input type="text" name="appointee_email" />
                </p>
            	<p>
                	<label>Appointee Phone </label>
                    <br />
                    <input type="text" name="appointee_phone" />
                </p>
            	<p>
                	<label>Appointment Location </label>
                    <br />
                    <textarea name="appointment_location"></textarea>
                </p>
            	<p>
                	<label>Appointment Time </label>
                    <br />
                    <input type="text"  name="appointment_time" />
                    <small>Ex: (11:00 AM - 11:30 AM)</small>
                </p>
                <p>
                	<input type="hidden" name="appointment_date" value="<?php //echo $dateFull; ?>" />
                	<input type="submit" name="appointment_submit" class="button button-primary" value="Submit" />
                </p>
            </form>-->
        <?php
			} elseif( isset($_GET['prid']) && !isset($_GET['cal_day']) ) {
				if( get_post_meta( $_GET['prid'], '_dac_clinic_permission', true ) == 'on' ) {
		?>
			<style type="text/css">
                table.calendar{ border-left:1px solid #999; }
                tr.calendar-row	{}
                td.calendar-day	{ min-height:80px; font-size:11px; position:relative; } * html div.calendar-day { height:80px; }
                td.calendar-day:hover { background:#eceff5; }
                td.calendar-day-np { background:#eee; min-height:80px; } * html div.calendar-day-np { height:80px; }
                td.calendar-day-head { background:#ccc; font-weight:bold; text-align:center; width:120px; padding:5px; border-bottom:1px solid #999; border-top:1px solid #999; border-right:1px solid #999; }
                div.day-number { background:#999; padding:5px; color:#fff; font-weight:bold; float:right; margin:-5px -5px 0 0; width:20px; text-align:center; }
                div.day-number a { color:#fff; text-decoration: none; }
                div.day-number.week-end { background: #F69; }
                td.calendar-day, td.calendar-day-np { width:120px; padding:5px; border-bottom:1px solid #999; border-right:1px solid #999; }
                .color-indicator { width:12px; height:11px; display:inline-block; vertical-align:middle; margin-right:5px; }
                .color-indicator.week-end { background: #F69; }
                div.day-number.day_full, .color-indicator.day_full { background:#F90; }
                .color-indicator.day_available { background:#999; }
            </style>
    	<?php
        		echo '<h3>'.date("F").' '.date("Y").'</h3>';
				echo '<span class="color-indicator week-end">&nbsp;</span> Weekly Off Day';
				echo '&nbsp;&nbsp;&nbsp;';
				echo '<span class="color-indicator day_full">&nbsp;</span> Day Full';
				echo '&nbsp;&nbsp;&nbsp;';
				echo '<span class="color-indicator day_available">&nbsp;</span> Available';
				echo '<p></p>';
				echo draw_calendar(date("m"), date("Y"), false, $_GET['prid']);
				} else {
					echo '<h3>You are not authorized to access this calendar.</h3>';
				}
			} else {
        ?>
    	<h2>Connected Professionals</h2>
        <?php
			global $wpdb, $current_user;
			$user_id = get_current_user_id();
			$user_roles = $current_user->roles;
			$user_role = array_shift($user_roles);
			if ( $user_role == 'account_manager' ) {
				$user_id = get_user_meta($user_id, '_dac_parent_clinic_user_id', true );
			}

			$post_id = get_user_meta( $user_id, '_dac_post_id', true );
			$post_table = $wpdb->base_prefix . "posts";
			$meta_table = $wpdb->base_prefix . "postmeta";
			$sql = "SELECT $post_table.ID, $post_table.post_title, $meta_table.meta_value FROM $post_table, $meta_table WHERE $meta_table.post_id = $post_table.ID AND $meta_table.meta_key = '_dac_associated_clinic' AND $meta_table.meta_value = '$post_id' ORDER BY $post_table.ID";
			//$sql = "SELECT post_id, meta_value FROM $meta_table WHERE meta_key='_dac_associated_clinic' AND meta_value='$post_id' ORDER BY post_id";
			$results = $wpdb->get_results($sql,ARRAY_A);
			//print_r($results);
			//echo $wpdb->last_query;
			
			if( count($results) > 0 ) {
				echo '<table cellspacing="0" border="1" cellpadding="15">';
				echo '<tr>
						<th>Name</th>
						<th>Profession</th>
						<th>&nbsp;</th>
					</tr>';
				foreach($results as $rc) {
					echo '<tr>
							<td>'.$rc['post_title'].'</td>
							<td>'.get_post_meta( $rc['ID'], '_dac_profession', true ).'</td>
							<td><a href="'.admin_url('/edit.php?post_type=clinic&page=clinic-professionals&prid='.$rc['ID']).'">Manage Calendar</a></td>
						</tr>';
				}
				echo '</table>';
			}
		
			}
		?>
    
    </div>
<?php
	}
	
	function clinic_administrators_page_callback() {
?>
    <div class="wrap">
    	<h2><?php echo __('Administrators'); ?></h2>
        <?php
			$user_id = get_current_user_id();
			//$post_id = get_user_meta( $user_id, '_dac_post_id', true );
        ?>
        <p></p>
        <?php
			global $wpdb;
			//$post_id = get_user_meta( $user_id, '_dac_post_id', true );
			$users_table = $wpdb->base_prefix . "users";
			$meta_table = $wpdb->base_prefix . "usermeta";
			$sql = "SELECT $users_table.ID, $users_table.user_email, $meta_table.meta_value FROM $users_table, $meta_table WHERE $meta_table.user_id = $users_table.ID AND $meta_table.meta_key = '_dac_parent_clinic_user_id' AND $meta_table.meta_value = '$user_id' ORDER BY $users_table.ID";
			//$sql = "SELECT post_id, meta_value FROM $meta_table WHERE meta_key='_dac_associated_clinic' AND meta_value='$post_id' ORDER BY post_id";
			$results = $wpdb->get_results($sql,ARRAY_A);
			//print_r($results);
			//echo $wpdb->last_query;
			
			if( count($results) > 0 ) {
				echo '<table cellspacing="0" border="1" cellpadding="15">';
				echo '<tr>
						<th>SL.</th>
						<th>Email</th>
					</tr>';
				$i=0;
				foreach($results as $rc) {
					echo '<tr>
							<td>'.++$i.'</td>
							<td>'.$rc['user_email'].'</td>
						</tr>';
				}
				echo '</table>';
				echo '<p></p>';
			}

			if( get_user_meta($user_id, '_dac_clinic_admins', true) < get_user_meta( $user_id, '_dac_num_of_admin_account', true) ) {
		?>
        <a class="button button-primary" href="<?php echo home_url('/administrator-sign-up/?cid='.$user_id); ?>" target="_blank">Add New</a>
        <p></p>
        <?php } ?>
    </div>
<?php
	}
	
	function appointments_settings_page_callback() {
?>
    	<div class="wrap">
    	<h2><?php echo __('Appointments'); ?></h2>
    <?php
		$user_id = get_current_user_id();
		global $current_user;
		$user_roles = $current_user->roles;
		$user_role = array_shift($user_roles);
		if ( $user_role == 'account_manager' ) {
			$user_id = get_user_meta( $user_id, '_dac_parent_clinic_user_id', true );
		}

		$post_id = get_user_meta( $user_id, '_dac_post_id', true );
		global $wpdb;
		$appointments_table = $wpdb->base_prefix . "dac_appointments";
		if( isset($_GET['appid']) ) {
			$appointments_id = $_GET['appid'];
			$wpdb->update( $appointments_table, array('appt_status' => "Read"), array('ID' => $appointments_id));
			$sql = "SELECT * FROM $appointments_table WHERE ID=$appointments_id";
			$appointment = $wpdb->get_row( $wpdb->prepare( $sql ), ARRAY_A );
		?>
        <style type="text/css">
			.appointment_details table tr th { background: #eeeeee; border-right: 1px solid #ffffff; text-align: left; color: #666666; }
			.appointment_details table tr td { background: #e3f4f2; border-right: 1px solid #ffffff; border-top: 1px solid #ffffff; color: #666666; }
			.appointment_details table tr td strong { color: #316c80; }
		</style>
        <div class="appointment_details">
        	<table border="0" cellspacing="0" cellpadding="10" width="100%">
            	<tr>
                	<th>Request Details</th>
                    <th>&nbsp;</th>
                    <th>&nbsp;</th>
                </tr>
                <tr>
                	<td><strong>Name:</strong> <?php echo $appointment['appt_patient_name']; ?></td>
                	<td><strong>Date:</strong> <?php echo substr($appointment['appt_date'], 0, 10); ?></td>
                	<td><strong>Email:</strong> <?php echo $appointment['appt_patient_email']; ?></td>
                </tr>
                <tr>
                	<td><strong>Phone:</strong> <?php echo $appointment['appt_patient_phone']; ?></td>
                	<td colspan="2"><strong>Location:</strong> <?php echo $appointment['appt_address']; ?></td>
                </tr>
            </table>
        	<!--<p>Name: <?php //echo $appointment['appt_patient_name']; ?></p>
        	<p>Phone: <?php //echo $appointment['appt_patient_phone']; ?></p>
        	<p>Email: <?php //echo $appointment['appt_patient_email']; ?></p>
        	<p>Date: <?php //echo substr($appointment['appt_date'], 0, 10); ?></p>
        	<p>Location: <?php //echo $appointment['appt_address']; ?></p>-->
            <p></p>
            <p>Give the Following Code: <em><strong style="color:#ea523d;"><?php echo get_post_meta( $post_id, '_dac_pin_code', true ); ?></strong></em> to any Client who makes an Appointment with You through TrustedTreatment.co.uk - so that they can Leave You Feedback on Our Website (give the code to them at the beginning of the Appointment).</p>
        </div>
	<?php
		} else {
			$sql = "SELECT ID, appt_patient_name, appt_date, appt_status FROM $appointments_table WHERE appt_doctor_id=$post_id ORDER BY created_datetime DESC LIMIT 30";
			//$sql = "SELECT post_id, meta_value FROM $meta_table WHERE meta_key='_dac_associated_clinic' AND meta_value='$post_id' ORDER BY post_id";
			$results = $wpdb->get_results($sql,ARRAY_A);
			//print_r($results);
			//echo $wpdb->last_query;
			
			if( count($results) > 0 ) {
			?>
			<style type="text/css">
                .appontments_list tr th { background: #eeeeee; border-right: 1px solid #ffffff; text-align: left; color: #666666; }
                .appontments_list tr td { background: #e3f4f2; border-right: 1px solid #ffffff; border-top: 1px solid #ffffff; color: #666666; }
                .appontments_list tr td a { color: #43a596; }
            </style>
            <?php
				echo '<table cellspacing="0" cellpadding="10" border="0" width="100%" class="appontments_list">
						<tr>
							<th>From</th>
							<th>Date</th>
							<th>&nbsp;</th>
						</tr>';
				foreach($results as $appointment) {
					$unread = 0;
					if($appointment['appt_status'] == 'Unread') {
						$style = 'style="color:#ca4425; text-decoration:none;"';
						$unread++;
					} else {
						$style = 'style="text-decoration:none;"';
					}
					/*$post_type = $_GET['post_type'];
					echo '<p><a href="'.admin_url('/edit.php?post_type='.$post_type.'&page=appointments&appid='.$appointment['ID']).'" '.$style.'>'.$appointment['appt_patient_name'].' on '.substr($appointment['appt_date'], 0, 10).'</a></p>';*/
					/*echo '<tr><td><a href="'.admin_url('/admin.php?page=appointments&appid='.$appointment['ID']).'" '.$style.'>'.$appointment['appt_patient_name'].' on '.substr($appointment['appt_date'], 0, 10).'</a></td></tr>';*/
					echo '<tr>
							<td>'.$appointment['appt_patient_name'].'</td>
							<td>'.substr($appointment['appt_date'], 0, 10).'</td>
							<td><a href="'.admin_url('/admin.php?page=appointments&appid='.$appointment['ID']).'" '.$style.'>View Now</a></td>
						</tr>';
				}
				echo '</table>';
			}
		}
	?>
    	</div>
    <?php
	}
	
	
	function email_claim_page_callback() {
		if( isset($_POST['claim_email_submit']) ) {
			$email = $_POST['claim_email'];
			/*$claim_url = $_POST['claim_url'];
			$claim_url_arr = explode("?", $claim_url);
			$claim_id_arr = explode("=", $claim_url_arr[1]);
			$claim_id = $claim_id_arr[1];*/
			$claim_id = $_POST['claim_id'];

			$args = array(
				'post_type' => 'claims',
				'p' => $claim_id,
				'post_status' => array( 'publish', 'draft' )
			);
			$query = new WP_Query( $args );
			
			// The Loop
			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					$expire = (time() + (24 * 60 * 60)); // +24 hour
					$message = 'Hi ' . get_the_title() . ", <br>";
					$message .= "<br>";
					$message .= "We have listed your page at TrustedTreatments.co.uk <br>";
					$message .= "<br>";
					$message .= 'To claim this page, make it public and keep it up-to-date please sign up here <a href="'.get_permalink().'?pt=claims&clid='.get_the_ID().'&sec='.$expire.'">'.get_permalink().'?pt=claims&clid='.get_the_ID().'&sec='.$expire.'</a> <br>';
					$message .= "<br>";
					$message .= "Why Trusted Treatment?";
					$message .= "<br>";
					$message .= "<ol>
									<li>We only allow medical professionals to list, and check all qualifications</li>
									<li>We verify training and insurance so pateints can book in confidence</li>
									<li>We do not publish treatment prices; we want professionals to be judged on quality and not price.</li>
									<li>Reviews can only be left by real patients</li>
									<li>We do not allow reviews that mention names, treatment outcome or breach our strict review guidelines</li>
								</ol>";
					$message .= "<br>";
					$message .= "If you have any questions please <a href='".home_url()."/contact-us/'>contact us</a> <br>";
					$message .= "<br>";
					$message .= "Regards, <br>";
					$message .= "TrustedTreatment Support Team";
					$headers = array('Content-Type: text/html; charset=UTF-8');
					if(wp_mail( $email, 'Claim your Trusted Treatment page', $message, $headers )) {
						global $wpdb;
						$table_claim_expires = $wpdb->base_prefix . "claim_expires";
						if($wpdb->insert( $table_claim_expires, array("post_id" => get_the_ID(), "expire" => $expire, "created_datetime" => current_time( 'mysql' ) ) )) {
							echo '<div class="updated">Successfully Sent!</div>';
						}
					}
				}
			}
		}
	?>
    	<div class="wrap">
            <h2><?php echo __('Email Claim Page'); ?></h2>
            <p></p>
        	<form action="<?php echo admin_url('/edit.php?post_type=claims&page=email-claim'); ?>" method="post">
            	<p><label for="claim_email" style="display: inline-block; padding-right: 5px; width: 80px;">Email: </label><input type="text" name="claim_email" id="claim_email" class="regular-text" /></p>
            	<!--<p>
                	<label for="claim_url" style="display: inline-block; padding-right: 5px; width: 80px;">Claim URL: </label><input type="text" name="claim_url" id="claim_url" class="regular-text" />
                	<br />
                    <label for="claim_url" style="display: inline-block; padding-right: 5px; width: 80px;"> </label>
                	<small>Ex: http://www.sitenex.com/wpr/preview/lwh/claims/john-husting/<strong>?clid=580</strong></small>
                    <br />
                    <small>Please insert the claim page ID in <strong>?clid</strong></small>
                </p>-->
            	<p>
                	<label for="claim_url" style="display: inline-block; padding-right: 5px; width: 80px;">Claim ID: </label>
                    <input type="text" name="claim_id" id="claim_id" />
                    <br />
                    <label for="claim_url" style="display: inline-block; padding-right: 5px; width: 80px;"> </label>
                    <small>Enter the claim page ID</small>
                </p>
                <input type="submit" name="claim_email_submit" class="button button-primary" value="Send" />
            </form>
        </div>
    <?php
	}
	
	function review_page_callback() {
		echo '<h2>'.__('Review').'</h2>';
		$review_id = $_GET['review_id'];
		if( isset($_GET['review_approve']) ) {
			global $wpdb;
			$post_table = $wpdb->base_prefix . "dac_reviews";
			$wpdb->update( $post_table, array( 'status' => "publish" ), array( "ID" => $review_id ) );
			echo '<p>Review Approved.</p>';
		} else {
			global $wpdb;
			$post_table = $wpdb->base_prefix . "dac_reviews";
			$result = $wpdb->get_row( $wpdb->prepare( "SELECT * from $post_table WHERE ID=$review_id"), ARRAY_A );
			if( count($result) > 0 ) {
				?>
				<style type="text/css">
					.review_details tr th { background: #eeeeee; border-right: 1px solid #ffffff; text-align: left; color: #666666; }
					.review_details tr td { background: #e3f4f2; border-right: 1px solid #ffffff; border-top: 1px solid #ffffff; color: #666666; }
					.review_details tr td a { color: #43a596; }
				</style>
				<?php
				echo '<table cellspacing="0" cellpadding="10" border="0" width="100%" class="review_details">
						<tr>
							<th>From</th>
							<th>Contact</th>
							<th>Message</th>
							<th>&nbsp;</td>
						</tr>
						<tr>
							<td>'.$result['name'].'</td>
							<td>Email: <strong>'.$result['email'].'</strong><br>Phone: <strong>'.$result['phone'].'</strong></td>
							<td>'.$result['message'].'</td>
							<td><a class="button button-primary" href="'.$_SERVER['REQUEST_URI'].'&review_approve=yes">Approve</a></td>
						</tr>
					</table>';
			}
		}
	}
?>