<?php

  $input = file_get_contents('php://input');
  $json  = json_decode($input);

  $problem = $json -> {"problem"};

  $answer  = $json -> {"answer"};
  $correct = "False";

  error_reporting(E_ALL);
  ini_set("display_errors", 1);

  $result = shell_exec("python 2>&1 - <<EOD
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
