<?php

session_start();

require_once __DIR__ . '/auth_helpers.php';

auth_clear_session();

header('Location: ../../index.php');
exit;
