<?php
require_once('sess.php');
require_once('helpers.php');
require_once('db_connection.php');
require_once('service_functions.php');

$categories_arr = [];
$items_arr = [];

$con = db_connect();

$user_name = getUserNameById($con, sess_get_user_id());

$categories_arr = getCategories($con);

$items_arr = getItems($con);

$page_content = include_template('main.php', [ 'items_arr' => $items_arr, 'categories_arr' => $categories_arr]);

$layout_content = include_template('layout.php', ['user_name' => $user_name, 'categories_arr' => $categories_arr, 'content' => $page_content ,'title' => 'Главная']);

print($layout_content);

/**
 * Возвращает массив открытых лотов в порядке от нового к старому.
 *
 * @param  mysqli $con Подключение к БД.
 * @return array Массив лотов.
 */
function getItems (mysqli $con): array
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
            ORDER BY date DESC";
    $items = [];
    $res = mysqli_query($con, $sql);
    while ($res && $row = $res->fetch_assoc()){
        $items[] = $row;
    }
    return $items;
}