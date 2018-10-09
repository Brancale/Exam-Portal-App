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
	} elseif($operID == '4') {
		getExam($conn, $data);
	} /*elseif($operID == '5') {
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

	function submitExam($conn, $data) {
		// INSERT INTO `STUDENT`(`SExID`, `EID`, `SName`, `Answers`, `Score`, `Comments`) VALUES ([value-1],[value-2],[value-3],[value-4],[value-5],[value-6])
		
	}

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
                        'EndTime'=>$row['EndTime'],
                        'Points'=>$row['Points']
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

	function getExam($conn, $data) {
		// Query DB
		$sql = "SELECT * FROM `EXAM` WHERE EID = '".$data."'";
		$result = $conn->query($sql);

		$response = array();

		// Check resulting records and read.
		if ($result->num_rows > 0) {
		    $response["success"] = "true";
		    // Output each return row
		    $row = $result->fetch_assoc();

		    /*(`Questions`, `AnsKey`, `OpenTime`, `EndTime`)*/
            $examID = $row['EID'];
            $questionArr = $row['Questions'];
	        $questionIDs = preg_split("/;/", $questionArr);

	        //$response["questionList"] = $questionArr;
	        //$response["questionIDs"] = $questionIDs;

	        $count = 0;

	        foreach ($questionIDs as $questid => $value) {
			    //$response["value".$count] = $value;
			    // query for $value in QUESTIONS table
				$sqlQ = "SELECT * FROM `QUESTIONS` WHERE QID = '".$value."'";
				$resultQ = $conn->query($sqlQ);
				if ($resultQ->num_rows > 0) {
					$rowQ = $resultQ->fetch_assoc();

					$response[$count] = array(
	                        'QID'=>$rowQ['QID'],
	                        'Question'=>$rowQ['Question'],
	                        'Answer'=>$rowQ['Answer'],
	                        'Subject'=>$rowQ['Subject'],
	                        'Difficulty'=>$rowQ['Difficulty'],
	                        'Points'=>$rowQ['Points']
	                                );
			        $count = $count + 1;
			    }
			}
		} else {
			$response["success"] = "false";
		}
		
		// Respond with JSON object
		$json_response = json_encode($response);
		echo $json_response;
	}

	function addExam($conn, $data) {
		
		// Query DB
		//INSERT INTO `EXAM`(`Questions`, `AnsKey`, `OpenTime`, `EndTime`) VALUES ('{"1","2","3"}','{"1", "2", "1"}',1539022862722,1545368400000, 100)
		$sql = "INSERT INTO `EXAM`(`Questions`, `AnsKey`, `OpenTime`, `EndTime`, `Points`) VALUES ".$data;
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
		$sql = "INSERT INTO `QUESTIONS`(`Question`, `Answer`, `Subject`, `Difficulty`, `Points`) VALUES ".$data;
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
		  INSERT INTO `QUESTIONS`(`Question`, `Answer`, `Subject`, `Difficulty`, `QType`, `AnsID`) VALUES ('What is the running time in the best case of Insertion Sort?', '{"O(1)","O(n)","O(n^2)","O(n^3)"}', 'CS610', 2, 1, 1, 20)
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
                        'Points'=>$row['Points']
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
?> 