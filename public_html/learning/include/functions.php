<?php
function pluck($arr, $key) {
    $result = array_map(function($item) use($key) {
        return $item[$key];
    }, $arr);
    return $result;
}

function output($value) {
    echo '<pre>';
    print_r($value);
    echo '</pre>';
}

function authenticate_user($email, $password) {
    return $email == USER_NAME && $password == PASSWORD;
}
?>
