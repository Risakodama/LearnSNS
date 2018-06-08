<?php

    session_start();
    require('dbconnect.php');//処理を実行している
    require('function.php');//こういう関数があるよ程度で、処理は実行していない
    $signin_user = get_signin_user($dbh,$_SESSION['id']);

//ログイン済みかチェックをし、未ログインであれば、ログイン画面に戻すバリデーション--------
    // if (!isset($_SESSION['id'])) {
    //   header('location:signin.php');
    //   exit();//下の処理を実行せずに、このタイミングで処理を終了する
    // }
    check_signin($_SESSION['id']);


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

    $follow_flag = 0;//ログインユーザーが今見ているプロフページの人をフォローしていたら１、いてなかったら０
    while (true) {
      $followerd_record = $followerd_stmt->fetch(PDO::FETCH_ASSOC);
      if ($followerd_record == false) {
        break;
      }

      //フォロワーの中に、ログインしている人がいるかチェック
      if ($followerd_record['user_id'] == $_SESSION['id']) {
        $follow_flag = 1;
      }

      $followerd[] = $followerd_record;
    }



      // echo "<pre>";//確認用
      // var_dump($followerd_record);
      // echo "</pre>";
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

<?php include('navbar.php'); ?>

  <div class="container" style="margin-bottom: 100px;">
    <div class="row">

      <div class="col-xs-3 text-center">
        <img src="user_profile_img/<?php echo $profile_user['img_name']; ?>" class="img-thumbnail" />
        <h2><?php echo $profile_user['name']; ?></h2>
      <?php if ($_SESSION['id'] != $user_id) { ?>
        <?php if ($follow_flag == 0) { ?>
        <a href="follow.php?follower_id=<?php echo $user_id ?>"><button class="btn btn-default btn-block">フォローする</button></a>
        <?php }else{ ?>
        <a href="unfollow.php?follower_id=<?php echo $user_id ?>"><button class="btn btn-default btn-block">フォロー解除</button></a>
        <?php } ?>
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
                  名前 <a href="profile.php?user_id=<?php echo $following_user['id'] ?>"><?php echo $followerd_user['name']; ?></a><br>
                  <li style="color: #7F7F7F;"><?php echo $followerd_user['created']; ?>からメンバー</li>
                </div>
              </div>
            </div>
    <?php } ?>



          </div>
          <div id="tab2" class="tab-pane fade">


    <?php foreach ($following as $following_user ) { ?>
            <div class="thumbnail">
              <div class="row">
                <div class="col-xs-2">
                  <img src="user_profile_img/<?php echo $following_user['img_name']; ?>" width="80">
                </div>
                <div class="col-xs-10">
                  名前 <a href="profile.php?user_id=<?php echo $following_user['id'] ?>"><?php echo $following_user['name']; ?></a><br>
                  <li style="color: #7F7F7F;"><?php echo $following_user['created']; ?>からメンバー</li>
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