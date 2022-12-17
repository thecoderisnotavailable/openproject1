<?php
/**
 * USER LOGIN
 */
session_start();
$_SESSION['user']['id'] = 1;
$_SESSION['parent'] = 1; //Parent will be in admin table, for admins, parent will be 0
$_SESSION['level'] = 2;
?>
{
    "a" : {
        "e" : 0
    }
}