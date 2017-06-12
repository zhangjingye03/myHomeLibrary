<?php
  $flag = true;
  require("database.php");
  if ($_POST) {
    $res = PDOQuery($dbcon, "INSERT INTO books"
        ." SET ISBN = ?, name = ?, doubanID = ?, imageID = ?, author = ?, pages = ?, price = ?, publisher = ?, translator = ?, pubdate = ?, placeID = ?",
        [ $_POST['ISBN'], $_POST['name'], $_POST['doubanID'], $_POST['imageID'], $_POST['author'], $_POST['pages'], $_POST['price'], $_POST['publisher'], $_POST['translator'], $_POST['pubdate'], $_POST['placeID'] ],
        [ PDO::PARAM_STR, PDO::PARAM_STR, PDO::PARAM_STR, PDO::PARAM_STR, PDO::PARAM_STR, PDO::PARAM_STR, PDO::PARAM_STR, PDO::PARAM_STR, PDO::PARAM_STR, PDO::PARAM_STR, PDO::PARAM_STR]);
    if ($res[1] != 1) die('0');
    else die('1');
  }

?>
<html>
<head>
  <meta charset="utf-8">
  <title>我的图书馆</title>
</head>
<script src="js/jquery-3.2.1.min.js"></script>
<script>
  state = 0; storage = null;
  window.onload = function() {
    $("#isbn").focus();
  };

  function keyPress(where) {
    if (event.which == 13) {
      if (where == "isbn") $("#place").focus().select();
      else showInfo();
    }
  }

  function showInfo() {
    if (state == 0) {
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
          state = 1; storage = ret;
        },
        error: function() {
          alert("查无此书/网络错误！");
        }
      });
    } else if (state == 1) {
        if (!confirm("确定提交此书么？")) return;
        preimage = storage.image.split("/");
        preimage = preimage[preimage.length - 1];
        preimage = (preimage.indexOf("s") != 0) ? storage.image : preimage;
        $.ajax({
          url: "addBook.php",
          type: "POST",
          data: {
            "ISBN": storage.isbn13,
            "name": storage.title,
            "doubanID": storage.id,
            "imageID": preimage,
            "author": storage.author.toString(),
            "pages": (storage.pages) ? storage.pages : 0,
            "price": storage.price,
            "publisher": storage.publisher,
            "translator": storage.translator.toString(),
            "pubdate": storage.pubdate,
            "placeID": $("#place").val()
          },
          success: function(ret) {
            console.log(ret);
            if (ret == 1) { alert("提交成功！"); location.reload(); }
            else alert("提交失败");
          },
          error: function() {
            alert("网络错误！");
          }
        });
    }


  }
</script>
<body>

<h1>增加图书</h1>
<hr>

<label>图书ISBN：</label>
<input type="text" id="isbn" onkeypress="keyPress('isbn');" />
<br>
<label>位置id：</label>
<input type="text" id="place" onkeypress="keyPress('place');" value="0" />
<div id="data">

</div>
<br>
<hr>
<input type="button" value="Submit" onclick="showInfo();" />
</body>
</html>
