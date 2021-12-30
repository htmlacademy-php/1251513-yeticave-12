<?php
require_once('sess.php');
require_once('helpers.php');
require_once('db_connection.php');
require_once('service_functions.php');

$categories_arr = [];

$con = dbСonnect();

$categories_arr = getCategories($con);

$form_errors =[];
$incoming_data = ['email' => '', 'password' => ''];

if (isset($_POST['submit'])) {
    $incoming_data = $_POST;
    $form_errors = checkLoginErrors($con, $incoming_data);
    if (count($form_errors) == 0) {
        sessStoreUserId(getUserIdByEmail($con, $incoming_data['email']));
        header('Location:index.php');
        die();
    }
}
$page_content = include_template('login_form.php', ['categories_arr' => $categories_arr,
    'incoming_data' => $incoming_data, 'form_errors' => $form_errors]);

$layout_content = include_template('layout.php', ['user_name' => '', 'categories_arr' => $categories_arr,
    'content' => $page_content ,'title' => 'Вход']);

print($layout_content);

/**
 * Проверяет переданные данные из формы.
 *
 * @param  mysqli $con Подключение к БД.
 * @param  array $data Массив переданых из формы данных
 * @return array Массив ошибок или пустой массив в случае их отсутствия.
 */
function checkLoginErrors(mysqli $con, array $data): array
{
    $result = [];
    $email_error = checkEmail($con, $data['email']);

    if (!empty($email_error)) {
        $result['email'] = $email_error;
    }

    $password_error = checkPassword($con, $data['email'], $data['password']);
    if (!empty($password_error)) {
        $result['password'] = $password_error;
    }

    return $result;
}

/**
 * Проверяет введенный емайл.
 *
 * @param  mysqli $con Подключение к БД.
 * @param  string $email Введенный емайл
 * @return string Возвращает ошибку.
 */
function checkEmail(mysqli $con, string $email): string
{
    if ($email == '') {
        return 'Введите e-mail';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Введен некорректный e-mail';
    }

    $sql = "SELECT * FROM user WHERE email = ?";
    $stmt = db_get_prepare_stmt($con, $sql, [$email]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $result = mysqli_num_rows($res) == 0;

    if ($result) {
        return 'Пользователь с введенным email отсутствует';
    }
    return '';
}

/**
 * Проверяет введенный пароль
 *
 * @param  mysqli $con Подключение к БД.
 * @param  string $email Введенный емайл
 * @param  string $password Введенный пароль.
 * @return string Возвращает ошибку.
 */
function checkPassword(mysqli $con, string $email, string $password): string
{
    if ($password == '') {
        return 'Введите пароль';
    }
    $sql = "SELECT password FROM user WHERE email = ?";
    $stmt = db_get_prepare_stmt($con, $sql, [$email]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $pass_hash = '';
    if ($result && $row = $result->fetch_assoc()) {
        $pass_hash = $row['password'];
    }
    if (!password_verify($password, $pass_hash)) {
        return 'Введен неправильный пароль';
    }
    return '';
}
