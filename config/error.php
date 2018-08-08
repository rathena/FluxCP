<?php
// Configuration information for critical error handling.
// Critical errors are exposed due to an exception in the program.

// Setting $showExceptions to true will cause not only exceptions to be displayed
// but also the backtrace, which can result in security issues such as exposing
// your MySQL user and password when unable to connect. Please keep it at false
// in a production environment.

$adminEmail      = 'admin@localhost'; // Administrator e-mail address.
$errorFile       = 'error.php';       // Error file to render.
$showExceptions  = true;              // Whether or not to show exceptions (only applies to error.php)
?>
