<?php
	//require_once("../../../../wp-load.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Appointment Form</title>

        <style type="text/css">
			#gmap_canvas{
				width:100%;
				height:30em;
			}
		</style>
    </head>
    
    <body>
    <?php
    // get latitude, longitude and formatted address
    $data_arr = geocode($_GET['location']);
 
    // if able to geocode the address
    if($data_arr){
         
        $latitude = $data_arr[0];
        $longitude = $data_arr[1];
        $formatted_address = $data_arr[2];
                     
    ?>
 
    <!-- google map will be shown here -->
    <div id="gmap_canvas">Loading map...</div>
 
    <!-- JavaScript to show google map -->
    <script type="text/javascript" src="http://maps.google.com/maps/api/js"></script>    
    <script type="text/javascript">
        function init_map() {
            var myOptions = {
                zoom: 14,
                center: new google.maps.LatLng(<?php echo $latitude; ?>, <?php echo $longitude; ?>),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            map = new google.maps.Map(document.getElementById("gmap_canvas"), myOptions);
            marker = new google.maps.Marker({
                map: map,
                position: new google.maps.LatLng(<?php echo $latitude; ?>, <?php echo $longitude; ?>)
            });
            infowindow = new google.maps.InfoWindow({
                content: "<?php echo $formatted_address; ?>"
            });
            google.maps.event.addListener(marker, "click", function () {
                infowindow.open(map, marker);
            });
            infowindow.open(map, marker);
        }
        google.maps.event.addDomListener(window, 'load', init_map);
    </script>
 
    <?php
		// if unable to geocode the address
		}else{
			//echo "No map found.";
		}


		// function to geocode address, it will return false if unable to geocode address
		function geocode($address){
		 
			// url encode the address
			$address = urlencode($address);
			 
			// google map geocode api url
			$url = "http://maps.google.com/maps/api/geocode/json?address={$address}";
		 
			// get the json response
			/*$resp_json = file_get_contents($url);*/
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
			if($resp['status']=='OK'){
		 
				// get the important data
				$lati = $resp['results'][0]['geometry']['location']['lat'];
				$longi = $resp['results'][0]['geometry']['location']['lng'];
				$formatted_address = $resp['results'][0]['formatted_address'];
				 
				// verify if data is complete
				if($lati && $longi && $formatted_address){
				 
					// put the data in the array
					$data_arr = array();            
					 
					array_push(
						$data_arr, 
							$lati, 
							$longi, 
							$formatted_address
						);
					 
					return $data_arr;
					 
				}else{
					return false;
				}
				 
			}else{
				return false;
			}
		}
    ?>
    </body>
</html>