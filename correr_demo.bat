@echo off
title SACAM - Servidor local de desarrollo
cd /d "%~dp0"
echo.
echo  SACAM - Demo del Sprint 1
echo  Servidor local de desarrollo de PHP
echo  -------------------------------------------------
echo  Abre tu navegador en:  http://localhost:8000/index.php
echo  Para detener el servidor cierra esta ventana.
echo  -------------------------------------------------
echo.
"C:\xampp\php\php.exe" -S localhost:8000 -t public
echo.
echo El servidor se detuvo.
pause