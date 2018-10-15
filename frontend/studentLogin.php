<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Student Login Page</title>
</head>
<body>
<h1>Student Login Page</h1>
<script>
var xhr = new XMLHttpRequest();
xhr.onreadystatechange = function() {
    if (xhr.readyState == 4) {
        console.log(xhr.responseText);
        var resp = xhr.responseText;
        var data = JSON.parse(resp);
        for (x in data) {

          if (x == "success"){continue;}
          var num = data[x].EID;

          var btn = document.createElement("button");
          btn.setAttribute("id", data[x].EID)
          var t = document.createTextNode("Exam"+data[x].EID);
          btn.appendChild(t);
          document.body.appendChild(btn);
          console.log(num);
          btn.onclick = btnclick(num);
        }
    }
}
xhr.open('POST', 'https://web.njit.edu/~sh424/middle.php');
xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
var resp = xhr.send(JSON.stringify({"operationID":"6", "data":""}));

function btnclick(val){
  return function(){
    window.location.replace("https://web.njit.edu/~pr327/cs490/takeExam.php?exam="+val);
  }
}
</script>

<a href="https://web.njit.edu/~pr327/cs490/index.html">Logout</a>

</body>
</html>
