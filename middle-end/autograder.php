<?php

  $input = file_get_contents('php://input');
  $json  = json_decode($input);

  $response    = $json -> {"studentAnswer"};     //Student Response
  $answer      = $json -> {"actualAnswer"};      //Array of Answers
  $testCase    = $json -> {"autoGrade"};         //Array of TestCases
  $constraints = $json -> {"constraints"};       //Array of Constraints
  $points      = $json -> {"points"};            //Points per question

  $points = intval($points);
  $pointsAwarded = 0;
  $pointsDeducted = 0;

  $testCasePoints = $points - 4;
  if($constraints != 0) {
    $testCasePoints -= 3;
  }

  $autogradeItems   = explode("|*|", $testCase);
  $autogradeAnswers = explode("|*|", $answer);

  $constraints = explode("|*|", $constraints);
  $totalConstraints = sizeof($constraints) + 2;

  if ($constraints[0] == "") {
    $totalConstraints = $totalConstraints - 1;
  }

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
    $pointsDeducted += 2;
    $response = preg_replace("/$functionCall\(/", $expectedCall . "(", $response);

    $feedback = $feedback . "<span stlye='color: red'>Incorrect Function Name: " . $functionCall . "<br>"
                          . "Expected Function Name: " . $expectedCall . "<br>"
                          . "Points Deducted: 2" . "</span><br><br>";
  } else {
    $feedback = $feedback . "<span stlye='color: green'>Constraint Passed: Valid Function Name: " . $functionCall . "<br>"
                          . "Points Awarded: 2" . "</span><br><br>";
    $pointsAwarded += 2;
  }

  // Return Calls
  if(!preg_match("/return/", $response)) {
    $pointsDeducted += 2;
    $response = str_replace("print(", "return(", $response);
    $feedback = $feedback . "<span style='color: red'>No Function Return" . "<br>"
                          . "Points Deducted: 2" . "</span><br><br>";
  } else {
    $feedback = $feedback . "<span stlye='color: green'>Constraint Passed: Function Returns a Value" . "<br>"
                          . "Points Awarded:  2" . "</span><br><br>";
    $pointsAwarded += 2;

  }

  // Constraint Checking - for/while loop and if/elif/else
  foreach($constraints as $constraint) {
    switch ($constraint) {
      case 1:
        if(!preg_match("/for.*:/", $response)) {
          $pointsDeducted += 3;
          $feedback = $feedback . "<span stlye='color: red'>Expected use of 'for' loop" . "<br>"
          . "No 'for' loop found" . "<br>"
          . "Points Deducted: 3" . "</span><br><br>";
        } else {
          $feedback = $feedback . "<span stlye='color: green'>Constraint Passed: Function uses 'for' loop" . "<br>"
                                . "Points Awarded: 3" . "<br><br>";
          $pointsAwarded += 3;

        }
        break;

      case 2:
        if(!preg_match("/if.*:/", $response)) {
          $pointsDeducted += 3;
          $feedback = $feedback . "<span stlye='color: red'>Expected use of Conditional Statements" . "<br>"
          . "No 'if', 'elif', or 'else' found" . "<br>"
          . "Points Deducted: 3" . "</span><br><br>";
        } else {
          $feedback = $feedback . "<span stlye='color: green'>Constraint Passed: Function uses conditional blocks" . "<br>"
                                . "Points Awarded: 3" . "</span><br><br>";
          $pointsAwarded += 3;
        }
        break;

        case 3:
          if(!preg_match("/while.*:/", $response)) {
            $pointsDeducted += 3;
            $feedback = $feedback . "<span stlye='color: red'>Expected use of 'while' loop" . "<br>"
            . "No 'while' found" . "<br>"
            . "Points Deducted: 3" . "</span><br><br>";
          } else {
            $feedback = $feedback . "<span stlye='color: green'>Constraint Passed: Function uses 'while' loop" . "<br>"
                                  . "Points Awarded: 3" . "</span><br><br>";
            $pointsAwarded += 3;
          }
          break;

        case 4:
          if(substr_count($response, $expectedCall . "(") < 2) {
            $pointsDeducted += 3;
            $feedback = $feedback . "<span stlye='color: red'>Expected use of recursion" . "<br>"
            . "No recursion used" . "<br>"
            . "Points Deducted: 3" . "</span><br><br>";
          } else {
            $feedback = $feedback . "<span stlye='color: green'>Constraint Passed: Function uses recursion" . "<br>"
                                  . "Points Awarded: 3" . "</span><br><br>";
            $pointsAwarded += 3;
          }
          break;
    }
  }

  //Evaluation Via Test Cases

  $pointsPerCase = ceil($testCasePoints/sizeof($autogradeItems));
  $i = 0;
  foreach($autogradeItems as $item) {
    $runcase =  $response . "\n\nif __name__ == \"__main__\":\n\tprint(" . $item . ", end='')";
$result = shell_exec("/afs/cad/sw.common/bin/python3 2>&1 - <<EOD
$runcase

EOD");
    if($originalPoints - ($pointsAwarded + $pointsDeducted) < $pointsPerCase) {
      $pointsPerCase = $originalPoints - ($pointsAwarded + $pointsDeducted);
    }
    if($result != $autogradeAnswers[$i]) {

      $pointsDeducted -= $pointsPerCase;
      if(preg_match("/.*File \"<stdin>\".*/", $result)) {
        $lines = explode("\n", $result);
        $lines = array_slice($lines, sizeof($lines) - 2);
        $result = implode("\n", $lines);
      }
      $feedback = $feedback . "<span stlye='color: red'>Test Case " . ($i+1) . " Failed.<br>"
                            . "Expected "  . $autogradeAnswers[$i] . " for " . $autogradeItems[$i] . "<br>"
                            . "Received "  . $result . "<br>"
                            . "Points Deducted: " . $pointsPerCase . "</span><br><br>";
    } else {

      $pointsAwarded += $pointsPerCase;
      $feedback = $feedback . "<span stlye='color: green'>Test Case " . ($i+1) . " has Passed ". "<br>"
      . "Points Awarded: ". $pointsPerCase . "</span><br><br>";
    }
    $i++;
  }

// Return Responses

  $response = array(
    "points" => max($pointsAwarded, 0),
    "feedback"  => $feedback
  );

  echo json_encode($response);

?>
