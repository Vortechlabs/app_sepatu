<?php
session_start();

// Hapus semua session yang terkait login admin
session_unset();
session_destroy();

// Redirect ke halaman login admin
header('Location: ./admin_login.php');
exit;
