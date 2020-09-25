<?php 
if (empty($_REQUEST['login']) || empty($_REQUEST['password'])) {
    header('Location: ./');
    exit;
}

$config = parse_ini_file('./config.ini', true);

if ($_REQUEST['login'] == $config['login'] && 
    password_verify($_REQUEST['password'], $config['password']) === true) {
        setcookie('auth', 'true', time() + 86400);
        header('Location: ./explorer.php');
        exit;
    } else {
        header('Location: ./');
        exit;
    }