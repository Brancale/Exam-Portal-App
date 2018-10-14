<!DOCTYPE html>
<html>
<head>
<title>Student Submissions</title>
</head>
<body>
<script>
var xhr = new XMLHttpRequest();
var data;
xhr.onreadystatechange = function() {
    if (xhr.readyState == 4) {
        console.log(xhr.responseText);
        var resp = xhr.responseText;
        var data = JSON.parse(resp);
        for (x in data){
          if (x == "success"){continue;}
          var submission = document.createElement("submission");
          submission.setAttribute('class','submission');
          var s2 = '<td>' + data[x].EID + '</td>';
          var s4 = '<td>' + data[x].SName+ '</td>';
          var s6 = '<td>' + data[x].Answers+ '</td>';
          var s8 = "";
          if(data[x].Score==null){
            var daat = data[x].SExamID;
            var daat1 = data[x].SName;
            var comb = daat+"#"+daat1;
            s8 = "<td><button class='grade' id='grade' onclick = 'grade("+daat+")'>Grade Now</button></td>";
          }
          else{
            s8 = '<td>' + data[x].Score+ '</td>';
          }
          var s10 = "";
          if(data[x].Comments==null){
            //s10 = "<td><form id = 'addcomment'><textarea class='comment' rows='3' cols='20'></textarea><input type='button' name = 'Add Comment' value ='addcomment' onclick='addcomment()''/></button></form></td>";
            var examid = data[x].SExamID;
            //s10 = "<td><form id = 'addcomment'><input type='text' name ='comment' id='comment'/><input type='button' name = 'Add Comment' value ='addcomment' onclick='addcomment()'/></form></td>";
            s10 = "<td><textarea id='myTextarea'></textarea><button type='button' onclick='addcomment("+examid+")'>Add</button></td>";
          }
          else{
            s10 = '<td>' + data[x].Comments+ '</td>';

          }
          var col = s2+s4+s6+s8+s10;
          var s1 = '<tr>' + col + '</tr>';



          console.log(s1);
          submission.innerHTML = s1;
          document.getElementById("subs").innerHTML += s1;
        }
      }
    }

    function grade(studentexamid){
      console.log(studentexamid);
      var xhr1 = new XMLHttpRequest();
      xhr1.onreadystatechange = function() {
          if (xhr1.readyState == 4) {
              console.log(xhr1.responseText);
              location.reload();
            }
          }
      xhr1.open('POST', 'https://web.njit.edu/~sh424/middle.php');
      xhr1.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
      var resp = xhr1.send(JSON.stringify({"operationID":"7", "SExamID":studentexamid}));
      }

      function addcomment(examid){
        var x = document.getElementById("myTextarea").value;
        console.log(x);
        console.log(examid);
        var xhr2 = new XMLHttpRequest();
        xhr2.onreadystatechange = function() {
            if (xhr2.readyState == 4) {
                console.log(xhr2.responseText);
                location.reload();
              }
            }
        xhr2.open('POST', 'https://web.njit.edu/~sh424/middle.php');
        xhr2.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
        var resp = xhr2.send(JSON.stringify({"operationID":"10", "SExamID":examid, "Comments":x}));
      }

    xhr.open('POST', 'https://web.njit.edu/~sh424/middle.php');
    xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
    var resp = xhr.send(JSON.stringify({"operationID":"8", "data":""}));
</script>
<div id = "div1">
  <a href="https://web.njit.edu/~pr327/cs490/index.html">Logout</a>
  <a href="https://web.njit.edu/~pr327/cs490/teacherLogin.php">Last Page</a>


<table id = "subs">
  <tr>
    <th>Exam Number</th>
    <th>Student Username</th>
    <th>Answers</th>
    <th>Score</th>
    <th>Comments</th>
  </tr>

</table>



</div>

</body>
</html>
