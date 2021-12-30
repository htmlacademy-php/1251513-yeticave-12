<?php

session_start();

/**
 * Проверяет существование сессии.
 */
function sessCheckAuth(): void
{
    if (!isset($_SESSION['id'])) {
        header('HTTP/1.0 403 Forbidden');
        die();
    }
}


/**
 * Проверяет отсутствие сессии.
 */
function sessCheckNotAuth(): void
{
    if (isset($_SESSION['id'])) {
        header("location:/", false, 302);
        die();
    }
}

/**
 * Возвращает id пользователя из сессии.
 * @return int id пользователя, либо null.
 */
function sessGetUserId(): ?int
{
    $value = $_SESSION['id'] ?? null;
    $user_id = null;

    if (!is_null($value)) {
        $user_id = (int) $value;
    }

    return $user_id;
}

/**
 * Удаляет сессию.
 */
function sessLogout(): void
{
    unset($_SESSION['id']);
}

/**
 * Сохраняет в сессию id пользователя.
 * @param int id пользователя.
 */
function sessStoreUserId(int $id): void
{
    $_SESSION['id'] = $id;
}
