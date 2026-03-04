<?php
require_once 'includes/init.php';
// on détruit la session et on redirige vers le login
session_destroy();
header('Location: login.php');
exit;
