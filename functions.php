<?php

/**
 * DB接続用
 *
 * @param string $user
 * @param string $pass
 * @param string $db
 * @param string $host
 */
function connectDb($user, $pass, $db, $host = 'localhost') {
    try {
        $dbh = new PDO('mysql:host=' . $host . ';dbname=' . $db . '; charset=utf8', $user, $pass);
    } catch (Exception $e) {
        echo "エラー発生: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "<br>";
        die();
    }
    return $dbh;
}

/**
 * メールアドレスとパスワードが正しく入力されているかチェックし、エラーを取得する
 *
 * @param string $mail
 * @param string $pass
 * @return array $errors
 */
function getLoginErrors($mail, $pass) {
    $errors = [];
    if (empty($_POST['mail'])) {
        $errors['mail'] = '<p class="error">メールアドレスを入力してください</p>';
    } else if (!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $mail)) {
        $errors['mail'] = '<p class="error">メールアドレスが不正です。</p>';
    }
    if (empty($_POST['pass'])) {
        $errors['pass'] = '<p class="error">パスワードを入力してください</p>';
    } else if (!preg_match("/^[a-zA-Z0-9]+$/", $pass)) {
        $errors['pass'] = '<p class="error">パスワードが不正です。</p>';
    }
    return $errors;
}

/**
 * ログインする
 *
 * @param PDO $dbh
 * @param string $mail
 * @param string $pass
 * @return array
 */
function login($dbh, $mail, $pass) {
    $sql = 'SELECT * FROM users WHERE mail = :logmail AND pass = :logpass;';
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':logmail', $mail, PDO::PARAM_STR);
    $stmt->bindParam(':logpass', $pass, PDO::PARAM_STR);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * ログインしているかチェック
 *
 * @param array $sessions
 * @return boolean
 */
function loggedIn($sessions) {
    if (!isset($sessions['mail']) && !isset($sessions['time'])) {
        return false;
    }
    return true;
}

/**
 * ユーザー画像の取得
 *
 * @param string mail
 */
function getUserImagePath($mail) {
    $path = './images/user_pictures/' . $mail;
    if (file_exists($path . '.jpg')) {
        return $path . '.jpg?' . filemtime($path . '.jpg');
    }
    if (file_exists($path . '.png')) {
        return $path . '.png?' . filemtime($path . '.png');
    }
    return '';
}
