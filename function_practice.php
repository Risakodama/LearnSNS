<?php

//練習問題_1
	//「seedくん」という文字列を出力する「nexseed」という名前の関数を作成してみましょう。（引数はなし）
function nexseed1(){
	echo "seedくん";
}

//練習問題_2
	//練習問題１で作成した「nexseed」関数を呼び出してみましょう。
// nexseed1();

//練習問題_3
	//練習問題２で作成した「nexseed」関数に、「greeting」という引数を追加して「△△△△△、seedくん」と表示されるように呼び出してみましょう。
function nexseed_3($greeting_3){
	echo $greeting_3."、seedくん";
}
// nexseed_3("こんにちは");//直接入力することもできる

$greeting_3 = "おはようございます";
// nexseed_3($greeting_3);


//練習問題_4
	//練習問題３で作成した「nexseed」関数に、「name」という引数を追加して「△△△△△、○○さん」と表示されるように呼び出してみましょう。
function nexseed_4($greeting_4,$name){
	return $greeting_4."、".$name."さん";
}
$greeting_4 = "おはようございます";
$name = "朝子";

nexseed_4($greeting_4,$name);



//戻り値の使い方-----------
// 2つの値の合算値を出す関数
function plus($num1, $num2) {
  $result = $num1 + $num2;
  return $result;
  echo "足し算終わりました";
}

//returnは条件分岐で何回も使用できる
function checkExam($score){
	if ($score > 80) {
		return "合格！";
	}else{
		return "不合格";
	}

	if ($score > 80) {
		$kekka = "合格！";
	}else{
		$kekka = "不合格";
	}
	return $kekka;

}

//-----------------------



//練習問題_5
	//練習問題４で作成した「nexseed」関数が、あいさつ文を戻り値として返すように修正しましょう。（関数内では出力しない）
	//※戻り値を受け取ってから出力してください。
function nexseed_5($greeting_5,$name){
	return $greeting_5."、".$name."さん";
}
// echo nexseed_5("かむばっく","いまじん");
$aisatu = nexseed_5("かむばっく","いまじん");
echo $aisatu;


 ?>
 :pencil2: 演習<br>
演習問題１<br>
２つの値を乗算して出力する関数「multiplication」関数を作成し、呼び出して結果を出力してください。<br>




演習問題２<br>
２つの値の平均値を計算し、10以上だったら平均値を、9以下だったら「0」を返す関数「average」関数を作成し、呼び出して結果を出力してください。<br>



演習問題３<br>
所持金と購入した物の値段を渡すと、余ったお金を計算して返す関数「shopping」を作成し、呼び出して結果を出力してください。<br>

