<?php
function dac_search_form( $atts, $content = null ) {
	extract(shortcode_atts(array(
		'button_text' => 'Search Now',
		'get_search_value' => 'no'
	), $atts));

	if ( isset($_GET['search_submit']) ) {
		$pagination = '';
		// process form data
		$treatment = $_GET['treatment'];
		$location = $_GET['location'];

		global $wpdb;
		$post_table = $wpdb->base_prefix . "posts";
		$meta_table = $wpdb->base_prefix . "postmeta";
		$distance_table = $wpdb->base_prefix . "dac_distance";

		// how many rows to show per page
		$rowsPerPage = 3;
		
		// by default we show first page
		$pageNum = 1;
		
		// if $_GET['page'] defined, use it as page number
		if(isset($_GET['offset'])){
			$pageNum = $_GET['offset'];
		}
		// counting the offset
		$offset = ($pageNum - 1) * $rowsPerPage;
		
		/*$sql = "SELECT DISTINCT m1.post_id, m3.meta_value 
				FROM $post_table 
				INNER JOIN $meta_table AS m1 
				ON m1.post_id = $post_table.ID 
				AND m1.meta_key IN ('_dac_profession', '_dac_treatments_procedures') 
				AND m1.meta_value LIKE '%$treatment%' 
				INNER JOIN $meta_table AS m2 
				ON m2.post_id = $post_table.ID 
				AND m2.meta_key IN ('_dac_location', '_dac_additional_location', '_dac_clinic_location') 
				AND m2.meta_value LIKE '%$location%' 
				INNER JOIN $meta_table AS m3 
				ON m3.post_id = $post_table.ID 
				AND m3.meta_key IN ('_dac_distance') 
				AND m3.meta_value <> '' 
				WHERE $post_table.post_status <> 'hold' 
				ORDER BY $post_table.ID DESC 
				LIMIT $offset, $rowsPerPage";*/

		$sql = "SELECT DISTINCT m1.post_id 
				FROM $post_table, $distance_table 
				INNER JOIN $meta_table AS m1 
				ON m1.post_id = $distance_table.post_id 
				AND m1.meta_key IN ('_dac_profession', '_dac_treatments_procedures') 
				AND m1.meta_value LIKE '%$treatment%' 
				INNER JOIN $meta_table AS m2 
				ON m2.post_id = $distance_table.post_id 
				AND m2.meta_key IN ('_dac_location', '_dac_additional_location', '_dac_clinic_location', '_dac_city') 
				AND m2.meta_value LIKE '%$location%' 
				WHERE $post_table.ID=$distance_table.post_id AND $post_table.post_status <> 'hold' AND $distance_table.distance <> '' 
				ORDER BY $distance_table.distance ASC 
				LIMIT $offset, $rowsPerPage";

		/*$sql = "SELECT $meta_table.post_id, $post_table.post_status 
			  FROM $post_table, $meta_table
			  WHERE $post_table.post_status <> 'hold' AND $meta_table.post_id=$post_table.ID 
			  AND $meta_table.meta_key IN ( '_dac_profession', '_dac_treatments_procedures') 
			  AND $meta_table.meta_value LIKE '%$treatment%' 
			  
			  ORDER BY $post_table.post_title 
			  LIMIT $offset, $rowsPerPage";*/

		$search_posts = $wpdb->get_results($sql,ARRAY_A);
		//echo $wpdb->last_query;
		//echo '<br>'.$wpdb->last_error;
		$search_result_posts = array();
		if( count($search_posts) > 0 ) {
			foreach($search_posts as $search_post) {
				$search_result_posts[] = $search_post['post_id'];
			}
		} else {
			$not_found = true;
		}

		$search_sql = "SELECT DISTINCT m1.post_id 
					FROM $post_table, $distance_table 
					INNER JOIN $meta_table AS m1 
					ON m1.post_id = $distance_table.post_id 
					AND m1.meta_key IN ('_dac_profession', '_dac_treatments_procedures') 
					AND m1.meta_value LIKE '%$treatment%' 
					INNER JOIN $meta_table AS m2 
					ON m2.post_id = $distance_table.post_id 
					AND m2.meta_key IN ('_dac_location', '_dac_additional_location', '_dac_clinic_location', '_dac_city') 
					AND m2.meta_value LIKE '%$location%' 
					WHERE $post_table.ID=$distance_table.post_id AND $post_table.post_status <> 'hold' AND $distance_table.distance <> '' 
					ORDER BY $distance_table.distance ASC";

		/*$search_sql = "SELECT $meta_table.post_id, $post_table.post_status From $meta_table, $post_table WHERE ($meta_table.meta_key='_dac_profession' AND $meta_table.meta_value LIKE '%$treatment%' AND $post_table.post_status <> 'hold' AND $meta_table.post_id=$post_table.ID) OR ($meta_table.meta_key='_dac_treatments_procedures' AND $meta_table.meta_value LIKE '%$treatment%' AND $post_table.post_status <> 'hold' AND $meta_table.post_id=$post_table.ID) UNION SELECT $meta_table.post_id, $post_table.post_status From $meta_table, $post_table WHERE ($meta_table.meta_key='_dac_location' AND $meta_table.meta_value LIKE '%$location%' AND $post_table.post_status <> 'hold' AND $meta_table.post_id=$post_table.ID) OR ($meta_table.meta_key='_dac_clinic_location' AND $meta_table.meta_value LIKE '%$location%' AND $post_table.post_status <> 'hold' AND $meta_table.post_id=$post_table.ID) OR ($meta_table.meta_key='_dac_additional_location' AND $meta_table.meta_value LIKE '%$location%' AND $post_table.post_status <> 'hold' AND $meta_table.post_id=$post_table.ID)";*/
		
		$result = $wpdb->get_results($search_sql,ARRAY_A);
		//$output .= $wpdb->last_query;
			
		if( (count($result) > 0) && (count($result) > $rowsPerPage) ) {
			$pagination .= '<div class="tablenav">
			  <div class="tablenav-pages">';
				   
				//$pages = $wpdb->get_results("SELECT ID FROM $table ORDER BY product_name");
				$numrows = count($result);
				// how many pages we have when using paging?
				$maxPage = ceil($numrows/$rowsPerPage);
				
				// print the link to access each page
				$path = '?'.$_SERVER['QUERY_STRING'];
				$nav = '';
			
				for($page = 1; $page <= $maxPage; $page++) {
				  if ($page == $pageNum){
					$nav .= ' <span class="page-numbers current">' . $page . '</span>'; // no need to create a link to current page
				  }else{
					$nav .= ' <a href="' . $path . '&offset=' . $page . '" class="page-numbers">' . $page . '</a>';
				  }
				}
			
				if ($pageNum > 1){
				  $page = $pageNum - 1;
			
				  $prev ='<a href="' . $path . '&offset=' . $page . '" class="prev_page"><i class="fa fa-chevron-circle-left"></i> Previous Page</a>';
				}else{
				  $prev = '&nbsp;'; // we're on page one, don't print previous link
				  $first = '&nbsp;'; // nor the first page link
				}
			
				if ($pageNum < $maxPage){
				  $page = $pageNum + 1;
				  $next = ' <a href="' . $path . '&offset=' . $page . '" class="next_page"><i class="fa fa-chevron-circle-right"></i> Next Page</a>';
				}else{
				  $next = '&nbsp;'; // we're on the last page, don't print next link
				  $last = '&nbsp;'; // nor the last page link
				}
			
				// print the navigation link
				$pagination .= $prev . $nav . $next;
			
			  $pagination .= '</div>
			  
			  <div class="clearfix"></div>
			  
			</div>';
		} else {
			//$pagination .= 'Nothing found.';
		}
	}
	
	ob_start();

	$treatment = '';
	$location = '';
	if( $get_search_value == 'yes' ) {
		if( isset($_GET['treatment']) ) {
			$treatment = $_GET['treatment'];
		}

		if( isset($_GET['location']) ) {
			$location = $_GET['location'];
		}
	}
	?>
    <div class="dac_search_form">
        <form action="<?php echo home_url('/search-results/'); ?>" method="get">
        	<div class="search_services">
                <input type="text" name="treatment" value="<?php echo $treatment; ?>" id="treatment" placeholder="Search for a specialist or treatment" autocomplete="off" />
                <div class="treatments_container"></div>
            </div>
            <div class="search_locations">
                <input type="text" name="location" value="<?php echo $location; ?>" id="location" placeholder="Select the location you want" autocomplete="off" />
                <div class="locations_container"></div>
            </div>
            <?php //wp_nonce_field( 'dac_search_action', 'dac_search_submit' ); ?>
            <div class="search_btn">
            	<input type="submit" value="<?php echo $button_text; ?>" id="search_submit" name="search_submit" />
            </div>
        </form>
	</div>
    
    <?php if( $get_search_value == 'yes' ) { ?>
    <div class="search_result_container clearfix">
    	<?php
			// get latitude, longitude and formatted address
			/*$data_arr = geocode($location);
			print_r($data_arr);
			if($data_arr){
				 
				$latitude = $data_arr[0];
				$longitude = $data_arr[1];
				$formatted_address = $data_arr[2];*/
		?>
        <!--<div id="gmap_canvas"></div>-->
    	<!--<script type="text/javascript" src="http://maps.google.com/maps/api/js"></script>-->
		<!--<script type="text/javascript">
            function init_map() {
                var myOptions = {
                    zoom: 14,
                    center: new google.maps.LatLng(<?php //echo $latitude; ?>, <?php //echo $longitude; ?>),
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                map = new google.maps.Map(document.getElementById("gmap_canvas"), myOptions);
                marker = new google.maps.Marker({
                    map: map,
                    position: new google.maps.LatLng(<?php //echo $latitude; ?>, <?php //echo $longitude; ?>)
                });
                infowindow = new google.maps.InfoWindow({
                    content: "<?php //echo $formatted_address; ?>"
                });
                google.maps.event.addListener(marker, "click", function () {
                    infowindow.open(map, marker);
                });
                infowindow.open(map, marker);
            }
            google.maps.event.addDomListener(window, 'load', init_map);
        </script>-->
       	<?php
			/*} else {
				echo "No map found.";
			}*/
			
			if( isset($not_found) ) {
				echo 'Nothing found.';
			}
			if( isset($pagination) ) {
				if( isset($search_result_posts) && sizeof($search_result_posts) > 0 ) {
					echo get_search_profiles($search_result_posts, $location);
					echo $pagination;
				}
			}
		?>
    </div>
	<?php
	}
	/*}*/
	
	$search_form = ob_get_contents();
	ob_end_clean();	
	return $search_form;
	
}
add_shortcode('dac-search-form', 'dac_search_form');
?>