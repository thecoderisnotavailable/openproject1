<?php

/**
 * Generates a random string
 */
function generateRandomAlpha($length = 10)
{
    $characters = '123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    //$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
/**
 * String clean fucntions, Test before use
 */
function cleanUTF($string)
{
    $string = preg_replace('/[\x00-\x1F\x7F]/u', '', $string);
    return htmlentities($string, ENT_QUOTES, 'UTF-8');
}
function cleanUTF1($str)
{
    return preg_replace('/[^[:print:]]/', '', htmlspecialchars($str));
}
/**
 * generate a unique id
 */
function generateUniqueId($n = 12)
{
    $rand = '';
    for ($i = 0; $i < $n; $i++) {
        $rand .= mt_rand(0, 9);
    }
    //generate random alpha
    $rand .= generateRandomAlpha(10);
    return $rand;
}

/**
 * Slug generator
 */
function slugify($text, $divider = '-')
{
    $text = preg_replace('~[^\pL\d]+~u', $divider, $text);
    $text = iconv('utf-8', 'ASCII//IGNORE//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, $divider);
    $text = preg_replace('~-+~', $divider, $text);
    $text = str_replace('.', $divider, $text);
    $text = str_replace('--', $divider, $text);
    $text = strtolower($text);
    if (empty($text)) {
        return '';
    }

    return $text;
}
/**
 * Sanitize all values in the array with htmlEntities
 */
function sanitizeArrayValues($arr, $allowed = []){
    if($allowed == [])
        $key = array_keys($arr);
    else
        $key = $allowed;
    for ($i=0; $i < count($key); $i++) 
    {
        if (empty($arr[$key[$i]]))
            $arr[$key[$i]] = null;
        else if(is_array($arr[$key[$i]]))
            $arr[$key[$i]] = sanitizeArrayValues([$key[$i]]);
        else
            $arr[$key[$i]] = cleanUTF($arr[$key[$i]]);
    }
    return $arr;
}
/**
 * Length checker
 * true: failed
 * false: passed
 */
function lengthCheck($map, $arr){
    $key = array_keys($map);
    for ($i=0; $i < count($key); $i++) 
    {
        if(!empty($arr[$key[$i]]))
            if(strlen($arr[$key[$i]]) > $map[$key[$i]])
                return true;
    }
    return false;
}
/**
 * Print error
 */
function printError($e, $id = 1){
    echo '
    {
        "a" : {
            "e" : '.$id.',
            "m" : "'.$e.'"
        }
    }';
}