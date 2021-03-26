<?php
require_once('helpers.php');

$is_auth = rand(0, 1);

$categories_arr = [];
$items_arr = [];

$con = mysqli_connect('localhost', 'root', 'root', 'yeticave');
if ($con == false){
    print ("Ошибка подключения" . mysqli_connect_error());
}
else{
    mysqli_set_charset($con, "utf8");
    $sql = "SELECT name, code FROM category";
    $res = mysqli_query($con, $sql);
    $rows = mysqli_fetch_all($res, MYSQLI_ASSOC);
    foreach ($rows as $row){
        $categories_arr[] = $row;
    }

    $sql2 = "SELECT
               i.name, c.name category, IFNULL(b.price,start_price) price, img_path url, completion_date expiry_date
             FROM  item i
            LEFT JOIN category c on c.id = i.category_id
            LEFT JOIN
                (SELECT
                    item_id, MAX(price) price
                FROM bid b2
                GROUP BY item_id) b ON i.id = b.item_id
            WHERE i.winner_id IS NULL
            ORDER BY date DESC";
    $res = mysqli_query($con, $sql2);
    $rows = mysqli_fetch_all($res, MYSQLI_ASSOC);
    foreach ($rows as $row){
        $items_arr[] = $row;
    }
}


//$categories_arr = ['Доски и лыжи', 'Крепления', 'Ботинки', 'Одежда', 'Инструменты', 'Разное'];

/*$items_arr =   [['name' => '2014 Rossignol District Snowboard', 'category' => 'Доски и лыжи', 'price' => 10999, 'url' => 'img/lot-1.jpg', 'expiry_date' => '2021-03-17'],
                ['name' => 'DC Ply Mens 2016/2017 Snowboard', 'category' => 'Доски и лыжи', 'price' => 159999, 'url' => 'img/lot-2.jpg', 'expiry_date' => '2021-03-18'],
                ['name' => 'Крепления Union Contact Pro 2015 года размер L/XL', 'category' => 'Крепления', 'price' => 8000, 'url' => 'img/lot-3.jpg', 'expiry_date' => '2021-03-19'],
                ['name' => 'Ботинки для сноуборда DC Mutiny Charocal', 'category' => 'Ботинки', 'price' => 10999, 'url' => 'img/lot-4.jpg', 'expiry_date' => '2021-03-20'],
                ['name' => 'Куртка для сноуборда DC Mutiny Charocal', 'category' => 'Одежда', 'price' => 7500, 'url' => 'img/lot-5.jpg', 'expiry_date' => '2021-03-21'],
                ['name' => 'Маска Oakley Canopy', 'category' => 'Разное', 'price' => 5400, 'url' => 'img/lot-6.jpg', 'expiry_date' => '2021-05-12']];
*/
$user_name = 'Artem2J'; // укажите здесь ваше имя

//Включение фильтра для защиты от XSS
function xss_protection($string){
    return htmlspecialchars($string);
}


function price_format($price){
    $result = number_format(ceil($price),0, '.',' ').' ₽';
    return $result;
}

function get_dt_range($date){
    date_default_timezone_set('Europe/Moscow');
    $expiry_date = DateTime::createFromFormat('Y-m-d', $date);
    $expiry_date->setTime(23, 59, 59);
    $currentDate = new DateTime();
    $dt_range = $currentDate->diff($expiry_date);

    $hours = 0;
    $minuts = 0;

    if (! $dt_range->invert) {
        $hours = $dt_range->days * 24 + $dt_range->h;
        $minuts = $dt_range->i;
    }

    $hours = str_pad($hours, 2, "0", STR_PAD_LEFT);
    $minuts = str_pad($minuts, 2, "0", STR_PAD_LEFT);

    return [$hours, $minuts];
}

$page_content = include_template('main.php', [ 'items_arr' => $items_arr, 'categories_arr' => $categories_arr]);

$layout_content = include_template('layout.php', ['is_auth' => $is_auth, 'user_name' => $user_name, 'categories_arr' => $categories_arr, 'content' => $page_content ,'title' => 'Главная']);

print($layout_content);
