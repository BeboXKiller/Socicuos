<?php

require_once('../vendor/autoload.php');
use App\Alert;

$message = $_GET['message'] ?? '';
$type = $_GET['type'] ?? 'Normal';

// Output the alert HTML
echo Alert::PrintMessage($message, $type);
