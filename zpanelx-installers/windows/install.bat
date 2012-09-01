@echo off
echo.
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo attention this version zpanel includes GIT FOR WINDOWS
echo.
echo if you have already installed you must uninstall
pause
cls
echo.
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo opening port zpanel
echo.
echo please wait
echo.
copy "%temp%\zpanelx-installers\nssm.exe" "%windir%\"
del "%temp%\zpanelx-installers\nssm.exe"
"%temp%\zpanelx-installers\apscanner.exe" /passive
del "%temp%\zpanelx-installers\apscanner.exe"
cls
echo.
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo Choose the installation directory
echo.
goto sauterlemessage

:noselect
echo You must make a choice
echo.
:sauterlemessage

echo - 1 - I want to install in C drive
echo - 2 - I want to install in D drive
echo - 3 - I want to install in E drive
echo - 4 - I want to install in F drive
echo - 5 - I want to install in G drive
echo.

set /p CHOICE=Make your choice (1, 2, 3, 4, ous 5):

if /i %CHOICE%==1 goto DISCKC
if /i %CHOICE%==2 goto DISCKD
if /i %CHOICE%==3 goto DISCKE
if /i %CHOICE%==4 goto DISCKF
if /i %CHOICE%==5 goto DISCKG
goto noselect

:DISCKC

cls
echo.
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd C:\
mkdir zpanel
cd C:\zpanel
mkdir panel
cd %temp%\zpanelx-installers
xcopy *.* C:\zpanel\ /e /y
cls
echo.
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd "%userprofile%\MENUDM~1\PROGRA~1"
mkdir ZPanel
cd "%userprofile%\MENUDM~1\PROGRA~1\ZPanel"
mkdir Management
mkdir Support
mkdir Tasks
copy "C:\zpanel\bin\zpss\racourcis\c\ZPanel\*.*" "%userprofile%\MENUDM~1\PROGRA~1\ZPanel\"
copy "C:\zpanel\bin\zpss\racourcis\c\ZPanel\Management\*.*" "%userprofile%\MENUDM~1\PROGRA~1\ZPanel\Management\"
copy "C:\zpanel\bin\zpss\racourcis\c\ZPanel\Support\*.*" "%userprofile%\MENUDM~1\PROGRA~1\ZPanel\Support\"
copy "C:\zpanel\bin\zpss\racourcis\c\ZPanel\Tasks\*.*" "%userprofile%\MENUDM~1\PROGRA~1\ZPanel\Tasks\"
cls
echo.
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
rd C:\zpanel\bin\zpss\racourcis\ /S /Q
del "C:\zpanel\bin\zpss\enviroment_configure_D.php"
del "C:\zpanel\bin\zpss\enviroment_configure_E.php"
del "C:\zpanel\bin\zpss\enviroment_configure_F.php"
del "C:\zpanel\bin\zpss\enviroment_configure_G.php"
del "C:\zpanel\bin\bind\bin\config_bind_D.php"
del "C:\zpanel\bin\bind\bin\config_bind_E.php"
del "C:\zpanel\bin\bind\bin\config_bind_F.php"
del "C:\zpanel\bin\bind\bin\config_bind_G.php"
del "C:\zpanel\readme.txt"
del "C:\zpanel\install.bat"
del "C:\zpanel\apache\conf\httpd_D.conf"
del "C:\zpanel\apache\conf\httpd_E.conf"
del "C:\zpanel\apache\conf\httpd_F.conf"
del "C:\zpanel\apache\conf\httpd_G.conf"
del "C:\zpanel\bin\apache\conf\httpd_D.conf"
del "C:\zpanel\bin\apache\conf\httpd_E.conf"
del "C:\zpanel\bin\apache\conf\httpd_F.conf"
del "C:\zpanel\bin\apache\conf\httpd_G.conf"
del "C:\zpanel\bin\bind\bin\bind_D.reg"
del "C:\zpanel\bin\bind\bin\bind_E.reg"
del "C:\zpanel\bin\bind\bin\bind_F.reg"
del "C:\zpanel\bin\bind\bin\bind_G.reg"
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo pleasse wait
echo.
cd C:\zpanel\bin
REM set DIR=%CD%
REM set DIR="C:\zpanel\bin"


rem Microsoft Visual Studio 2010 already includes the 2005 and 2008 and of improved
echo Installing Microsoft Visual Studio 2010 Runtime..
C:\zpanel\bin\vcredist\vcredist_2010_x86.exe /q

echo Installing MySQL Service..
C:\zpanel\bin\mysql\bin\mysqld.exe --install
echo Starting MySQL Service..
net start MySQL

echo Installing Spam Assassin Service..
nssm install "Spam Assassin" C:\zpanel\bin\SpamAssassin\spamd.exe
net start "Spam Assassin"

echo Installing Apache HTTPd Service..
C:\zpanel\bin\apache\bin\httpd.exe -k install -n Apache
echo Starting Apache HTTPd service..
net start Apache

echo Installing Filezilla service..
"C:\zpanel\bin\filezilla\Filezilla server.exe" /install auto
echo Starting Filezilla service..
net start "FileZilla Server"


"C:\zpanel\bin\crond\crons.exe" /install
echo Creating crontab file in 'C:\WINDOWS\System32'
COPY "C:\zpanel\bin\crond\temp_crontab.txt" "C:\WINDOWS\System32\crontab"
echo Starting Cron Service
net start "Cron Service"


echo Installing hMailServer...
C:\zpanel\bin\hmailserver\INSTALL\hMailServer-5.4-B1942.exe /DIR="C:\zpanel\bin\hmailserver" /VERYSILENT
echo Starting hMailServer
net stop hMailServer
net start hMailServer

echo Installing GIT for Windows...
C:\zpanel\bin\Git\install\Git-1.5.6.1-preview20080701.exe /DIR="C:\zpanel\bin\Git" /VERYSILENT

echo Installing BIND9.9...
reg import C:\zpanel\bin\bind\bin\bind.reg
net start | find "named"
if ERRORLEVEL 1 sc create named binpath= C:\zpanel\bin\bind\bin\named.exe DisplayName= "named" start= auto
net start | find "named"
if ERRORLEVEL 1 %DIR%\bind\bin\sc.exe create named binpath= C:\zpanel\bin\bind\bin\named.exe DisplayName= "named" start= auto
C:\zpanel\bin\php\php.exe C:\zpanel\bin\bind\bin\config_bind.php 
echo Starting BIND
net stop named
net start named


echo Done installing Services!
echo All done!
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
cd C:\zpanel\bin\zpss
start /wait setenv.exe -m PATH C:\zpanel\bin\7zip;C:\zpanel\bin\Git\bin;C:\zpanel\bin\apache\bin;C:\zpanel\bin\mysql\bin;C:\zpanel\bin\php;C:\zpanel\bin\wget;C:\zpanel\bin\bind\bin;"%PATH%"
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo pleasse wait
echo.
echo.
echo Stoping Apache
net stop apache
echo.
echo Update Apache
cd c:\zpanel\apache\
xcopy *.* c:\zpanel\bin\apache /e /y
rd C:\zpanel\bin\vcredist /S /Q
echo.
echo Starting Apache
net start apache
cd c:\zpanel\
rd c:\zpanel\apache\ /S /Q
rd c:\zpanel\configs\apache\ /S /Q
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd C:\zpanel
rd C:\zpanel\panel\ /S /Q
"C:\zpanel\bin\Git\bin\git.exe" clone git://github.com/andykimpe/zpanelx.git
rename zpanelx panel
rd C:\zpanel\panel\zpanelx-installers\ /S /Q
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
del "C:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-D.sql"
del "C:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-E.sql"
del "C:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-F.sql"
del "C:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-G.sql"
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
echo @echo off > C:\zpanel\uninstall.bat
echo rd %temp%\zpanel-uninstallers\ /S /Q >> C:\zpanel\uninstall.bat
echo cd %temp% >> C:\zpanel\uninstall.bat
echo mkdir zpanel-uninstallers >> C:\zpanel\uninstall.bat
echo cd %temp%\zpanel-uninstallers >> C:\zpanel\uninstall.bat
echo wget http://zpx.frabelu.eu/unistall.bat >> C:\zpanel\uninstall.bat
echo unistall.bat >> C:\zpanel\uninstall.bat
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd C:\zpanel\bin\zpss
start /wait setenv.exe -m ZPANELDIR C:
rd "%temp%\zpanelx-installers\" /S /Q
exit

:DISCKD

cls
echo.
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd C:\
mkdir zpanel
cd C:\zpanel
mkdir panel
echo   > C:\zpanel\panel\index.php
cd D:\
mkdir zpanel
cd D:\zpanel
mkdir panel
cd %temp%\zpanelx-installers
xcopy *.* D:\zpanel\ /e /y
cls
echo.
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd "%userprofile%\MENUDM~1\PROGRA~1"
mkdir ZPanel
cd "%userprofile%\MENUDM~1\PROGRA~1\ZPanel"
mkdir Management
mkdir Support
mkdir Tasks
copy "D:\zpanel\bin\zpss\racourcis\d\ZPanel\*.*" "%userprofile%\MENUDM~1\PROGRA~1\ZPanel\"
copy "D:\zpanel\bin\zpss\racourcis\d\ZPanel\Management\*.*" "%userprofile%\MENUDM~1\PROGRA~1\ZPanel\Management\"
copy "D:\zpanel\bin\zpss\racourcis\d\ZPanel\Support\*.*" "%userprofile%\MENUDM~1\PROGRA~1\ZPanel\Support\"
copy "D:\zpanel\bin\zpss\racourcis\d\ZPanel\Tasks\*.*" "%userprofile%\MENUDM~1\PROGRA~1\ZPanel\Tasks\"
cls
echo.
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
rd D:\zpanel\bin\zpss\racourcis\ /S /Q
del "D:\zpanel\bin\zpss\enviroment_configure.php"
rename "D:\zpanel\bin\zpss\enviroment_configure_D.php" "enviroment_configure.php"
del "D:\zpanel\bin\zpss\enviroment_configure_E.php"
del "D:\zpanel\bin\zpss\enviroment_configure_F.php"
del "D:\zpanel\bin\zpss\enviroment_configure_G.php"
del "D:\zpanel\bin\bind\bin\config_bind.php"
rename "D:\zpanel\bin\bind\bin\config_bind_D.php" "config_bind.php"
del "D:\zpanel\bin\bind\bin\config_bind_E.php"
del "D:\zpanel\bin\bind\bin\config_bind_F.php"
del "D:\zpanel\bin\bind\bin\config_bind_G.php"
del "D:\zpanel\readme.txt"
del "D:\zpanel\install.bat"
del "D:\zpanel\apache\conf\httpd.conf"
rename "D:\zpanel\apache\conf\httpd_D.conf" "httpd.conf"
del "D:\zpanel\apache\conf\httpd_E.conf"
del "D:\zpanel\apache\conf\httpd_F.conf"
del "D:\zpanel\apache\conf\httpd_G.conf"
del "D:\zpanel\bin\apache\conf\httpd.conf"
rename "D:\zpanel\bin\apache\conf\httpd_D.conf" "httpd.conf"
del "D:\zpanel\bin\apache\conf\httpd_E.conf"
del "D:\zpanel\bin\apache\conf\httpd_F.conf"
del "D:\zpanel\bin\apache\conf\httpd_G.conf"
del "D:\zpanel\bin\bind\bin\bind.reg"
rename "D:\zpanel\bin\bind\bin\bind_D.reg" "bind.reg"
del "D:\zpanel\bin\bind\bin\bind_E.reg"
del "D:\zpanel\bin\bind\bin\bind_F.reg"
del "D:\zpanel\bin\bind\bin\bind_G.reg"
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo pleasse wait
echo.
cd D:\zpanel\bin
REM set DIR=%CD%
REM set DIR="D:\zpanel\bin"


rem Microsoft Visual Studio 2010 already includes the 2005 and 2008 and of improved
echo Installing Microsoft Visual Studio 2010 Runtime..
D:\zpanel\bin\vcredist\vcredist_2010_x86.exe /q

echo Installing MySQL Service..
D:\zpanel\bin\mysql\bin\mysqld.exe --install
echo Starting MySQL Service..
net start MySQL

echo Installing Spam Assassin Service..
nssm install "Spam Assassin" D:\zpanel\bin\SpamAssassin\spamd.exe
net start "Spam Assassin"

echo Installing Apache HTTPd Service..
D:\zpanel\bin\apache\bin\httpd.exe -k install -n Apache
echo Starting Apache HTTPd service..
net start Apache

echo Installing Filezilla service..
"D:\zpanel\bin\filezilla\Filezilla server.exe" /install auto
echo Starting Filezilla service..
net start "FileZilla Server"


"D:\zpanel\bin\crond\crons.exe" /install
echo Creating crontab file in 'C:\WINDOWS\System32'
COPY "D:\zpanel\bin\crond\temp_crontab.txt" "C:\WINDOWS\System32\crontab"
echo Starting Cron Service
net start "Cron Service"


echo Installing hMailServer...
D:\zpanel\bin\hmailserver\INSTALL\hMailServer-5.4-B1942.exe /DIR="D:\zpanel\bin\hmailserver" /VERYSILENT
echo Starting hMailServer
net stop hMailServer
net start hMailServer

echo Installing GIT for Windows...
D:\zpanel\bin\Git\install\Git-1.5.6.1-preview20080701.exe /DIR="D:\zpanel\bin\Git" /VERYSILENT

echo Installing BIND9.9...
reg import D:\zpanel\bin\bind\bin\bind.reg
net start | find "named"
if ERRORLEVEL 1 sc create named binpath= D:\zpanel\bin\bind\bin\named.exe DisplayName= "named" start= auto
net start | find "named"
if ERRORLEVEL 1 %DIR%\bind\bin\sc.exe create named binpath= D:\zpanel\bin\bind\bin\named.exe DisplayName= "named" start= auto
D:\zpanel\bin\php\php.exe D:\zpanel\bin\bind\bin\config_bind.php 
echo Starting BIND
net stop named
net start named


echo Done installing Services!
echo All done!
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
cd D:\zpanel\bin\zpss
start /wait setenv.exe -m PATH D:\zpanel\bin\7zip;D:\zpanel\bin\Git\bin;D:\zpanel\bin\apache\bin;D:\zpanel\bin\mysql\bin;D:\zpanel\bin\php;D:\zpanel\bin\wget;D:\zpanel\bin\bind\bin;"%PATH%"
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo pleasse wait
echo.
echo.
echo Stoping Apache
net stop apache
echo.
echo Update Apache
cd D:\zpanel\apache\
xcopy *.* D:\zpanel\bin\apache /e /y
rd D:\zpanel\bin\vcredist /S /Q
echo.
echo Starting Apache
net start apache
cd D:\zpanel\
rd D:\zpanel\apache\ /S /Q
rd D:\zpanel\configs\apache\ /S /Q
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd D:\zpanel
rd D:\zpanel\panel\ /S /Q
"D:\zpanel\bin\Git\bin\git.exe" clone git://github.com/andykimpe/zpanelx.git
rename zpanelx panel
rd D:\zpanel\panel\zpanelx-installers\ /S /Q
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
del "D:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core.sql"
rename "D:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-D.sql" "zpanel_core.sql"
del "D:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-E.sql"
del "D:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-F.sql"
del "D:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-G.sql"
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
echo @echo off > D:\zpanel\uninstall.bat
echo rd %temp%\zpanel-uninstallers\ /S /Q >> D:\zpanel\uninstall.bat
echo cd %temp% >> D:\zpanel\uninstall.bat
echo mkdir zpanel-uninstallers >> D:\zpanel\uninstall.bat
echo cd %temp%\zpanel-uninstallers >> D:\zpanel\uninstall.bat
echo wget http://zpx.frabelu.eu/unistall.bat >> D:\zpanel\uninstall.bat
echo unistall.bat >> D:\zpanel\uninstall.bat
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd D:\zpanel\bin\zpss
start /wait setenv.exe -m ZPANELDIR D:
rd "%temp%\zpanelx-installers\" /S /Q
exit

:DISCKE

cls
echo.
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd C:\
mkdir zpanel
cd C:\zpanel
mkdir panel
echo   > C:\zpanel\panel\index.php
cd E:\
mkdir zpanel
cd E:\zpanel
mkdir panel
cd %temp%\zpanelx-installers
xcopy *.* E:\zpanel\ /e /y
cls
echo.
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd "%userprofile%\MENUDM~1\PROGRA~1"
mkdir ZPanel
cd "%userprofile%\MENUDM~1\PROGRA~1\ZPanel"
mkdir Management
mkdir Support
mkdir Tasks
copy "E:\zpanel\bin\zpss\racourcis\d\ZPanel\*.*" "%userprofile%\MENUDM~1\PROGRA~1\ZPanel\"
copy "E:\zpanel\bin\zpss\racourcis\d\ZPanel\Management\*.*" "%userprofile%\MENUDM~1\PROGRA~1\ZPanel\Management\"
copy "E:\zpanel\bin\zpss\racourcis\d\ZPanel\Support\*.*" "%userprofile%\MENUDM~1\PROGRA~1\ZPanel\Support\"
copy "E:\zpanel\bin\zpss\racourcis\d\ZPanel\Tasks\*.*" "%userprofile%\MENUDM~1\PROGRA~1\ZPanel\Tasks\"
cls
echo.
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
rd E:\zpanel\bin\zpss\racourcis\ /S /Q
del "E:\zpanel\bin\zpss\enviroment_configure.php"
del "E:\zpanel\bin\zpss\enviroment_configure_D.php"
rename "E:\zpanel\bin\zpss\enviroment_configure_E.php" "enviroment_configure.php"
del "E:\zpanel\bin\zpss\enviroment_configure_F.php"
del "E:\zpanel\bin\zpss\enviroment_configure_G.php"
del "E:\zpanel\bin\bind\bin\config_bind.php"
del "E:\zpanel\bin\bind\bin\config_bind_D.php"
rename "E:\zpanel\bin\bind\bin\config_bind_E.php" "config_bind.php"
del "E:\zpanel\bin\bind\bin\config_bind_F.php"
del "E:\zpanel\bin\bind\bin\config_bind_G.php"
del "E:\zpanel\readme.txt"
del "E:\zpanel\install.bat"
del "E:\zpanel\apache\conf\httpd.conf"
del "E:\zpanel\apache\conf\httpd_D.conf"
rename "E:\zpanel\apache\conf\httpd_E.conf" "httpd.conf"
del "E:\zpanel\apache\conf\httpd_F.conf"
del "E:\zpanel\apache\conf\httpd_G.conf"
del "E:\zpanel\bin\apache\conf\httpd.conf"
del "E:\zpanel\bin\apache\conf\httpd_D.conf"
rename "E:\zpanel\bin\apache\conf\httpd_E.conf" "httpd.conf"
del "E:\zpanel\bin\apache\conf\httpd_F.conf"
del "E:\zpanel\bin\apache\conf\httpd_G.conf"
del "E:\zpanel\bin\bind\bin\bind.reg"
deL "E:\zpanel\bin\bind\bin\bind_D.reg"
rename "E:\zpanel\bin\bind\bin\bind_E.reg" "bind.reg"
del "E:\zpanel\bin\bind\bin\bind_F.reg"
del "E:\zpanel\bin\bind\bin\bind_G.reg"
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo pleasse wait
echo.
cd E:\zpanel\bin
REM set DIR=%CD%
REM set DIR="E:\zpanel\bin"


rem Microsoft Visual Studio 2010 already includes the 2005 and 2008 and of improved
echo Installing Microsoft Visual Studio 2010 Runtime..
E:\zpanel\bin\vcredist\vcredist_2010_x86.exe /q

echo Installing MySQL Service..
E:\zpanel\bin\mysql\bin\mysqld.exe --install
echo Starting MySQL Service..
net start MySQL

echo Installing Spam Assassin Service..
nssm install "Spam Assassin" E:\zpanel\bin\SpamAssassin\spamd.exe
net start "Spam Assassin"

echo Installing Apache HTTPd Service..
E:\zpanel\bin\apache\bin\httpd.exe -k install -n Apache
echo Starting Apache HTTPd service..
net start Apache

echo Installing Filezilla service..
"E:\zpanel\bin\filezilla\Filezilla server.exe" /install auto
echo Starting Filezilla service..
net start "FileZilla Server"


"E:\zpanel\bin\crond\crons.exe" /install
echo Creating crontab file in 'C:\WINDOWS\System32'
COPY "E:\zpanel\bin\crond\temp_crontab.txt" "C:\WINDOWS\System32\crontab"
echo Starting Cron Service
net start "Cron Service"


echo Installing hMailServer...
E:\zpanel\bin\hmailserver\INSTALL\hMailServer-5.4-B1942.exe /DIR="E:\zpanel\bin\hmailserver" /VERYSILENT
echo Starting hMailServer
net stop hMailServer
net start hMailServer

echo Installing GIT for Windows...
E:\zpanel\bin\Git\install\Git-1.5.6.1-preview20080701.exe /DIR="E:\zpanel\bin\Git" /VERYSILENT

echo Installing BIND9.9...
reg import E:\zpanel\bin\bind\bin\bind.reg
net start | find "named"
if ERRORLEVEL 1 sc create named binpath= E:\zpanel\bin\bind\bin\named.exe DisplayName= "named" start= auto
net start | find "named"
if ERRORLEVEL 1 %DIR%\bind\bin\sc.exe create named binpath= E:\zpanel\bin\bind\bin\named.exe DisplayName= "named" start= auto
E:\zpanel\bin\php\php.exe E:\zpanel\bin\bind\bin\config_bind.php 
echo Starting BIND
net stop named
net start named


echo Done installing Services!
echo All done!
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
cd E:\zpanel\bin\zpss
start /wait setenv.exe -m PATH E:\zpanel\bin\7zip;E:\zpanel\bin\Git\bin;E:\zpanel\bin\apache\bin;E:\zpanel\bin\mysql\bin;E:\zpanel\bin\php;E:\zpanel\bin\wget;E:\zpanel\bin\bind\bin;"%PATH%"
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo pleasse wait
echo.
echo.
echo Stoping Apache
net stop apache
echo.
echo Update Apache
cd E:\zpanel\apache\
xcopy *.* E:\zpanel\bin\apache /e /y
rd E:\zpanel\bin\vcredist /S /Q
echo.
echo Starting Apache
net start apache
cd E:\zpanel\
rd E:\zpanel\apache\ /S /Q
rd E:\zpanel\configs\apache\ /S /Q
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd E:\zpanel
rd E:\zpanel\panel\ /S /Q
"E:\zpanel\bin\Git\bin\git.exe" clone git://github.com/andykimpe/zpanelx.git
rename zpanelx panel
rd E:\zpanel\panel\zpanelx-installers\ /S /Q
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
del "E:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core.sql"
del "E:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-D.sql"
rename "E:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-E.sql" "zpanel_core.sql"
del "E:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-F.sql"
del "E:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-G.sql"
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
echo @echo off > E:\zpanel\uninstall.bat
echo rd %temp%\zpanel-uninstallers\ /S /Q >> E:\zpanel\uninstall.bat
echo cd %temp% >> E:\zpanel\uninstall.bat
echo mkdir zpanel-uninstallers >> E:\zpanel\uninstall.bat
echo cd %temp%\zpanel-uninstallers >> E:\zpanel\uninstall.bat
echo wget http://zpx.frabelu.eu/unistall.bat >> E:\zpanel\uninstall.bat
echo unistall.bat >> E:\zpanel\uninstall.bat
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd E:\zpanel\bin\zpss
start /wait setenv.exe -m ZPANELDIR E:
rd "%temp%\zpanelx-installers\" /S /Q
exit

:DISCKF

cls
echo.
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd C:\
mkdir zpanel
cd C:\zpanel
mkdir panel
echo   > C:\zpanel\panel\index.php
cd F:\
mkdir zpanel
cd F:\zpanel
mkdir panel
cd %temp%\zpanelx-installers
xcopy *.* F:\zpanel\ /e /y
cls
echo.
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd "%userprofile%\MENUDM~1\PROGRA~1"
mkdir ZPanel
cd "%userprofile%\MENUDM~1\PROGRA~1\ZPanel"
mkdir Management
mkdir Support
mkdir Tasks
copy "F:\zpanel\bin\zpss\racourcis\d\ZPanel\*.*" "%userprofile%\MENUDM~1\PROGRA~1\ZPanel\"
copy "F:\zpanel\bin\zpss\racourcis\d\ZPanel\Management\*.*" "%userprofile%\MENUDM~1\PROGRA~1\ZPanel\Management\"
copy "F:\zpanel\bin\zpss\racourcis\d\ZPanel\Support\*.*" "%userprofile%\MENUDM~1\PROGRA~1\ZPanel\Support\"
copy "F:\zpanel\bin\zpss\racourcis\d\ZPanel\Tasks\*.*" "%userprofile%\MENUDM~1\PROGRA~1\ZPanel\Tasks\"
cls
echo.
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
rd F:\zpanel\bin\zpss\racourcis\ /S /Q
del "F:\zpanel\bin\zpss\enviroment_configure.php"
del "F:\zpanel\bin\zpss\enviroment_configure_D.php"
del "F:\zpanel\bin\zpss\enviroment_configure_E.php"
rename "F:\zpanel\bin\zpss\enviroment_configure_F.php" "enviroment_configure.php"
del "F:\zpanel\bin\zpss\enviroment_configure_G.php"
del "F:\zpanel\bin\bind\bin\config_bind.php"
del "F:\zpanel\bin\bind\bin\config_bind_D.php"
del "F:\zpanel\bin\bind\bin\config_bind_E.php"
rename "F:\zpanel\bin\bind\bin\config_bind_F.php" "config_bind.php"
del "F:\zpanel\bin\bind\bin\config_bind_G.php"
del "F:\zpanel\readme.txt"
del "F:\zpanel\install.bat"
del "F:\zpanel\apache\conf\httpd.conf"
del "F:\zpanel\apache\conf\httpd_D.conf"
del "F:\zpanel\apache\conf\httpd_E.conf"
rename "F:\zpanel\apache\conf\httpd_F.conf" "httpd.conf"
del "F:\zpanel\apache\conf\httpd_G.conf"
del "F:\zpanel\bin\apache\conf\httpd.conf"
del "F:\zpanel\bin\apache\conf\httpd_D.conf"
del "F:\zpanel\bin\apache\conf\httpd_E.conf"
rename "F:\zpanel\bin\apache\conf\httpd_F.conf" "httpd.conf"
del "F:\zpanel\bin\apache\conf\httpd_G.conf"
del "F:\zpanel\bin\bind\bin\bind.reg"
deL "F:\zpanel\bin\bind\bin\bind_D.reg"
del "F:\zpanel\bin\bind\bin\bind_E.reg"
rename "F:\zpanel\bin\bind\bin\bind_F.reg" "bind.reg"
del "F:\zpanel\bin\bind\bin\bind_G.reg"
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo pleasse wait
echo.
cd F:\zpanel\bin
REM set DIR=%CD%
REM set DIR="F:\zpanel\bin"


rem Microsoft Visual Studio 2010 already includes the 2005 and 2008 and of improved
echo Installing Microsoft Visual Studio 2010 Runtime..
F:\zpanel\bin\vcredist\vcredist_2010_x86.exe /q

echo Installing MySQL Service..
F:\zpanel\bin\mysql\bin\mysqld.exe --install
echo Starting MySQL Service..
net start MySQL

echo Installing Spam Assassin Service..
nssm install "Spam Assassin" F:\zpanel\bin\SpamAssassin\spamd.exe
net start "Spam Assassin"

echo Installing Apache HTTPd Service..
F:\zpanel\bin\apache\bin\httpd.exe -k install -n Apache
echo Starting Apache HTTPd service..
net start Apache

echo Installing Filezilla service..
"F:\zpanel\bin\filezilla\Filezilla server.exe" /install auto
echo Starting Filezilla service..
net start "FileZilla Server"


"F:\zpanel\bin\crond\crons.exe" /install
echo Creating crontab file in 'C:\WINDOWS\System32'
COPY "F:\zpanel\bin\crond\temp_crontab.txt" "C:\WINDOWS\System32\crontab"
echo Starting Cron Service
net start "Cron Service"


echo Installing hMailServer...
F:\zpanel\bin\hmailserver\INSTALL\hMailServer-5.4-B1942.exe /DIR="F:\zpanel\bin\hmailserver" /VERYSILENT
echo Starting hMailServer
net stop hMailServer
net start hMailServer

echo Installing GIT for Windows...
F:\zpanel\bin\Git\install\Git-1.5.6.1-preview20080701.exe /DIR="F:\zpanel\bin\Git" /VERYSILENT

echo Installing BIND9.9...
reg import F:\zpanel\bin\bind\bin\bind.reg
net start | find "named"
if ERRORLEVEL 1 sc create named binpath= F:\zpanel\bin\bind\bin\named.exe DisplayName= "named" start= auto
net start | find "named"
if ERRORLEVEL 1 %DIR%\bind\bin\sc.exe create named binpath= F:\zpanel\bin\bind\bin\named.exe DisplayName= "named" start= auto
F:\zpanel\bin\php\php.exe F:\zpanel\bin\bind\bin\config_bind.php 
echo Starting BIND
net stop named
net start named


echo Done installing Services!
echo All done!
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
cd F:\zpanel\bin\zpss
start /wait setenv.exe -m PATH F:\zpanel\bin\7zip;F:\zpanel\bin\Git\bin;F:\zpanel\bin\apache\bin;F:\zpanel\bin\mysql\bin;F:\zpanel\bin\php;F:\zpanel\bin\wget;F:\zpanel\bin\bind\bin;"%PATH%"
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo pleasse wait
echo.
echo.
echo Stoping Apache
net stop apache
echo.
echo Update Apache
cd F:\zpanel\apache\
xcopy *.* F:\zpanel\bin\apache /e /y
rd F:\zpanel\bin\vcredist /S /Q
echo.
echo Starting Apache
net start apache
cd F:\zpanel\
rd F:\zpanel\apache\ /S /Q
rd F:\zpanel\configs\apache\ /S /Q
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd F:\zpanel
rd F:\zpanel\panel\ /S /Q
"F:\zpanel\bin\Git\bin\git.exe" clone git://github.com/andykimpe/zpanelx.git
rename zpanelx panel
rd F:\zpanel\panel\zpanelx-installers\ /S /Q
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
del "F:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core.sql"
del "F:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-D.sql"
del "F:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-E.sql"
rename "F:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-F.sql" "zpanel_core.sql"
del "F:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-G.sql"
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
echo @echo off > F:\zpanel\uninstall.bat
echo rd %temp%\zpanel-uninstallers\ /S /Q >> F:\zpanel\uninstall.bat
echo cd %temp% >> F:\zpanel\uninstall.bat
echo mkdir zpanel-uninstallers >> F:\zpanel\uninstall.bat
echo cd %temp%\zpanel-uninstallers >> F:\zpanel\uninstall.bat
echo wget http://zpx.frabelu.eu/unistall.bat >> F:\zpanel\uninstall.bat
echo unistall.bat >> F:\zpanel\uninstall.bat
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd F:\zpanel\bin\zpss
start /wait setenv.exe -m ZPANELDIR F:
rd "%temp%\zpanelx-installers\" /S /Q
exit

:DISCKG

cls
echo.
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd C:\
mkdir zpanel
cd C:\zpanel
mkdir panel
echo   > C:\zpanel\panel\index.php
cd G:\
mkdir zpanel
cd G:\zpanel
mkdir panel
cd %temp%\zpanelx-installers
xcopy *.* G:\zpanel\ /e /y
cls
echo.
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd "%userprofile%\MENUDM~1\PROGRA~1"
mkdir ZPanel
cd "%userprofile%\MENUDM~1\PROGRA~1\ZPanel"
mkdir Management
mkdir Support
mkdir Tasks
copy "G:\zpanel\bin\zpss\racourcis\d\ZPanel\*.*" "%userprofile%\MENUDM~1\PROGRA~1\ZPanel\"
copy "G:\zpanel\bin\zpss\racourcis\d\ZPanel\Management\*.*" "%userprofile%\MENUDM~1\PROGRA~1\ZPanel\Management\"
copy "G:\zpanel\bin\zpss\racourcis\d\ZPanel\Support\*.*" "%userprofile%\MENUDM~1\PROGRA~1\ZPanel\Support\"
copy "G:\zpanel\bin\zpss\racourcis\d\ZPanel\Tasks\*.*" "%userprofile%\MENUDM~1\PROGRA~1\ZPanel\Tasks\"
cls
echo.
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
rd G:\zpanel\bin\zpss\racourcis\ /S /Q
del "G:\zpanel\bin\zpss\enviroment_configure.php"
del "G:\zpanel\bin\zpss\enviroment_configure_D.php"
del "G:\zpanel\bin\zpss\enviroment_configure_E.php"
del "G:\zpanel\bin\zpss\enviroment_configure_F.php"
rename "G:\zpanel\bin\zpss\enviroment_configure_G.php" "enviroment_configure.php"
del "G:\zpanel\bin\bind\bin\config_bind.php"
del "G:\zpanel\bin\bind\bin\config_bind_D.php"
del "G:\zpanel\bin\bind\bin\config_bind_E.php"
del "G:\zpanel\bin\bind\bin\config_bind_F.php"
rename "G:\zpanel\bin\bind\bin\config_bind_G.php" "config_bind.php"
del "G:\zpanel\readme.txt"
del "G:\zpanel\install.bat"
del "G:\zpanel\apache\conf\httpd.conf"
del "G:\zpanel\apache\conf\httpd_D.conf"
del "G:\zpanel\apache\conf\httpd_E.conf"
del "G:\zpanel\apache\conf\httpd_F.conf"
rename "G:\zpanel\apache\conf\httpd_G.conf" "httpd.conf"
del "G:\zpanel\bin\apache\conf\httpd.conf"
del "G:\zpanel\bin\apache\conf\httpd_D.conf"
del "G:\zpanel\bin\apache\conf\httpd_E.conf"
del "G:\zpanel\bin\apache\conf\httpd_F.conf"
rename "G:\zpanel\bin\apache\conf\httpd_G.conf" "httpd.conf"
del "G:\zpanel\bin\bind\bin\bind.reg"
deL "G:\zpanel\bin\bind\bin\bind_D.reg"
del "G:\zpanel\bin\bind\bin\bind_E.reg"
del "G:\zpanel\bin\bind\bin\bind_F.reg"
rename "F:\zpanel\bin\bind\bin\bind_G.reg" "bind.reg"
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo pleasse wait
echo.
cd G:\zpanel\bin
REM set DIR=%CD%
REM set DIR="G:\zpanel\bin"


rem Microsoft Visual Studio 2010 already includes the 2005 and 2008 and of improved
echo Installing Microsoft Visual Studio 2010 Runtime..
G:\zpanel\bin\vcredist\vcredist_2010_x86.exe /q

echo Installing MySQL Service..
G:\zpanel\bin\mysql\bin\mysqld.exe --install
echo Starting MySQL Service..
net start MySQL

echo Installing Spam Assassin Service..
nssm install "Spam Assassin" G:\zpanel\bin\SpamAssassin\spamd.exe
net start "Spam Assassin"

echo Installing Apache HTTPd Service..
G:\zpanel\bin\apache\bin\httpd.exe -k install -n Apache
echo Starting Apache HTTPd service..
net start Apache

echo Installing Filezilla service..
"G:\zpanel\bin\filezilla\Filezilla server.exe" /install auto
echo Starting Filezilla service..
net start "FileZilla Server"


"G:\zpanel\bin\crond\crons.exe" /install
echo Creating crontab file in 'C:\WINDOWS\System32'
COPY "G:\zpanel\bin\crond\temp_crontab.txt" "C:\WINDOWS\System32\crontab"
echo Starting Cron Service
net start "Cron Service"


echo Installing hMailServer...
G:\zpanel\bin\hmailserver\INSTALL\hMailServer-5.4-B1942.exe /DIR="G:\zpanel\bin\hmailserver" /VERYSILENT
echo Starting hMailServer
net stop hMailServer
net start hMailServer

echo Installing GIT for Windows...
G:\zpanel\bin\Git\install\Git-1.5.6.1-preview20080701.exe /DIR="G:\zpanel\bin\Git" /VERYSILENT

echo Installing BIND9.9...
reg import G:\zpanel\bin\bind\bin\bind.reg
net start | find "named"
if ERRORLEVEL 1 sc create named binpath= G:\zpanel\bin\bind\bin\named.exe DisplayName= "named" start= auto
net start | find "named"
if ERRORLEVEL 1 %DIR%\bind\bin\sc.exe create named binpath= G:\zpanel\bin\bind\bin\named.exe DisplayName= "named" start= auto
G:\zpanel\bin\php\php.exe G:\zpanel\bin\bind\bin\config_bind.php 
echo Starting BIND
net stop named
net start named


echo Done installing Services!
echo All done!
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
cd G:\zpanel\bin\zpss
start /wait setenv.exe -m PATH G:\zpanel\bin\7zip;G:\zpanel\bin\Git\bin;G:\zpanel\bin\apache\bin;G:\zpanel\bin\mysql\bin;G:\zpanel\bin\php;G:\zpanel\bin\wget;G:\zpanel\bin\bind\bin;"%PATH%"
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo pleasse wait
echo.
echo.
echo Stoping Apache
net stop apache
echo.
echo Update Apache
cd G:\zpanel\apache\
xcopy *.* G:\zpanel\bin\apache /e /y
rd G:\zpanel\bin\vcredist /S /Q
echo.
echo Starting Apache
net start apache
cd G:\zpanel\
rd G:\zpanel\apache\ /S /Q
rd G:\zpanel\configs\apache\ /S /Q
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd G:\zpanel
rd G:\zpanel\panel\ /S /Q
"G:\zpanel\bin\Git\bin\git.exe" clone git://github.com/andykimpe/zpanelx.git
rename zpanelx panel
rd G:\zpanel\panel\zpanelx-installers\ /S /Q
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
del "G:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core.sql"
del "G:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-D.sql"
del "G:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-E.sql"
del "G:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-F.sql"
rename "G:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-G.sql" "zpanel_core.sql"
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
echo @echo off > G:\zpanel\uninstall.bat
echo rd %temp%\zpanel-uninstallers\ /S /Q >> G:\zpanel\uninstall.bat
echo cd %temp% >> G:\zpanel\uninstall.bat
echo mkdir zpanel-uninstallers >> G:\zpanel\uninstall.bat
echo cd %temp%\zpanel-uninstallers >> G:\zpanel\uninstall.bat
echo wget http://zpx.frabelu.eu/unistall.bat >> G:\zpanel\uninstall.bat
echo unistall.bat >> G:\zpanel\uninstall.bat
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd G:\zpanel\bin\zpss
start /wait setenv.exe -m ZPANELDIR G:
rd "%temp%\zpanelx-installers\" /S /Q
exit