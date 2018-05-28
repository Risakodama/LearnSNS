<?php
	//DBへ接続
	require('dbconnect.php');
	//DELETE文（SQL文）
	//DELETE FROM テーブル名 WHERE 条件; <<--条件がないと全て削除されてしまう
	$sql='DELETE FROM `feeds` WHERE `feeds`.`id` = ?';
	//SQL実行
	$data=array($_GET['feed_id']);
	$stmt=$dbh->prepare($sql);
	$stmt->execute($data);

	//一覧に戻る
	header('location:timeline.php');
 ?>