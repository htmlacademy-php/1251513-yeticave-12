<?php
require_once('sess.php');
require_once('helpers.php');
require_once('db_connection.php');
require_once('service_functions.php');

if (!isset($_GET['id'])) {
    header('Location: pages/404.html');
    die();
}

$id = (int)$_GET['id'];
$categories_arr =[];
$error = null;
$bid = null;

$con = dbСonnect();

$user_name = getUserNameById($con, sessGetUserId());

checkId($con, $id);

$categories_arr = getCategories($con);

$item = getItem($con, $id);

$display_lot_item_form_flag = checkAccessForMakinBet($con, $id, sessGetUserId());

$bid_history_arr = getBidHistory($con, $id);


if (isset($_POST['cost'])) {
    $bid = $_POST['cost'];
    $error = checkCostForError($bid, $item);
    if (!isset($error)) {
        sendBidToDB($con, $id, $bid, sessGetUserId());
        header('Location: lot.php?id='.$id);
        die();
    }
}

$page_content = include_template('item.php', ['id' => $id, 'user_name' => $user_name,
    'categories_arr' => $categories_arr, 'item_name' => $item['name'], 'img_path' => $item['img_path'],
    'category_name' => $item['category_name'], 'description' => $item['description'],
    'completion_date' => $item['completion_date'], 'current_price' => $item['current_price'],
    'min_bid' => $item['min_bid'], 'bid' => $bid ,'error' => $error, 'bid_history' => $bid_history_arr,
    'display_lot_item_form_flag' => $display_lot_item_form_flag]);

$layout_content = include_template('layout.php', ['user_name' => $user_name, 'categories_arr' => $categories_arr,
    'content' => $page_content ,'title' => $item['name']]);

print($layout_content);

/**
 * Проверяет наличие лота с заданным id. В случае отсутствия перенаправляет на страницу 404
 *
 * @param  mysqli $con Подключение к БД.
 * @param  int $id id лота.
 * @return void
 */
function checkId(mysqli $con, int $id)
{
    $sql = "SELECT id FROM item WHERE id = ?";
    $stmt = db_get_prepare_stmt($con, $sql, [$id]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if (mysqli_num_rows($res) == 0) {
        header('Location: 404.php');
    }
}

/**
 * Возвращает лот по id.
 *
 * @param  mysqli $con Подключение к БД.
 * @param  mixed $id id лота.
 * @return array Массив с данными лота.
 */
function getItem(mysqli $con, int $id): array
{
    $sql = "SELECT i.name, img_path, c.name category_name,description, completion_date,
       IFNULL(b.price,start_price) current_price,
                IFNULL(b.price + i.bid_step, start_price) min_bid
            FROM item i
            LEFT JOIN category c on c.id = i.category_id
            LEFT JOIN (SELECT
                item_id, MAX(price) price
            FROM bid b2
            GROUP BY item_id) b ON i.id = b.item_id
            WHERE i.id = ?";
    $item = [];
    $stmt = db_get_prepare_stmt($con, $sql, [$id]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res && $row = $res->fetch_assoc()) {
        $item = $row;
    }
    return $item;
}

/**
 * Проверяет введенные в форме ставки данные на корректность
 * @param mixed $bid введенная ставка
 * @param array $item текущий лот
 * @return string сообщение об ошибке
 */
function checkCostForError($bid, array $item): ?string
{
    if (!is_numeric($bid)) {
        return 'Неверный формат данных';
    }
    if ($bid < $item['min_bid']) {
        return 'Введенная ставка меньше минимальной';
    }
    return null;
}

/**
 * Отправляет проверенную ставку в БД
 * @param  mysqli $con Подключение к БД.
 * @param  int $item_id id лота.
 * @param int $bid Передаваемая ставка.
 * @param int $user_id id автора ставки.
 * @return void
  */
function sendBidToDB(mysqli $con, int $item_id, int $bid, int $user_id)
{
    $sql = "INSERT INTO bid (date, price, user_id, item_id)
        VALUE (?, ?, ?, ?)";
    $stmt = db_get_prepare_stmt($con, $sql, [date('Y-m-d H:i:s', time()), $bid, $user_id, $item_id]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if (mysqli_errno($con)) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        die();
    }
}

/**
 * Возвращает массив с историей ставок для заданного лота.
 * @param  mysqli $con Подключение к БД.
 * @param  int $id id лота.
 * @return array Массив ставок для заданного лота.
 */
function getBidHistory(mysqli $con, int $id): array
{
    $sql = "SELECT u.name, price, date FROM bid b LEFT JOIN user u on b.user_id = u.id  WHERE item_id=?
    ORDER BY date DESC";
    $item = [];
    $stmt = db_get_prepare_stmt($con, $sql, [$id]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($res && $row = $res->fetch_assoc()) {
        $item[] = $row;
    }
    return $item;
}



/**
 * Проверяет, можно ли разрешать производить ставку.
 * @param  mysqli $con Подключение к БД.
 * @param  int $id id лота.
 * @param int $user_id id текущего пользователя, или null, если пользователь не авторезирован
 * @return bool true, если производить ставку разрешено, false в противном случае
 */
function checkAccessForMakinBet(mysqli $con, int $id, ?int $user_id): bool
{
    if (!isset($user_id)) {
        return false;
    }
    $authorItemId = getItemAuthorId($con, $id);
    if (isset($authorItemId)) {
        if ($authorItemId == $user_id) {
            return false;
        }
    }
    $authorLastBidId = getAuthorLastBidId($con, $id);
    if (isset($authorLastBidId)) {
        if ($authorLastBidId == $user_id) {
            return false;
        }
    }

    $date_range = getDateRange(getItemDate($con, $id));
    if (isset($date_range)) {
        if ($date_range[0] ==0 && $date_range[1] == 0) {
            return false;
        }
    }

    return true;
}

/**
 * Возвращает id автора лота, по id лота.
 * @param  mysqli $con Подключение к БД.
 * @param  int $id id лота.
 * @return int id автора лота
 */
function getItemAuthorId(mysqli $con, int $id): ?int
{
    $sql = "SELECT author_id FROM item WHERE id = ?";
    $author_id = null;
    $stmt = db_get_prepare_stmt($con, $sql, [$id]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res && $row = $res->fetch_assoc()) {
        $author_id = $row['author_id'];
    }
    return $author_id;
}

/**
 * Возвращает id автора последней ставки у лота.
 * @param  mysqli $con Подключение к БД.
 * @param  int $id id лота.
 * @return int id автора последней ставки
 */
function getAuthorLastBidId(mysqli $con, int $id): ?int
{
    $sql = "SELECT user_id FROM bid WHERE item_id=? ORDER BY date DESC LIMIT 1";
    $bid_author_id = null;
    $stmt = db_get_prepare_stmt($con, $sql, [$id]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res && $row = $res->fetch_assoc()) {
        $bid_author_id = $row['user_id'];
    }
    return $bid_author_id;
}

/**
 * Возвращает дату истечения лота.
 * @param  mysqli $con Подключение к БД.
 * @param  int $id id лота.
 * @return string дата истечения лота.
 */
function getItemDate(mysqli $con, int $id): ?string
{
    $date = '';
    $sql = "SELECT * FROM item WHERE id = ?";
    $stmt = db_get_prepare_stmt($con, $sql, [$id]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($res && $row = $res->fetch_assoc()) {
        $date = $row['completion_date'];
    }
    return $date;
}
