<?php

  #Get Post Request From Frontend

  $input = file_get_contents('php://input');

  $dbPost = curl("https://web.njit.edu/~jmb75/serverOps.php", $input);

  echo $dbPost;

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
