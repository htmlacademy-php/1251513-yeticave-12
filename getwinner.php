<?php
require_once('vendor/autoload.php');

$winners = checkWinners($con);
foreach($winners as $winner){
    setWinnerOnDB($con, $winner);
    sendCongratulations($con, $winner);
}


/**
 * Проверка БД на наличие новых победителей.
 *
 * @param  mysqli $con Подключение к БД.
 * @return array Возвращает массив с данными каждого победителя [id лота, id победителя, имя лота].
 */
function checkWinners(mysqli $con): array{
    $pair = [];
    $date = new DateTime();
    $current_date = $date->format('Y-m-d');
    $sql = "SELECT i.id, b.user_id as winner, i.name as item_name
    FROM bid b
    LEFT JOIN item i ON i.id = b.item_id
    LEFT JOIN category c ON c.id=i.category_id
    LEFT JOIN user u ON u.id = i.author_id
    WHERE price IN (SELECT MAX(price) price FROM bid GROUP BY item_id)
    AND i.completion_date < ? AND i.winner_id IS NULL";
    $stmt = db_get_prepare_stmt($con, $sql, [$current_date]);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($result && $row = $result->fetch_assoc()) {
        $pair[] = ['item_id' => $row['id'], 'winner_id' => $row['winner'], 'item_name' => $row['item_name']];
    }
    return $pair;
}

/**
 * Отмечает в лоте победителя
 *
 * @param  mysqli $con Подключение к БД.
 * @param array $winner Массив с данными победителя [id лота, id победителя, имя лота].
 */
function setWinnerOnDB(mysqli $con, array $winner)
{
    $sql = "UPDATE item SET winner_id=? WHERE id=?";
    $stmt = db_get_prepare_stmt($con, $sql, [(int)$winner['winner_id'], (int)$winner['item_id']]);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if (mysqli_errno($con) && $res) {
        printf("Connect failed: %s\n", mysqli_connect_error());
        die();
    }
}

/**
 * Отправляет сообщение о победе на email пользователя
 *
 * @param  mysqli $con Подключение к БД.
 * @param array $winner Массив с данными победителя [id лота, id победителя, имя лота].
 */
function sendCongratulations(mysqli $con, array $winner)
{
    $config = include('config.php');
    $transport = (new Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))
    ->setUsername($config->gm_login)
    ->setPassword($config->gm_password)
    ;

    $text = include_template('email.php', ['winner_arr' => $winner,
        'winner_name' => getUserNameById($con, $winner['winner_id'])]);

    $message = (new Swift_Message())
    ->setSubject('Поздравления от Yeticave')
    ->setFrom([$config->gm_login])
    ->setTo([getUserEmailById($con, $winner['winner_id'])])
    ->addPart($text, 'text/html')
    ;

    $mailer = new Swift_Mailer($transport);

    try {
        $result = $mailer->send($message);
    } catch (Exception $e) {
        $e->getMessage();
    }
}
