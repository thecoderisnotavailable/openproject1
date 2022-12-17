<?php

/**
 * do a curl request
 */
function curlIt($url, $header, $data, $fast = false)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    if (!empty($header))
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
    if (!empty($data)) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
    if ($fast)
        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 100);
    $data = json_decode(curl_exec($ch), true);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($http_code != 200)
        return false;
    return $data;
}
