<?php
require_once('sess.php');
require_once('helpers.php');
require_once('db_connection.php');
require_once('service_functions.php');

$con = dbСonnect();

sessCheckAuth();

$user_id = sessGetUserId();

$user_name = getUserNameById($con, $user_id);

$incoming_data = ['lot-name' => '', 'category' => '', 'message' => '',
                  'lot-rate' => 0, 'lot-step' => 0, 'lot-date' => ''];

$form_errors = [];

if (isset($_POST['submit'])) {
    $incoming_data = $_POST;
    $incoming_data['lot-rate'] = (int)$_POST['lot-rate'];
    $incoming_data['lot-step'] = (int)$_POST['lot-step'];
    $form_errors = checkForErrors($incoming_data, $_FILES);

    if (count($form_errors) == 0) {
        $id = sentDataToDB($con, $incoming_data, $_FILES, $user_id);
        header('Location:lot.php?id='.$id);
        die();
    }
}

$categories_arr = [];
$categories_arr = getCategories($con);

$page_content = include_template('add_lot.php', ['categories_arr' => $categories_arr,
    'incoming_data' => $incoming_data, 'form_errors' => $form_errors]);

$layout_content = include_template('layout.php', ['user_name' => $user_name, 'categories_arr' => $categories_arr,
    'content' => $page_content ,'title' => 'Добавление лота']);

print($layout_content);

/**
 * Проверяются переданные из формы данные на наличие ошибок
 *
 * @param  array $incoming_data Массив переданных из формы данных.
 * @param  array $files_data Массив переданного изображения.
 * @return array Массив выявленных ошибок, либо пустой массив, в случае отсутствия ошибок.
 */
function checkForErrors(array $incoming_data, array $files_data): array
{
    $result = [];
    if ($incoming_data['lot-name'] == '') {
        $result['lot-name'] = 'Введите наименование лота';
    }
    if ($incoming_data['category'] == 'Выберите категорию') {
        $result['category'] = 'Выберите категорию';
    }
    if ($incoming_data['message'] == '') {
        $result['message'] = 'Заполните описание';
    }
    if ((int)$incoming_data['lot-rate'] <= 0) {
        $result['lot-rate'] = 'Начальная цена должна быть больше 0';
    }
    if (!is_numeric($incoming_data['lot-step']) || (int)$incoming_data['lot-step'] <= 0) {
        $result['lot-step'] = 'Шаг ставки должен быть целым положительным числом';
    }
    if ($files_data['lot-img']['error'] == 4) {
        $result['lot-img'] = 'Загрузите изображение';
    } elseif (!in_array(mime_content_type($files_data['lot-img']['tmp_name']), ['image/png', 'image/jpeg']) ||
    !in_array(substr(strrchr($files_data['lot-img']['name'], '.'), 1), ['jpg', 'jpeg', 'png'])) {
        $result['lot-img'] = 'Загрузите изображение в формате JPEG или PNG';
    }
    if ($incoming_data['lot-date'] == '') {
        $result['lot-date'] = 'Выберите дату';
    } elseif (!checkLotDate($incoming_data['lot-date'])) {
        $result['lot-date'] = 'Выберите дату из будущего';
    }

    return $result;
}

/**
 * Проверяет не истекла ли дата.
 *
 * @param  string $date Вводимая дата.
 * @return bool Истина, если введенная дата еще не истекла, лож в противном случае.
 */
function checkLotDate(string $date): bool
{
    $endDate = DateTime::createFromFormat('Y-m-d', $date);
    $currentDate = new DateTime();
    $range = $currentDate -> diff($endDate);
    $result = true;
    if ($range->invert) {
        $result = false;
    }
    return $result;
}

/**
 * Записывает введенные в форму данные в БД и возвращает id записанного лота.
 *
 * @param  mysqli $con Подключение к БД.
 * @param  array $incoming_data Массив переданных из формы данных.
 * @param  array $img_file Массив переданного изображения.
 * @param  int $user_id id автора размещаемого лота
 * @return int id записанного в БД лота.
 */
function sentDataToDB(mysqli $con, array $incoming_data, array $img_file, int $user_id): int
{
    $category_id = getCategoryId($con, $incoming_data['category']);
    $incoming_data['lot-img'] = 'test_path';
    $sql = "INSERT INTO
    item (date, name, description, start_price, completion_date,bid_step, author_id, category_id)
    VALUE
        (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = db_get_prepare_stmt($con, $sql, [date('Y-m-d H:i:s', time()), $incoming_data['lot-name'],
        $incoming_data['message'],
    $incoming_data['lot-rate'], $incoming_data['lot-date'], $incoming_data['lot-step'], $user_id, $category_id]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if (mysqli_errno($con)) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        die();
    }
    $id = mysqli_insert_id($con);
    $img_path = 'uploads/lot-img-'.$id.'.'.substr(strrchr($img_file['lot-img']['name'], '.'), 1);
    if (move_uploaded_file($img_file['lot-img']['tmp_name'], $img_path)) {
        $sql = "UPDATE item SET img_path = ? WHERE id = ?";
    }
    $stmt = db_get_prepare_stmt($con, $sql, [ $img_path, $id]);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_get_result($stmt);
    return $id;
}

/**
 * Возвращает id категории по заданному имени.
 *
 * @param  mysqli $con Подключение к БД.
 * @param  string $str Вводимое имя категории.
 * @return int id категории.
 */
function getCategoryId(mysqli $con, string $str) : int
{
    $sql = "SELECT id FROM category WHERE name = ?";
    $stmt = db_get_prepare_stmt($con, $sql, [$str]);
    mysqli_stmt_execute($stmt);
    $result =  mysqli_stmt_get_result($stmt);
    $res = [];
    if ($result && $row = $result->fetch_assoc()) {
        $res = $row;
    }
    return $res['id'];
}
