<?php

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













 ?>