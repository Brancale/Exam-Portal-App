<?php
  $input = file_get_contents('php://input');
  $json  = json_decode($input);
  $problem  = "def maxArray(arr):\n\tmax_ = arr[0]\n\tfor item in arr:\n\t\tif item > max_:\n\t\t\tmax_ = item\n\treturn max_";
  $answer   = "12";
  $testCase = "maxArray([1,6,2,5,12])";
  $correct = "False";
  $problem =  "print(hello)\n, end='')";
  error_reporting(E_ALL);
  ini_set("display_errors", 1);
  $result = shell_exec("/afs/cad/sw.common/bin/python3 2>&1 - <<EOD
$problem
EOD");
  if($answer == "hello") {
    $correct = "True";
  }
  $response = array(
    "correct" => $correct,
    "result"  => $result
  );
  echo ($response[$result]);
?>