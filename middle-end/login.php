<?php


  #Get Post Request From Frontend
  /*
  $input=file_get_contents('php://input');
  $json=json_decode($input);
  */
  $username="username";//$json["login"]["username"];
  $password="password";//$json["login"]["password"];

  $jsonData = array();
	$jsonData["username"] = "username";
	$jsonData["password"] = "password";
	//echo $jsonData["username"] . "\n";
	//echo $jsonData["password"] . "\n";
	// Encode the array into JSON.
	$input = json_encode($jsonData);


  #Check Against NJIT's login via Post

  $URL = "https://aevitepr2.njit.edu/myhousing/login.cfm";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $URL);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
  $fields= array(
    "ucid" => $username,
    "pass" => $password
  );
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));

  $result = curl_exec ($ch);
  //echo $result;

  #Check Against BackEnd's DB via Post
  $dbPost=curl("https://web.njit.edu/~jmb75/server.php", $input);


  #TO-DO enrich dbPost with NJIT POST

  #Send Results Back to Frontend via Post
  echo $dbPost;


  #Helper Functions
  function curl($url, $content) {
    $postRequest=curl_init($url);
    curl_setopt($postRequest, CURLOPT_POST, 1);
    curl_setopt($postRequest, CURLOPT_POSTFIELDS, $content);
    curl_setopt($postRequest, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($postRequest, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json'
    ));
    $result = curl_exec($postRequest);
    curl_close($postRequest);
    return $result;
  }

?>
