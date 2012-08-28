@echo off
echo.
echo Stoping Apache
net stop apache
echo.
echo Update Apache
cd G:\zpanel\apache\
xcopy *.* c:\zpanel\bin\apache /s /y
rd G:\zpanel\bin\vcredist /S /Q
rd G:\zpanel\bin\hmailserver\INSTALL /S /Q
echo.
echo Starting Apache
net start apache
"G:\zpanel\delete.bat"