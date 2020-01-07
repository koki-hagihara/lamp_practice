<?php

function dd($var){
  var_dump($var);
  exit();
}


function redirect_to($url){
  header('Location: ' . $url);
  exit;
}


function get_get($name){
  if(isset($_GET[$name]) === true){
    return $_GET[$name];
  };
  return '';
}

//POST送信されてきたデータを取得する関数
function get_post($name){
  //POSTでデータが送られてきたら
  if(isset($_POST[$name]) === true){
    //送られてきた値($_POST[$name]を返す)
    return $_POST[$name];
  };
  return '';
}


function get_file($name){
  //input type="file"でpost送信されてきたら
  if(isset($_FILES[$name]) === true){
    //$_FILES[$name]の値を返す(アップされたファイルデータが連想配列になって格納されている)
    return $_FILES[$name];
  };
  return array();
}


//$nameを渡すと$_SESSION[$name]がセット済みか確認しその値または空文字を返す
function get_session($name){
  //セッション変数が登録済であれば
  if(isset($_SESSION[$name]) === true){
    //登録されているセッション変数の値を返す
    return $_SESSION[$name];
  };
  return '';
}


function set_session($name, $value){
  //セッション変数に値を登録
  $_SESSION[$name] = $value;
}

function set_error($error){
  //セッション変数にエラーメッセージを格納
  $_SESSION['__errors'][] = $error;
}

function get_errors(){
  //$_SESSION['__errors']の中身を$errorsに代入
  $errors = get_session('__errors');
  //エラーメッセージが空であれば空の配列を返す
  if($errors === ''){
    return array();
  }
  //$_SESSION['__errors']にarray()をセット
  set_session('__errors',  array());
  //$errorsを返す
  return $errors;
}

function has_error(){
  //$_SESSION変数がセットされておりかつ中の値の数が0でない
  return isset($_SESSION['__errors']) && count($_SESSION['__errors']) !== 0;
}

function set_message($message){
  //引数のメッセージ内容を$_SESSION変数へ格納
  $_SESSION['__messages'][] = $message;
}

function get_messages(){
  $messages = get_session('__messages');
  if($messages === ''){
    return array();
  }
  set_session('__messages',  array());
  return $messages;
}

//get_session関数の結果を返す関数
function is_logined(){
  //get_session関数で取得した$_SESSION['user_id]が空文字ならFALSE、空文字じゃなければTRUE
  return get_session('user_id') !== '';
}


function get_upload_filename($file){
  //is_valid_upload_image関数によりPOST送信されてきたものか、jpegまたはpngファイルであるかをチェック
  if(is_valid_upload_image($file) === false){
    //POSTでないまたはjpeg・pngでなかった場合ファイル名を付けない
    return '';
  }
  //exif_imagetype関数は画像ファイルかどうかを調べファイル形式を返す関数(画像形式でなければFALSEを返す)
  //取得したファイル形式を$mimetypeへ格納
  $mimetype = exif_imagetype($file['tmp_name']);
  //PERMITTED_IMAGE_TYPES === array(IMAGETYPE_JPEG => 'jpg',IMAGETYPE_PNG => 'png',)
  //上記の配列の中のキー$mimetypeの値を$extへ格納('jpg'か'png'になる)
  $ext = PERMITTED_IMAGE_TYPES[$mimetype];
//get_random_string関数でランダムな文字列を取得し拡張子$extと連結したものを返す
  return get_random_string() . '.' . $ext;
}

//ランダムな文字列を取得する関数
function get_random_string($length = 20){
  //substr(対象文字列,切り出し開始位置,切り出し文字数)
  //base_convert(変換する数値(number),変換前のnumberの基数,変換後のnumberの基数)、下記は16進数から36進数への変換
  //hash('sha256',変換したい値)
  //sha256:入力されたデータに対して適当な値を返してくれるハッシュ関数の一つ
  //uniqid関数:マイクロ秒単位の現在時刻にもとづいた接頭辞つきの一意なIDを取得
  return substr(base_convert(hash('sha256', uniqid()), 16, 36), 0, $length);
}

function save_image($image, $filename){
  //move_uploaded_file関数で画像ファイルを一時フォルダから移動させて保存
  //move_uploaded_file(移動前パス,移動先パス)
  return move_uploaded_file($image['tmp_name'], IMAGE_DIR . $filename);
}

function delete_image($filename){
  //file_exists関数で引数のファイルが存在するかチェック(存在すればtrueを返す)
  if(file_exists(IMAGE_DIR . $filename) === true){
    //unlink関数でファイル削除(削除成功の場合はtrueを返す)
    unlink(IMAGE_DIR . $filename);
    //ファイル削除成功でtrueを返す
    return true;
  }
  //ファイル削除失敗でfalseを返す
  return false;
  
}


//PHP_INT_MAX:定義済み定数、整数型(int)の最大値、上限
function is_valid_length($string, $minimum_length, $maximum_length = PHP_INT_MAX){
  //文字数を取得し$lengthへ格納
  $length = mb_strlen($string);
  //$stringの文字数の判定($minimum_length以上かつ$maximum_length)
  return ($minimum_length <= $length) && ($length <= $maximum_length);
}

//半角英数字かどうかのチェック関数
function is_alphanumeric($string){
  //半角英数字かどうかをis_valid_format関数でバリデーションし、結果をリターン
  return is_valid_format($string, REGEXP_ALPHANUMERIC);
}

function is_positive_integer($string){
  //is_valid_format関数に数字の正規表現・文字列を渡しバリデーションした結果をリターン
  return is_valid_format($string, REGEXP_POSITIVE_INTEGER);
}

//バリデーションのための関数
function is_valid_format($string, $format){
  //第一引数に$format(チェック用正規表現)、第二引数に$string(チェックする文字列)を渡しバリデーション、結果をリターン
  return preg_match($format, $string) === 1;
}


function is_valid_upload_image($image){
  //is_uploaded_file関数:「引数の名前のファイル」がPOSTによりアップされたものであればTRUEを返す
  //POSTによりアップされたものでなければ...
  if(is_uploaded_file($image['tmp_name']) === false){
    //set_error関数によりセッション変数に引数のエラーメッセージ格納
    set_error('ファイル形式が不正です。');
    //falseを返す
    return false;
  }
  //exif_imagetype関数は画像ファイルかどうかを調べファイル形式を返す関数(画像形式でなければFALSEを返す)
  //取得したファイル形式を$mimetypeへ格納
  $mimetype = exif_imagetype($image['tmp_name']);
  //PERMITTED_IMAGE_TYPES === array(IMAGETYPE_JPEG => 'jpg',IMAGETYPE_PNG => 'png',)
  //ファイル形式がjpgまたはpngでなければ...
  if( isset(PERMITTED_IMAGE_TYPES[$mimetype]) === false ){
    //set_error関数でセッション変数に引数のエラーメッセージを格納
    //implode関数は配列の要素を結合して文字列にする関数、第一引数に区切り文字をセット
    set_error('ファイル形式は' . implode('、', PERMITTED_IMAGE_TYPES) . 'のみ利用可能です。');
    return false;
  }
  //ファイルがPOST送信されてきたもの、jpg・pngファイルであった場合trueを返す
  return true;
}

function entity_str($str){
  return htmlspecialchars($str,ENT_QUOTES,'UTF-8');
}

