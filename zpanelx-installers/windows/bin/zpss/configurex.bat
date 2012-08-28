@echo off

echo Copying config packs..
xcopy /s/e C:\zpanel\panel\etc\build\config_packs\ms_windows\* C:\zpanel\configs
echo Done!

echo Importing ZPanel database..
mysql -uroot < C:\zpanel\configs\zpanel_core.sql
echo Cleaning up MySQL users (securing MySQL server)..
mysql -uroot < C:\zpanel\bin\zpss\MySQL_User_Cleanup.sql

echo Registering tools..
COPY C:\zpanel\panel\etc\build\bin\zppy.bat %windir%\zppy.bat /Y
COPY C:\zpanel\panel\etc\build\bin\setso.bat %windir%\setso.bat /Y
echo Done!

echo Running configuration task..
php C:\zpanel\bin\zpss\enviroment_configure.php
echo The installer will now finalise the install...
pause

echo Restarting services..
echo Stopping Apache
net stop Apache 
echo Starting Apache
net start Apache
echo Stopping hMailServer
net stop hMailServer
echo Starting hMailServer
net start hMailServer
echo Stopping BIND
net stop named
copy C:\zpanel\panel\etc\build\config_packs\ms_windows\bind\etc\named.conf.slave C:\zpanel\configs\bind\etc
copy C:\zpanel\panel\etc\build\config_packs\ms_windows\bind\etc\named.conf.vide.slave C:\zpanel\configs\bind\etc
echo Starting BIND
net start named
nssm install "named slave" C:\zpanel\bin\bind\bin\namedslave.exe
net start "named slave"

echo Running the daemon for the first time..
php C:\zpanel\panel\bin\daemon.php
echo Done!

echo Cleaning up..
DEL C:\zpanel\bin\zpss\*.php /Q
DEL C:\zpanel\configs\bind\zones\*.* /Q
RMDIR /Q/S C:\zpanel\bin\vcredist