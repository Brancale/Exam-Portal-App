<!DOCTYPE html>
<html>
<head>
  <title>Add a Question</title>
</head>
<body>
  <script>
  function PostData() {
      const formData = new FormData(addquestion);
      let jsonObject = {};
      var data = "(";
      for (const [key, value]  of formData.entries()) { //save form data into a json obj
          data+="\""+value+"\",";
      }
      data = data.substring(0, data.length-1);
      data+=")";
      console.log(data);
      var xhr = new XMLHttpRequest();
      xhr.onreadystatechange = function () {
          if (xhr.readyState === 4) {
            console.log(xhr.responseText);
            alert("Success");
            window.location.replace("https://web.njit.edu/~pr327/cs490/teacherLogin.php");
          }
      }
      xhr.open('POST', 'https://web.njit.edu/~sh424/middle.php');
      xhr.setRequestHeader("Content-Type", "application/json;charset=UTF-8");
      var resp = xhr.send(JSON.stringify({"operationID":"2", "data":data}));
  }
  </script>

<a href="https://web.njit.edu/~pr327/cs490/index.html">Logout</a>
<a href="https://web.njit.edu/~pr327/cs490/teacherLogin.php">Last Page</a>
<div class="addQuestion">
  <form id = "addquestion">
      <label for="question">Question         :</label>
      <textarea id="question" rows="5" cols="80" name="question"></textarea></br>

      <label for="answer">Answer     :</label>
      <textarea id="answer" rows="3" cols="80" name="answer"></textarea></br>

      <label for="subject">Subject          :</label>
      <textarea id="subject" rows="1" cols="80" name="subject"></textarea></br>

      <label for="difficulty">Difficulty       :</label>
      <textarea id="difficulty" rows="1" cols="80" name="difficulty"></textarea></br>

      <label for="points">Points           :</label>
      <textarea id="points" rows="1" cols="80" name="points"></textarea></br>

      <label for="autograde">Autograde Values :</label>
      <textarea id="autograde" rows="2" cols="80" name="autograde"></textarea></br>

      <input type="button" value ="Add Question" style="cursor:pointer" onclick="PostData()" />
  </form>
</div>

</body>
</html>
