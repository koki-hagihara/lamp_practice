<?php

function get_db_connect(){
  // MySQL用のDSN文字列
  $dsn = 'mysql:dbname='. DB_NAME .';host='. DB_HOST .';charset='.DB_CHARSET;
 
  try {
    // データベースに接続
    //PDOクラスを使ってデータベースに接続  array()はオプション,変更したい属性=>値
    $dbh = new PDO($dsn, DB_USER, DB_PASS, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8mb4'));
    //setAttributeメソッドでオプションをさらに追加 setAttribute(変更したい属性 , 値)
    //エラーが発生した場合に例外として自動的にPDOExceptionを投げる(エラーが発生した時点でスクリプトの実行を停止させることによりコード内の問題点を見つけやすくなる)
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //プリペアドステートメントのエミュレーションを無効に、デフォルトではtrueの設定になっている
    //true状態ではprepareでプレースホルダを指定しても実際にMySQLに渡る際にはプリペアドステートメントではなくなってしまう
    $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    //デフォルトのフェッチモードを設定
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
    //PDOExceptionをcatchしたらエラーメッセージを表示してプログラムを終了(exit)
    exit('接続できませんでした。理由：'.$e->getMessage() );
  }
  return $dbh;
}

function fetch_query($db, $sql, $params = array()){
  try{
    //prepareメソッドでデータベースに送信するSQL文の準備、queryメソッドと違い?で記述しておくことができる
    $statement = $db->prepare($sql);
    //SQL文に引数(?)があった場合executeメソッドの引数にその値を配列の形で指定
    $statement->execute($params);
    //成功すればSQL実行結果の値をfetchメソッドで取得し返す
    return $statement->fetch();
  }catch(PDOException $e){
    //ユーザー定義関数でセッション変数にエラーメッセージを格納
    set_error('データ取得に失敗しました。');
  }
  //fetchは失敗した場合常にFALSEを返す
  return false;
}

function fetch_all_query($db, $sql, $params = array()){
  try{
    //prepareメソッドでデータベースに送信するSQL文の準備
    $statement = $db->prepare($sql);
    //executeメソッドでsql実行
    $statement->execute($params);
    //fetchAllメソッドで結果を全行取得し返す
    return $statement->fetchAll();
  }catch(PDOException $e){
    //set_error関数でセッション変数にエラーメッセージを格納
    set_error('データ取得に失敗しました。');
  }
  //fetchAllは失敗した場合常にFALSEを返す
  return false;
}

function execute_query($db, $sql, $params = array()){
  try{
    $statement = $db->prepare($sql);
    //executeメソッドでsqlを実行しリターン
    return $statement->execute($params);
  }catch(PDOException $e){
    //データベース接続が失敗したらset_error関数でエラーメッセージを格納
    set_error('更新に失敗しました。');
  }
  //失敗の場合falseをリターン
  return false;
}