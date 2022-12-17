<?php
/**
 * Without captcha
 * Imports DB and basic security modules
*/
require dirname(__FILE__)."/../db.class.php";
require dirname(__FILE__)."/../Security.php";

if (!checkAjax() || !checkLoggedin()) {
    call400();
}