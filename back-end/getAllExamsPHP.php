 <?php
 	/*
 	 * Created by James Brancale
 	 * CS490 - Tier 3 - Server/Backend
 	 */

 	//// START Perform cURL post request to DB server ////

 	// Create JSON object to send, with username and password to query DB
 	echo "Creating JSON object\n";
	$jsonData = array();

	// Option to get all Exams
	$jsonData["operationID"] = '6';
	$jsonData["data"] = "";

	// Encode the array into JSON.
	$jsonDataEncoded = json_encode($jsonData);

	// Initialize cURL
	$postRequest = curl_init();

	// Initialize URL
	$url = "https://web.njit.edu/~jmb75/serverOps.php";
	 
	// Specify post request in curl (CURLOPT_POST)
	curl_setopt($postRequest, CURLOPT_POST, true);
	 
	// Attach encoded JSON string to POST fields
	curl_setopt($postRequest, CURLOPT_POSTFIELDS, $jsonDataEncoded);

	curl_setopt($postRequest, CURLOPT_HTTPAUTH, CURLAUTH_ANY);

	// Set URL to send to
	curl_setopt($postRequest, CURLOPT_URL, $url);

	// Print cURL statistics
	curl_setopt($postRequest, CURLOPT_VERBOSE, true);

	// Expect return value to store in $result
	curl_setopt($postRequest, CURLOPT_RETURNTRANSFER, true);
	 
	// Set Content-Type to application/json and Content-Length to length of content.
    curl_setopt($postRequest, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json')
    );
	 
	echo "Sending JSON obj to server...\n";
	// Execute the request
	$result = curl_exec($postRequest);
	curl_close($postRequest);

	//// END Perform cURL post request to DB server ////

	//// START Collect JSON Response ////

	$json = json_decode($result,true);
	echo "\nObject received.\n";

	$json_string = json_encode($json, JSON_PRETTY_PRINT);
	print($json_string);

	//// END Collect JSON Response ////
?> 