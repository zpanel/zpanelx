@echo off
echo.
echo installation complete.
echo.
echo All the team thanks you install zpanel
echo.
echo if you encounter a problem visit http://planet.zpanelcp.com/
echo.
echo or the forums http://forums.zpanelcp.com/forumdisplay.php?61-ZPanel-X
echo.
pause
rd %ZPANELDIR%\zpanel\panel\modules\webmail\install /S /Q
rd %ZPANELDIR%\zpanel\bin\Git\install /S /Q
rd %ZPANELDIR%\zpanel\bin\hmailserver\INSTALL /S /Q
rd "%Temp%\zpanelx-installers\" /S /Q
DEL %ZPANELDIR%\zpanel\bin\zpss\*.bat /Q
exit