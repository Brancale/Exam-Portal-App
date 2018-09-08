<?php
	/*
	# Get Post Request From mid-end
	$input=file_get_contents('php://input');
	$json=json_decode($input);
	$isValid=$json->{"isValid"};

	echo $isValid . "<br>";

	$jsonData = array(
	    'isValid' => $isValid
	);

	// Encode the array into JSON.
	$jsonDataEncoded = json_encode($jsonData);

	// Initiate cURL
	$postRequest = curl_init("https://web.njit.edu/~jmb75/server.php");
	 
	// Specify post request in curl (CURLOPT_POST)
	curl_setopt($postRequest, CURLOPT_POST, 1);
	 
	// Attach encoded JSON string to POST fields
	curl_setopt($postRequest, CURLOPT_POSTFIELDS, $jsonDataEncoded);
	 
	// Set Content-Type to application/json and Content-Length to length of content.
    curl_setopt($postRequest, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Content-Length: ' . strlen($content))
    );
	 
	// Execute the request
	$result = curl_exec($postRequest);
	*/
	echo "Nice meme"
?>