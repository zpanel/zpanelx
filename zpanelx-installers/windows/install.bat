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
copy "%temp%\zpanelx-installers\nssm.exe" "%windir%"
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
cd %temp%\zpanelx-installers
xcopy *.* C:\zpanel /s /y
cls
echo.
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd %userprofile%\MENUDM~1\PROGRA~1
mkdir ZPanel
cd C:\zpanel\bin\zpss\racourcis\c\ZPanel
xcopy *.* %userprofile%\MENUDM~1\PROGRA~1\ZPanel /s /y
cls
echo.
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
rd C:\zpanel\bin\zpss\racourcis /S /Q
del "C:\zpanel\bin\zpss\configurex_D.bat"
del "C:\zpanel\bin\zpss\configurex_E.bat"
del "C:\zpanel\bin\zpss\configurex_F.bat"
del "C:\zpanel\bin\zpss\configurex_G.bat"
del "C:\zpanel\bin\zpss\end_D.bat"
del "C:\zpanel\bin\zpss\end_E.bat"
del "C:\zpanel\bin\zpss\end_F.bat"
del "C:\zpanel\bin\zpss\end_G.bat"
del "C:\zpanel\bin\zpss\enviroment_configure_D.php"
del "C:\zpanel\bin\zpss\enviroment_configure_E.php"
del "C:\zpanel\bin\zpss\enviroment_configure_F.php"
del "C:\zpanel\bin\zpss\enviroment_configure_G.php"
del "C:\zpanel\bin\zpss\install_services_D.bat"
del "C:\zpanel\bin\zpss\install_services_E.bat"
del "C:\zpanel\bin\zpss\install_services_F.bat"
del "C:\zpanel\bin\zpss\install_services_G.bat"
del "C:\zpanel\bin\zpss\patch-apache-2.4_D.bat"
del "C:\zpanel\bin\zpss\patch-apache-2.4_E.bat"
del "C:\zpanel\bin\zpss\patch-apache-2.4_F.bat"
del "C:\zpanel\bin\zpss\patch-apache-2.4_G.bat"
del "C:\zpanel\bin\zpss\register_paths_D.bat"
del "C:\zpanel\bin\zpss\register_paths_E.bat"
del "C:\zpanel\bin\zpss\register_paths_F.bat"
del "C:\zpanel\bin\zpss\register_paths_G.bat"
del "C:\zpanel\bin\bind\bin\config_bind_D.php"
del "C:\zpanel\bin\bind\bin\config_bind_E.php"
del "C:\zpanel\bin\bind\bin\config_bind_F.php"
del "C:\zpanel\bin\bind\bin\config_bind_G.php"
del "C:\zpanel\readme.txt
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo pleasse wait
echo.
C:\zpanel\bin\zpss\install_services.bat
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo pleasse wait
echo.
START /WAIT C:\zpanel\bin\zpss\register_paths.bat
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo pleasse wait
echo.
C:\zpanel\bin\zpss\patch-apache-2.4.bat
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo are you on windows server?
echo.
goto sauterlemessagec2

:noselectc2
echo You must make a choice
echo.
:sauterlemessagec2

echo - 1 - yes
echo - 2 - no
echo.

set /p CHOICE2=Make your choice (1 ous 2):

if /i %CHOICE2%==1 goto DISCKCY
if /i %CHOICE2%==2 goto DISCKCN
goto noselectc2

:DISCKCY
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
START /WAIT C:\zpanel\bin\zpss\Patch-Server-Version.bat
echo "%windir%\System32\reg.exe" ADD HKLM\SOFTWARE\Microsoft\Windows\CurrentVersion\Policies\System /v EnableLUA /t REG_DWORD /d 1 /f > %WINDIR%\uninstall-zpanel-patch-win-server.bat
goto DISCKC2
:DISCKCN
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
del "C:\zpanel\bin\zpss\Patch-Server-Version.bat"
goto DISCKC2
:DISCKC2
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd C:\zpanel
git clone git://github.com/andykimpe/zpanelx.git
rename zpanelx panel
rd C:\zpanel\panel\zpanelx-installers /S /Q
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
del "C:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-D.bat"
del "C:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-E.bat"
del "C:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-F.bat"
del "C:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-G.bat"
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
C:\zpanel\bin\zpss\configurex.bat
C:\zpanel\bin\zpss\end.bat
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
echo @echo off > C:\zpanel\uninstall.bat
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
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo Enjoy
echo.
pause
rd C:\zpanel\panel\zpanelx-installers /S /Q
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
cd D:\
mkdir zpanel
cd %temp%\zpanelx-installers
xcopy *.* D:\zpanel /s /y
cls
echo.
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd %userprofile%\MENUDM~1\PROGRA~1
mkdir ZPanel
cd D:\zpanel\bin\zpss\racourcis\d\ZPanel
xcopy *.* %userprofile%\MENUDM~1\PROGRA~1\ZPanel /s /y
cls
echo.
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
rd D:\zpanel\bin\zpss\racourcis /S /Q
del "D:\zpanel\bin\zpss\configurex.bat"
rename "D:\zpanel\bin\zpss\configurex_D.bat" "configurex.bat"
del "D:\zpanel\bin\zpss\configurex_E.bat"
del "D:\zpanel\bin\zpss\configurex_R.bat"
del "D:\zpanel\bin\zpss\configurex_G.bat"
del "D:\zpanel\bin\zpss\end.bat"
rename "D:\zpanel\bin\zpss\end_D.bat" "end.bat"
del "D:\zpanel\bin\zpss\end_E.bat"
del "D:\zpanel\bin\zpss\end_F.bat"
del "D:\zpanel\bin\zpss\end_G.bat"
del "D:\zpanel\bin\zpss\enviroment_configure.php"
rename "D:\zpanel\bin\zpss\enviroment_configure_D.php" "enviroment_configure.php"
del "D:\zpanel\bin\zpss\enviroment_configure_E.php"
del "D:\zpanel\bin\zpss\enviroment_configure_F.php"
del "D:\zpanel\bin\zpss\enviroment_configure_G.php"
del "D:\zpanel\bin\zpss\install_services.bat"
rename "D:\zpanel\bin\zpss\install_services_D.bat" "install_services.bat"
del "D:\zpanel\bin\zpss\install_services_E.bat"
del "D:\zpanel\bin\zpss\install_services_F.bat"
del "D:\zpanel\bin\zpss\install_services_G.bat"
del "D:\zpanel\bin\zpss\patch-apache-2.4.bat"
rename "D:\zpanel\bin\zpss\patch-apache-2.4_D.bat" "patch-apache-2.4.bat"
del "D:\zpanel\bin\zpss\patch-apache-2.4_E.bat"
del "D:\zpanel\bin\zpss\patch-apache-2.4_F.bat"
del "D:\zpanel\bin\zpss\patch-apache-2.4_G.bat"
del "D:\zpanel\bin\zpss\register_paths.bat"
rename "D:\zpanel\bin\zpss\register_paths_D.bat" "register_paths.bat"
del "D:\zpanel\bin\zpss\register_paths_E.bat"
del "D:\zpanel\bin\zpss\register_paths_F.bat"
del "D:\zpanel\bin\zpss\register_paths_G.bat"
del "D:\zpanel\bin\bind\bin\config_bind.php"
rename "D:\zpanel\bin\bind\bin\config_bind_D.php" "config_bind.php"
del "D:\zpanel\bin\bind\bin\config_bind_E.php"
del "D:\zpanel\bin\bind\bin\config_bind_F.php"
del "D:\zpanel\bin\bind\bin\config_bind_G.php"
del "D:\zpanel\readme.txt
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo pleasse wait
echo.
D:\zpanel\bin\zpss\install_services.bat
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo pleasse wait
echo.
START /WAIT D:\zpanel\bin\zpss\register_paths.bat
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo pleasse wait
echo.
D:\zpanel\bin\zpss\patch-apache-2.4.bat
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo are you on windows server?
echo.
goto sauterlemessagec3

:noselectc3
echo You must make a choice
echo.
:sauterlemessagec3

echo - 1 - yes
echo - 2 - no
echo.

set /p CHOICE3=Make your choice (1 ous 2):

if /i %CHOICE3%==1 goto DISKDY
if /i %CHOICE3%==2 goto DISCKDN
goto noselectc3

:DISCKDY
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
START /WAIT D:\zpanel\bin\zpss\Patch-Server-Version.bat
echo "%windir%\System32\reg.exe" ADD HKLM\SOFTWARE\Microsoft\Windows\CurrentVersion\Policies\System /v EnableLUA /t REG_DWORD /d 1 /f > %WINDIR%\uninstall-zpanel-patch-win-server.bat
goto DISCKD2
:DISCKDN
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
del "D:\zpanel\bin\zpss\Patch-Server-Version.bat"
goto DISCKD2
:DISCKD2
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd D:\zpanel
git clone git://github.com/andykimpe/zpanelx.git
rename zpanelx panel
rd D:\zpanel\panel\zpanelx-installers /S /Q
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
del "D:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core.bat"
rename "D:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-D.bat" "zpanel_core.bat"
del "D:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-E.bat"
del "D:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-F.bat"
del "D:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-G.bat"
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
D:\zpanel\bin\zpss\configurex.bat
D:\zpanel\bin\zpss\end.bat
cls
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
echo @echo off > D:\zpanel\uninstall.bat
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
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo Enjoy
echo.
pause
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
cd E:\
mkdir zpanel
cd %temp%\zpanelx-installers
xcopy *.* E:\zpanel /s /y
cls
echo.
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd %userprofile%\MENUDM~1\PROGRA~1
mkdir ZPanel
cd E:\zpanel\bin\zpss\racourcis\e\ZPanel
xcopy *.* %userprofile%\MENUDM~1\PROGRA~1\ZPanel /s /y
cls
echo.
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
rd E:\zpanel\bin\zpss\racourcis /S /Q
del "E:\zpanel\bin\zpss\configurex.bat"
del "E:\zpanel\bin\zpss\configurex_D.bat"
rename "E:\zpanel\bin\zpss\configurex_E.bat" "configurex.bat"
del "E:\zpanel\bin\zpss\configurex_F.bat"
del "E:\zpanel\bin\zpss\configurex_G.bat"
del "E:\zpanel\bin\zpss\end.bat"
del "E:\zpanel\bin\zpss\end_D.bat"
rename "E:\zpanel\bin\zpss\end_E.bat" "end.bat"
del "E:\zpanel\bin\zpss\end_F.bat"
del "E:\zpanel\bin\zpss\end_G.bat"
del "E:\zpanel\bin\zpss\enviroment_configure.php"
del "E:\zpanel\bin\zpss\enviroment_configure_D.php"
rename "E:\zpanel\bin\zpss\enviroment_configure_E.php" "enviroment_configure.php"
del "E:\zpanel\bin\zpss\enviroment_configure_F.php"
del "E:\zpanel\bin\zpss\enviroment_configure_G.php"
del "E:\zpanel\bin\zpss\install_services.bat"
del "E:\zpanel\bin\zpss\install_services_D.bat"
rename "E:\zpanel\bin\zpss\install_services_E.bat" "install_services.bat"
del "E:\zpanel\bin\zpss\install_services_F.bat"
del "E:\zpanel\bin\zpss\install_services_G.bat"
del "E:\zpanel\bin\zpss\patch-apache-2.4.bat"
del "E:\zpanel\bin\zpss\patch-apache-2.4_D.bat"
rename "E:\zpanel\bin\zpss\patch-apache-2.4_E.bat" "patch-apache-2.4.bat"
del "E:\zpanel\bin\zpss\patch-apache-2.4_F.bat"
del "E:\zpanel\bin\zpss\patch-apache-2.4_G.bat"
del "E:\zpanel\bin\zpss\register_paths.bat"
del "E:\zpanel\bin\zpss\register_paths_D.bat"
rename "E:\zpanel\bin\zpss\register_paths_E.bat" "register_paths.bat"
del "E:\zpanel\bin\zpss\register_paths_F.bat"
del "E:\zpanel\bin\zpss\register_paths_G.bat"
del "E:\zpanel\bin\bind\bin\config_bind.php"
del "E:\zpanel\bin\bind\bin\config_bind_D.php"
rename "E:\zpanel\bin\bind\bin\config_bind_E.php" "config_bind.php"
del "E:\zpanel\bin\bind\bin\config_bind_F.php"
del "E:\zpanel\bin\bind\bin\config_bind_G.php"
del "E:\zpanel\readme.txt
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo pleasse wait
echo.
E:\zpanel\bin\zpss\install_services.bat
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo pleasse wait
echo.
START /WAIT E:\zpanel\bin\zpss\register_paths.bat
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo pleasse wait
echo.
E:\zpanel\bin\zpss\patch-apache-2.4.bat
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo are you on windows server?
echo.
goto sauterlemessagec4

:noselectc4
echo You must make a choice
echo.
:sauterlemessagec4

echo - 1 - yes
echo - 2 - no
echo.

set /p CHOICE4=Make your choice (1 ous 2):

if /i %CHOICE4%==1 goto DISKEY
if /i %CHOICE4%==2 goto DISCKEN
goto noselectc4

:DISCKEY
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
START /WAIT E:\zpanel\bin\zpss\Patch-Server-Version.bat
echo "%windir%\System32\reg.exe" ADD HKLM\SOFTWARE\Microsoft\Windows\CurrentVersion\Policies\System /v EnableLUA /t REG_DWORD /d 1 /f > %WINDIR%\uninstall-zpanel-patch-win-server.bat
goto DISCKE2
:DISCKEN
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
del "E:\zpanel\bin\zpss\Patch-Server-Version.bat"
goto DISCKE2
:DISCKE2
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd E:\zpanel
git clone git://github.com/andykimpe/zpanelx.git
rename zpanelx panel
rd E:\zpanel\panel\zpanelx-installers /S /Q
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
del "E:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core.bat"
del "E:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-D.bat"
rename "E:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-E.bat" "zpanel_core.bat"
del "E:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-F.bat"
del "E:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-G.bat"
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
E:\zpanel\bin\zpss\configurex.bat
E:\zpanel\bin\zpss\end.bat
cls
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
echo @echo off > E:\zpanel\uninstall.bat
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
cd F:\zpanel\bin\zpss
start /wait setenv.exe -m ZPANELDIR E:
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo Enjoy
echo.
pause
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
cd F:\
mkdir zpanel
cd %temp%\zpanelx-installers
xcopy *.* F:\zpanel /s /y
cls
echo.
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd %userprofile%\MENUDM~1\PROGRA~1
mkdir ZPanel
cd F:\zpanel\bin\zpss\racourcis\f\ZPanel
xcopy *.* %userprofile%\MENUDM~1\PROGRA~1\ZPanel /s /y
cls
echo.
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
rd F:\zpanel\bin\zpss\racourcis /S /Q
del "F:\zpanel\bin\zpss\configurex.bat"
del "F:\zpanel\bin\zpss\configurex_D.bat"
del "F:\zpanel\bin\zpss\configurex_E.bat"
rename "F:\zpanel\bin\zpss\configurex_F.bat" "configurex.bat"
del "F:\zpanel\bin\zpss\configurex_G.bat"
del "F:\zpanel\bin\zpss\configurex_D.bat"
del "F:\zpanel\bin\zpss\configurex_D.bat"
del "F:\zpanel\bin\zpss\end.bat"
del "F:\zpanel\bin\zpss\end_D.bat"
del "F:\zpanel\bin\zpss\end_E.bat"
rename "F:\zpanel\bin\zpss\end_F.bat" "end.bat"
del "F:\zpanel\bin\zpss\end_G.bat"
del "F:\zpanel\bin\zpss\enviroment_configure.php"
del "F:\zpanel\bin\zpss\enviroment_configure_D.php"
del "F:\zpanel\bin\zpss\enviroment_configure_E.php"
rename "F:\zpanel\bin\zpss\enviroment_configure_F.php" "enviroment_configure.php"
del "F:\zpanel\bin\zpss\enviroment_configure_G.php"
del "F:\zpanel\bin\zpss\install_services.bat"
del "F:\zpanel\bin\zpss\install_services_D.bat"
del "F:\zpanel\bin\zpss\install_services_E.bat"
rename "F:\zpanel\bin\zpss\install_services_F.bat" "install_services.bat"
del "F:\zpanel\bin\zpss\install_services_G.bat"
del "F:\zpanel\bin\zpss\patch-apache-2.4.bat"
del "F:\zpanel\bin\zpss\patch-apache-2.4_D.bat"
del "F:\zpanel\bin\zpss\patch-apache-2.4_E.bat"
rename "F:\zpanel\bin\zpss\patch-apache-2.4_F.bat" "patch-apache-2.4.bat"
del "F:\zpanel\bin\zpss\patch-apache-2.4_G.bat"
del "F:\zpanel\bin\zpss\register_paths.bat"
del "F:\zpanel\bin\zpss\register_paths_D.bat"
del "F:\zpanel\bin\zpss\register_paths_E.bat"
rename "F:\zpanel\bin\zpss\register_paths_F.bat" "register_paths.bat"
del "F:\zpanel\bin\zpss\register_paths_G.bat"
del "F:\zpanel\bin\bind\bin\config_bind.php"
del "F:\zpanel\bin\bind\bin\config_bind_D.php"
del "F:\zpanel\bin\bind\bin\config_bind_E.php"
rename "F:\zpanel\bin\bind\bin\config_bind_F.php" "config_bind.php"
del "F:\zpanel\bin\bind\bin\config_bind_G.php"
del "F:\zpanel\readme.txt
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo pleasse wait
echo.
F:\zpanel\bin\zpss\install_services.bat
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo pleasse wait
echo.
START /WAIT F:\zpanel\bin\zpss\register_paths.bat
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo pleasse wait
echo.
F:\zpanel\bin\zpss\patch-apache-2.4.bat
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo are you on windows server?
echo.
goto sauterlemessagec5

:noselectc5
echo You must make a choice
echo.
:sauterlemessagec5

echo - 1 - yes
echo - 2 - no
echo.

set /p CHOICE5=Make your choice (1 ous 2):

if /i %CHOICE5%==1 goto DISKFY
if /i %CHOICE5%==2 goto DISCKFN
goto noselectc5

:DISCKFY
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
START /WAIT E:\zpanel\bin\zpss\Patch-Server-Version.bat
echo "%windir%\System32\reg.exe" ADD HKLM\SOFTWARE\Microsoft\Windows\CurrentVersion\Policies\System /v EnableLUA /t REG_DWORD /d 1 /f > %WINDIR%\uninstall-zpanel-patch-win-server.bat
goto DISCKF2
:DISCKFN
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
del "F:\zpanel\bin\zpss\Patch-Server-Version.bat"
goto DISCKF2
:DISCKF2
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd F:\zpanel
git clone git://github.com/andykimpe/zpanelx.git
rename zpanelx panel
rd F:\zpanel\panel\zpanelx-installers /S /Q
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
del "F:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core.bat"
del "F:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-D.bat"
del "F:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-E.bat"
rename "F:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-F.bat" "zpanel_core.bat"
del "F:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-G.bat"
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
F:\zpanel\bin\zpss\configurex.bat
F:\zpanel\bin\zpss\end.bat
cls
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
echo @echo off > F:\zpanel\uninstall.bat
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
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo Enjoy
echo.
pause
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
cd G:\
mkdir zpanel
cd %temp%\zpanelx-installers
xcopy *.* G:\zpanel /s /y
cls
echo.
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd %userprofile%\MENUDM~1\PROGRA~1
mkdir ZPanel
cd G:\zpanel\bin\zpss\racourcis\g\ZPanel
xcopy *.* %userprofile%\MENUDM~1\PROGRA~1\ZPanel /s /y
cls
echo.
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
rd G:\zpanel\bin\zpss\racourcis /S /Q
del "G:\zpanel\bin\zpss\configurex.bat"
del "G:\zpanel\bin\zpss\configurex_D.bat"
del "G:\zpanel\bin\zpss\configurex_E.bat"
deL "G:\zpanel\bin\zpss\configurex_F.bat"
rename "G:\zpanel\bin\zpss\configurex_G.bat" "configurex.bat"
del "G:\zpanel\bin\zpss\configurex_D.bat"
del "G:\zpanel\bin\zpss\configurex_D.bat"
del "G:\zpanel\bin\zpss\end.bat"
del "G:\zpanel\bin\zpss\end_D.bat"
del "G:\zpanel\bin\zpss\end_E.bat"
del "G:\zpanel\bin\zpss\end_F.bat"
rename "G:\zpanel\bin\zpss\end_G.bat" "end.bat"
del "G:\zpanel\bin\zpss\enviroment_configure.php"
del "G:\zpanel\bin\zpss\enviroment_configure_D.php"
del "G:\zpanel\bin\zpss\enviroment_configure_E.php"
del "G:\zpanel\bin\zpss\enviroment_configure_F.php"
rename "G:\zpanel\bin\zpss\enviroment_configure_G.php" "enviroment_configure.php"
del "G:\zpanel\bin\zpss\install_services.bat"
del "G:\zpanel\bin\zpss\install_services_D.bat"
del "G:\zpanel\bin\zpss\install_services_E.bat"
del "G:\zpanel\bin\zpss\install_services_F.bat"
rename "G:\zpanel\bin\zpss\install_services_G.bat" "install_services.bat"
del "G:\zpanel\bin\zpss\patch-apache-2.4.bat"
del "G:\zpanel\bin\zpss\patch-apache-2.4_D.bat"
del "G:\zpanel\bin\zpss\patch-apache-2.4_E.bat"
del "G:\zpanel\bin\zpss\patch-apache-2.4_F.bat"
rename "G:\zpanel\bin\zpss\patch-apache-2.4_G.bat" "patch-apache-2.4.bat"
del "G:\zpanel\bin\zpss\register_paths.bat"
del "G:\zpanel\bin\zpss\register_paths_D.bat"
del "G:\zpanel\bin\zpss\register_paths_E.bat"
del "G:\zpanel\bin\zpss\register_paths_F.bat"
rename "G:\zpanel\bin\zpss\register_paths_G.bat" "register_paths.bat"
del "G:\zpanel\bin\bind\bin\config_bind.php"
del "G:\zpanel\bin\bind\bin\config_bind_D.php"
del "G:\zpanel\bin\bind\bin\config_bind_E.php"
del "G:\zpanel\bin\bind\bin\config_bind_F.php"
rename "G:\zpanel\bin\bind\bin\config_bind_G.php" "config_bind.php"
del "G:\zpanel\readme.txt
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo pleasse wait
echo.
G:\zpanel\bin\zpss\install_services.bat
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo pleasse wait
echo.
START /WAIT G:\zpanel\bin\zpss\register_paths.bat
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo pleasse wait
echo.
G:\zpanel\bin\zpss\patch-apache-2.4.bat
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo are you on windows server?
echo.
goto sauterlemessagec6

:noselectc6
echo You must make a choice
echo.
:sauterlemessagec6

echo - 1 - yes
echo - 2 - no
echo.

set /p CHOICE6=Make your choice (1 ous 2):

if /i %CHOICE6%==1 goto DISKGY
if /i %CHOICE6%==2 goto DISCKGN
goto noselectc6

:DISCKGY
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
START /WAIT G:\zpanel\bin\zpss\Patch-Server-Version.bat
echo "%windir%\System32\reg.exe" ADD HKLM\SOFTWARE\Microsoft\Windows\CurrentVersion\Policies\System /v EnableLUA /t REG_DWORD /d 1 /f > %WINDIR%\uninstall-zpanel-patch-win-server.bat
goto DISCKG2
:DISCKGN
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
del "G:\zpanel\bin\zpss\Patch-Server-Version.bat"
goto DISCKG2
:DISCKF2
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
cd G:\zpanel
git clone git://github.com/andykimpe/zpanelx.git
rename zpanelx panel
rd G:\zpanel\panel\zpanelx-installers /S /Q
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
del "G:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core.bat"
del "G:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-D.bat"
del "G:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-E.bat"
del "G:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-F.bat"
rename "G:\zpanel\panel\etc\build\config_packs\ms_windows\zpanel_core-G.bat" "zpanel_core.bat"
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo please wait
echo.
G:\zpanel\bin\zpss\configurex.bat
G:\zpanel\bin\zpss\end.bat
cls
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
echo @echo off > G:\zpanel\uninstall.bat
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
cls
echo #######################################
echo #       ZpanelX v10.0.0a              #
echo #   install script by andykimpe       #
echo #######################################
echo.
echo Enjoy
echo.
pause
exit