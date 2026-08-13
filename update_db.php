<?php
require_once 'config/database.php';
$pdo->exec("UPDATE vehicles SET status = 'available' WHERE status = ''");
echo 'Updated successfully';
