<?php

  #Get Post Request From Frontend
  $input=file_get_contents('php://input');
  $json=json_decode($input);

  $username=$json["login"]["username"];
  $password=$json["login"]["password"];

  echo $password;
  echo $username;
  /*
  #Check Against NJIT's login via Post
  $URL = "";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL,"https://webauth.njit.edu/idp/profile/SAML2/POST/SSO?execution=e1s1");
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  #curl_setopt($ch, CURLOPT_COOKIESESSION, 1);
  #curl_setopt($curl, CURLOPT_COOKIEFILE, "");
  #curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, "j_username=sh424&j_password=toiw0kanr3&_eventId_proceed=");

  $result = curl_exec ($ch);
  curl_close($ch);

  $njit_url="https://webauth.njit.edu/idp/profile/SAML2/POST/SSO?execution=e1s1";
  $njit_fields = array("j_username" => "sh424", "j_password" => "toiuw0kanr3", "_eventId_proceed=" => "");;
  $ch2 = curl_init();
  curl_setopt($ch2, CURLOPT_URL, $njit_url);
  curl_setopt($ch2, CURLOPT_POST, 1);
  curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query($njit_fields));
  curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch2, CURLOPT_FOLLOWLOCATION, 1);
  curl_setopt($ch2, CURLOPT_MAXREDIRS, 2);
  $njit_result = curl_exec($ch2);
  echo $njit_result;
  curl_close($ch2);

*/
  #Check Against BackEnd's DB via Post
  $dbPost=curl("https://web.njit.edu/~jmb37/server.php", $input);


  #TO-DO enrich dbPost with NJIT POST

  #Send Results Back to Frontend via Post
  $frontendPost=curl("https://web.njit.edu/~pr347/front.php", $dbPost);


  #Helper Functions
  function curl($url, $content) {
    $postRequest=curl_init($url);
    curl_setopt($postRequest, CURLOPT_POST, 1);
    curl_setopt($postRequest, CURLOPT_POSTFIELDS, $content);
    curl_setopt($postRequest, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($postRequest, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json',
      'Content-Length: ' . strlen($content))
    );

    $result = curl_exec($postRequest);
    curl_close($postRequest);
    return $result;
  }
*/
?>
