 <?php
 	/*
 	 * Created by James Brancale
 	 * 9/9/2018
 	 * CS490 - Tier 3 - Server/Backend
 	 * Server test code
 	 */

 	//// START Perform cURL post request to DB server ////

 	// Create JSON object to send, with username and password to query DB
 	echo "Creating JSON object\n";
	$jsonData = array();
	$jsonData["username"] = "username";
	$jsonData["password"] = "password";

	//echo $jsonData["username"] . "\n";
	//echo $jsonData["password"] . "\n";

	// Encode the array into JSON.
	$jsonDataEncoded = json_encode($jsonData);

	// Initialize cURL
	$postRequest = curl_init();

	// Initialize URL
	$url = "https://web.njit.edu/~jmb75/server.php";
	 
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
	echo $result . "\nObject received.\n";
	/*// Iterate through JSON results
	$jsonIterator = new RecursiveIteratorIterator(
    new RecursiveArrayIterator(json_decode($result, TRUE)),
    RecursiveIteratorIterator::SELF_FIRST);

	foreach ($jsonIterator as $key => $val) {
	    if(is_array($val)) {
	        echo "$key:\n";
	    } else {
	        echo "$key => $val\n";
	    }
	}
	*/
	$jsonDataResult=$json["isValid"];

	echo "Response: isValid : " . $jsonDataResult;

	//// END Collect JSON Response ////
?> 