 <?php
 	// Set content type to JSON
	header("Content-type: application/json");

	//$arrayTest = array(1, 2, 3, 4);
	//$keywords = preg_split("/;/", $arrayTest);
	//print_r($keywords);



	/*foreach ($arrayTest as $value) {
	    // $arr[3] will be updated with each value from $arr...
	    echo $value;
	}*/

	$operID=(string)$json->{"operationID"};

	// SQL Credentials
	$servername = "sql2.njit.edu";
	$username = "jmb75";
	$password = "4AU1Ydkiz";
	$dbname = "jmb75";

	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}

	// Create JSON object to send, with username and password to query DB
 	$json = array();
	$json["operationID"] = 16;
	$json["Subject"] = "2";
	$json["Difficulty"] = "1";
	$json["Keyword"] = "function";

	filterQuestions($conn, $json);

	function filterQuestions($conn, $json) {
		$Subject=(string)$json->{"Subject"};
		$Difficulty=(string)$json->{"Difficulty"};
		$data=(string)$json->{"Keyword"};
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

			if ($data != "" || $Subject != "" || $Difficulty != "") {
				$keyWResult = true;
				$subjResult = false;
				$diffResult = false;

				if ($data != "") { // if match found, add item to qid Selection
					$keyWResult = strpos($prompt, $data); // if this is true, keyword was found
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
	}
?> 