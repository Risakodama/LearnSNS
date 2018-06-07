<?php

//session変数を使えるようにする
	session_start();
//
	$follower_id=$_GET['follower_id'];
//
	// $user_id=$_SESSION['id'];
//DBに接続
	require('dbconnect.php');

	// if ($like_flag == 0) {
	//SQL文(INSERT文)
		$sql = 'DELETE FROM `followers` WHERE `user_id`=? AND `follower_id`=?';
		// $sql='INSERT INTO `followers` (`id`,`user_id`, `follower_id`) VALUES (NULL,?, ?)';
	//SQL実行
	    $data = array($_SESSION['id'],$follower_id);
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
	    header('location:profile.php');

 ?>