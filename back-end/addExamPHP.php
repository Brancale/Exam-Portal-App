 <?php
 	/*
 	 * Created by James Brancale
 	 * CS490 - Tier 3 - Server/Backend
 	 */

 	//// START Perform cURL post request to DB server ////

 	// Create JSON object to send, with username and password to query DB
 	echo "Creating JSON object\n";
	$jsonData = array();

	// Test JSON for adding an Exam
	$jsonData["operationID"] = '3';
	// Data format: delimeter separated Question IDs, Open time in milliseconds, close time
	$jsonData["data"] = "('1|*|2',1543640400000,1545368400000)";

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

	// See if submission succeeded
	print($json["success"]);

	//// END Collect JSON Response ////
?> 