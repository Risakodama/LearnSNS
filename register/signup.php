<?php
    session_start();

    $errors = array();   // この配列の意味はエラーの種類

    if (!empty($_POST)) {  //POST送信があったときに以下を実行する
        $name = $_POST['input_name'];
        $email = $_POST['input_email'];
        $password = $_POST['input_password'];
        $count = strlen($password);

        // ユーザー名の空チェック
        if ($name == '') {
            $errors['name'] = 'blank';
        }

        // メールアドレスの空チェック
        if ($email == '') {
            $errors['email'] = 'blank';
        }
        else {
            //1.DB接続
            require('../dbconnect.php');

            //2.SQL
            $sql = 'SELECT COUNT(*) as `cnt` FROM `users` WHERE `email`=?';
            $data = array($email);
            $stmt = $dbh->prepare($sql);
            $stmt->execute($data);

            //3.DB切断
            $dbh = null;

            //4.取り出し
            $rec = $stmt->fetch(PDO::FETCH_ASSOC);

            // var_dump($rec);

            if ( $rec['cnt'] > 0) {  //メールアドレスの数が０より大きい＝すでに登録がある
                //重複しているメールアドレスがあったら
                $errors['email'] = 'duplicate';
            }
        }

        // パスワードの空チェック
        if ($password == '') {
            $errors['password'] = 'blank';
        }
        elseif ( $count < 4 || 16 < $count) {
            $errors['password'] = 'length';
        }

        // 画像名を取得
        // $_FIlES はtype='file'を使った時に使うスーパーグローバル変数
        $file_name = $_FILES['input_img_name']['name'];
         if(!empty($file_name)) {
              //　拡張子チェック
              $file_type = substr($file_name, -4); // 画像名の後ろから4文字取得
              $file_type = strtolower($file_type); //比較するために取得した拡張子を小文字に変換する
              if( $file_type != '.jpg' && $file_type != '.png' && $file_type != '.gif' && $file_type != 'jpeg' ) {
                //エラーの時の処理
                $errors['img_name'] = 'type';
              }
         }
         else {
              // ファイルがない時の処理
              $errors['img_name'] = 'blank';

         }

        // echo "<pre>";
        // var_dump($_FILES);
        // echo "</pre>";

        if(empty($errors)) {
            // エラーがなかった時の処理
            date_default_timezone_set('Asia/Manila');  // フィリピン時間に設定
            $date_str = date('YmdHis');  // この行を実行した日時を取得
            $submit_file_name = $date_str.$file_name;
            echo $date_str;
            echo "<br>";
            echo $submit_file_name;
            // move_uploaded_file(テンポラリパス,保存したい場所+ファイル名)
            move_uploaded_file($_FILES['input_img_name']['tmp_name'], '../user_profile_img/'.$submit_file_name);

            var_dump($_FILES);

            // $_SESSION サーバに保存されるスーパーグローバル変数
            // ログインしているユーザ情報などを保存しておくことが多い
            $_SESSION['register']['name'] = $_POST['input_name'];
            $_SESSION['register']['email'] = $_POST['input_email'];
            $_SESSION['register']['password'] = $_POST['input_password'];
            // 上記3つは$_SESSION['register'] = $_POST;という書き方で1文にまとめることもできます
            $_SESSION['register']['img_name'] = $submit_file_name;


            header('Location: check.php');
            exit();
        }

    }


?>
<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <title>Learn SNS</title>
  <link rel="stylesheet" type="text/css" href="../assets/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="../assets/font-awesome/css/font-awesome.css">
  <link rel="stylesheet" type="text/css" href="../assets/css/style.css"> <!-- 追加 -->

</head>
<body style="margin-top: 60px">
  <div class="container">
    <div class="row">
<div class="col-xs-8 col-xs-offset-2 thumbnail">
        <h2 class="text-center content_header">アカウント作成</h2>
        <form method="POST" action="signup.php" enctype="multipart/form-data">
          <div class="form-group">
            <label for="name">ユーザー名</label>
            <input type="text" name="input_name" class="form-control" id="name" placeholder="山田 太郎">
            <?php if(isset($errors['name']) && $errors['name'] == 'blank') { ?>
              <p class="text-danger">ユーザー名を入力してください</p>
            <?php } ?>
          </div>
          <div class="form-group">
            <label for="email">メールアドレス</label>
            <input type="email" name="input_email" class="form-control" id="email" placeholder="example@gmail.com">
            <?php if(isset($errors['email']) && $errors['email'] == 'blank') { ?>
              <p class="text-danger">メールアドレスを入力してください</p>
            <?php } ?>
            <?php if(isset($errors['email']) && $errors['email'] == 'duplicate') { ?>
              <p class="text-danger">すでに登録されているメールアドレスです</p>
            <?php } ?>


          </div>
          <div class="form-group">
            <label for="password">パスワード</label>
            <input type="password" name="input_password" class="form-control" id="password" placeholder="4 ~ 16文字のパスワード">
            <?php if(isset($errors['password']) && $errors['password'] == 'blank') { ?>
              <p class="text-danger">パスワードを入力してください</p>
            <?php } ?>
            <?php if(isset($errors['password']) && $errors['password'] == 'length') { ?>
              <p class="text-danger">パスワードは４〜１６文字で入力してください</p>
            <?php } ?>
          </div>
          <div class="form-group">
            <label for="img_name">プロフィール画像</label>
            <input type="file" name="input_img_name" id="img_name" accept="image/*">
            <?php if(isset($errors['img_name']) && $errors['img_name'] == 'blank') { ?>
              <p class="text-danger">画像を選択してください</p>
            <?php } ?>
            <?php if(isset($errors['img_name']) && $errors['img_name'] == 'type') { ?>
              <p class="text-danger">拡張子が「jpg」「png」「gif」の画像を選択してください</p>
            <?php } ?>
          </div>
          <input type="submit" class="btn btn-default" value="確認">
          <a href="../signin.php" style="float: right; padding-top: 6px;" class="text-success">サインイン</a>
        </form>
      </div>
    </div>
  </div>
  <script src="../assets/js/jquery-3.1.1.js"></script>
  <script src="../assets/js/jquery-migrate-1.4.1.js"></script>
  <script src="../assets/js/bootstrap.js"></script>
</body>
</html>
