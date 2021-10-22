<?php
require_once('sess.php');
require_once('helpers.php');
require_once('db_connection.php');
require_once('service_functions.php');

$categories_arr = [];
$items_arr = [];

$con = db_connect();

$position = 1;
if (isset($_GET['page']) && is_numeric($_GET['page']) && (int) $_GET['page'] > 0){
    $position = (int)$_GET['page'];
}

if(isset($_GET['category'])){
    if(!in_array($_GET['category'], getCategoryCodes($con))){
        header('Location: pages/404.html');
        die();
    }
    $item_count = getCategoryItemCount($con, $_GET['category']);
    $paginationListNumber = (int)($item_count / 9) +  1;
    $items_arr = getCategoryItems($con, $_GET['category'], $position);
}else{
    $item_count = getItemCount($con);
    $paginationListNumber = (int)($item_count / 9) +  1;
    $items_arr = getItems($con, $position);
}

$user_name = getUserNameById($con, sess_get_user_id());

$categories_arr = getCategories($con);
$cat = $_GET['category'];
$page_content = include_template('main.php', [ 'items_arr' => $items_arr, 'categories_arr' => $categories_arr, 'position' => $position, 'paginationListNumber' => $paginationListNumber,
'items_category' =>  $cat]);

$layout_content = include_template('layout.php', ['user_name' => $user_name, 'categories_arr' => $categories_arr, 'content' => $page_content ,'title' => 'Главная']);

print($layout_content);

require_once('getwinner.php');


/**
 * Возвращает массив открытых лотов в порядке от нового к старому.
 *
 * @param mysqli $con Подключение к БД.
 * @param int $page Номер страницы, для которой ищутся лоты.
 * @return array Массив лотов.
 */
function getItems (mysqli $con, int $page): array
{
    $sql = "SELECT
                i.id id, i.name, c.name category, IFNULL(b.price,start_price) price, img_path url, completion_date expiry_date
             FROM  item i
            LEFT JOIN category c on c.id = i.category_id
            LEFT JOIN
                (SELECT
                    item_id, MAX(price) price
                FROM bid b2
                GROUP BY item_id) b ON i.id = b.item_id
            WHERE i.winner_id IS NULL
            ORDER BY date DESC LIMIT 9 OFFSET ?";
    $items = [];
    $offset = ($page-1) * 9;
    $stmt = db_get_prepare_stmt($con, $sql, [$offset]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    while ($res && $row = $res->fetch_assoc()){
        $items[] = $row;
    }
    return $items;
}


/**
 * Возвращает существующие в БД категории
 * 
 * @param mysqli $con Подключение к БД.
 * @return array Массив категорий.
 */
function getCategoryCodes(mysqli $con): array
{
    $sql = "SELECT code FROM category";
    $codes = [];
    $res = mysqli_query($con, $sql);
    while ($res && $row = $res->fetch_assoc()){
        $codes[] = $row['code'];
    }
    return $codes;
}

/**
 * Возвращает массив открытых лотов определенной категории.
 *
 * @param  mysqli $con Подключение к БД.
 * @param string $categoryCode код категории.
 * @param int $page Номер страницы, для которой ищутся лоты.
 * @return array Массив лотов.
 */
function getCategoryItems(mysqli $con, string $categoryCode, int $page): array
{
    $sql = "SELECT
                i.id id, i.name, c.name category, IFNULL(b.price,start_price) price, img_path url, completion_date expiry_date
            FROM  item i
            LEFT JOIN category c on c.id = i.category_id
            LEFT JOIN
                (SELECT
                    item_id, MAX(price) price
                FROM bid b2
                GROUP BY item_id) b ON i.id = b.item_id
            WHERE i.winner_id IS NULL AND c.code = ?
            ORDER BY date DESC LIMIT 9 OFFSET ?";
$items = [];
$offset = ($page-1) * 9;
$stmt = db_get_prepare_stmt($con, $sql, [$categoryCode, $offset]);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
while ($res && $row = $res->fetch_assoc()){
$items[] = $row;
}
return $items;   
}


/**
 * Возвращает количество активных лотов.
 * 
 * @param  mysqli $con Подключение к БД.
 * @return int Количество активных лотов.
 */
function getItemCount(mysqli $con): int
{
    $count = 0;
    $sql = "SELECT COUNT(*) AS count 
    FROM (item i) 
    WHERE i.winner_id IS NULL";
    $res = mysqli_query($con, $sql);

    if ($res && $row = $res->fetch_assoc()){
        $count = $row['count'];
    }

    return $count;
}

/**
 * Возвращает количество активных лотов определенной категории.
 * 
 * @param  mysqli $con Подключение к БД.
 * @param string $categoryCode Код категории.
 * @return int Количество активных лотов.
 */
function getCategoryItemCount(mysqli $con, string $categoryCode): int
{
    $count = 0;
    $sql = "SELECT COUNT(*) AS count 
    FROM (item i) 
    LEFT JOIN category c on c.id = i.category_id
    WHERE c.code =? AND i.winner_id IS NULL";
    $stmt = db_get_prepare_stmt($con, $sql, [$categoryCode]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if ($res && $row = $res->fetch_assoc()){
        $count = $row['count'];
    }

    return $count;
}