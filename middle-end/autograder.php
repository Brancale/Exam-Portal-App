<?php

  $input = file_get_contents('php://input');
  $json  = json_decode($input);

  $response    = $json -> {"studentAnswer"};     //Student Response
  $answer      = $json -> {"actualAnswer"};      //Array of Answers
  $testCase    = $json -> {"autoGrade"};         //Array of TestCases
  $constraints = $json -> {"constraints"};       //Array of Constraints
  $points      = $json -> {"points"};            //Points per question

  $testCasePoints   = intval($points) * 3/4;
  $constraintPoints = intval($points) * 1/4;

  $autogradeItems   = explode("|*|", $testCase);
  $autogradeAnswers = explode("|*|", $answer);

  $constraints = explode("|*|", $constraints);

  error_reporting(E_ALL);
  ini_set("display_errors", 1);

  $feedback = "";

  // Evaluation of Constraints

  // Function Name Checking
  $functionCall = array();
  preg_match("/def (.+)\(.*\):/", $response, $functionCall);
  $functionCall = $functionCall[1];

  $expectedCall = array();
  preg_match("/(.+)\(.*\)/", $autogradeItems[0], $expectedCall);
  $expectedCall = $expectedCall[1];

  if($functionCall != $expectedCall) {
    $points = $points - $constraintPoints/(sizeof($constraints) + 1);
    $response = preg_replace("/def (.*)\(/", "def " . $expectedCall . "(", $response);

    $feedback = $feedback . "Incorrect Function Name: " . $functionCall . "\n"
                          . "Execting Function Name " . $expectedCall . "\n"
                          . "Points Deducted: " . $constraintPoints/(sizeof($constraints) + 1) . "\n\n";
  } else {
    $feedback = $feedback . "Constraint Passed: Valid Function Name: " . $functionCall . "\n"
                          . "Points Awarded: ". $constraintPoints/(sizeof($constraints) + 1) . "\n\n";
  }

  // Return Calls
  if(!preg_match("/return/", $response)) {
    $points = $points - $constraintPoints/(sizeof($constraints) + 1);
    $response = str_replace("print(", "return(", $response);
    $feedback = $feedback . "No Function Return" . "\n"
                          . "Points Deducted: "  . $constraintPoints/(sizeof($constraints) + 1) . "\n\n";
  } else {
    $feedback = $feedback . "Constraint Passed: Function Returns a Value" . "\n"
                          . "Points Awarded: ". $constraintPoints/(sizeof($constraints) + 1) . "\n\n";
  }

  // Constraint Checking - for/while loop and if/elif/else
  foreach($constraints as $constraint) {
    switch ($constraint) {
      case 1:
        if(!preg_match("/for.*:/", $response) && !preg_match("/while.*:/", $response)) {
          $points = $points - $constraintPoints/(sizeof($constraints) + 1);
          $feedback = $feedback . "Expected use of 'for' loop or 'while' loop" . "\n"
          . "No 'for' loop or 'while' Found" . "\n"
          . "Points Deducted: " . $constraintPoints/(sizeof($constraints) + 1) . "\n\n";
        } else {
          $feedback = $feedback . "Constraint Passed: Function uses Loop" . "\n"
                                . "Points Awarded: ". $constraintPoints/(sizeof($constraints) + 1) . "\n\n";
        }
        break;

      case 2:
        if(!preg_match("/if.*:/", $response)) {
          $points = $points - $constraintPoints/(sizeof($constraints) + 1);
          $feedback = $feedback . "Expected use of Conditional Statements" . "\n"
          . "No 'if', 'elif', or 'else' Found" . "\n"
          . "Points Deducted: " . $constraintPoints/(sizeof($constraints) + 1) . "\n\n";
        } else {
          $feedback = $feedback . "Constraint Passed: Function uses Conditional Blocks" . "\n"
                                . "Points Awarded: ". $constraintPoints/(sizeof($constraints) + 1) . "\n\n";
        }
        break;
    }
  }

  //Evaluation Via Test Cases

  $i = 0;
  foreach($autogradeItems as $item) {
    $runcase =  $response . "\n\nif __name__ == \"__main__\":\n\tprint(" . $item . ", end='')";
      $result = shell_exec("/afs/cad/sw.common/bin/python3 2>&1 - <<EOD
$runcase

EOD");

    if($result != $autogradeAnswers[$i]) {
      $points = $points - $testCasePoints/sizeof($autogradeItems);
      $feedback = $feedback . "Test Case " . ($i+1) . " Failed.\n"
                            . "Expected "  . $autogradeAnswers[$i] . " for " . $autogradeItems[$i] . "\n"
                            . "Received "  . $result . "\n"
                            . "Points Deducted: " . $testCasePoints/sizeof($autogradeItems) . "\n\n";
    } else {
      $feedback = $feedback . "Test Case " . ($i+1) . " has Passed ". "\n"
      . "Points Awarded: ". $testCasePoints/sizeof($autogradeItems) . "\n\n";
    }
    $i++;
  }

// Return Responses

  $response = array(
    "points" => $points,
    "feedback"  => $feedback
  );

  echo json_encode($response);

?>
