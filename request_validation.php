<?php
/**
 * Валидация GET запроса для строки
 * @param string $param ключ параметра запроса
 * @param string|null $default Значение по умолчанию
 * @return string|null Возвращает параметр запроса в формате строки
 */
function requestValidationGetString(string $param, ?string $default): ?string
{
    if (isset($_GET[$param])){
        return (string)$_GET[$param];
    }
    return $default;
}

/**
 * Валидация GET запроса для целых чисел
 * @param string $param Ключ массива $_GET
 * @param int|null $default Значение по умолчанию
 * @return int|null Возвращает число
 */
function requestValidationGetInt(string $param, ?int $default): ?int
{
    if (isset($_GET[$param])) {
        $result = $_GET[$param];
        if (is_numeric($result)) {
            return (int)$result;
        }
    }
    return $default;
}

/**
 * Валидация GET запроса для чисел с плавающей точкой
 * @param string $param Ключ массива $_GET
 * @param float|null $default Значение по умолчанию
 * @return float|null Возвращает число
 */
function requestValidationGetFloat(string $param, ?float $default): ?float
{
    if (isset($_GET[$param])) {
        $result = $_GET[$param];
        if (is_numeric($result)) {
            return (float)$result;
        }
    }
    return $default;
}

/**
 * Валидация GET запроса для булевых переменных
 * @param string $param Ключ массива $_GET
 * @param bool|null $default Значение по умолчанию
 * @return bool|null Возвращает булеву переменную
 */
function requestValidationGetBoolean(string $param, ?bool $default): ?bool
{
    if (isset($_GET[$param])){
        switch (mb_strtolower($_GET[$param])) {
            case 'true':
                return true;
            case 'false':
                return false;
            default:
                return $default;
        }
    }
}
