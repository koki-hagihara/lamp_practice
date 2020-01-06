<?php
require_once 'functions.php';

// トークンの生成
function get_csrf_token(){
    //ランダムな文字列を取得し$tokenに格納
    $token = get_random_string(48);
    //セッション変数$_SESSION['csrf_token']へ生成したトークンをセット
    set_session('csrf_token', $token);
    //生成したトークンをリターン
    return $token;
  }
  
// トークンのチェック
function is_valid_csrf_token($token){
  if($token === '') {
    return false;
  }
  return $token === get_session('csrf_token');
}


function token_destroy($token) {
  unset($_SESSION['csrf_token']);

  $token = '';
  return $token;
}
