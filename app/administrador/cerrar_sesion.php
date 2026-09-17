<?php
session_start();

unset($_SESSION['admin_autenticado']);
session_destroy();

header('Location: login.php');
exit;