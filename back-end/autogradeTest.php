<?php
	// Set content type to JSON
	header("Content-type: application/json");

    // Create JSON object to send, with username and password to query DB
 	$jsonData = array();

	// Test JSON for autograding an exam
	$jsonData["operationID"] = 7;

	// Grade SExamID 1
	$jsonData["SExamID"] = "1";

	$jsonDataEncoded = json_encode($jsonData);

	// Initialize cURL
	$postRequest = curl_init();
	$url = "https://web.njit.edu/~jmb75/serverOps.php";
	curl_setopt($postRequest, CURLOPT_POST, true);
	curl_setopt($postRequest, CURLOPT_POSTFIELDS, $jsonDataEncoded);
	curl_setopt($postRequest, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	curl_setopt($postRequest, CURLOPT_URL, $url);
	curl_setopt($postRequest, CURLOPT_VERBOSE, true);
	curl_setopt($postRequest, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($postRequest, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json')
    );
	// Execute the request
	$result = curl_exec($postRequest);
	curl_close($postRequest);
	$jsonA = json_decode($result,true);
	$json_string = json_encode($jsonA, JSON_PRETTY_PRINT);
	print($json_string);

	echo $isCorrect;

?>