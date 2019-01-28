<?php
function dac_install_appointments_table(){
	global $wpdb;
	$table_appointments = $wpdb->base_prefix . "dac_appointments";
	$structure_appointments = "CREATE TABLE IF NOT EXISTS $table_appointments (	
	  ID BIGINT(20) NOT NULL AUTO_INCREMENT,
	  appt_doctor_id BIGINT(20) NOT NULL COMMENT 'Who will be handling the appointment',
	  appt_address VARCHAR(128) NOT NULL,
	  appt_date DATETIME NOT NULL,
	  appt_time VARCHAR(20) NOT NULL,
	  appt_day VARCHAR(10) NOT NULL,
	  appt_organizer VARCHAR(30) NOT NULL COMMENT 'Clinic/Own',
	  appt_patient_name VARCHAR(30) NOT NULL,
	  appt_patient_phone VARCHAR(12) NOT NULL,
	  appt_patient_address VARCHAR(128) NOT NULL,
	  appt_patient_email VARCHAR(30) NOT NULL,
	  appt_desc TEXT NOT NULL,
	  appt_month VARCHAR(2) NOT NULL,
	  appt_week VARCHAR(2) NOT NULL,
	  appt_year VARCHAR(4) NOT NULL,
	  appt_status VARCHAR(10) NOT NULL COMMENT 'Pending/Complete/Cancelled',
	  appt_cancel_date DATETIME NOT NULL,
	  appt_cancelled_by VARCHAR(30) NOT NULL,
	  created_by BIGINT(20) NOT NULL,
	  created_datetime DATETIME NOT NULL,
	  updated_datetime DATETIME NOT NULL,
	  PRIMARY KEY (ID)
	)";
	$wpdb->query($structure_appointments);
}

function dac_uninstall_appointments_table(){
	global $wpdb;
	$table_appointments = $wpdb->base_prefix . "dac_appointments";
	$structure_appointments = "DROP TABLE IF EXISTS $table_appointments";
	$wpdb->query($structure_appointments);
}

function dac_install_profile_views_table(){
	global $wpdb;
	$table_profile_views = $wpdb->base_prefix . "dac_profile_views";
	$structure_profile_views = "CREATE TABLE IF NOT EXISTS $table_profile_views (	
	  ID BIGINT(20) NOT NULL AUTO_INCREMENT,
	  post_id BIGINT(20) NOT NULL,
	  total_views INT(9) NOT NULL,
	  monthly_views LONGTEXT NOT NULL,
	  weekly_views LONGTEXT NOT NULL,
	  created_datetime DATETIME NOT NULL,
	  updated_datetime DATETIME NOT NULL,
	  PRIMARY KEY (ID)
	)";
	$wpdb->query($structure_profile_views);
}

function dac_install_distance_table(){
	global $wpdb;
	$table_distance = $wpdb->base_prefix . "dac_distance";
	$structure_distance = "CREATE TABLE IF NOT EXISTS $table_distance (	
	  ID BIGINT(20) NOT NULL AUTO_INCREMENT,
	  post_id BIGINT(20) NOT NULL,
	  distance FLOAT NOT NULL,
	  created_datetime DATETIME NOT NULL,
	  updated_datetime DATETIME NOT NULL,
	  PRIMARY KEY (ID)
	)";
	$wpdb->query($structure_distance);
}

function dac_install_claim_expire_table(){
	global $wpdb;
	$table_claim_expires = $wpdb->base_prefix . "claim_expires";
	$structure_claim_expires = "CREATE TABLE IF NOT EXISTS $table_claim_expires (	
	  ID BIGINT(20) NOT NULL AUTO_INCREMENT,
	  post_id BIGINT(20) NOT NULL,
	  expire BIGINT(20) NOT NULL,
	  created_datetime DATETIME NOT NULL,
	  PRIMARY KEY (ID)
	)";
	$wpdb->query($structure_claim_expires);
}

function dac_install_reviews_table() {
	global $wpdb;
	$table_reviews = $wpdb->base_prefix . "dac_reviews";
	$structure_reviews = "CREATE TABLE IF NOT EXISTS $table_reviews (	
	  ID BIGINT(20) NOT NULL AUTO_INCREMENT,
	  profile_id BIGINT(20) NOT NULL,
	  email VARCHAR(30) NOT NULL,
	  name VARCHAR(20) NOT NULL,
	  phone VARCHAR(15) NOT NULL,
	  rating FLOAT NOT NULL,
	  message TEXT NOT NULL,
	  status VARCHAR(8) NOT NULL,
	  created_datetime DATETIME NOT NULL,
	  PRIMARY KEY (ID)
	)";
	$wpdb->query($structure_reviews);
}
//add_action( 'admin_init', 'dac_install_profile_views_table' );
?>