<?php
require_once('helpers.php');

$is_auth = rand(0, 1);

$categories_arr = ['Доски и лыжи', 'Крепления', 'Ботинки', 'Одежда', 'Инструменты', 'Разное'];

$items_arr =   [['name' => '2014 Rossignol District Snowboard', 'category' => 'Доски и лыжи', 'price' => 10999, 'url' => 'img/lot-1.jpg', 'expiry_date' => '2021-03-12'],
                ['name' => 'DC Ply Mens 2016/2017 Snowboard', 'category' => 'Доски и лыжи', 'price' => 159999, 'url' => 'img/lot-2.jpg', 'expiry_date' => '2021-03-13'],
                ['name' => 'Крепления Union Contact Pro 2015 года размер L/XL', 'category' => 'Крепления', 'price' => 8000, 'url' => 'img/lot-3.jpg', 'expiry_date' => '2021-03-14'],
                ['name' => 'Ботинки для сноуборда DC Mutiny Charocal', 'category' => 'Ботинки', 'price' => 10999, 'url' => 'img/lot-4.jpg', 'expiry_date' => '2021-03-15'],
                ['name' => 'Куртка для сноуборда DC Mutiny Charocal', 'category' => 'Одежда', 'price' => 7500, 'url' => 'img/lot-5.jpg', 'expiry_date' => '2021-03-16'],
                ['name' => 'Маска Oakley Canopy', 'category' => 'Разное', 'price' => 5400, 'url' => 'img/lot-6.jpg', 'expiry_date' => '2021-05-12']];

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
    $expiry_date = strtotime($date);
    $dt_range = $expiry_date - date(time());
    $all_minuts = floor($dt_range / 60);
    $hours = floor($all_minuts / 60);
    $minuts = $all_minuts - $hours * 60;
    
    return [str_pad($hours, 2, "0", STR_PAD_LEFT), str_pad($minuts, 2, "0", STR_PAD_LEFT)];
}

$page_content = include_template('main.php', [ 'items_arr' => $items_arr, 'categories_arr' => $categories_arr]);

$layout_content = include_template('layout.php', ['is_auth' => $is_auth, 'user_name' => $user_name, 'categories_arr' => $categories_arr, 'content' => $page_content ,'title' => 'Главная']);

print($layout_content);