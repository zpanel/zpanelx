@echo off

echo Copying config packs..
xcopy /s/e F:\zpanel\panel\etc\build\config_packs\ms_windows\* F:\zpanel\configs
echo Done!

echo Importing ZPanel database..
mysql -h localhost -u root < F:\zpanel\configs\zpanel_core.sql
echo Cleaning up MySQL users (securing MySQL server)..
mysql -uroot < F:\zpanel\bin\zpss\MySQL_User_Cleanup.sql

echo Registering tools..
COPY F:\zpanel\panel\etc\build\bin\zppy.bat %windir%\zppy.bat /Y
COPY F:\zpanel\panel\etc\build\bin\setso.bat %windir%\setso.bat /Y
echo Done!

echo Running configuration task..
php F:\zpanel\bin\zpss\enviroment_configure.php
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
copy F:\zpanel\panel\etc\build\config_packs\ms_windows\bind\etc\named.conf.slave F:\zpanel\configs\bind\etc
copy F:\zpanel\panel\etc\build\config_packs\ms_windows\bind\etc\named.conf.vide.slave F:\zpanel\configs\bind\etc
echo Starting BIND
net start named
nssm install "named slave" F:\zpanel\bin\bind\bin\namedslave.exe
net start "named slave"

echo Running the daemon for the first time..
php F:\zpanel\panel\bin\daemon.php
echo Done!

echo Cleaning up..
DEL F:\zpanel\bin\zpss\*.php /Q
DEL F:\zpanel\configs\bind\zones\*.* /Q
RMDIR /Q/S F:\zpanel\bin\vcredist