<?php session_start();?>
<!DOCTYPE html>
<html>
<head>
<title>Exam</title>
</head>
<body>
<div id = "heading"></div>
<script>
  var user = "<?php echo $_SESSION["username"]?>";
  var exam = "<?php echo $_GET["exam"] ?>";
  document.getElementById("heading").innerHTML= "User: "+user + " Exam: Exam" + exam;
  console.log(exam);
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function() {
      if (xhr.readyState == 4) {
          console.log(xhr.responseText);
          var resp = xhr.responseText;
          var data = JSON.parse(resp);
          for (x in data){
            console.log(x);
            if (x == "success"){continue;}
            var question = document.createElement("question");
            question.setAttribute('class','question');
            var beg = "<div class = 'answerDiv'>";
            var ques =  data[x].QID + data[x].Question;
            var text = "</br><textarea class='answer' rows='5' cols='80'></textarea></br></br></div>";
            question.innerHTML = beg+ques + text;
            document.getElementById("ques").appendChild(question);
          }
      }
  }
  xhr.open('POST', 'https://web.njit.edu/~sh424/middle.php');
  xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
  var resp = xhr.send(JSON.stringify({"operationID":"4", "data":exam}));
</script>

<a href="https://web.njit.edu/~pr327/cs490/index.html">Logout</a>
<a href="https://web.njit.edu/~pr327/cs490/studentLogin.php">Last Page</a>
<div id = "ques"></div>
<button class="submitButton" id="submitButton">Submit</button>

<script>
var submitButton = document.getElementById("submitButton");
submitButton.addEventListener("click", onExamSubmission);

  function onExamSubmission() {
    var answerDivs = document.getElementsByClassName("answerDiv");
    console.log(answerDivs);
    var postdata = {};
    postdata["\"operationID\""] = "5";
    postdata["\"EID\""] = "<?php echo $_GET["exam"] ?>";
    postdata["\"SName\""] = "<?php echo $_SESSION["username"]?>";
    var concat = "";
    for (var i = 0; i < answerDivs.length; i++) {
        var answer = answerDivs[i].getElementsByTagName("textarea")[0].value;
        if(i>0){
            concat+="|*|"+answer;
        }
        else{
            concat+=answer;
        }
    }
    postdata["\"Answers\""] = concat;
    console.log(postdata);
    var xhr1 = new XMLHttpRequest();
    xhr1.open('POST', 'https://web.njit.edu/~sh424/middle.php');
    xhr1.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
    xhr1.send(JSON.stringify({"operationID":"5", "EID":"<?php echo $_GET["exam"] ?>", "SName":"<?php echo $_SESSION["username"]?>", "Answers":concat}));
    xhr1.onreadystatechange = function() {
      if (xhr1.readyState == 4) {
            console.log(xhr1.responseText);
            alert("Success");
            window.location.replace("https://web.njit.edu/~pr327/cs490/studentLogin.php");
      }
    }
  }
</script>
</body>
</html>
