<?php
require __DIR__ . '/config.php';
unset($_SESSION['visitante']);
header('Location: index.php');
exit;
