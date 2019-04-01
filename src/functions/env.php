<?php

use Dotenv\Dotenv;

// Inspired by Laravel's env() helper
function env($key, $default = null) {
    $loaded = false;

    if(!$loaded) {
        $dotenv = Dotenv::create(__DIR__ . '/../..');
        $dotenv->load();
        $loaded = true;
    }

    $val = getenv($key);

    if($val === false) {
        return $default;
    }

    switch($val) {
        case 'true':
        case '(true)':
            return true;
        case 'false':
        case '(false)':
            return false;
        case 'empty':
        case '(empty)':
            return '';
        case 'null':
        case '(null)':
            return null;
    }

    return $val;
}
