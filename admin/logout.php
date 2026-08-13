<?php
// admin/logout.php
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

logoutAdmin();
redirect('/soutarah/public/index.php');
