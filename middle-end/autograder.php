<?php

  $input = file_get_contents('php://input');
  $json  = json_decode($input);

  $problem  = $json -> {"studentAnswer"};
  $answer   = $json -> {"actualAnswer"};
  $testCase = $json -> {"autoGrade"};

  $correct = "False";

  $problem =  $problem . "\n\nif __name__ == \"__main__\":\n\tprint(" . $testCase . ", end='')";

  error_reporting(E_ALL);
  ini_set("display_errors", 1);

  $result = shell_exec("/afs/cad/sw.common/bin/python3 2>&1 - <<EOD
$problem

EOD");


  if($answer == $result) {
    $correct = "True";
  }

  $response = array(
    "correct" => $correct,
    "result"  => $result
  );

  echo json_encode($response);

?>
