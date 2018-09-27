<?php

  #Get Post Request From Frontend

  $input = file_get_contents('php://input');
  $json  = json_decode($input);

  $username = $json -> {"username"};
  $password = $json -> {"password"};

  #Check Against NJIT's login via Post

  $fields = array(
    "ucid" => $username,
    "pass" => $password
  );

  $njitPost = curl("https://aevitepr2.njit.edu/myhousing/login.cfm", $fields, "");

  $njitLogin = False;
  if(pageTitle($njitPost) == "Please Select a System to Sign Into") {
    $njitLogin = True;
  }

  #Check Against BackEnd's DB via Post

  $dbPost = curl("https://web.njit.edu/~jmb75/server.php", $input, "db");
  $valid = json_decode($dbPost);
  $dbResult = $valid -> isValid;

  #Send Results Back to Frontend via Post

  $response = array(
    "njitLogin" => $njitLogin,
    "dbLogin"   => $dbResult
  );

  $response = json_encode($response);

  echo $response;


  #Helper Functions
  function curl($url, $content, $type) {
    $postRequest = curl_init($url);

    curl_setopt($postRequest, CURLOPT_POST, True);
    curl_setopt($postRequest, CURLOPT_RETURNTRANSFER, True);
    curl_setopt($postRequest, CURLOPT_FOLLOWLOCATION, True);

    if ($type == "db") {
      curl_setopt($postRequest, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json'
      ));
      curl_setopt($postRequest, CURLOPT_POSTFIELDS, $content);

    } else {
      curl_setopt($postRequest, CURLOPT_POSTFIELDS, http_build_query($content));
    }

    $result = curl_exec($postRequest);
    curl_close($postRequest);

    return $result;
  }

  function pageTitle($html) {

    preg_match('/<TITLE>(.*?)<\/TITLE>/', $html, $matches);
    return $matches[1];

  }

?>
