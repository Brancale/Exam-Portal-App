<?php
	// Respond with JSON object

	// Create JSON object
	$response = array();

	// Add entries into JSON object
	$response["field1"] = "1";
	$response["field2"] = "2";

	// Set content type to JSON
	header("Content-type: application/json");
	
	// Encode JSON object
	$json_response = json_encode($response);

	// Echo JSON object to page (to be read by receiver)
	echo $json_response;
?>