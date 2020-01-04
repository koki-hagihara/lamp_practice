<?php 
require_once 'functions.php';
require_once 'db.php';
require_once 'history.php';

//渡すもの:データベースへのリンク、ユーザーID
//返すもの:該当ユーザーのカートの中身情報の全行配列またはfalse
function get_user_carts($db, $user_id){
  //itemsテーブルとcartsテーブルを結合し該当ユーザーのカートの中身情報を取得
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = {$user_id}
  ";
  ////fetchAllメソッドの結果またはfalseをリターン
  return fetch_all_query($db, $sql);
}

function get_user_cart($db, $user_id, $item_id){
  //cartsJOINitemsテーブルから該当ユーザーが$item_idを以前にカートに入れているか確認
  $sql = "
    SELECT
      items.item_id,
      items.name,
      items.price,
      items.stock,
      items.status,
      items.image,
      carts.cart_id,
      carts.user_id,
      carts.amount
    FROM
      carts
    JOIN
      items
    ON
      carts.item_id = items.item_id
    WHERE
      carts.user_id = {$user_id}
    AND
      items.item_id = {$item_id}
  ";
//fetchメソッドで取得した結果 or falseを返す
  return fetch_query($db, $sql);

}

//渡すもの:データベースへのリンク、商品ID、ユーザーID
//返すもの:
function add_cart($db, $item_id, $user_id) {
  //そのユーザーのカート内にすでに同じ商品が入れられているか確認
  $cart = get_user_cart($db, $item_id, $user_id);
  //もし初めてカートに追加する商品であれば
  if($cart === false){
//cartsテーブルに新しく追加するSQL実行
    return insert_cart($db, $user_id, $item_id);
  }
  //$cartに商品情報が取得できていれば(以前追加したことあれば)
  //カート内該当商品の数量を変更
  return update_cart_amount($db, $cart['cart_id'], $cart['amount'] + 1);
}

//渡すもの:データベースへのリンク、商品ID、購入数(初期値1)
function insert_cart($db, $item_id, $user_id, $amount = 1){
  //カートに追加する商品をcartsテーブルにインサート
  $sql = "
    INSERT INTO
      carts(
        item_id,
        user_id,
        amount
      )
    VALUES(:item_id, :user_id, :amount)
  ";
  $params = array(':item_id' => $item_id, ':user_id' => $user_id, ':amount' => $amount);
//SQL実行した結果 or falseをリターン
  return execute_query($db, $sql, $params);
}

function update_cart_amount($db, $cart_id, $amount){
  //cartsテーブルの該当cart_idのamountの値をアップデートするSQL文
  $sql = "
    UPDATE
      carts
    SET
      amount = :amount
    WHERE
      cart_id = :cart_id
    LIMIT 1
  ";
  $params = array(':amount' => $amount, ':cart_id' => $cart_id);
  //SQL文の実行
  return execute_query($db, $sql, $params);
}

//データベースへのリンク・カートIDを渡すと対象レコード削除のSQLを実行しその結果を返す関数
function delete_cart($db, $cart_id){
  //該当のcart_idのレコードを削除するSQL文
  $sql = "
    DELETE FROM
      carts
    WHERE
      cart_id = :cart_id
    LIMIT 1
  ";
  $params = array(':cart_id' => $cart_id);
  //SQL実行し結果を返す
  return execute_query($db, $sql, $params);
}

//渡すもの:データベースへのリンク、該当ユーザーのカートの中身の配列
//返すもの:falseまたは在庫数変更update文・カート内delete文実行
function purchase_carts($db, $carts){
  //validate_cart_purchaseでカートの中身をチェックしfalseであれば
  if(validate_cart_purchase($carts) === false){
    //falseを返す
    return false;
  }
  try {
    //トランザクション開始
    $db->beginTransaction();

    //$carts内をループし購入分だけ商品在庫を減らす
    foreach($carts as $cart){
      //$carts内の商品ID・在庫から購入数を引いた数をupdate_item_stock関数に渡し、update文実行
      //失敗の場合$_SESSIONにエラーメッセージ代入
      if(update_item_stock(
          $db, 
          $cart['item_id'], 
          $cart['stock'] - $cart['amount']
        ) === false){
          //$_SESSIONにエラーメッセージ代入
        set_error($cart['name'] . 'の購入に失敗しました。');
      }
    }
  
    //該当ユーザーのカートの中身をデータベースから削除
    delete_user_carts($db, $carts[0]['user_id']);

    //order_historyテーブルに購入履歴残す
    record_order_history($db, $carts[0]['user_id']);

    //history_detailsテーブルに購入明細残す
    global $order_number;
    $order_number = $db->lastInsertId();
    foreach ($carts as $cart) {
      record_order_details($db, $order_number, $cart['item_id'], $cart['price'], $cart['amount']);
    }

    $db->commit();
  } catch(PDOException $e) {
    $db->rollback();
    return false;
  }

}

//渡すもの:データベースへのリンク、ユーザーID
function delete_user_carts($db, $user_id){
  //cartsテーブルから、引数で渡されたユーザーIDのレコードを全て削除するSQL
  $sql = "
    DELETE FROM
      carts
    WHERE
      user_id = :user_id
  ";
  $params = array(':user_id' => $user_id);
//SQL実行、失敗の場合falseを返す
  execute_query($db, $sql, $params);
}

//渡すもの:カートの中身情報の配列
//返すもの:カート内商品の合計金額($total_price)
function sum_carts($carts){
  //合計金額を入れる$total_priceを定義
  $total_price = 0;
  //$cartsをループ
  foreach($carts as $cart){
    //小計を$total_priceに足していく
    $total_price += $cart['price'] * $cart['amount'];
  }
  //カート内合計金額をリターン
  return $total_price;
}

//渡すもの:カートの中身の配列
//返すもの:falseかtrue
function validate_cart_purchase($carts){
  //$cartsの中をcount関数でチェックし中身0であったら...
  if(count($carts) === 0){
    //$_SESSIONにエラーメッセージ代入
    set_error('カートに商品が入っていません。');
    //falseを返す
    return false;
  }
  //$carts内をループ
  foreach($carts as $cart){
    //カート内商品の公開ステータスを調べ、非公開のものが含まれていれば
    if(is_open($cart) === false){
      //$_SESSIONにエラーメッセージ代入
      set_error($cart['name'] . 'は現在購入できません。');
    }
    //購入数が在庫数よりも多ければ
    if($cart['stock'] - $cart['amount'] < 0){
      //$_SESSIONにエラーメッセージ代入
      set_error($cart['name'] . 'は在庫が足りません。購入可能数:' . $cart['stock']);
    }
  }
  //$_SESSION['__errors']の中がカラでなかったら
  if(has_error() === true){
    //falseを返す
    return false;
  }
  //上記条件クリアであればtrueを返す
  return true;
}

