<?php

$query = $_SERVER['QUERY_STRING'] ?? '';
$target = '../../forgot.php';
if ($query !== '') {
    $target .= '?' . $query;
}

header('Location: ' . $target);
exit;
