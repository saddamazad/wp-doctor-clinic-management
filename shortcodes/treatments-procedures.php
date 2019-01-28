<?php
function dac_treatments_procedures_shortcode( $atts, $content = null ) {
	extract(shortcode_atts(array(
		'profile_id' => '',
	), $atts));

	ob_start();
	if ( isset($_POST['profile_id']) ) {
		$profile_id = $_POST['profile_id'];
		$user_id = get_post_meta( $profile_id, '_dac_user_id', true );
		$user_type = get_post_type( $profile_id );

		//$treatments = get_post_meta( $profile_id, '_dac_treatments', true );
		$treatment_arr = explode(",", get_post_meta( $profile_id, '_dac_treatments_procedures', true ));
	?>
    <div class="treatments_category">
    	<?php if($treatment_arr) { ?>
        <ul>
        	<?php
			$count = 1;
            foreach($treatment_arr as $treatment) {
				if( $count%2 != 0 ) $last = 'prodact_calor';
				else $last = '';
			?>
            <!--<li class="<?php //echo $last; ?>"><?php //echo get_the_title($treatment); ?></li>-->
            <li class="<?php echo $last; ?>"><?php echo $treatment; ?></li>
            <?php
			$count++;
            }
			?>
            <!--<li><span class="heading">Main Category</span>
                <ul>
                    <li class="prodact_calor">Treatment Name</li>
                    <li>Treatment Name</li>
                    <li class="prodact_calor">Treatment Name</li>
                    <li>Treatment Name</li>
                    <li class="prodact_calor">Treatment Name</li>
                </ul>
            </li>-->
        </ul>
        <?php } else { ?>
        	<p>No treatments listed.</p>
        <?php } ?>
    </div>
    <!--<script type="text/javascript">
		jQuery( document ).ready(function() {
			jQuery(".et_pb_tab_2 a").text('Clinic');
		});
	</script>-->
	<?php	
	}
	
	$treatments_procedures = ob_get_contents();
	ob_end_clean();	
	return $treatments_procedures;	
}
add_shortcode('treatments_procedures', 'dac_treatments_procedures_shortcode');
?>