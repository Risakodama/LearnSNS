<?php

//session変数を使えるようにする
	session_start();
//feed_idを取得
	$feed_id=$_GET['feed_id'];
//like_flagを取得
	// $like_flag=$_GET['like_flag'];
//DBに接続
	require('dbconnect.php');

	// if ($like_flag == 0) {
	//SQL文(INSERT文)
		$sql='INSERT INTO `likes` (`user_id`, `feed_id`) VALUES (?, ?)';
	//SQL実行
	    $data = array($_SESSION['id'],$feed_id);
	    $stmt = $dbh->prepare($sql);
	    $stmt->execute($data);
	// }else{
		// $sql='DELETE FROM `likes` WHERE `user_id`=?,`feed_id`=?';
	//SQL実行
	    // $data = array($_SESSION['id'],$feed_id);
	    // $stmt = $dbh->prepare($sql);
	    // $stmt->execute($data);
	// }
	//一覧に戻る
	    header('location:timeline.php');

 ?>