<?php

    session_start();
    require('dbconnect.php');
    // $errors=array();


//user_idの取得----------------------------------------
    if (isset($_GET['user_id'])) {
      $user_id = $_GET['user_id'];
    }else{
      $user_id = $_SESSION['id'];
    }
      $sql = 'SELECT * FROM `users` WHERE `id`=?';
      $data = array($user_id);
      $stmt = $dbh->prepare($sql);
      $stmt->execute($data);
    //サインインユーザー情報の取出し-------------------------
      $profile_user = $stmt->fetch(PDO::FETCH_ASSOC);

//following　一覧の取得--------------------------------
//ログインしているユーザーがフォローしている人が表示される
    $following_sql = 'SELECT `fw`.*, `u`.`name`,`u`.`created`,`u`.`img_name` FROM `followers` AS `fw` LEFT JOIN `users` AS `u` ON `fw`.`follower_id` = `u`.`id` WHERE `user_id`=?';
    $following_data = array($user_id);
    $following_stmt = $dbh->prepare($following_sql);
    $following_stmt->execute($following_data);

    $following=array();

    while (true) {
      $following_record = $following_stmt->fetch(PDO::FETCH_ASSOC);
      if ($following_record == false) {
        break;
      }
      $following[] = $following_record;
    }

//follower　一覧の取得--------------------------------
//ログインしているユーザーがフォローされている人が表示される
    $followerd_sql = 'SELECT `fw`.*, `u`.`name`,`u`.`created`,`u`.`img_name` FROM `followers` AS `fw` LEFT JOIN `users` AS `u` ON `fw`.`user_id` = `u`.`id` WHERE `follower_id`=?';
    $followerd_data = array($user_id);
    $followerd_stmt = $dbh->prepare($followerd_sql);
    $followerd_stmt->execute($followerd_data);

    $followerd=array();

    while (true) {
      $followerd_record = $followerd_stmt->fetch(PDO::FETCH_ASSOC);
      if ($followerd_record == false) {
        break;
      }
      $followerd[] = $followerd_record;
    }

      echo "<pre>";//確認用
      var_dump($profile_user);
      echo "</pre>";


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
          <li class="active"><a href="user_index.php">ユーザー一覧</a></li>
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
    <div class="row">

      <div class="col-xs-3 text-center">
        <img src="user_profile_img/<?php echo $profile_user['img_name']; ?>" class="img-thumbnail" />
        <h2><?php echo $profile_user['name']; ?></h2>
    <?php if ($_SESSION['id'] !== $profile_user['id']) { ?>
        <a href="follow.php?follower_id=<?php echo $user_id ?>"><button class="btn btn-default btn-block">フォローする</button></a>
    <?php } ?>

      </div>

      <div class="col-xs-9">
        <ul class="nav nav-tabs">
          <li class="active">
            <a href="#tab1" data-toggle="tab">Followers</a>
          </li>
          <li>
            <a href="#tab2" data-toggle="tab">Following</a>
          </li>
        </ul>
        <!--タブの中身-->
        <div class="tab-content">
          <div id="tab1" class="tab-pane fade in active">


    <?php foreach ($followerd as $followerd_user ) { ?>
            <div class="thumbnail">
              <div class="row">
                <div class="col-xs-2">
                  <img src="user_profile_img/<?php echo $followerd_user['img_name']; ?>" width="80">
                </div>
                <div class="col-xs-10">
                  名前 <?php echo $followerd_user['name']; ?><br>
                  <a href="#" style="color: #7F7F7F;"><?php echo $followerd_user['created']; ?>からメンバー</a>
                </div>
              </div>
            </div>
    <?php } ?>


            <div class="thumbnail">
              <div class="row">
                <div class="col-xs-2">
                  <img src="http://placehold.jp/80x80.png" width="80">
                </div>
                <div class="col-xs-10">
                  名前 オモえもん1<br>
                  <a href="#" style="color: #7F7F7F;">2018-05-29 10:00:00からメンバー</a>
                </div>
              </div>
            </div>
            <div class="thumbnail">
              <div class="row">
                <div class="col-xs-2">
                  <img src="http://placehold.jp/80x80.png" width="80">
                </div>
                <div class="col-xs-10">
                  名前 オモえもん2<br>
                  <a href="#" style="color: #7F7F7F;">2018-05-29 10:00:00からメンバー</a>
                </div>
              </div>
            </div><!-- thumbnail -->
          </div>
          <div id="tab2" class="tab-pane fade">


    <?php foreach ($following as $following_user ) { ?>
            <div class="thumbnail">
              <div class="row">
                <div class="col-xs-2">
                  <img src="user_profile_img/<?php echo $following_user['img_name']; ?>" width="80">
                </div>
                <div class="col-xs-10">
                  名前 <?php echo $following_user['name']; ?><br>
                  <a href="#" style="color: #7F7F7F;"><?php echo $following_user['created']; ?>からメンバー</a>
                </div>
              </div>
            </div>
    <?php } ?>
          </div>
        </div>

      </div><!-- class="col-xs-12" -->

    </div><!-- class="row" -->
  </div><!-- class="cotainer" -->
  <script src="assets/js/jquery-3.1.1.js"></script>
  <script src="assets/js/jquery-migrate-1.4.1.js"></script>
  <script src="assets/js/bootstrap.js"></script>
</body>
</html>