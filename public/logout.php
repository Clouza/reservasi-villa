<?php
session_start();
/*
    logout.php - Menghancurkan session dan kembali ke halaman login.
*/
session_destroy();
header('Location: index.php');
exit;