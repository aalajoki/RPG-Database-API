<?php
// Show error reporting
error_reporting(E_ALL);
 
// Set your default time-zone
date_default_timezone_set('Asia/Manila');
 
// Variables used for JWT
$key = "example_key";
$iss = "http://example.org";
$aud = "http://example.com";
$iat = 1356999524;
$nbf = 1357000000;