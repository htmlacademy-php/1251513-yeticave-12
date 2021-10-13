<?php

/**
 * Подключение к БД.
 *
 * @return mysqli Возвращает подключение к БД.
 */
function db_connect():mysqli
{
    $con = mysqli_connect('localhost', 'root', 'root', 'yeticave');
    if ($con == false){
        print ("Ошибка подключения" . mysqli_connect_error());
    die();
    }
    mysqli_set_charset($con, "utf8");
    return $con;
}