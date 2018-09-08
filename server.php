 <?php

	# Get Post Request From mid-end
	$input=file_get_contents('php://input');
	$json=json_decode($input);
	$userid=$json->{"username"};
	$userpwd=$json->{"password"};

	echo $userid;
	echo $userpwd;

	// Credentials
	$servername = "sql2.njit.edu";
	$username = "jmb75";
	$password = "wkTMUX7BC";
	$dbname = "jmb75";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}

	// Query DB
	$sql = "SELECT ID, PWD FROM USERS WHERE ID = " . $userid;
	echo $sql . "<br>";
	$result = $conn->query($sql);

	// SAMPLE to insert into DB:
	// INSERT INTO `jmb75`.`USERS` (`ID`, `PWD`) VALUES ('username', PASSWORD('password'));

	$recordFound = 0;

	$hash = "";

	// Check resulting records and read.
	if ($result->num_rows == 1) {
		$recordFound = 1;
	    // output data of each row
	    while($row = $result->fetch_assoc()) {
	        echo "ID: " . $row["ID"]. " - PWD: " . $row["PWD"]. "<br>";
	    }
	    $hash = $row["PWD"];
	} elseif ($result->num_rows > 1) {
		echo "DB entry error. Too many rows with same key.<br>";
	} else {
	    echo "0 results<br>";
	}

	$jsonData = array(
	    'isValid' => 'false'
	);

	if ($recordFound = 1) {
		if (password_verify($userpwd, $hash)) {
		    echo "Password matches<br>";
		    //The JSON data.
			$jsonData = array(
			    'isValid' => 'true'
			);
		} else {
		    echo "Password does not match<br>";
		}
	}

	// Encode the array into JSON.
	$jsonDataEncoded = json_encode($jsonData);

	$conn->close();

	// Initiate cURL
	$postRequest = curl_init("https://web.njit.edu/~jmb75/resptest.php");
	 
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

	curl_close($postRequest);
?> 