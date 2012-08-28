@echo off
echo.
echo Stoping Apache
net stop apache
echo.
echo Update Apache
cd c:\zpanel\apache\
xcopy *.* c:\zpanel\bin\apache /s /y
rd C:\zpanel\bin\vcredist /S /Q
rd C:\zpanel\bin\hmailserver\INSTALL /S /Q
echo.
echo Starting Apache
net start apache
"c:\zpanel\delete.bat"