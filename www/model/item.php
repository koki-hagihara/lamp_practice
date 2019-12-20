<?php
require_once 'functions.php';
require_once 'db.php';

// DB利用

function get_item($db, $item_id){
  //itemsテーブルから商品情報を取得するSQL
  $sql = "
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
    WHERE
      item_id = {$item_id}
  ";
//fetch_query関数の結果を返す(sql実行しfetchメソッドでデータ取得)
  return fetch_query($db, $sql);
}

function get_items($db, $is_open = false){
  //itemsテーブルから商品情報を該当のもの全て取得するsql
  $sql = '
    SELECT
      item_id, 
      name,
      stock,
      price,
      image,
      status
    FROM
      items
  ';
  //get_items関数呼び出し時、引数でtrueが与えられてきたら
  if($is_open === true){
    //.=で右辺を$sqlに追加代入(WHERE句で「ステータスが公開のもの」という条件追加)
    $sql .= '
      WHERE status = 1
    ';
  }
//fetch_all_query関数の結果を返す(sql実行しfetchAllメソッドでデータ取得)
  return fetch_all_query($db, $sql);
}

function get_all_items($db){
  //get_items関数に引数$dbのみ与え実行した結果を返す
  return get_items($db);
}

function get_open_items($db){
  return get_items($db, true);
}


function regist_item($db, $name, $price, $stock, $status, $image){
  //get_upload_filename関数でランダムなファイルネームを取得し$filenameへ格納(失敗の場合空文字)
  $filename = get_upload_filename($image);
  //validate_item関数により入力値のチェックを行い1項目でもfalseが返ってくれば...
  if(validate_item($name, $price, $stock, $filename, $status) === false){
    //falseを返す
    return false;
  }
  //入力値に問題なければ
  return regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename);
}


function regist_item_transaction($db, $name, $price, $stock, $status, $image, $filename){
  //トランザクション開始
  $db->beginTransaction();
  //insert_item関数でデータベースへのインサート成功かつ
  if(insert_item($db, $name, $price, $stock, $filename, $status) 
  //画像ファイルを一時フォルダからの移動保存に成功すれば
    && save_image($image, $filename)){
      //コミット処理
    $db->commit();
    //trueを返す
    return true;
  }
  //インサート処理か画像保存どちらか失敗すればロールバック
  $db->rollback();
  //falseを返す
  return false;
}


function insert_item($db, $name, $price, $stock, $filename, $status){
  //商品ステータスの値(0か1)を$status_valueへ代入
  $status_value = PERMITTED_ITEM_STATUSES[$status];
  //itemsテーブルに新規追加商品情報をインサート
  $sql = "
    INSERT INTO
      items(
        name,
        price,
        stock,
        image,
        status
      )
    VALUES('{$name}', {$price}, {$stock}, '{$filename}', {$status_value});
  ";
//execute_query関数でsql実行(executeメソッド実行結果がリターン)
  return execute_query($db, $sql);
}

function update_item_status($db, $item_id, $status){
  //itemsテーブルの該当商品のステータスを変更するSQL文
  $sql = "
    UPDATE
      items
    SET
      status = {$status}
    WHERE
      item_id = {$item_id}
    LIMIT 1
  ";
  //上記SQLをexecuteまで行い実行
  return execute_query($db, $sql);
}

function update_item_stock($db, $item_id, $stock){
  //itemsテーブルの該当商品の在庫数変更
  $sql = "
    UPDATE
      items
    SET
      stock = {$stock}
    WHERE
      item_id = {$item_id}
    LIMIT 1
  ";
  //execute_query関数でSQLを実行
  return execute_query($db, $sql);
}

function destroy_item($db, $item_id){
  //データベースから該当商品データを取得し$itemへ代入
  $item = get_item($db, $item_id);
  //取得に失敗し中身がfalseであれば
  if($item === false){
    //falseを返す
    return false;
  }
  //商品情報取得成功していればトランザクション開始
  $db->beginTransaction();
  //delete_item関数で該当レコード削除成功かつ
  if(delete_item($db, $item['item_id'])
  //画像ファイル削除成功したら
    && delete_image($item['image'])){
      //コミット処理
    $db->commit();
    //処理成功でtrueを返す
    return true;
  }
  //処理失敗の場合ロールバック処理
  $db->rollback();
  //falseを返す
  return false;
}

function delete_item($db, $item_id){
  //itemsテーブルから該当商品のレコードを削除するSQL文
  $sql = "
    DELETE FROM
      items
    WHERE
      item_id = {$item_id}
    LIMIT 1
  ";
  
  //SQL文実行し結果をリターン
  return execute_query($db, $sql);
}


// 非DB

function is_open($item){
  //商品の公開ステータスをリターン
  return $item['status'] === 1;
}

function validate_item($name, $price, $stock, $filename, $status){
  //is_valid_item_name関数に$name(商品名)を渡し正しい入力かチェック、返り値は$is_valid(true or false)
  $is_valid_item_name = is_valid_item_name($name);
  //is_valid_item_price関数に$priceを渡し入力値が正の整数であるかをチェック、返り値は$is_valid(true or false)
  $is_valid_item_price = is_valid_item_price($price);
  //is_valid_item_stock関数に$stockを渡し、入力値が正の整数であるかをチェック、返り値は$is_valid(true or false)
  $is_valid_item_stock = is_valid_item_stock($stock);
  //is_valid_item_filename関数に$filenameを渡し、正しくファイルネームがつけられているかチェック、返り値は$is_valid(true or false)
  $is_valid_item_filename = is_valid_item_filename($filename);
  //is_valid_item_status関数に$statusを渡し、$statusが'open''close'どちらかであることをチェック、返り値は$is_valid(true or false)
  $is_valid_item_status = is_valid_item_status($status);

  //上記バリデーションの結果true or falseが格納された下記変数をリターン
  return $is_valid_item_name
    && $is_valid_item_price
    && $is_valid_item_stock
    && $is_valid_item_filename
    && $is_valid_item_status;
}

function is_valid_item_name($name){
  //$is_validにtrueを代入しておく
  $is_valid = true;
  //is_valid_length関数に$name(商品名)・最低入力文字数・最高入力文字数100を渡しリターンがfalseであれば...
  if(is_valid_length($name, ITEM_NAME_LENGTH_MIN, ITEM_NAME_LENGTH_MAX) === false){
    //set_error関数にエラーメッセージを渡し$_SESSIONに格納
    set_error('商品名は'. ITEM_NAME_LENGTH_MIN . '文字以上、' . ITEM_NAME_LENGTH_MAX . '文字以内にしてください。');
    //$is_validにfalseを格納
    $is_valid = false;
  }
  //$is_validをリターン
  return $is_valid;
}

function is_valid_item_price($price){
  //$is_validにtrueを代入しておく
  $is_valid = true;
  //is_positive_integer関数に$priceを渡しバリデーション、返り値がfalseであれば
  if(is_positive_integer($price) === false){
    //set_error関数にエラーメッセージを渡し$_SESSIONに格納
    set_error('価格は0以上の整数で入力してください。');
    //$is_validにfalseを格納
    $is_valid = false;
  }
  //$is_validをリターン
  return $is_valid;
}

function is_valid_item_stock($stock){
  //$is_validにtrueを代入しておく
  $is_valid = true;
  //is_positive_integer関数に$priceを渡しバリデーション、返り値がfalseであれば
  if(is_positive_integer($stock) === false){
    //set_error関数にエラーメッセージを渡し$_SESSIONに格納
    set_error('在庫数は0以上の整数で入力してください。');
    //$is_validにfalseを格納
    $is_valid = false;
  }
  //$is_validをリターン
  return $is_valid;
}

function is_valid_item_filename($filename){
  //$is_validにtrueを代入しておく
  $is_valid = true;
  //もし$filenameが空文字であれば
  if($filename === ''){
    //$is_validにfalseを格納
    $is_valid = false;
  }
  //$is_validをリターン
  return $is_valid;
}

function is_valid_item_status($status){
  //$is_validにtrueを代入しておく
  $is_valid = true;
  //PERMITTED_ITEM_STATUSES === array('open' => 1,'close' => 0,)
  //$statusが'open'か'close'でなければ...
  if(isset(PERMITTED_ITEM_STATUSES[$status]) === false){
    //$is_validにfalseを格納
    $is_valid = false;
  }
  //$is_validをリターン
  return $is_valid;
}