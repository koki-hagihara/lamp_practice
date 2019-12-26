<?php
//定数ファイル読み込み
require_once '../conf/const.php';
//凡用関数ファイル読み込み
require_once MODEL_PATH . 'functions.php';
//ユーザー登録情報に関する関数ファイル読み込み
require_once MODEL_PATH . 'user.php';
//商品情報登録に関する関数ファイル読み込み
require_once MODEL_PATH . 'item.php';

//セッション開始(ログインチェック)
session_start();

//is_logined関数でセット済セッション変数の値を確認、空文字であれば...
if(is_logined() === false){
  //ログインページへリダイレクト
  redirect_to(LOGIN_URL);
}

//データベース接続
$db = get_db_connect();


//get_login_user関数でデータベースからセット済セッション変数の値をもとにユーザー情報を取得し$userへ代入
$user = get_login_user($db);


//ユーザーが管理者でなければ
if(is_admin($user) === false){
  //ログインページへリダイレクト
  redirect_to(LOGIN_URL);
}

//ステータス変更を行う商品のidを取得し$item_idへ代入
$item_id = get_post('item_id');
//変更後ステータス(open or close)の値を$changes_toへ代入
$changes_to = get_post('changes_to');

//ステータス「open」への変更の場合
if($changes_to === 'open'){
  //update_item_status関数の第3引数に定数ファイルで定めた「1」を渡しアップデートSQL実行
  update_item_status($db, $item_id, ITEM_STATUS_OPEN);
  //$_SESSIONにメッセージ代入
  set_message('ステータスを変更しました。');
  //ステータス「close」への変更の場合
}else if($changes_to === 'close'){
  //update_item_status関数の第3引数に定数ファイルで定めた「0」を渡しアップデートSQL実行
  update_item_status($db, $item_id, ITEM_STATUS_CLOSE);
  //$_SESSIONにメッセージ代入
  set_message('ステータスを変更しました。');
}else {
  //アップデート失敗の場合$_SESSIONにエラーメッセージ代入
  set_error('不正なリクエストです。');
}

//管理者ページへリダイレクト
redirect_to(ADMIN_URL);