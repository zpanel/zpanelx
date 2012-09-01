@echo off

echo Copying config packs..
xcopy /s/e %ZPANELDIR%\zpanel\panel\etc\build\config_packs\ms_windows\* %ZPANELDIR%\zpanel\configs
echo Done!

echo Importing ZPanel database..
mysql -uroot < %ZPANELDIR%\zpanel\configs\zpanel_core.sql
mysql -uroot < %ZPANELDIR%\zpanel\panel\modules\webmail\apps\roundcube\SQL\mysql.initial.sql
echo Cleaning up MySQL users (securing MySQL server)..
mysql -uroot < %ZPANELDIR%\zpanel\bin\zpss\MySQL_User_Cleanup.sql

echo Registering tools..
COPY %ZPANELDIR%\zpanel\panel\etc\build\bin\zppy.bat %windir%\zppy.bat /Y
COPY %ZPANELDIR%\zpanel\panel\etc\build\bin\setso.bat %windir%\setso.bat /Y
echo Done!

echo Running configuration task..
php %ZPANELDIR%\zpanel\bin\zpss\enviroment_configure.php
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
copy %ZPANELDIR%\zpanel\panel\etc\build\config_packs\ms_windows\bind\etc\named.conf.slave %ZPANELDIR%\zpanel\configs\bind\etc
copy %ZPANELDIR%\zpanel\panel\etc\build\config_packs\ms_windows\bind\etc\named.conf.vide.slave %ZPANELDIR%\zpanel\configs\bind\etc
echo Starting BIND
net start named
nssm install "named slave" %ZPANELDIR%\zpanel\bin\bind\bin\namedslave.exe
net start "named slave"

echo Running the daemon for the first time..
php %ZPANELDIR%\zpanel\panel\bin\daemon.php
echo Done!

echo Cleaning up..
DEL %ZPANELDIR%\zpanel\bin\zpss\*.php /Q
DEL %ZPANELDIR%\zpanel\configs\bind\zones\*.* /Q
RMDIR /Q/S %ZPANELDIR%\zpanel\bin\vcredist
exit