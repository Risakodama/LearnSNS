<?php
	session_start();

	//$_SESSION変数の破棄（ローカル）= 空の配列を代入
	$_SESSION = array();//session_destroyではローカルサーバーに残ってしまうため

	//セッションを破棄（サーバー）
	session_destroy();

	header('location:signin.php');
	exit();
 ?>