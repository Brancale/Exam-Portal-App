 <?php
 	// Set content type to JSON
	header("Content-type: application/json");

	# Get Post Request From mid-end
	$input=file_get_contents('php://input');
	$json=json_decode($input);
	$operID=(string)$json->{"operationID"};

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
		$data=(string)$json->{"data"};
		addQuestion($conn, $data);
	} elseif($operID == '3') {
		$data=(string)$json->{"data"};
		addExam($conn, $data);
	} elseif($operID == '4') {
		$data=(string)$json->{"data"};
		getExam($conn, $data);
	} elseif($operID == '5') {
		submitExam($conn, $json);
	} elseif($operID == '6') {
		$data=(string)$json->{"data"};
		getAllExams($conn, $data);
	} elseif($operID == '7') {
		gradeExam($conn, $json);
	} elseif($operID == '8') {
		viewSubmittedExams($conn);
	} elseif($operID == '9') {
		getStudentExam($conn, $json);
	} elseif($operID == '10') {
		addComments($conn, $json);
	} else {
		$response["success"] = "invalid input option";
		
		// Respond with JSON object
		$json_response = json_encode($response);
		echo $json_response;
	}

	// Close DB connection
	$conn->close();

	function addComments($conn, $json) {
		$SexamID=(string)$json->{"SExamID"};
		$instComments=(string)$json->{"Comments"};

		$commentsStr = '';

		foreach ($instComments as $instCom) {
			$commentsStr = $commentsStr.';\"'.$instCom.'\"';
		}

		$commentsStr = rtrim($commentsStr,"; ");

		$response = array();

		$sqlQ = "UPDATE `STUDENT` SET `Comments` = ".$commentsStr." WHERE `SExamID` = ".$SexamID;
		if($conn->query($sqlQ)) {
			$response["success"] = "true";
		} else {
			$response["success"] = "false";
		}
		
		// Respond with JSON object
		$json_response = json_encode($response);
		echo $json_response;
	}

	function viewSubmittedExams($conn) {
		//(`SExamID`, `EID`, `SName`, `Answers`, `Score`, `Comments`)

		// Query DB
		$sql = "SELECT * FROM `STUDENT`";
		$result = $conn->query($sql);

		$response = array();
		$count = 0;

		// Check resulting records and read.
		if ($result->num_rows > 0) {
			$response["success"] = "true";
		    while($row = $result->fetch_assoc()) {
		        $response['obj'.$count] = array(
                        'SExamID'=>$row['SExamID'],
                        'EID'=>$row['EID'],
                        'SName'=>$row['SName'],
                        'Answers'=>$row['Answers'],
                        'Score'=>$row['Score'],
                        'Comments'=>$row['Comments']
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

	function getStudentExam($conn, $json) {
		$SexamID=(string)$json->{"SExamID"};
		// Query DB
		$sql = "SELECT * FROM `STUDENT` WHERE SExamID = '".$SexamID."'";
		$result = $conn->query($sql);

		$response = array();

		// Check resulting records and read.
		if ($result->num_rows > 0) {
			$response["success"] = "true";
		    $row = $result->fetch_assoc();
	        $response['obj'.$count] = array(
                    'SExamID'=>$row['SExamID'],
                    'EID'=>$row['EID'],
                    'SName'=>$row['SName'],
                    'Answers'=>$row['Answers'],
                    'Score'=>$row['Score'],
                    'Comments'=>$row['Comments']
                            );
		} else {
			$response["success"] = "false";
		}
		
		// Respond with JSON object
		$json_response = json_encode($response);
		echo $json_response;
	}

	function submitExam($conn, $json) {
		$examID=(string)$json->{"EID"};
		$username=(string)$json->{"SName"};
		$studentAns=$json->{"Answers"};

		// Query DB
		$sql = "INSERT INTO `STUDENT`(`EID`, `SName`, `Answers`) VALUES ('".$examID."','".$username."','".$studentAns."')";
		if($conn->query($sql)) {
			$response["success"] = "true";
			$response["EID"] = $examID;
			$response["SName"] = $username;
			$response["Answers"] = $studentAns;
		} else {
			$response["success"] = "false";
		}

		// Respond with JSON object
		$json_response = json_encode($response);
		echo $json_response;
	}

	function gradeExam($conn, $json) {
		$response["success"] = "false";

		$SexamID=(string)$json->{"SExamID"};
		$examID = '';
		$studentAns=array();

		$response["test"] = $SexamID;

		$sqlQ = "SELECT * FROM `STUDENT` WHERE SExamID = '".$SexamID."'";
		$resultQ = $conn->query($sqlQ);
		if ($resultQ->num_rows > 0) {
			$rowQ = $resultQ->fetch_assoc();
			$studentAns = explode("|*|", $rowQ['Answers']);
			$examID = $rowQ['EID'];
	    }

	    $response["test"] = 1;

		$qidArr = array();
		$actualAns = array();
		$pointsArr = array();
		$gradeArr = array();
		$totalPts = 0;
		$stdPts = 0;

		$count = 0;
		
		$sqlQ = "SELECT * FROM `EXAM` WHERE EID = '".$examID."'";
		$resultQ = $conn->query($sqlQ);
		if ($resultQ->num_rows > 0) {
			$rowQ = $resultQ->fetch_assoc();
			$qidArr = explode("|*|", $rowQ['Questions']);
	    }

	    foreach ($qidArr as $qidA) {
		    $sqlQ = "SELECT * FROM `QUESTIONS` WHERE QID = '".$qidA."'";
			$resultQ = $conn->query($sqlQ);
			
			if ($resultQ->num_rows > 0) {
				$rowQ = $resultQ->fetch_assoc();

				$actualAns[$count] = $rowQ['Answer'];
				$pointsArr[$count] = $rowQ['Points'];
				$gradeArr[$count] = $rowQ['Autograde'];
				$totalPts = $totalPts + (int)$rowQ['Points'];

		        $count = $count + 1;
		    }

		}

	    $count = 0;

		foreach ($studentAns as $stdans) {
		    // Create JSON object to send, with username and password to query DB
		 	$jsonData = array();
			$jsonData["studentAnswer"] = $stdans;
			$jsonData["actualAnswer"] = $actualAns[$count];
			$jsonData["autoGrade"] = $gradeArr[$count];
			$jsonDataEncoded = json_encode($jsonData);

			// Initialize cURL
			$postRequest = curl_init();
			$url = "https://web.njit.edu/~sh424/autograder.php";
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
			$isCorrect = $jsonA["correct"];

			if ($isCorrect == "True") {
				$stdPts = $stdPts + $pointsArr[$count];
			}

			$count = $count + 1;
		}

		$scoreVal = (double)$stdPts / (double)$totalPts;
		$response["score"] = $scoreVal * 100.0;

		$sqlQ = "UPDATE `STUDENT` SET `Score` = ".$scoreVal." WHERE `SExamID` = ".$SexamID;
		if($conn->query($sqlQ)) {
			$response["success"] = "true";
		}

		// Respond with JSON object
		$json_response = json_encode($response);
		echo $json_response;
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
		        $response['obj'.$count] = array(
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
	        $questionIDs = explode("|*|", $questionArr);

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

					$response['obj'.$count] = array(
	                        'QID'=>$rowQ['QID'],
	                        'Question'=>$rowQ['Question'],
	                        'Subject'=>$rowQ['Subject'],
	                        'Difficulty'=>$rowQ['Difficulty']
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
		//INSERT INTO `EXAM`(`Questions`, `AnsKey`, `OpenTime`, `EndTime`, `Points`) VALUES ('1|*|2','Hello World!|*|1 1 2 3 5',1543640400000,1545368400000)
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
		$sql = "INSERT INTO `QUESTIONS`(`Question`, `Answer`, `Subject`, `Difficulty`, `Points`, `Autograde`) VALUES ".$data;
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
		        $response['obj'.$count] = array(
                        'QID'=>$row['QID'],
                        'Question'=>$row['Question'],
                        'Answer'=>$row['Answer'],
                        'Subject'=>$row['Subject'],
                        'Difficulty'=>$row['Difficulty'],
                        'Points'=>$row['Points'],
                        'Autograde'=>$row['Autograde']
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