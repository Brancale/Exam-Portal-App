<?php
  // header("Content-type: application/json");
  # Get Post Request From mid-end
  $input=file_get_contents('php://input');
  $json=json_decode($input);
  $userid=$json->{"username"};
  $userpwd=$json->{"password"};

  $jsonData = array();
  $jsonData["username"] = $userid;
  $jsonData["password"] = $userpwd;

  session_start();
  $_SESSION["username"] = $userid;

  $jsonData = json_encode($jsonData);
  $ch = curl_init('https://web.njit.edu/~sh424/login.php');
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
  curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Content-Length: ' . strlen($jsonData))
  );

  $result = curl_exec($ch);
  $json = json_decode($result);
  if($json->{"dbLogin"} == "true"){
    $dat = array();
    $dat[0]="dbsuccess";
    $dat[1]=$json->{"type"};
    echo json_encode($dat);
  }

  else{
    echo "error";
  }
?>
