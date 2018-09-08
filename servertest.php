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
	$postRequest = curl_init("https://web.njit.edu/~jmb75/server.php");
	 
	// Specify post request in curl (CURLOPT_POST)
	curl_setopt($postRequest, CURLOPT_POST, 1);
	 
	// Attach encoded JSON string to POST fields
	curl_setopt($postRequest, CURLOPT_POSTFIELDS, $jsonDataEncoded);

	curl_setopt($postRequest, CURLOPT_RETURNTRANSFER, true);
	 
	// Set Content-Type to application/json and Content-Length to length of content.
    curl_setopt($postRequest, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Content-Length: ' . strlen($content))
    );
	 
	echo "Sending JSON obj to server...\n";
	// Execute the request
	$message = curl_exec($postRequest);

	echo "Object received.\n";
	# Get Post Request From mid-end
	$input=file_get_contents("https://web.njit.edu/~jmb75/server.php");
	$json=json_decode($input);
	$isValid=$json->{"isValid"};

	echo $isValid . "<br>";

	curl_close($postRequest);


?> 