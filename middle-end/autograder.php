<?php

  $input = file_get_contents('php://input');
  $json  = json_decode($input);

  $response    = $json -> {"studentAnswer"};     //Student Response
  $answer      = $json -> {"actualAnswer"};      //Array of Answers
  $testCase    = $json -> {"autoGrade"};         //Array of TestCases
  $constraints = $json -> {"constraints"};       //Array of Constraints
  $points      = $json -> {"points"};            //Points per question

  $points = intval($points);
  $testCasePoints   = $points * 3/4;
  $constraintPoints = $points * 1/4;

  $autogradeItems   = explode("|*|", $testCase);
  $autogradeAnswers = explode("|*|", $answer);

  $constraints = explode("|*|", $constraints);

  #error_reporting(E_ALL);
  #ini_set("display_errors", 1);

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
    $points = $points - $constraintPoints/(sizeof($constraints) + 2);
    $response = preg_replace("/$functionCall\(/", $expectedCall . "(", $response);

    $feedback = $feedback . "Incorrect Function Name: " . $functionCall . "\n"
                          . "Expected Function Name: " . $expectedCall . "\n"
                          . "Points Deducted: " . round($constraintPoints/(sizeof($constraints) + 2), 2) . "\n\n";
  } else {
    $feedback = $feedback . "Constraint Passed: Valid Function Name: " . $functionCall . "\n"
                          . "Points Awarded: ". round($constraintPoints/(sizeof($constraints) + 2), 2) . "\n\n";
  }

  // Return Calls
  if(!preg_match("/return/", $response)) {
    $points = $points - $constraintPoints/(sizeof($constraints) + 2);
    $response = str_replace("print(", "return(", $response);
    $feedback = $feedback . "No Function Return" . "\n"
                          . "Points Deducted: "  . round($constraintPoints/(sizeof($constraints) + 2), 2) . "\n\n";
  } else {
    $feedback = $feedback . "Constraint Passed: Function Returns a Value" . "\n"
                          . "Points Awarded: ". round($constraintPoints/(sizeof($constraints) + 2), 2) . "\n\n";
  }

  // Constraint Checking - for/while loop and if/elif/else
  foreach($constraints as $constraint) {
    switch ($constraint) {
      case 1:
        if(!preg_match("/for.*:/", $response)) {
          $points = $points - $constraintPoints/(sizeof($constraints) + 2);
          $feedback = $feedback . "Expected use of 'for' loop" . "\n"
          . "No 'for' loop found" . "\n"
          . "Points Deducted: " . round($constraintPoints/(sizeof($constraints) + 2), 2) . "\n\n";
        } else {
          $feedback = $feedback . "Constraint Passed: Function uses 'for' loop" . "\n"
                                . "Points Awarded: ". $constraintPoints/(sizeof($constraints) + 2) . "\n\n";
        }
        break;

      case 2:
        if(!preg_match("/if.*:/", $response)) {
          $points = $points - round($constraintPoints/(sizeof($constraints) + 2), 2);
          s;

          $feedback = $feedback . "Expected use of Conditional Statements" . "\n"
          . "No 'if', 'elif', or 'else' found" . "\n"
          . "Points Deducted: " . round($constraintPoints/(sizeof($constraints) + 2), 2) . "\n\n";
        } else {
          $feedback = $feedback . "Constraint Passed: Function uses conditional blocks" . "\n"
                                . "Points Awarded: ". round($constraintPoints/(sizeof($constraints) + 2), 2) . "\n\n";
        }
        break;

        case 3:
          if(!preg_match("/while.*:/", $response)) {
            $points = $points - $constraintPoints/(sizeof($constraints) + 2);
            $feedback = $feedback . "Expected use of 'while' loop" . "\n"
            . "No 'while' found" . "\n"
            . "Points Deducted: " . round($constraintPoints/(sizeof($constraints) + 2), 2) . "\n\n";
          } else {
            $feedback = $feedback . "Constraint Passed: Function uses 'while' loop" . "\n"
                                  . "Points Awarded: ". round($constraintPoints/(sizeof($constraints) + 2), 2) . "\n\n";
          }
          break;

        case 4:
          if(substr_count($response, $expectedCall . "(") < 2) {
            $points = $points - $constraintPoints/(sizeof($constraints) + 2);
            $feedback = $feedback . "Expected use of recursion" . "\n"
            . "No recursion used" . "\n"
            . "Points Deducted: " .  round($constraintPoints/(sizeof($constraints) + 2), 2) . "\n\n";
          } else {
            $feedback = $feedback . "Constraint Passed: Function uses recursion" . "\n"
                                  . "Points Awarded: ". round($constraintPoints/(sizeof($constraints) + 2), 2) . "\n\n";
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
      $points = $points - round($testCasePoints/sizeof($autogradeItems), 2);
      if(preg_match("/.*File \"<stdin>\".*/", $result)) {
        $lines = explode("\n", $result);
        $lines = array_slice($lines, sizeof($lines) - 2);
        $result = implode("\n", $lines);
      }
      $feedback = $feedback . "Test Case " . ($i+1) . " Failed.\n"
                            . "Expected "  . $autogradeAnswers[$i] . " for " . $autogradeItems[$i] . "\n"
                            . "Received "  . $result . "\n"
                            . "Points Deducted: " . round($testCasePoints/sizeof($autogradeItems), 2) . "\n\n";
    } else {
      $feedback = $feedback . "Test Case " . ($i+1) . " has Passed ". "\n"
      . "Points Awarded: ". round($testCasePoints/sizeof($autogradeItems), 2) . "\n\n";
    }
    $i++;
  }

// Return Responses

  $response = array(
    "points" => abs(round($points, 2)),
    "feedback"  => $feedback
  );

  echo json_encode($response);

?>
