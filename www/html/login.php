<?php
//定数ファイル読み込み
require_once '../conf/const.php';
//凡用関数ファイル読み込み
require_once MODEL_PATH . 'functions.php';

//セッションスタート(ログインチェック)
session_start();

//is_logined関数で$_SESSION変数がセット済み(ログイン済)かどうか確認
if(is_logined() === true){
  //ログイン済であればredirect_to関数により商品一覧ページへリダイレクト
  redirect_to(HOME_URL);
}

//Viewファイル読み込み
include_once '../view/login_view.php';