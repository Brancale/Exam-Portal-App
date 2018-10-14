<?php

  #Get Post Request From Frontend

  $input = file_get_contents('php://input');
  $json  = json_decode($input);

  $username = $json -> {"username"};
  $password = $json -> {"password"};

  #Check Against BackEnd's DB via Post

  $dbPost = curl("https://web.njit.edu/~jmb75/server.php", $input);
  $valid = json_decode($dbPost);
  $dbResult = $valid -> isValid;
  $type = $valid -> {"type"};
  #Send Results Back to Frontend via Post

  $response = array(
    "dbLogin"   => $dbResult,
    "type" => $type;
  );

  $response = json_encode($response);

  echo $response;


  #Helper Functions
  function curl($url, $content) {
    $postRequest = curl_init($url);

    curl_setopt($postRequest, CURLOPT_POST, True);
    curl_setopt($postRequest, CURLOPT_RETURNTRANSFER, True);
    curl_setopt($postRequest, CURLOPT_FOLLOWLOCATION, True);

    curl_setopt($postRequest, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json'
    ));
    curl_setopt($postRequest, CURLOPT_POSTFIELDS, $content);


    $result = curl_exec($postRequest);
    curl_close($postRequest);

    return $result;
  }


?>
