@echo off

echo Copying config packs..
xcopy /s/e G:\zpanel\panel\etc\build\config_packs\ms_windows\* G:\zpanel\configs
echo Done!

echo Importing ZPanel database..
mysql -h localhost -u root < G:\zpanel\configs\zpanel_core.sql
echo Cleaning up MySQL users (securing MySQL server)..
mysql -uroot < G:\zpanel\bin\zpss\MySQL_User_Cleanup.sql

echo Registering tools..
COPY G:\zpanel\panel\etc\build\bin\zppy.bat %windir%\zppy.bat /Y
COPY G:\zpanel\panel\etc\build\bin\setso.bat %windir%\setso.bat /Y
echo Done!

echo Running configuration task..
php G:\zpanel\bin\zpss\enviroment_configure.php
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
copy G:\zpanel\panel\etc\build\config_packs\ms_windows\bind\etc\named.conf.slave G:\zpanel\configs\bind\etc
copy G:\zpanel\panel\etc\build\config_packs\ms_windows\bind\etc\named.conf.vide.slave G:\zpanel\configs\bind\etc
echo Starting BIND
net start named
nssm install "named slave" G:\zpanel\bin\bind\bin\namedslave.exe
net start "named slave"

echo Running the daemon for the first time..
php G:\zpanel\panel\bin\daemon.php
echo Done!

echo Cleaning up..
DEL G:\zpanel\bin\zpss\*.php /Q
DEL G:\zpanel\configs\bind\zones\*.* /Q
RMDIR /Q/S G:\zpanel\bin\vcredist