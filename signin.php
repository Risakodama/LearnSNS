<?php

    require('dbconnect.php');
    session_start();  // $_SESSIONを使うための合図
    $errors = array();


    if (!empty($_POST)) {//POST送信があった時--ここから
      $email = $_POST['input_email'];
      $password = $_POST['input_password'];

        //メールアドレス空チェック
        if ($email=='') {
          $errors['signin'] = 'blank';
        }
        //パスワード空チェック
        if ($password=='') {
          $errors['signin'] = 'failed';
        }elseif//パスワード文字数チェック--ここから
            (strlen($password)<4 || strlen($password)>16) {
              $errors['password'] = 'length';
        }//パスワード文字数チェック--ここまで

      if ($email != "" && $password != ""){//emailをデータベースとの照合処理--ここから
          $sql = 'SELECT * FROM `users` WHERE `email`=?';
          $data=array($email);
          $stmt=$dbh->prepare($sql);
          $stmt->execute($data);
          // $dbh=null;
          $record=$stmt->fetch(PDO::FETCH_ASSOC);

        if ($record == false) {//一致するレコードがなかったら
          $errors['signin'] = 'failed';
        }else{
        // $errors['signin']='blank';
          //パスワード照合チェック--ここから
          if (password_verify($password,$record['password'])) {
            $_SESSION['id'] = $record['id'];
            header('location:timeline.php');//認証成功
          // echo '認証成功';
            exit();
          }else{
            $errors['signin']='failed';//認証失敗
          }//パスワード照合チェック--ここまで
        }

      }//emailをデータベースとの照合処理--ここまで

    }//POST送信があった時--ここまで


      // echo "<pre>";//確認用
      // var_dump($data);
      // echo "</pre>";

      // echo "<pre>";//確認用
      // var_dump($record);
      // echo "</pre>";

      // echo "<pre>";//確認用
      // var_dump($errors);
      // echo "</pre>";


?>

<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>Learn SNS</title>
  <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="assets/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" type="text/css" href="assets/css/style.css"> <!-- 追加 -->
</head>
<body style="margin-top: 60px">
  <div class="container">
    <div class="row">
      <div class="col-xs-8 col-xs-offset-2 thumbnail">
        <h2 class="text-center content_header">サインイン</h2>
        <form method="POST" action="signin.php" enctype="multipart/form-data">
          <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" name="input_email" class="form-control" id="email" placeholder="example@gmail.com">
            <?php if(isset($errors['signin']) && $errors['signin']=='blank'){ ?>
            <p class="text-danger">正しいメールアドレスを入力してください</p>
            <?php } ?>
            <?php if(isset($errors['signin']) && $errors['signin']=='failed'){ ?>
            <p class="text-danger">サインインに失敗しました</p>
            <?php } ?>
          </div>
          <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" name="input_password" class="form-control" id="password" placeholder="パスワード">
            <?php if(isset($errors['password']) && $errors['password']=='length'){ ?>
            <p class="text-danger">パスワードは４〜１６文字で入力してください</p>
            <?php } ?>
          </div>
          <div class="form-group">
            <label for="password">自動サインイン</label><input type="checkbox" name="" value="自動サインイン">
          </div>
          <div class="form-group">
            <input type="submit" class="btn btn-info" value="サインイン">
          </div>
        </form>
      </div>
    </div>
  </div>
  <script src="assets/js/jquery-3.1.1.js"></script>
  <script src="assets/js/jquery-migrate-1.4.1.js"></script>
  <script src="assets/js/bootstrap.js"></script>
</body>

</html>
