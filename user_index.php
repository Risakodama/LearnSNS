<?php

    // session_start();
    require('dbconnect.php');
    // $errors=array();

    //SQL文
    $sql='SELECT * FROM `users` WHERE 1';
    //SQL実行
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
    //繰り返し文でfetch（配列に保存）
    while (true) {
      $record = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($record == false) {
          break;
      }
        $users[] = $record;
    }

    $feed_cnt_sql='SELECT COUNT(*) AS `feed_cnt` FROM `likes` WHERE `user_id` = ?'
    $feed_cnt_data = array($users['id']);
    $feed_cnt_stmt = $dbh->prepare($sql);
    $feed_cnt_stmt->execute($feed_cnt_data);
    while (true) {
      $feed_cnt = $feed_cnt_stmt->fetch(PDO::FETCH_ASSOC);
        if ($feed_cnt == false) {
          break;
      }
        $users[] = $feed_cnt;
    }



    //データ保存した配列をHTMLで表示させる



        // foreach ($users as $user) { 
        //     echo $user['name'] };
// array(7) {
//   ["id"]=>
//   string(1) "1"
//   ["name"]=>
//   string(15) "コダマリサ"
//   ["email"]=>
//   string(17) "example@world.com"
//   ["password"]=>
//   string(8) "90909090"
//   ["img_name"]=>
//   string(26) "20180426040756IMG_2087.JPG"
//   ["created"]=>
//   string(19) "2018-04-26 10:20:55"
//   ["updated"]=>
//   string(19) "2018-04-26 10:20:55"
// }

 ?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>Learn SNS</title>
  <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="assets/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>
<body style="margin-top: 60px; background: #E4E6EB;">
    <nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse1" aria-expanded="false">
          <span class="sr-only">Toggle navigation</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="/">Learn SNS</a>
      </div>
      <div class="collapse navbar-collapse" id="navbar-collapse1">
        <ul class="nav navbar-nav">
          <li><a href="timeline.php">タイムライン</a></li>
          <li class="active"><a href="#">ユーザー一覧</a></li>
        </ul>
        <form method="GET" action="" class="navbar-form navbar-left" role="search">
          <div class="form-group">
            <input type="text" name="search_word" class="form-control" placeholder="投稿を検索">
          </div>
          <button type="submit" class="btn btn-default">検索</button>
        </form>
        <ul class="nav navbar-nav navbar-right">
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><img src="" width="18" class="img-circle">test <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li><a href="#">マイページ</a></li>
              <li><a href="signout.php">サインアウト</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">
    <?php foreach ($users as $user) { ?>
    <div class="row">
      <div class="col-xs-12">

          <div class="thumbnail">
            <div class="row">
              <div class="col-xs-1">
                <img src="user_profile_img/<?php echo $user['img_name']; ?>" width="80">
              </div>
              <div class="col-xs-11">
                名前 <?php echo $user['name']; ?><br>
                <a href="#" style="color: #7F7F7F;"><?php echo $user['created']; ?>からメンバー</a>
              </div>
            </div>
            <div class="row feed_sub">
              <div class="col-xs-12">
                <span class="comment_count">つぶやき数 : 5</span>
              </div>
            </div>
          </div><!-- thumbnail -->
      </div><!-- class="col-xs-12" -->
    </div><!-- class="row" -->
    <?php } ?>
  </div><!-- class="cotainer" -->
</body>
</html>