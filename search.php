<?php
require_once('sess.php');
require_once('helpers.php');
require_once('db_connection.php');
require_once('service_functions.php');

if (!isset($_GET['search'])){
    header('Location: pages/404.html');
    die();
}

$categories_arr = [];
$items_arr = [];

$con = db_connect();

$rqt = trim($_GET['search']);

$user_name = getUserNameById($con, sess_get_user_id());

$items_arr = getSearchItems($con, $rqt);

$categories_arr = getCategories($con);

$page_content = include_template('search_tmp.php', ['categories_arr' => $categories_arr]);

$layout_content = include_template('layout.php', ['user_name' => $user_name, 'categories_arr' => $categories_arr, 'content' => $page_content ,'title' => 'Главная']);

print($layout_content);

function getSearchItems(mysqli $con, string $rqt) : array{
    $sql = "SELECT    
    i.id id, i.name, c.name category, IFNULL(b.price,start_price) price, img_path url, completion_date expiry_date
        FROM  item i
    LEFT JOIN category c on c.id = i.category_id
    LEFT JOIN
    (SELECT
        item_id, MAX(price) price
    FROM bid b2
    GROUP BY item_id) b ON i.id = b.item_id
    WHERE MATCH (i.name, i.description) AGAINST(?) AND i.winner_id IS NULL 
    ORDER BY date DESC";
    $items = [];
    $stmt = db_get_prepare_stmt($con, $sql, [$rqt]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    
    if ($res && $row = $res->fetch_assoc()){
        $items[] = $row;
    }
    
    return $items;
}