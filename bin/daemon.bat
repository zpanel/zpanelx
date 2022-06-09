@echo off
:label
php daemon.php
timeout /t 30 /NOBREAK
GOTO label