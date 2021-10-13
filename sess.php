<?

session_start();

/**
 * Проверяет существование сессии.
 */
function sess_check_auth(): void
{
    if(!isset($_SESSION['id'])){
        header('HTTP/1.0 403 Forbidden');
        die();
    }
}

/**
 * Возвращает id пользователя из сессии.
 * @return int id пользователя, либо null.
 */
function sess_get_user_id(): ?int
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
function sess_logout(): void
{
    unset($_SESSION['id']);
}

/**
 * Сохраняет в сессию id пользователя.
 * @param int id пользователя.
 */
function sess_store_user_id(int $id): void{
    $_SESSION['id'] = $id;
}