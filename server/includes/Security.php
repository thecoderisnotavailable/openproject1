<?php

/**
 * Check if its an ajax request
 * TODO: MAX_INSTANCE = 10
 */
function checkAjax()
{
    if (!empty($_SERVER['HTTP_REQUESTX']) && $_SERVER['HTTP_REQUESTX'] == 'xmlhttprequest') {
        return true;
    }
    return false;
}
function checkAjaxCSRF()
{
    if (!empty($_SERVER['HTTP_REQUESTX']) && strtolower($_SERVER['HTTP_REQUESTX']) == 'xmlhttprequest' && $_SERVER['csrf'] == generateCSRFToken()) {
        return true;
    }
    return false;
}
/**
 * Token protection
 */
function generateToken($form, $instance = -1)
{
    if (!isset($_SESSION)) {
        session_start();
    }
    $token = generateRandomAlpha(10);
    if($instance == -1)
        $_SESSION['token'][$form] = $token;
    else if($instance < 10){
        $_SESSION['token'][(int)$instance][$form] = $token;
    }
    return $token;
}
function checkToken($form, $instance = -1)
{
    if (!empty($_SESSION['token'][$instance][$form]) && !empty($_POST['token']) && $_SESSION['token'][$instance][$form] == $_POST['token'])
        return true;
    if (!empty($_SESSION['token'][$form]) && !empty($_POST['token']) && $_SESSION['token'][$form] == $_POST['token'])
        return true;
    return false;
}
/**
 * Generate a csrf token; one day
 */
function generateCSRFToken()
{
    return md5(substr(time(), 0, -5) ^ "earlybird");
}
/**
 * Call malformed request
 */
function call400()
{
    header("HTTP/1.0 400 Bad Request");
    die();
}
/**
 * check loggedin?
 */
function checkLoggedin()
{
    if (!isset($_SESSION)) {
        session_start();
    }
    // require_once 'db.class.php';

    if (!isset($_SESSION["user"])) {
        return false;
    }

    // if (isset($_COOKIE["_nwiz"])) { //check if cookie exists
    //     //only alpha numeric is allowed
    //     if (!preg_match("/^[a-zA-Z0-9]+$/", $_COOKIE["_nwiz"])) {
    //         return false;
    //     }
    //     $cookie = DB::queryFirstRow("SELECT uid FROM loggedin WHERE uid=%i AND auth=%s", $_SESSION['user']['id'], $_COOKIE["_nwiz"]);
    //     if (empty($cookie['uid'])) { //empty cookie
    //         return false;
    //     }
    // } else { //no cookie case
    //     return false;
    // }
    return true;
}
/**
 * logout
 */
function doLogout()
{
    if (!isset($_SESSION)) {
        session_start();
    }
    unset($_SESSION["user"]);
    session_destroy();
    require_once("Cookie.php");
    deleteCookie("_nwiz");
}
/**
 * Path traversal checker
 */
function isTraversal($fileName) //$basePath, 
{
    if (strpos(urldecode($fileName), '..') !== false)
        return true;
    /*
    //Until have a use case for this, this has been commented - TO BE TESTED
    $realBase = realpath($basePath);
    $userPath = $basePath.$fileName;
    $realUserPath = realpath($userPath);
    while ($realUserPath === false)
    {
        $userPath = dirname($userPath);
        $realUserPath = realpath($userPath);
    }
    return strpos($realUserPath, $realBase) !== 0;
    */
    return false;
}