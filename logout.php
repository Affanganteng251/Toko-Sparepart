<?php
require_once 'db.php';
require_once 'Auth.php';

$auth = new Auth($conn);

$auth->logout();
?>