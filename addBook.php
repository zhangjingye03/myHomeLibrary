<html>
<head>
  <meta charset="utf-8">
  <title>我的图书馆</title>
</head>
<script src="js/jquery-3.2.1.min.js"></script>
<script>
  function keyPress() {
    if (event.which == 13) showInfo();
  }

  function showInfo() {
    ret = $.ajax({
      url: "https://api.douban.com/v2/book/isbn/" + $("#isbn").val(),
      dataType: "jsonp",
      success: function(ret) {
        console.log(ret);
        tmp = "<hr><img src='" + ret.images.large + "'><br>";
        tmp += "<h4>" + ret.title + "</h4>";
        tmp += "<h5>" + ret.author + "</h5>";
        tmp += "<p>Publisher: " + ret.publisher + "</p>";
        tmp += "<p>Price: " + ret.price + "</p>";
        tmp += "<p>ISBN: " + ret.isbn13 + "</p>";
        tmp += "<hr><p>" + ret.summary + "</p>";
        $("#data").html(tmp);
      },
      error: function() {
        alert("查无此书/网络错误！");
      }
    });


    return;
  }
</script>
<body>
<?php
  if ($_POST) {

  }

?>
<h1>增加图书</h1>
<hr>

<label>图书ISBN：</label>
<input type="text" id="isbn" onkeypress="keyPress();" />
<br>
<div id="data">

</div>
<br>
<hr>
<input type="button" value="Submit" onclick="showInfo();" />
</form>
</body>
</html>
