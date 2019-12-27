<?php 
require_once 'functions.php';
require_once 'db.php';

function record_order_history() {
    $sql = "INSERT INTO
            order_history (
            user_id,
            "
}