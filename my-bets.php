<?php
require_once('sess.php');
require_once('helpers.php');
require_once('db_connection.php');
require_once('service_functions.php');

$con = db_connect();

sess_check_auth();

$user_id = sess_get_user_id();

$user_name = getUserNameById($con, $user_id);

$categories_arr = [];
$categories_arr = getCategories($con);

$bets_arr = [];
$bets_arr = getBetsArr($con, $user_id);

$page_content = include_template('my-bets_tmp.php', ['categories_arr' => $categories_arr, 'bets_arr' => $bets_arr, 'user_id' => $user_id]);

$layout_content = include_template('layout.php', ['user_name' => $user_name, 'categories_arr' => $categories_arr, 'content' => $page_content ,'title' => 'Добавление лота']);

print($layout_content);

function getBetsArr(mysqli $con, int $user_id): array
{
    $result = [];
    $sql = "SELECT i.id, i.img_path, i.name as item_name, u.contacts as contact, c.name as category_name, i.completion_date, 
    b.price, b.date, winner_id 
    FROM bid b 
    LEFT JOIN item i ON i.id = b.item_id 
    LEFT JOIN category c ON c.id=i.category_id 
    LEFT JOIN user u ON u.id = i.author_id
    WHERE price IN (SELECT MAX(price) price FROM bid GROUP BY item_id) AND user_id=? ORDER BY b.date DESC";
    $stmt = db_get_prepare_stmt($con, $sql, [$user_id]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($res && $row = $res->fetch_assoc()){
        $result[] = $row;
    }
    return $result;
}