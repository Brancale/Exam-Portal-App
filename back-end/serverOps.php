 <?php
 	// Set content type to JSON
	header("Content-type: application/json");

	# Get Post Request From mid-end
	$input=file_get_contents('php://input');
	$json=json_decode($input);
	$operID=$json->{"operationID"};
	$data=$json->{"data"};

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

	if ($operID == '1') {
		getQuestions($conn);
	} elseif($operID == '2') {
		addQuestion($conn, $data);
	} elseif($operID == '3') {
		addExam($conn, $data);
	} /*elseif($operID == '4') {
		getExam($conn, $data);
	} elseif($operID == '5') {
		submitExam($conn, $data);
	} */elseif($operID == '6') {
		getAllExams($conn, $data);
	} else {
		$response["success"] = "invalid input option";
		
		// Respond with JSON object
		$json_response = json_encode($response);
		echo $json_response;
	}

	// Close DB connection
	$conn->close();

	function getAllExams($conn) {
		// Query DB
		$sql = "SELECT * FROM `EXAM`";
		$result = $conn->query($sql);

		$response = array();

		$count = 0;

		// Check resulting records and read.
		if ($result->num_rows > 0) {
		    $response["success"] = "true";
		    // Output each return row
		    while($row = $result->fetch_assoc()) {
		        /*(`Questions`, `AnsKey`, `OpenTime`, `EndTime`)*/
		        $response[$count] = array(
                        'EID'=>$row['EID'],
                        'OpenTime'=>$row['OpenTime'],
                        'EndTime'=>$row['EndTime']
                                );
		        $count = $count + 1;
		    }
		} else {
			$response["success"] = "false";
		}
		
		// Respond with JSON object
		$json_response = json_encode($response);
		echo $json_response;
	}

	/*function getExam($conn, $data) {
		// Query DB
		$sql = "SELECT * FROM `EXAM` WHERE EID = '".$data."'";
		$result = $conn->query($sql);

		$response = array();

		// Check resulting records and read.
		if ($result->num_rows > 0) {
		    $response["success"] = "true";
		    // Output each return row
		    $row = $result->fetch_assoc();

		} else {
			$response["success"] = "false";
		}
		
		// Respond with JSON object
		$json_response = json_encode($response);
		echo $json_response;
	}*/

	function addExam($conn, $data) {
		
		// Query DB
		//INSERT INTO `EXAM`(`Questions`, `AnsKey`, `OpenTime`, `EndTime`) VALUES ('{"1","2","3"}','{"1", "2", "1"}',1539022862722,1545368400000)
		$sql = "INSERT INTO `EXAM`(`Questions`, `AnsKey`, `OpenTime`, `EndTime`) VALUES ".$data;
		if($conn->query($sql)) {
			$response["success"] = "true";
		} else {
			$response["success"] = "false";
		}
		
		// Respond with JSON object
		$json_response = json_encode($response);
		echo $json_response;
	}

	function addQuestion($conn, $data) {
		
		// Query DB
		$sql = "INSERT INTO `QUESTIONS`(`Question`, `Answer`, `Subject`, `Difficulty`, `QType`, `AnsID`) VALUES ".$data;
		if($conn->query($sql)) {
			$response["success"] = "true";
		} else {
			$response["success"] = "false";
		}
		
		// Respond with JSON object
		$json_response = json_encode($response);
		echo $json_response;
	}

	function getQuestions($conn) {
		/*
		 * Sample
		  INSERT INTO `QUESTIONS`(`Question`, `Answer`, `Subject`, `Difficulty`, `QType`, `AnsID`) VALUES ('What is the running time in the best case of Insertion Sort?', '{"O(1)","O(n)","O(n^2)","O(n^3)"}', 'CS610', 2, 1, 1)
		 */
		// Query DB
		$sql = "SELECT * FROM `QUESTIONS`";
		$result = $conn->query($sql);

		$response = array();

		$count = 0;

		// Check resulting records and read.
		if ($result->num_rows > 0) {
		    $response["success"] = "true";
		    // Output each return row
		    while($row = $result->fetch_assoc()) {
		        /*echo "QID: " . $row["QID"]. "; Question: " . $row["Question"]. "; Answer: " . $row["Answer"]. "; Subject: " . $row["Subject"]. "; Difficulty: " . $row["Difficulty"]. "; QType: " . $row["QType"]. "; AnsID: " . $row["AnsID"]. "\n";*/
		        $response[$count] = array(
                        'QID'=>$row['QID'],
                        'Question'=>$row['Question'],
                        'Answer'=>$row['Answer'],
                        'Subject'=>$row['Subject'],
                        'Difficulty'=>$row['Difficulty'],
                        'QType'=>$row['QType'],
                        'AnsID'=>$row['AnsID']
                                );
		        $count = $count + 1;
		    }
		} else {
			$response["success"] = "false";
		}
		
		// Respond with JSON object
		$json_response = json_encode($response);
		echo $json_response;
	}

	/*$response = array();

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
	echo $json_response;*/
?> 