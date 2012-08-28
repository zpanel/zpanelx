@echo off
echo.
echo Stoping Apache
net stop apache
echo.
echo Update Apache
cd D:\zpanel\apache\
xcopy *.* c:\zpanel\bin\apache /s /y
rd D:\zpanel\bin\vcredist /S /Q
rd D:\zpanel\bin\hmailserver\INSTALL /S /Q
echo.
echo Starting Apache
net start apache
"D:\zpanel\delete.bat"