 <?php
 	/*
 	 * Created by James Brancale
 	 * 9/9/2018
 	 * CS490 - Tier 3 - Server/Backend
 	 */

 	// Set content type to JSON
	header("Content-type: application/json");

	# Get Post Request From mid-end
	$input=file_get_contents('php://input');
	$json=json_decode($input);
	$userid=$json->{"username"};
	$userpwd=$json->{"password"};

	//echo "User: " . $userid . "\n";
	//echo "Pawd: " . $userpwd . "\n";

	// SQL Credentials
	$servername = "sql2.njit.edu";
	$username = "jmb75";
	$password = "ask JMB75";
	$dbname = "jmb75";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}

	// Query DB
	$sql = "SELECT ID, PWD FROM USERS WHERE ID = '" . $userid . "'";
	$result = $conn->query($sql);

	// SAMPLE to insert into DB:
	// INSERT INTO `jmb75`.`USERS` (`ID`, `PWD`) VALUES ('username', MD5('password'));

	// Local vars for flags and values
	$recordFound = 0;
	$uid = "";
	$hash = "";

	// Check resulting records and read.
	if ($result->num_rows > 0) {
		$recordFound = 1;
	    // Output each return row
	    //while($row = $result->fetch_assoc()) {
	        //echo "ID: " . $row["ID"]. " - PWD: " . $row["PWD"]. "\n";
	    //}
	    $row = $result->fetch_assoc();
	    $uid = $row["ID"];
	    $hash = $row["PWD"];
	} /*elseif ($result->num_rows > 1) {
		//echo "DB entry error. Too many rows with same key.\n";
	} else {
	    //echo "0 results\n";
	}*/
	// Close DB connection
	$conn->close();

	$response = array();

	// If records found, compare password to hash and set isValid flag
	if ($recordFound == 1) {
		if (md5($userpwd) === $hash) {
		    //echo "Password matches\n";
		    //The JSON data.
			$response["isValid"] = "true";
		} else {
			$response["isValid"] = "error";
		}
	} else {
		$response["isValid"] = "false";
	}
	//$response["username"] = $uid;
	//$response["password"] = $hash;

	// Respond with JSON object
	$json_response = json_encode($response);
	echo $json_response;
?> 