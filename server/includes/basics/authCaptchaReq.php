<?php
/**
* Extension of authReq with captcha
*/
require dirname(__FILE__)."/../db.class.php";
require dirname(__FILE__)."/../Security.php";

if (!checkAjax() || !reCaptcha($_POST['captcha']) || !checkLoggedin()) {
    call400();
}
