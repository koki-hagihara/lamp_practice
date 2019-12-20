<?php
require_once 'functions.php';
require_once 'db.php';

function get_user($db, $user_id){
  //usersテーブルから、user_idが$user_idであるレコードのカラムデータを取得する
  //LIMIT句は指定した行数だけデータ取得
  $sql = "
    SELECT
      user_id, 
      name,
      password,
      type
    FROM
      users
    WHERE
      user_id = {$user_id}
    LIMIT 1
  ";
//上記のSQLをfetch_query関数でfetchメソッドまで実行し結果をリターン
  return fetch_query($db, $sql);
}


function get_user_by_name($db, $name){
  //usersテーブルからnameカラムが$nameのユーザーのレコードを取得
  $sql = "
    SELECT
      user_id, 
      name,
      password,
      type
    FROM
      users
    WHERE
      name = '{$name}'
    LIMIT 1
  ";
//上記SQLを実行(fetchは失敗の場合常にfalseを返す)
  return fetch_query($db, $sql);
}


function login_as($db, $name, $password){
  //get_user_by_name関数でユーザー名$nameのユーザーの登録情報を取得し$userに格納
  $user = get_user_by_name($db, $name);
  //登録情報取得失敗または登録済パスワードが入力されたものと違う場合
  if($user === false || $user['password'] !== $password){
    //falseを返す
    return false;
  }
  //set_session関数でセッション変数登録
  set_session('user_id', $user['user_id']);
  //データベースから取得したユーザー登録情報をリターン
  return $user;
}


function get_login_user($db){
  //get_session関数でセット済の$_SESSION['user_id']変数の値を取得し$login_user_idへ格納
  $login_user_id = get_session('user_id');

  //get_user関数でユーザーIDが$login_user_idのユーザーの登録情報を取得しリターン
  return get_user($db, $login_user_id);
}


function regist_user($db, $name, $password, $password_confirmation) {
  //is_valid_user関数でユーザー名とパスワードをバリデーション
  if( is_valid_user($name, $password, $password_confirmation) === false){
    //is_valid_user関数の結果falseであればfalseをreturn
    return false;
  }
  //is_valid_user関数の結果trueであればinsert_user関数でデータベースへの新規登録実行
  return insert_user($db, $name, $password);
}


function is_admin($user){
  //登録されているユーザータイプが「管理者」かどうかチェック
  return $user['type'] === USER_TYPE_ADMIN;
}


function is_valid_user($name, $password, $password_confirmation){
  // 短絡評価を避けるため一旦代入。
  //ユーザー名をバリデーションしtrue or falseを$is_valid_user_nameに代入
  $is_valid_user_name = is_valid_user_name($name);
  //パスワードをバリデーションしtrue or falseを$is_valid_passwordに代入
  $is_valid_password = is_valid_password($password, $password_confirmation);
  //ユーザー名・パスワードのバリデーション結果をリターン、左右辺とも条件に合えばtrue
  return $is_valid_user_name && $is_valid_password ;
}


//ユーザー名が正しいものかチェックする関数、正しくなければfalseを返す
function is_valid_user_name($name) {
  $is_valid = true;
  //is_valid_length関数で文字数の判定、6文字以上100文字以内でなかったら...
  if(is_valid_length($name, USER_NAME_LENGTH_MIN, USER_NAME_LENGTH_MAX) === false){
    //set_error関数でエラーメッセージ格納
    set_error('ユーザー名は'. USER_NAME_LENGTH_MIN . '文字以上、' . USER_NAME_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  //is_alphanumeric関数で半角英数字かどうかの判定、半角英数字でなかったら...
  if(is_alphanumeric($name) === false){
    //set_error関数でエラーメッセージ格納
    set_error('ユーザー名は半角英数字で入力してください。');
    $is_valid = false;
  }
  return $is_valid;
}


//パスワードが正しいものかどうかチェックする関数、正しくなければfalseを返す
function is_valid_password($password, $password_confirmation){
  $is_valid = true;
  //is_valid_length関数で文字数の判定、6文字以上100文字以内でなかったら...
  if(is_valid_length($password, USER_PASSWORD_LENGTH_MIN, USER_PASSWORD_LENGTH_MAX) === false){
    //set_error関数でエラーメッセージ格納
    set_error('パスワードは'. USER_PASSWORD_LENGTH_MIN . '文字以上、' . USER_PASSWORD_LENGTH_MAX . '文字以内にしてください。');
    $is_valid = false;
  }
  //is_alphanumeric関数で半角英数字かどうかの判定、半角英数字でなかったら...
  if(is_alphanumeric($password) === false){
    //set_error関数でエラーメッセージ格納
    set_error('パスワードは半角英数字で入力してください。');
    $is_valid = false;
  }
  //パスワードが再確認用パスワードと一致いなければ
  if($password !== $password_confirmation){
    //set_error関数でエラーメッセージ格納
    set_error('パスワードがパスワード(確認用)と一致しません。');
    $is_valid = false;
  }
  return $is_valid;
}


//新規ユーザー登録(データベースへ情報インサート)する関数
function insert_user($db, $name, $password){
  //ユーザー名とパスワードをデータベースへインサート
  $sql = "
    INSERT INTO
      users(name, password)
    VALUES ('{$name}', '{$password}');
  ";
//execute_query関数でsqlを実行しリターン
  return execute_query($db, $sql);
}

