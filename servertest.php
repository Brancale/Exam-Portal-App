 <?php

 	echo "Creating JSON object\n";
	$jsonData = array(
	    'username' => 'user',
	    'password' => 'password'
	);

	echo $jsonData . "\n";

	// Encode the array into JSON.
	$jsonDataEncoded = json_encode($jsonData);

	// Initiate cURL
	$postRequest = curl_init();
	 
	// Specify post request in curl (CURLOPT_POST)
	curl_setopt($postRequest, CURLOPT_POST, true);
	 
	// Attach encoded JSON string to POST fields
	curl_setopt($postRequest, CURLOPT_POSTFIELDS, $jsonDataEncoded);

	curl_setopt($postRequest, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	curl_setopt($postRequest, CURLOPT_URL, "https://web.njit.edu/~jmb75/server.php");


	curl_setopt($postRequest, CURLOPT_VERBOSE, true);
	curl_setopt($postRequest, CURLOPT_RETURNTRANSFER, true);
	 
	// Set Content-Type to application/json and Content-Length to length of content.
    curl_setopt($postRequest, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json')
    );
	 
	echo "Sending JSON obj to server...\n";
	// Execute the request
	$result = curl_exec($postRequest);
	curl_close($postRequest);

		$json = json_decode($message,true);
		echo "Object received.\n";
		# Get Post Request From mid-end
		$isValid=$json->{"isValid"};

		echo $isValid . "<br>";
?> 