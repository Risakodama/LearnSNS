<?php

	//feed_id を取得
	$feed_id = $_GET['feed_id'];


  	//更新ボタンが押されたとき-->>formタブのPOST送信がされたとき
  	if (!empty($_POST)) {
  		//DBに保存-->>UPDATE文(UPDATE テーブル名 SET カラム名=値(,カラム名2=値2)WHERE　条件;)
		require('dbconnect.php');
  		$update_sql='UPDATE `feeds` SET `feed`=? WHERE `feeds`.`id`=?';
  		$data=array($_POST['feed'],$feed_id);
  			//sql文実行
  			  	$stmt = $dbh->prepare($update_sql);
			  	$stmt->execute($data);
  		//timeline一覧に戻る
  		header('Location:timeline.php');
  	}

	//編集したいfeedsテーブルのデータを取得して、入力欄に初期表示しましょう
	//ポイント１　書いた人の情報を表示したい→テーブル結合を使う
	//ポイント２　編集したいfeedsテーブルの値は一件だけ（繰り返し処理は必要ない

	//SQL文作成
	require('dbconnect.php');
	//SQL文実行
		//SQLエラー文 ambiguous :あいまい
  	$sql = "SELECT `feeds`.*,`users`.`name`,`users`.`img_name` FROM `feeds` LEFT JOIN `users` ON `feeds`.`user_id`=`users`.`id` WHERE `feeds`.`id`=$feed_id";
  	$stmt = $dbh->prepare($sql);
  	$stmt->execute();
  	//フェッチ ループがない時は$feedを連想配列にする必要なし
  	$feed = $stmt->fetch(PDO::FETCH_ASSOC);




	  echo "<pre>";//確認用
      var_dump($feed);
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
<body style="margin-top: 60px;">
	<div class="container">
	  <div class="row">
	  	<!-- ココニコンテンツ -->
	  	<div class="col-xs-4 col-xs-offset-4">
	  	  <form class="form-group" method="POST">
	  	  	<img src="user_profile_img/<?php echo $feed['img_name'] ?>" width="60">
	  	  	<?php echo $feed['name'] ?><br>
	  	  	<?php echo $feed['created'] ?><br>
	  	  	<textarea name="feed" class="form-control"><?php echo $feed['feed'] ?></textarea>
	  	  	<input type="submit" name="" value="更新" class="btn btn-warning btn-xs">
	  	  </form>
	  	</div>

	  </div>
	</div>


  <script src="assets/js/jquery-3.1.1.js"></script>
  <script src="assets/js/jquery-migrate-1.4.1.js"></script>
  <script src="assets/js/bootstrap.js"></script>
</body>
</html>