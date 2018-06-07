
<?php
  	session_start();
  	require('dbconnect.php');
    $errors=array();

  	//SELECT usersテーブルからidを１件だけ取り出す
  	$sql = 'SELECT * FROM `users` WHERE `id`=?';
  	$data = array($_SESSION['id']);
  	$stmt = $dbh->prepare($sql);
  	$stmt->execute($data);
  	//変数$signin_user に取り出したレコードを代入する
  	$signin_user = $stmt->fetch(PDO::FETCH_ASSOC);
  	//写真と名前をレコードから取り出す
  	//変数$name に名前を代入する
  	$name = $signin_user['name'];
  	//変数img_name に写真のファイル名を代入する
  	$img_name = $signin_user['img_name'];
    //ポスト送信（ボタンを押した）時の処理
    if (!empty($_POST)) {
      $feed = $_POST['feed'];
      //投稿内容空チェック
      if($feed != "") {


      //投稿処理
        $sql='INSERT INTO `feeds` SET `feed`=?,`user_id`=?,`created`=NOW()';
        $data=array($feed,$signin_user['id']);
        $stmt=$dbh->prepare($sql);
        $stmt->execute($data);

        //headerとexitセットでないと、ページが2重に動作を続けてしまうため】
        header('location:timeline.php');//ブラウザからリク＆レスポンスを行う
        exit();//POSTの中身が残ったままのため、読み込みをそこで止める

      }else{
        $errors['feed'] = 'blank';//未入力だとエラー
     }
    }
// -----------Pagingの処理-----------------------------
      $page = '';//ページ番号が入る変数
      $page_row_number = 5;//1ページあたりに表示するデータの数

      if (isset($_GET['page'])) {
        $page = $_GET['page'];
      }else{
        $page = 1;
      }
    //不正なページ番号を指定された場合のみの対処
      //データの件数から、最大ページ数を計算する
      $sql_count='SELECT COUNT(*) AS `cnt` FROM `feeds`';
      $stmt_count=$dbh->prepare($sql_count);
      $stmt_count->execute();

      $record_cnt = $stmt_count->fetch(PDO::FETCH_ASSOC);

      //ページ数を取得
      $all_page_number = ceil($record_cnt['cnt'] / $page_row_number);//ceil関数

      //不正に大きい数字を指定された場合、最大ページ番号に変換
      //min関数:カンマ区切りの数字の中から最小値を取り出す関数
      $page=min($page,$all_page_number);
      //データを取得する開始番号を計算
      $start = ($page -1)*$page_row_number;
      //max関数：カンマ区切りで羅列された数字の中から、最大の数を返す
      $page=max($page,1);



//---------------------------------------------------------
    //検索ボタンが押されたら、あいまい検索
    //検索ボタンが押された＝GET送信されたserch_wordというキーのデータがある
      if (isset($_GET['search_word']) == true) {
             //あいまい検索用SQL文
      $sql = 'SELECT f.*,u.name,u.img_name FROM feeds AS f LEFT JOIN users AS u ON f.user_id = u.id WHERE f.`feed` LIKE "%'.$_GET['search_word'].'%" ORDER BY f.`created` DESC';
      }else{
    //通常（検索ボタンを押していない）は全件検索
    // LEFT JOINで全件取得
    $sql = "SELECT f.*,u.name,u.img_name FROM feeds AS f LEFT JOIN users AS u ON f.user_id = u.id WHERE 1 ORDER BY `created` DESC LIMIT $start,$page_row_number";
    //$sql = 'SELECT * FROM feeds';//テーブル、カラム名の``と、WHERE 1 は省略できる
      }


    $data = array();
    $stmt = $dbh->prepare($sql);
    $stmt->execute($data);//executeで変換したタイミングではObject型

    // 表示用の配列を初期化
    $feeds = array();

    while (true) {
        $record = $stmt->fetch(PDO::FETCH_ASSOC);//いちfetchいちレコード
                                                 //fetchは自動で次のレコードを取得する

        if ($record == false) {
            break;
        }

//---------------------------------------------------------
        //commentテーブルから取得できているfeedに対してのコメントを取得
        $comment_sql='SELECT `c`.*,`u`.`name`,`u`.`img_name` FROM `comments` AS `c` LEFT JOIN `users` AS `u` ON `c`.`user_id`=`u`.`id` WHERE `feed_id` = ?';
        $comment_data = array($record['id']);
        $comment_stmt = $dbh->prepare($comment_sql);
        $comment_stmt->execute($comment_data);
        //コメントを格納するためのarray変数
        $comments_array = array();

        while (1) {
          $comment_record=$comment_stmt->fetch(PDO::FETCH_ASSOC);
  
          if ($comment_record == false) {
            break;
          }
        $comments_array[] = $comment_record;

        }
        //一行分の変数(連想配列)に、新しくcommentsというキーを追加し、コメント情報を代入（!超重要!）
        $record['comments']=$comments_array;

        //like数を取得するSQL文を作成
        $like_sql = 'SELECT COUNT(*) AS `like_cnt` FROM `likes` WHERE `feed_id` = ?';
        $like_data = array($record['id']);
        //SQL文実行
        $like_stmt = $dbh->prepare($like_sql);
        $like_stmt->execute($like_data);
        //like数を取得
        $like = $like_stmt->fetch(PDO::FETCH_ASSOC);
        $record['like_cnt'] = $like['like_cnt'];



        //like済みか判断するSQL文を作成
        $like_flag_sql = 'SELECT COUNT(*) AS `like_flag` FROM `likes` WHERE `user_id`=? AND `feed_id`=?';
        $like_flag_data =array($_SESSION['id'],$record['id']);
        //SQL文実行
        $like_flag_stmt = $dbh->prepare($like_flag_sql);
        $like_flag_stmt->execute($like_flag_data);
        //likeしている数を取得
        $like_flag = $like_flag_stmt->fetch(PDO::FETCH_ASSOC);

        if ($like_flag['like_flag'] > 0) {
          $record['like_flag'] = 1;
        }else{
          $record['like_flag'] = 0;
        }

        //いいね済みのリンクが押されたときは配列にfeed_select=likesを代入する
        if (isset($_GET['feed_select']) && ($_GET['feed_select'] == 'likes') && ($record['like_flag'] == 1)) {
        $feeds[] = $record;
        }
        //feed_selectがされてないときは全体表示
        if (!isset($_GET['feed_select'])) {
        $feeds[] = $record;
        }
        //新着順が押されたとき、全件表示
        if (isset($_GET['feed_select']) && ($_GET['feed_select'] == 'news')) {
        $feeds[] = $record;
        }


        // $feeds[] = $record;//配列追加構文（配列への要素追加)
      //$feeds[2] = $record;//配列上書き構文（配列3番目の要素上書き)

    }


      // echo "<pre>";//確認用
      // var_dump($_POST);
      // echo "</pre>";


      // echo "<pre>";//確認用
      // var_dump($feeds['record'][]);
      // echo "</pre>";

      // echo "<pre>";//確認用
      // var_dump($feeds);
      // echo "</pre>";



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

<body style="margin-top: 60px; background: #E4E6EB;">

<?php include('navbar.php'); ?>

  <div class="container">
    <div class="row">
      <div class="col-xs-3">
        <ul class="nav nav-pills nav-stacked">
          <?php if (isset($_GET['feed_select']) && ($_GET['feed_select'] == 'likes')){ ?>
          <li><a href="timeline.php?feed_select=news">新着順</a></li>
          <li class="active"><a href="timeline.php?feed_select=likes">いいね！済み</a></li>
          <?php }else{ ?>
          <li class="active"><a href="timeline.php?feed_select=news">新着順</a></li>
          <li><a href="timeline.php?feed_select=likes">いいね！済み</a></li>
          <?php } ?>
          <li><a href="timeline.php?feed_select=follows">フォロー</a></li>
        </ul>
      </div>
      <div class="col-xs-9">
        <div class="feed_form thumbnail">
          <form method="POST" action="">
            <div class="form-group">
              <textarea name="feed" class="form-control" rows="3" placeholder="Happy Hacking!" style="font-size: 24px;"></textarea><br>
              <?php
              if (isset($errors['feed']) && $errors['feed'] == 'blank') { ?>
                 <p class="alert alert-danger">投稿データを入力してください</p>
               <?php } ?>
            </div>
            <input type="submit" value="投稿する" class="btn btn-primary">
          </form>
        </div>

        <!-- ここから投稿サムネイル -->

        <?php foreach ($feeds as $feed) { ?>
          <div class="thumbnail">
            <div class="row">
              <div class="col-xs-1">
                <img src="user_profile_img/<?php echo $feed['img_name']; ?>" width="40">
              </div>
              <div class="col-xs-11">
                <?php echo $feed['name']; ?><br>
                <a href="#" style="color: #7F7F7F;"><?php echo $feed['created']; ?></a>
              </div>
            </div>
            <div class="row feed_content">
              <div class="col-xs-12" >
                <span style="font-size: 24px;"><?php echo $feed['feed']; ?></span>
              </div>
            </div>
            <div class="row feed_sub">
              <div class="col-xs-12">
                <!-- <form method="POST" action="" style="display: inline;"> -->
                  <input type="hidden" name="feed_id" >

                    <!-- <input type="hidden" name="like" value="like"> -->
                    <?php if ($feed['like_flag'] == 0) { ?>
                    <a href="like.php?feed_id=<?php echo $feed['id']; ?>">
                    <button type="submit" class="btn btn-default btn-xs"><i class="fa fa-thumbs-up" aria-hidden="true"></i>いいね！</button></a>
                    <?php }else{ ?>
                    <a href="unlike.php?feed_id=<?php echo $feed['id']; ?>">
                    <button type="submit" class="btn btn-default btn-xs"><i class="fa fa-thumbs-up" aria-hidden="true"></i>いいね!を取消す</button></a>
                    <?php } ?>
                <!-- </form> -->
                <?php if ($feed['like_cnt'] > 0) { ?>
                <span class="like_count">いいね数 :<?php echo $feed['like_cnt']; ?></span> <?php } ?>

                <a href="#collapseComment<?php echo $feed['id'] ?>" data-toggle="collapse" aria-expanded="false">
            <?php if ($feed['comment_count'] == 0){ ?>
                  <span class="comment_count">コメント</span></a>
            <?php }else{ ?>
                  <span class="comment_count">コメント数 : <?php echo $feed['comment_count']; ?></span></a>
            <?php } ?>

            <?php if ($feed['user_id'] == $_SESSION['id']) { ?>
                  <a href="edit.php?feed_id=<?php echo $feed['id'] ?>" class="btn btn-success btn-xs">編集</a>
                  <a onClick="return confirm('本当に消しますか？');" href="delete.php?feed_id=<?php echo $feed['id'] ?>" class="btn btn-danger btn-xs">削除</a>
                   <?php } ?>
              </div>

              <!-- コメントが押されたら表示される領域 -->
              <!-- <div class="collapse" id="collapseComment"> -->
              <!-- 表示の確認 -->
              <!-- </div> -->
              <?php include('comment_view.php'); ?>
            </div>
          </div>
          <?php } 
      // echo "<pre>";//確認用
      // var_dump($feeds);
      // echo "</pre>"; ?>
          <!-- ここまで投稿サムネイル -->
        <div aria-label="Page navigation">
          <ul class="pager">
            <!-- 1ページ目で押せないようclass=disabledで設定してある -->
            <?php if ($page == 1) {?>
            <li class="previous disabled"><a><span aria-hidden="true">&larr;</span> Preview</a></li>
            <?php }else{ ?>
            <li class="previous"><a href="timeline.php?page=<?php echo $page-1; ?>"><span aria-hidden="true">&larr;</span> Preview</a></li>
            <?php } ?>
            <?php if ($page == $all_page_number) { ?>
            <li class="next disabled"><a>Next <span aria-hidden="true">&rarr;</span></a></li>
            <?php }else{ ?>
            <li class="next"><a href="timeline.php?page=<?php echo $page+1; ?>">Next <span aria-hidden="true">&rarr;</span></a></li>
            <?php } ?>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <script src="assets/js/jquery-3.1.1.js"></script>
  <script src="assets/js/jquery-migrate-1.4.1.js"></script>
  <script src="assets/js/bootstrap.js"></script>

</body>
</html>
