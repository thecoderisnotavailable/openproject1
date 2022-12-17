<?php
/**
 * Cookie Manager
 */
class CookieList{
    public $USER_COOKIE1 = "_nwiz";
    public $USER_COOKIE2 = "";

    public $DOMAIN = "127.1.1.3";
    public $SECURE = false;
    public $HTTPS = false;

    public function TIME(){ return time() + (86400 * 30); }
}
/**
 * Set a cookie
 */
function setACookie($name, $value, $expire = 0, $domain = "", $secure = false, $httponly = false)
{
    if (!setcookie($name, $value, $expire, '/', $domain, $secure, $httponly)) {
        echo "error";
        return false;
    }
    return true;
}
/**
 * Get a cookie
 */
function getCookie($name)
{
    if (isset($_COOKIE[$name]))
        return $_COOKIE[$name];
    return false;
}
/**
 * Delete a cookie
 */
function deleteCookie($name)
{
    setCookie($name, '', time() - 3600);
}
/**
 * Check if a cookie exists
 */
function cookieExists($name)
{
    if (isset($_COOKIE[$name]))
        return true;
    return false;
}