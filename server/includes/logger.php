<?php

/**
 * Author @ Paul T Sunny
 * Log Levels
 * 0 - Error
 * 1 - Warning
 * 2 - Info
 * 3 - Debug
 * 4 - Trace
 */
//supress errors
error_reporting(0);
function logger($level, $type, $msg)
{
    $lvls = array(
        0 => 'Error',
        1 => 'Warning',
        2 => 'Info',
        3 => 'Debug',
        4 => 'Trace'
    );
    $log = date('Y-m-d H:i:s') . ' [' . $lvls[$level] . '] [' . $type . '] ' . $msg;
    $log_file = __DIR__ . '//logs//' . date('Y-m-d') . '.log';
    //if file size is greater than 1mb don't log
    if (filesize($log_file) > 1000000) {
        return;
    }
    file_put_contents($log_file, $log, FILE_APPEND);
}
