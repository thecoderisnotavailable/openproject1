<?php

/**
 * List of all regexes
 */
function getRegex($key){
    switch($key){
        case "name":
            return '/^[\p{L}\p{M} ?]{2,50}$/s';
        case "email":
            return '/^(?=[a-z][a-z0-9@._-]{5,50}$)[a-z0-9._-]{1,30}@(?:(?=[a-z0-9-]{1,15}\.)[a-z0-9]+(?:-[a-z0-9]+)*\.){1,2}[a-z]{2,6}$/m';
        case "phone":
            return '/^(\+91)?\d{10}$/';
        default:
            return '/(.*)/g';
    }
}