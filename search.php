<?php
require_once('sess.php');
require_once('helpers.php');
require_once('db_connection.php');
require_once('service_functions.php');

if (!isset($_GET['search'])){
    header('Location: 404.php');
    die();
}
$query = trim($_GET['search']);
if ($query == '') {
    header('Location: index.php');
    die();
}

$categories_arr = [];
$items_arr = [];
$con = db_connect();
$user_name = getUserNameById($con, sess_get_user_id());

$position = 1;
if (isset($_GET['page']) && is_numeric($_GET['page']) && (int) $_GET['page'] > 0){
    $position = (int)$_GET['page'];
}

$search_result_count = getResultCount($con, $query);

$paginationListNumber = (int)($search_result_count / 9) +  1;

$items_arr = getSearchItems($con, $query, $position);

$categories_arr = getCategories($con);

$page_content = include_template('search_tmp.php', ['categories_arr' => $categories_arr, 'items_arr' => $items_arr,'query' => $query, 'position' => $position, 'paginationListNumber' => $paginationListNumber ]);

$layout_content = include_template('layout.php', ['user_name' => $user_name, 'categories_arr' => $categories_arr, 'content' => $page_content ,'title' => 'Главная']);

print($layout_content);

/**
 * Получаем массив данных по поисковому запросу.
 *
 * @param  mysqli $con Подключение к БД.
 * @param string $query Поисковый запрос.
 * @param int $page Номер страницы, для которой ищутся лоты
 * @return array Массив лотов.
 */

function getSearchItems(mysqli $con, string $query, int $page) : array
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
    WHERE MATCH (i.name, i.description) AGAINST(?) AND i.winner_id IS NULL
    ORDER BY date DESC LIMIT 9 OFFSET ?";

    $items = [];
    $offset = ($page-1) * 9;
    $stmt = db_get_prepare_stmt($con, $sql, [$query, $offset]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    while ($res && $row = $res->fetch_assoc()){
        $items[] = $row;
    }

    return $items;
}


/**
 * Получаем число результатов поискового запроса.
 *
 * @param  mysqli $con Подключение к БД.
 * @param string $query Поисковый запрос.
 * @return int Количество найденых лотов.
 */
function getResultCount(mysqli $con, string $query): int
{
    $count = 0;

    $sql = "SELECT COUNT(*) AS count
    FROM (item i)
    WHERE MATCH (i.name, i.description) AGAINST(?)
    AND i.winner_id IS NULL";

    $stmt = db_get_prepare_stmt($con, $sql, [$query]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if ($res && $row = $res->fetch_assoc()){
        $count = $row['count'];
    }

    return $count;
}
