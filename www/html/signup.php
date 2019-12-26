<?php
//定数ファイルを読み込み
require_once '../conf/const.php';
//凡用関数ファイル読み込み
require_once MODEL_PATH . 'functions.php';
//セッション開始(ログインチェック)
session_start();

//$_SESSION['user_id']がセット済みであれば
if(is_logined() === true){
  //商品一覧ページへリダイレクト
  redirect_to(HOME_URL);
}
//サインアップ画面viewファイル読み込み
include_once '../view/signup_view.php';



