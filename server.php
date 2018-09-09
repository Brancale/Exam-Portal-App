 <?php

	header("Content-type: application/json");
	# Get Post Request From mid-end
	$input=file_get_contents('php://input');
	$json=json_decode($input);
	$userid=$json->{"username"};
	$userpwd=$json->{"password"};

	//echo "User: " . $userid . "\n";
	//echo "Pawd: " . $userpwd . "\n";

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
	//echo $sql . "\n";
	$result = $conn->query($sql);

	// SAMPLE to insert into DB:
	// INSERT INTO `jmb75`.`USERS` (`ID`, `PWD`) VALUES ('username', PASSWORD('password'));

	$recordFound = 0;

	$hash = "";

	// Check resulting records and read.
	if ($result->num_rows == 1) {
		$recordFound = 1;
	    // output data of each row
	    //while($row = $result->fetch_assoc()) {
	        //echo "ID: " . $row["ID"]. " - PWD: " . $row["PWD"]. "\n";
	    //}
	    $hash = $row["PWD"];
	} /*elseif ($result->num_rows > 1) {
		//echo "DB entry error. Too many rows with same key.\n";
	} else {
	    //echo "0 results\n";
	}*/
	$conn->close();

	$response = array();

	if ($recordFound = 1) {
		if (password_verify($userpwd, $hash)) {
		    //echo "Password matches\n";
		    //The JSON data.
			$response["isValid"] = "true";
		} else {
			$response["isValid"] = "error";
		}
	} else {
		$response["isValid"] = "false";
	}
	$response["username"] = $userid;
	$response["password"] = $userpwd;

	// Respond with JSON object
	$json_response = json_encode($response);
	echo $json_response;
	
	//echo "test\n";
?> 