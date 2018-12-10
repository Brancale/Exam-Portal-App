 <?php
 	// Set content type to JSON
	header("Content-type: application/json");

	# Get Post Request From mid-end
	$input=file_get_contents('php://input');
	$json=json_decode($input);
	$operID=(string)$json->{"operationID"};

	// SQL Credentials
	$servername = "";
	$username = "";
	$password = "";
	$dbname = "";

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
		getAllExams($conn);
	} elseif($operID == '7') {
		gradeExam($conn, $json);
	} elseif($operID == '8') {
		viewSubmittedExams($conn);
	} elseif($operID == '9') {
		getStudentExam($conn, $json);
	} elseif($operID == '10') {
		addComments($conn, $json);
	} elseif($operID == '11') {
		$data=(string)$json->{"data"};
		viewComments($conn, $data);
	} elseif($operID == '12') {
		changeGrade($conn, $json);
	} elseif($operID == '13') {
		editQuestion($conn, $json);
	} elseif($operID == '14') {
		deleteQuestion($conn, $json);
	} elseif($operID == '15') {
		$data=(string)$json->{"data"};
		getQuestionID($conn, $data);
	} elseif($operID == '16') {
		filterQuestions($conn, $json);
	} elseif($operID == '17') {
		stdExamsNotTaken($conn, $json);
	} elseif($operID == '18') {
		isOverwritten($conn, $json);
	} else {
		$response["success"] = "invalid input option";

		// Respond with JSON object
		$json_response = json_encode($response);
		echo $json_response;
	}

	// Close DB connection
	$conn->close();

  function isOverwritten($conn, $json) {
    $SexamID=(string)$json->{"SExamID"};

    // Query DB
    $sql = "SELECT `gradeChanged` FROM `STUDENT` WHERE `SExamID` = ".$SexamID;
    $result = $conn->query($sql);

    $response = array();
    $response["sql"] = $sql;

    // Check resulting records and read.
    if ($result->num_rows > 0) {
        $response["success"] = "true";
        $row = $result->fetch_assoc();

        // Check if gradeChanged value is not 0.
        if ((string)$row['gradeChanged'] != '0') {
          // if not 0, grade was changed
          $response["isOverwritten"] = "true";
        } else {
          // if 0, grade was not changed
          $response["isOverwritten"] = "false";
        }
    } else {
      $response["success"] = "false";
      $response["isOverwritten"] = "false";
    }

    // Respond with JSON object
    $json_response = json_encode($response);
    echo $json_response;
  }

	function stdExamsNotTaken($conn, $json) {
		$data=(string)$json->{"data"};

		// Query DB
		$sql = "SELECT `EID` FROM `EXAM` WHERE `EID` NOT IN (SELECT `EID` FROM `STUDENT` WHERE `SName` = '".$data."')";
		$result = $conn->query($sql);

		$response = array();
		$count = 0;

		// Check resulting records and read.
		if ($result->num_rows > 0) {
			$response["success"] = "true";
		    while($row = $result->fetch_assoc()) {

		        $response['obj'.$count] = array(
                        'EID'=>$row['EID']
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

	function deleteQuestion($conn, $json) {
		$questionID=(string)$json->{"QID"};
		// Query DB
		$sql1 = "DELETE FROM `QUESTIONS` WHERE QID = ".$questionID;

		$response["sql1"] = $sql1;
		if($conn->query($sql1)) {
			$response["success"] = "true";
		} else {
			$response["success"] = "false";
		}

		// Respond with JSON object
		$json_response = json_encode($response);
		echo $json_response;
	}

	function editQuestion($conn, $json) {
		$questionID=(string)$json->{"QID"};
		$Question=(string)$json->{"Question"};
		$Answer=(string)$json->{"Answer"};
		$Subject=(string)$json->{"Subject"};
		$Difficulty=(string)$json->{"Difficulty"};
		$Autograde=(string)$json->{"Autograde"};
		$Constraints=(string)$json->{"Constraints"};
		// Query DB
		$sql1 = "UPDATE `QUESTIONS` SET `Question` = \"".$Question."\" WHERE QID = ".$questionID;
		$sql2 = "UPDATE `QUESTIONS` SET `Answer` = \"".$Answer."\" WHERE QID = ".$questionID;
		$sql3 = "UPDATE `QUESTIONS` SET `Subject` = '".$Subject."' WHERE QID = ".$questionID;
		$sql4 = "UPDATE `QUESTIONS` SET `Difficulty` = '".$Difficulty."' WHERE QID = ".$questionID;
		$sql5 = "UPDATE `QUESTIONS` SET `Autograde` = \"".$Autograde."\" WHERE QID = ".$questionID;
		$sql6 = "UPDATE `QUESTIONS` SET `Constraints` = '".$Constraints."' WHERE QID = ".$questionID;

		$response["sql1"] = $sql1;
		$response["sql2"] = $sql2;
		$response["sql3"] = $sql3;
		$response["sql4"] = $sql4;
		$response["sql5"] = $sql5;
		$response["sql6"] = $sql6;
		if($conn->query($sql1) && $conn->query($sql2) && $conn->query($sql3) && $conn->query($sql4) && $conn->query($sql5) && $conn->query($sql6)) {
			$response["success"] = "true";
		} else {
			$response["success"] = "false";
		}

		// Respond with JSON object
		$json_response = json_encode($response);
		echo $json_response;
	}

	function changeGrade($conn, $json) {

		$SexamID=(string)$json->{"SExamID"};
		$newPoints=(string)$json->{"PointsPerProblem"};

		$sqlQ = "UPDATE `STUDENT` SET `PointsPerProblem` = '".$newPoints."' WHERE `SExamID` = ".$SexamID;

		if($conn->query($sqlQ)) {
			$stdPoints = explode("|*|", $newPoints);
			$examID = '';

			// Pulls student exam Exam ID from DB
			$sqlQ = "SELECT * FROM `STUDENT` WHERE SExamID = '".$SexamID."'";
			$resultQ = $conn->query($sqlQ);
			if ($resultQ->num_rows > 0) {
				$rowQ = $resultQ->fetch_assoc();
				$examID = $rowQ['EID'];
		    }

			// create values for total points earned and total exam points
			$totalPts = 0;
			$count = 0;

			// Gets all question IDs from Exam table
			$sqlQ = "SELECT * FROM `EXAM` WHERE EID = '".$examID."'";
			$resultQ = $conn->query($sqlQ);
			if ($resultQ->num_rows > 0) {
				$rowQ = $resultQ->fetch_assoc();
				$totalPts = $rowQ['TotalPoints'];
		    }

		    $earnedPts = 0;

			foreach ($stdPoints as $pointVal) {
				$earnedPts = $earnedPts + $pointVal;
			}
			$scoreVal = (double)$earnedPts; // (double)$totalPts)  * 100.0;
			$scoreVal = round($scoreVal,2);
			$response["score"] = $scoreVal;

			$sqlQ1 = "UPDATE `STUDENT` SET `Score` = ".$scoreVal." WHERE `SExamID` = ".$SexamID;
      $sqlQ2 = "UPDATE `STUDENT` SET `gradeChanged` = 1 WHERE `SExamID` = ".$SexamID;

			if($conn->query($sqlQ1) && $conn->query($sqlQ2)) {
				$response["success"] = "true";
			}
		} else {
			$response["success"] = "false";
		}

		// Respond with JSON object
		$json_response = json_encode($response);
		echo $json_response;
	}

	function viewComments($conn, $data) {
		$SexamID=(string)$data;

		$response = array();

		$sqlQ = "SELECT `Comments` FROM `STUDENT` WHERE `SExamID` = '".$SexamID."'";
		if($result = $conn->query($sqlQ)) {
			$response["success"] = "true";
			if ($result->num_rows > 0) {
				$row = $result->fetch_assoc();
				$commentList = explode("|*|", $row['Comments']);

		        $count = 0;

		        foreach ($commentList as $comment) {
					$response['obj'.$count] = $comment;
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

	function addComments($conn, $json) {
		$SexamID=(string)$json->{"SExamID"};
		$instComments=(string)$json->{"Comments"};

		$response = array();

		$sqlQ = "UPDATE `STUDENT` SET `Comments` = '".$instComments."' WHERE `SExamID` = '".$SexamID."'";
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

		// Query DB
		$sql = "SELECT * FROM `STUDENT`";
		$result = $conn->query($sql);

		$response = array();
		$count = 0;

		// Check resulting records and read.
		if ($result->num_rows > 0) {
			$response["success"] = "true";
		    while($row = $result->fetch_assoc()) {
		    	$eid = $row['EID'];

		        // Query DB
				$sql2 = "SELECT * FROM `EXAM` WHERE EID = '".$eid."'";
				$result2 = $conn->query($sql2);

			    $row2 = $result2->fetch_assoc();
			    $totalPts = $row2['TotalPoints'];

		        $response['obj'.$count] = array(
                        'SExamID'=>$row['SExamID'],
                        'EID'=>$row['EID'],
                        'SName'=>$row['SName'],
                        'Answers'=>$row['Answers'],
                        'Score'=>$row['Score'],
                        'Comments'=>$row['Comments'],
	                    'Breakdown'=>$row['Breakdown'],
	                    'PointsPerProblem'=>$row['PointsPerProblem'],
	                    'OrigPointsPerProblem'=>$row['OrigPointsPerProblem'],
	                    'OrigScore'=>$row['OrigScore'],
	                    'TotalPoints'=>$totalPts
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
			$eid = $row['EID'];

	        // Query DB
			$sql2 = "SELECT * FROM `EXAM` WHERE EID = '".$eid."'";
			$result2 = $conn->query($sql2);

		    $row2 = $result2->fetch_assoc();
		    $totalPts = $row2['TotalPoints'];
            $questionArr = $row2['Questions'];
            $pointsPerQArr = $row2['PointsPerQuestion'];
	        $questionIDs = explode("|*|", $questionArr);
	        $promptArr = array();
	        $AnswerArr = array();

	        $count = 0;
	        $maxCount = 0;

	        foreach ($questionIDs as $questid => $value) {
	        	$maxCount = $maxCount + 1;
			}

	        foreach ($questionIDs as $questid => $value) {

				$sqlQ = "SELECT * FROM `QUESTIONS` WHERE QID = '".$value."'";
				$resultQ = $conn->query($sqlQ);
				if ($resultQ->num_rows > 0) {
					$rowQ = $resultQ->fetch_assoc();

					if ($count < $maxCount - 1) {
						$promptArr = $promptArr.$rowQ['Question']."|*|";
	                	$AnswerArr = $AnswerArr.$rowQ['Answer']."|/|";
					} else {
						$promptArr = $promptArr.$rowQ['Question'];
	                	$AnswerArr = $AnswerArr.$rowQ['Answer'];
					}

			        $count = $count + 1;
			    }
			}

			$response['obj'] = array(
                    'SExamID'=>$row['SExamID'],
                    'EID'=>$row['EID'],
                    'SName'=>$row['SName'],
                    'Answers'=>$row['Answers'],
                    'Score'=>$row['Score'],
                    'Comments'=>$row['Comments'],
                    'Breakdown'=>$row['Breakdown'],
                    'PointsPerProblem'=>$row['PointsPerProblem'],
                    'OrigPointsPerProblem'=>$row['OrigPointsPerProblem'],
                    'OrigScore'=>$row['OrigScore'],
                    'QIDs'=>$questionArr,
                    'Questions'=>$promptArr,
                    'ActualAnswers'=>$AnswerArr,
                    'PointsPerQuestion'=>$pointsPerQArr,
                    'TotalPoints'=>$totalPts
                            );
		} else {
			$response["success"] = "false";
		}

		// Respond with JSON object
		$json_response = json_encode($response);
		echo $json_response;
	}

	function submitExam($conn, $json) {
		$operID=(string)$json->{"operationID"};
		$examID=(string)$json->{"EID"};
		$username=(string)$json->{"SName"};
		$studentAns=$json->{"Answers"};

		$response["operationID"] = $operID;
		$response["EID"] = $examID;
		$response["SName"] = $username;
		$response["Answers"] = $studentAns;

		// Query DB
		$sql = "INSERT INTO `STUDENT`(`EID`, `SName`, `Answers`) VALUES ('".$examID."','".$username."',\"".$studentAns."\")";

		$response["sql"] = $sql;
		if($conn->query($sql)) {
			$response["success"] = "true";
		} else {
			$response["success"] = "false";
		}

		// Respond with JSON object
		$json_response = json_encode($response);
		echo $json_response;
	}

	function gradeExam($conn, $json) {
		$response["success"] = "false";

		// Student Exam ID is passed in
		$SexamID=(string)$json->{"SExamID"};
		$examID = '';
		$studentAns=array();

		$response["test"] = $SexamID;

		// Pulls student exam Answers and Exam ID from DB
		$sqlQ = "SELECT * FROM `STUDENT` WHERE SExamID = '".$SexamID."'";
		$resultQ = $conn->query($sqlQ);
		if ($resultQ->num_rows > 0) {
			$rowQ = $resultQ->fetch_assoc();
      $studentAns = explode("|*|", $rowQ['Answers']);
			$examID = $rowQ['EID'];
	  }

	    // create arrays for Question IDs, actual answers, points, autograde values (gradeArr)
		$qidArr = array();
		$actualAns = array();
		$pointsArr = array();
		$gradeArr = array();
		$constraintArr = array();

		// create values for total points earned and total exam points
		$totalPts = 0;
		$stdPts = 0;
		$count = 0;

		// Gets all question IDs from Exam table
		$sqlQ = "SELECT * FROM `EXAM` WHERE EID = '".$examID."'";
		$resultQ = $conn->query($sqlQ);
		if ($resultQ->num_rows > 0) {
			$rowQ = $resultQ->fetch_assoc();
			$qidArr = explode("|*|", $rowQ['Questions']);
			$pointsArr = explode("|*|", $rowQ['PointsPerQuestion']);
			$totalPts = $rowQ['TotalPoints'];
	    }

	    // Once we have all questions, get the actuals answers
	    foreach ($qidArr as $qidA) {
		    $sqlQ = "SELECT * FROM `QUESTIONS` WHERE QID = '".$qidA."'";
			$resultQ = $conn->query($sqlQ);

			if ($resultQ->num_rows > 0) {
				$rowQ = $resultQ->fetch_assoc();

				$actualAns[$count] = $rowQ['Answer'];

				$autogradeVal = (string)$rowQ['Autograde'];
				if ($autogradeVal == "For Loops") {
					$autogradeVal = 1;
				} else if ($autogradeVal == "While Loops") {
					$autogradeVal = 3;
				} else if ($autogradeVal == "Recursion") {
					$autogradeVal = 4;
				} else if ($autogradeVal == "Constraints") {
					$autogradeVal = 2;
				} else if ($autogradeVal == "None") {
					$autogradeVal = 0;
				}

				$gradeArr[$count] = $autogradeVal;
				$constraintArr[$count] = $rowQ['Constraints'];

		        $count = $count + 1;
		    }

		}

		$maxCount = $count;
	    $count = 0;
	    $breakdownStr = "";
	    $pointsPerProblemStr = "";

	    // Start grading process
		foreach ($studentAns as $stdans) {

			// Loop through autograde items

		    // Create JSON object to send, with username and password to query DB
		 	$jsonData = array();
			$jsonData["studentAnswer"] = $stdans;
			$jsonData["actualAnswer"] = $actualAns[$count];
			$jsonData["autoGrade"] = $gradeArr[$count];
			$jsonData["contsraints"] = $constraintArr[$count];
			$jsonData["points"] = $pointsArr[$count];
			$jsonDataEncoded = json_encode($jsonData);
      $response["jsondata"] = $jsonData;

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
			$pointsEarned = $jsonA["points"];
			$scoreBreakdown = $jsonA["feedback"];
      $response["jsonA"] = $jsonA;
      $response["feedback"] = $jsonA["feedback"];

			$stdPts = $stdPts + $pointsEarned;
			if ($count < $maxCount - 1) {
				$breakdownStr = $breakdownStr.$scoreBreakdown."|*|";
				$pointsPerProblemStr = $pointsPerProblemStr.$pointsEarned."|*|";
			} else {
				$breakdownStr = $breakdownStr.$scoreBreakdown;
				$pointsPerProblemStr = $pointsPerProblemStr.$pointsEarned;
			}

			$count = $count + 1;
		}

		$scoreVal = (double)$stdPts; // (double)$totalPts)  * 100.0;
		$scoreVal = round($scoreVal,2);
		$response["grades"] = $gradeArr;
		$response["score"] = $scoreVal;
		$response["Breakdown"] = $breakdownStr;
		$response["PointsPerProblem"] = $pointsPerProblemStr;

		$sqlQ1 = "UPDATE `STUDENT` SET `Score` = ".$scoreVal." WHERE `SExamID` = ".$SexamID;
		$sqlQ2 = "UPDATE `STUDENT` SET `Breakdown` = \"".$breakdownStr."\" WHERE `SExamID` = ".$SexamID;
		$sqlQ3 = "UPDATE `STUDENT` SET `PointsPerProblem` = '".$pointsPerProblemStr."' WHERE `SExamID` = ".$SexamID;
		$sqlQ4 = "UPDATE `STUDENT` SET `OrigPointsPerProblem` = '".$pointsPerProblemStr."' WHERE `SExamID` = ".$SexamID;
		$sqlQ5 = "UPDATE `STUDENT` SET `OrigScore` = '".$scoreVal."' WHERE `SExamID` = ".$SexamID;

		$response["sqlQ2"] = $sqlQ2;
		if($conn->query($sqlQ1) && $conn->query($sqlQ3) && $conn->query($sqlQ4) && $conn->query($sqlQ5) && $conn->query($sqlQ2)) {
			$response["success"] = "true";
		}

		// Respond with JSON object
		/*$json_response = json_encode($response);
		echo $json_response;*/
    echo json_encode($response, JSON_UNESCAPED_SLASHES);
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
		        $response['obj'.$count] = array(
                        'EID'=>$row['EID'],
                        'TotalPoints'=>$row['TotalPoints']
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
            $examID = $row['EID'];
            $questionArr = $row['Questions'];
            $pointsPerQArr = $row['PointsPerQuestion'];
	        $questionIDs = explode("|*|", $questionArr);
	        $pointsPerQ = explode("|*|", $pointsPerQArr);

	        $count = 0;

	        foreach ($questionIDs as $questid => $value) {
				$sqlQ = "SELECT * FROM `QUESTIONS` WHERE QID = '".$value."'";
				$resultQ = $conn->query($sqlQ);
				if ($resultQ->num_rows > 0) {
					$rowQ = $resultQ->fetch_assoc();

					$response['obj'.$count] = array(
	                        'QID'=>$rowQ['QID'],
	                        'Question'=>$rowQ['Question'],
	                        'Subject'=>$rowQ['Subject'],
	                        'Difficulty'=>$rowQ['Difficulty'],
	                        'Points'=>$pointsPerQ[$count]
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
		$sql = "INSERT INTO `EXAM`(`Questions`, `PointsPerQuestion`, `TotalPoints`) VALUES ".$data;
		if($conn->query($sql)) {
			$response["success"] = "true";
		} else {
			$response["success"] = "false";
			$response["test"] = $sql;
		}

		// Respond with JSON object
		$json_response = json_encode($response);
		echo $json_response;
	}

	function addQuestion($conn, $data) {

		// Query DB
		$sql = "INSERT INTO `QUESTIONS`(`Question`, `Subject`, `Difficulty`, `Constraints`, `Autograde`, `Answer`) VALUES ".(string)$data;
		if($conn->query($sql)) {
			$response["success"] = "true";
		} else {
			$response["success"] = "false";
		}

		// Respond with JSON object
		$json_response = json_encode($response);
		echo $json_response;
	}

	function filterQuestions($conn, $json) {
		$Subject=(string)$json->{"Subject"};
		$Difficulty=(string)$json->{"Difficulty"};
		$data=(string)$json->{"Keyword"};

		if ($data != "" || $Subject != "" || $Difficulty != "") {
			// Query DB
			$sql = "SELECT * FROM `QUESTIONS`";
			$result = $conn->query($sql);

			$response = array();

			$count = 0;

			$qidArr = array();
			$qidSelectArr = array();
			$promptArr = array();
			$subjArr = array();
			$diffArr = array();

			$response["success"] = "false";

			// Check resulting records and read.
			if ($result->num_rows > 0) {
			    // Output each return row
			    while($row = $result->fetch_assoc()) {
			        $qidArr[$count] = $row['QID'];
	                $promptArr[$count] = $row['Question'];
	                $subjArr[$count] = $row['Subject'];
	                $diffArr[$count] = $row['Difficulty'];
			        $count = $count + 1;
			    }
			}

			$count = 0;
			$item = 0;


			foreach ($promptArr as $prompt) {


				$keyWResult = true;
				$subjResult = false;
				$diffResult = false;

				if ($data != "") { // if match found, add item to qid Selection
					if (strstr(strtolower($prompt), strtolower($data), false) != false) {
						$keyWResult = true; // if this is true, keyword was found
					} else {
						$keyWResult = false;
					}

				}

				if ($Subject == "" || $subjArr[$count] == $Subject) {
					$subjResult = true;
				} else {
					$subjResult = false;
				}

				if ($Difficulty == "" || $diffArr[$count] == $Difficulty) {
					$diffResult = true;
				} else {
					$diffResult = false;
				}

				if ($keyWResult && $subjResult && $diffResult) {
					$qidSelectArr[$item] = $qidArr[$count];
			    	$item = $item + 1;
				}

				$count = $count + 1;
			}

			$count = 0;

			foreach ($qidSelectArr as $qidQ) {
				$sql2 = "SELECT * FROM `QUESTIONS` WHERE QID = '".$qidQ."'";
				$result = $conn->query($sql2);

				// Check resulting records and read.
				if ($result->num_rows > 0) {
					$response["success"] = "true";
				    // Output each return row
				    while($row = $result->fetch_assoc()) {
				        $response['obj'.$count] = array(
		                        'QID'=>$row['QID'],
		                        'Question'=>$row['Question'],
		                        'Answer'=>$row['Answer'],
		                        'Subject'=>$row['Subject'],
		                        'Difficulty'=>$row['Difficulty'],
		                        'Autograde'=>$row['Autograde'],
		                        'Constraints'=>$row['Constraints']
		                                );
				        $count = $count + 1;
				    }
				}
			}
			// Respond with JSON object
			$json_response = json_encode($response);
			echo $json_response;
		} else {
			getQuestions($conn);
		}

	}

	function getQuestionID($conn, $data) {
		// Query DB
		$sql = "SELECT * FROM `QUESTIONS` WHERE QID = '".$data."'";
		$result = $conn->query($sql);

		$response = array();

		$count = 0;

		// Check resulting records and read.
		if ($result->num_rows > 0) {
		    $response["success"] = "true";
		    // Output each return row
		    while($row = $result->fetch_assoc()) {
		        $response['obj'.$count] = array(
                        'QID'=>$row['QID'],
                        'Question'=>$row['Question'],
                        'Answer'=>$row['Answer'],
                        'Subject'=>$row['Subject'],
                        'Difficulty'=>$row['Difficulty'],
                        'Autograde'=>$row['Autograde'],
                        'Constraints'=>$row['Constraints']
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

	function getQuestions($conn) {
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
		        $response['obj'.$count] = array(
                        'QID'=>$row['QID'],
                        'Question'=>$row['Question'],
                        'Answer'=>$row['Answer'],
                        'Subject'=>$row['Subject'],
                        'Difficulty'=>$row['Difficulty'],
                        'Autograde'=>$row['Autograde'],
                        'Constraints'=>$row['Constraints']
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
