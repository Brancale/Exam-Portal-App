 <?php
 	// Set content type to JSON
	header("Content-type: application/json");

	# Get Post Request From mid-end
	$input=file_get_contents('php://input');
	$json=json_decode($input);
	$userid=$json->{"username"};
	$userpwd=$json->{"password"};

	// SQL Credentials
	$servername = "sql2.njit.edu";
	$username = "jmb75";
	$password = "";
	$dbname = "jmb75";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}

	// SAMPLE to insert into DB:
	// INSERT INTO `jmb75`.`USERS` (`ID`, `PWD`, `TYPE`) VALUES ('username', MD5('password'), 1);

	// Query DB
	$sql = "SELECT ID, PWD FROM USERS WHERE ID = '" . $userid . "'";
	$result = $conn->query($sql);

	// Local vars for flags and values
	$recordFound = 0;
	$uid = "";
	$hash = "";

	// Check resulting records and read.
	if ($result->num_rows > 0) {
		$recordFound = 1;
	    $row = $result->fetch_assoc();
	    $uid = $row["ID"];
	    $hash = $row["PWD"];
	    $type = $row["TYPE"];
	}
	// Close DB connection
	$conn->close();

	$response = array();
	$response["type"] = $type;

	// If records found, compare password to hash and set isValid flag
	if ($recordFound == 1) {
		if (md5($userpwd) === $hash) {
		    //echo "Password matches\n";
		    //The JSON data.
			$response["isValid"] = "true";
		} else {
			$response["isValid"] = "false";
		}
	} else {
		$response["isValid"] = "false";
	}

	// Respond with JSON object
	$json_response = json_encode($response);
	echo $json_response;
?> 