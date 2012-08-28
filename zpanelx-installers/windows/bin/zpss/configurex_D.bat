@echo off

echo Copying config packs..
xcopy /s/e D:\zpanel\panel\etc\build\config_packs\ms_windows\* D:\zpanel\configs
echo Done!

echo Importing ZPanel database..
mysql -h localhost -u root < D:\zpanel\configs\zpanel_core.sql
echo Cleaning up MySQL users (securing MySQL server)..
mysql -uroot < D:\zpanel\bin\zpss\MySQL_User_Cleanup.sql

echo Registering tools..
COPY D:\zpanel\panel\etc\build\bin\zppy.bat %windir%\zppy.bat /Y
COPY D:\zpanel\panel\etc\build\bin\setso.bat %windir%\setso.bat /Y
echo Done!

echo Running configuration task..
php D:\zpanel\bin\zpss\enviroment_configure.php
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
copy D:\zpanel\panel\etc\build\config_packs\ms_windows\bind\etc\named.conf.slave D:\zpanel\configs\bind\etc
copy D:\zpanel\panel\etc\build\config_packs\ms_windows\bind\etc\named.conf.vide.slave D:\zpanel\configs\bind\etc
echo Starting BIND
net start named
nssm install "named slave" D:\zpanel\bin\bind\bin\namedslave.exe
net start "named slave"

echo Running the daemon for the first time..
php D:\zpanel\panel\bin\daemon.php
echo Done!

echo Cleaning up..
DEL D:\zpanel\bin\zpss\*.php /Q
DEL D:\zpanel\configs\bind\zones\*.* /Q
RMDIR /Q/S D:\zpanel\bin\vcredist