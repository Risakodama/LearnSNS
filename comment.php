<?php

	session_start();

      $login_user_id=$_SESSION['id'];
      $comment=$_POST['write_comment'];
      $feed_id=$_POST['feed_id'];


 	 	require('dbconnect.php');
        // $sql='INSERT INTO `comments` SET `comment`=?,`user_id`=?,`feed_id`=?';
        $sql='INSERT INTO `comments`(`comment`,`user_id`,`feed_id`,`created`) VALUES (?,?,?,NOW());';
	  	$data = array($comment,$login_user_id,$feed_id);
	  	$stmt = $dbh->prepare($sql);
	  	$stmt->execute($data);

	  	//feedsテーブルにcommentのカウントをUpdateする
        $update_sql='UPDATE `feeds` SET `comment_count` = `comment_count`+1 WHERE `id`=?';

	  	$update_data = array($feed_id);

	  	$update_stmt = $dbh->prepare($update_sql);
	  	$update_stmt->execute($update_data);

	  	header('location:timeline.php');



	  	// $sql_comment='SELECT * FROM `comments` WHERE `feed_id`=?';
	  	// $data_comment = array($feed_id);
	  	// $stmt_comment = $dbh->prepare($sql_comment);
	  	// $stmt_comment->execute($data_comment);

    //     $record_comment = $stmt_comment->fetch(PDO::FETCH_ASSOC);

    //   echo "<pre>";//確認用
    //   var_dump($record_comment);
    //   echo "</pre>";

 ?>