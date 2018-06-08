<?php
//require('function.php');を対象サイト上部に貼り付けることで、これらの関数が使用できる
//何故なら、下記はただ関数の定義をしているだけだから



//サインインしているユーザーの情報を取得して、返す関数
//引数$dbh:データベース接続オブジェクト
//引数$user_id:サインインしているユーザーのid
function get_signin_user($dbh,$user_id){

      $sql = 'SELECT * FROM `users` WHERE `id`=?';
      $data = array($user_id);
      $stmt = $dbh->prepare($sql);
      $stmt->execute($data);
    //サインインユーザー情報の取出し-------------------------
      $signin_user = $stmt->fetch(PDO::FETCH_ASSOC);

      return $signin_user;
}

	//ログイン済みかチェックし、未ログインであればログイン画面に戻す
function check_signin($user_id){
	if(!isset($_SESSION['id']))
      header('location:signin.php');
      exit();//下の処理を実行せずに、
  }
}











 ?>