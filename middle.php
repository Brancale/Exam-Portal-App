<?php

  #Get Post Request From Frontend

  $input=file_get_contents('php://input');
  $json=json_decode($input);

  $username=$json->{"username"};
  $password=$json->{"password"};

  echo $password;
  echo $username;
  #Check Against NJIT's login via Post


  #Check Against BackEnd's DB via Post
  $dbPost=curl("https://web.njit.edu/~jmb37/back.php", $input);


  #TO-DO enrich dbPost with NJIT POST

  #Send Results Back to Frontend via Post
  $frontendPost=curl("https://web.njit.edu/~pr347/front.php", $dbPost);


  #Helper Functions
  function curl($url, $content) {
    $postRequest=curl_init($url);
    curl_setopt($postRequest, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($postRequest, CURLOPT_POSTFIELDS, $content);
    curl_setopt($postRequest, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($postRequest, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Content-Length: ' . strlen($content))
    );

    return curl_exec($postRequest);
  }

?>
