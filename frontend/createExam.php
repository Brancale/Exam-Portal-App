<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
  .split {
      height: 100%;
      width: 50%;
      position: fixed;
      z-index: 1;
      top: 0;
      overflow-x: hidden;
      padding-top: 20px;
  }
  .left {
      left: 0;
  }
  .right {
      right: 0;
  }
  </style>
</head>
<body>
<script>
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function() {
    if (xhr.readyState == 4) {
        console.log(xhr.responseText);
        var resp = xhr.responseText;
        var data = JSON.parse(resp);
        for (x in data){
          if (x == "success"){continue;}
          console.log(x);
          var question = document.createElement("question");
          question.setAttribute('class','question');
          var check1 = "<input id=";
          var check2 = data[x].QID;
          var check3 = " type='checkbox' name='qid' value = ";
          var check4 = data[x].QID;
          var check5 = ">";
          var field = check1+check2+check3+check4+check5;
          console.log(field);

          var beg = "<div class = 'questionsDiv'>";
          var qid = data[x].QID;
          var dotgap = ":   ";
          var quest = data[x].Question;
          var br = "</br>";
          var s = "Subject:   ";
          var subject = data[x].Subject;
          var a = "Answer:   "
          var answer = data[x].Answer;
          var d = "Difficulty:   ";
          var diff = data[x].Difficulty;
          var p = "Points:   ";
          var points = data[x].Points;
          var a = "Autograde Test Values:   ";
          var autograde = data[x].Autograde;
          var text = "</br></div></br>";
          question.innerHTML = field + beg + qid + dotgap + quest + br + a+answer + br + s+subject+ br+d+diff + br + p+points+br+ a+autograde+text;
          document.getElementById("slefts").appendChild(question);
      }
    }
  }
  xhr.open('POST', 'https://web.njit.edu/~sh424/middle.php');
  xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
  var resp = xhr.send(JSON.stringify({"operationID":"1", "data":""}));
</script>

<div class="split left" id = "sleft">
  <a href="https://web.njit.edu/~pr327/cs490/index.html">Logout</a>
  <a href="https://web.njit.edu/~pr327/cs490/teacherLogin.php">Last Page</a>
  <h2>Question Bank</h2>

  <div id = "slefts"></div>
  <div id = "sub">
      <button class="submitButton" id="submitButton" type="submit">Transfer</button>
  </div>
  </br></br></br></br></br></br>
</div>

<script>
  var submitButton = document.getElementById("submitButton");
  submitButton.addEventListener("click", onSubmission);
  var selectedQues = []
  function onSubmission() {
      var checkedBoxes = getCheckedBoxes("qid");
      for (var i = 0; i < checkedBoxes.length; i++) {
          console.log(checkedBoxes[i]);
          console.log(typeof(checkedBoxes[i].value));
          selectedQues[i] = checkedBoxes[i].value;
      }
      leftside();
    }
  function getCheckedBoxes(checkboxName) {
      var checkboxes = document.getElementsByName(checkboxName);
      var checkedBoxes = [];
      for (var i = 0; i < checkboxes.length; i++) {
          if (checkboxes[i].checked) {
              checkedBoxes.push(checkboxes[i]);
          }
      }
      return checkedBoxes;
  }
  function leftside(){
      var xhr1 = new XMLHttpRequest();
      xhr1.onreadystatechange = function() {
      if (xhr1.readyState == 4) {
          console.log(xhr1.responseText);
          var resp = xhr.responseText;
          var data = JSON.parse(resp);
          for (x in data){
            if (x == "success"){continue;}
            for( var i = 0; i<selectedQues.length; i++){
              if(selectedQues[i]==data[x].QID){
                console.log(data[x].QID);
                var questionright = document.createElement("questionright");
                questionright.setAttribute('class','questionright');
                var qid = data[x].QID;
                var dotgap = ":   ";
                var quest = data[x].Question;
                var br = "</br>";
                var s = "Subject:   ";
                var subject = data[x].Subject;
                var a = "Answer:   "
                var answer = data[x].Answer;
                var d = "Difficulty:   ";
                var diff = data[x].Difficulty;
                var p = "Points:   ";
                var points = data[x].Points;
                var a = "Autograde Test Values:   ";
                var autograde = data[x].Autograde;
                var text = "</br></div></br>";
                questionright.innerHTML = qid + dotgap + quest + br + a+answer + br + s+subject+ br+d+diff + br + p+points+br+ a+autograde+text;
                document.getElementById("sright").appendChild(questionright);
              }
            }
          }
        }
      }
      xhr1.open('POST', 'https://web.njit.edu/~sh424/middle.php');
      xhr1.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
      var resp = xhr1.send(JSON.stringify({"operationID":"1", "data":""}));
  }
</script>

<div class="split right">
    <h2>Selected Questions</h2>
    <div id = "sright"></div>
    <form id = "time">
      Exam Start Date:
      <input type="datetime-local" name="startime"></br>
      Exam End Date:
      <input type="datetime-local" name="endtime"></br>
      <input type="button" value="Send" onclick = "sendques()">
    </form>
</div>

<script>
  function sendques(){
    const formData = new FormData(time);
    var dtime = {};
    for (const [key, value]  of formData.entries()) { //save form data into a json obj
      dtime[key] = value;
    }
    var st = new Date(dtime["startime"]);
    var milliStart = st.getTime();
    var et = new Date(dtime["endtime"]);
    var milliEnd = et.getTime();

    var data = "(";
    var qs = '';
    for( var i = 0; i<selectedQues.length; i++){
      if(i>0){
        qs+="|*|"+selectedQues[i];
      }
      else{
        qs+=selectedQues[i];
      }
    }
    data+="\'"+qs+"\'"+","+milliStart+","+milliEnd+")";
    console.log(data);
    var xhr2 = new XMLHttpRequest();
    xhr2.onreadystatechange = function() {
      if (xhr2.readyState == 4) {
          console.log(xhr2.responseText);
          alert("Success");
          window.location.replace("https://web.njit.edu/~pr327/cs490/teacherLogin.php");
        }
    }
    xhr2.open('POST', 'https://web.njit.edu/~sh424/middle.php');
    xhr2.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
    var resp = xhr2.send(JSON.stringify({"operationID":3, "data":data}));
}
</script>

</body>
</html>
