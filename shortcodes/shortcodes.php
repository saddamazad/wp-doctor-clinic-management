<?php
	if (!session_id()) {
		session_start();
	}
	require_once( 'user-registration.php' );
	require_once( 'admin-registration.php' );
	require_once( 'search-form.php' );
	require_once( 'user-profile.php' );
	require_once( 'staff-clinic.php' );
	require_once( 'treatments-procedures.php' );
	require_once( 'profile-updates.php' );
	require_once( 'reviews.php' );
	require_once( 'write-a-review.php' );
?>