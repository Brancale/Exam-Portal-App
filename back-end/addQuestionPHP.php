 <?php
 	/*
 	 * Created by James Brancale
 	 * CS490 - Tier 3 - Server/Backend
 	 */

 	//// START Perform cURL post request to DB server ////

 	// Create JSON object to send, with username and password to query DB
 	echo "Creating JSON object\n";
	$jsonData = array();

	// Test JSON for adding a question
	$jsonData["operationID"] = '2';
	// Data format: (Question Prompt, Question Answer, Subject, Difficulty, Autograde value, Constraints)
	$jsonData["data"] = "('Test Question', 'Test Answer', 'Course', 1, 'test1|*|test2', '1|*|2')";

	// Encode the array into JSON.
	$jsonDataEncoded = json_encode($jsonData);
	$postRequest = curl_init();
	$url = "https://web.njit.edu/~jmb75/serverOps.php";
	curl_setopt($postRequest, CURLOPT_POST, true);
	curl_setopt($postRequest, CURLOPT_POSTFIELDS, $jsonDataEncoded);
	curl_setopt($postRequest, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	curl_setopt($postRequest, CURLOPT_URL, $url);
	curl_setopt($postRequest, CURLOPT_VERBOSE, true);
	curl_setopt($postRequest, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($postRequest, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
	echo "Sending JSON obj to server...\n";
	// Execute the request
	$result = curl_exec($postRequest);
	curl_close($postRequest);
	//// END Perform cURL post request to DB server ////

	//// START Collect JSON Response ////
	$json = json_decode($result,true);
	echo "\nObject received.\n";

	//$json_string = json_encode($json, JSON_PRETTY_PRINT);
	if ($json["success"] == "true") {
		print("Your question was successfully added to DB!");
	} else {
		print("ERROR: Your question was not formatted properly");
	}

	//// END Collect JSON Response ////
?> 