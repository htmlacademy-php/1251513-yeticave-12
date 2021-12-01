<?php

require_once('sess.php');
require_once('helpers.php');
require_once('db_connection.php');
require_once('service_functions.php');

$con = db_connect();
$categories_arr = getCategories($con);
$user_name = getUserNameById($con, sess_get_user_id());

$page_content = include_template('404_tmp.php', ['categories_arr' => $categories_arr]);

$layout_content = include_template('layout.php', ['user_name' => $user_name, 'categories_arr' => $categories_arr, 'content' => $page_content ,'title' => 'Страница не найдена']);

print($layout_content);
