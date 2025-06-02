<?php
require_once('../vendor/autoload.php');

$auth = new App\Authenticate();
$auth->validatePasswordAjax();
