<?php
session_start();

if (empty($_SESSION['admin_autenticado'])) {
    header('Location: login.php');
    exit;
}