<?php

  #Get Post Request From Frontend
/*
  $input=file_get_contents('php://input');
  $json=json_decode($input);

  $username=$json->{"username"};
  $password=$json->{"password"};

  echo $password;
  echo $username;
  #Check Against NJIT's login via Post
*/
  $URL = 'https://njit2.mrooms.net/auth/saml2/login.php';
  $fields = array('j_username'=>urlencode($_POST['j_username']), 'j_password'=>urlencode($_POST['j_password']));

  foreach($fields as $key=>$value) { $fields_string  .= $key.'='.$value.'&'; };
  echo $fields_string;

  rtrim($fields_string,'&');
  $ch = curl_init();

  curl_setopt($ch,CURLOPT_URL,$URL);
  curl_setopt($ch,CURLOPT_POST,count($fields));
  curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);

  $result = curl_exec($ch);
  echo $result;

  curl_close($ch);

/*
  #Check Against BackEnd's DB via Post
  $dbPost=curl("https://web.njit.edu/~jmb37/server.php", $input);


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
*/
?>
