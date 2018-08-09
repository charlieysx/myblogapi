<?php

/**
 * 明文密码哈希
 *
 * @param  string $password         明文密码
 * @return array  password和salt
 */
function cb_encrypt($password) {
    $salt = password_hash('mypassword', PASSWORD_BCRYPT, ['cost' => 10]);
    $password = md5($password . $salt);
    return [
        'password' => $password,
        'salt' => $salt,
    ];
}

/**
 * 密码比对
 *
 * @param  string $hash          哈希值
 * @param  string $salt          盐
 * @param  string $password      明文密码
 * @return void   一致为真
 */
function cb_passwordEqual($hash, $salt, $password) {
    $new_hash = md5($password . $salt);
    if (hash_equals($hash, $new_hash)) {
        return true;
    }
    return false;
}
