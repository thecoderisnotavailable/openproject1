<?php

/**
 * TODO :: COOKIE, USER AGENT CHECK, EVERY 10 MINUTE CHECK REQURIED!!!
 */
session_start();
include 'db.class.php';
if (!isset($_SESSION['user'])) {
    header("location: /login?next=" . $_SERVER['REQUEST_URI']);
    die();
}
// if (!isset($_SESSION['cp'])) {
//     header("location: /checkpoint/user?next=" . $_SERVER['REQUEST_URI']);
//     die();
// }
// if ($_SESSION['cp']['c']) {
//     /**
//      * if current url not in approved pages list
//      */
//     $curl = strtok($_SERVER["REQUEST_URI"], '?');
//     if (!in_array($curl, $_SESSION['cp']['a'])) {
//         header("location: /checkpoint/user");
//         die();
//     }
// }
// include 'User.php';