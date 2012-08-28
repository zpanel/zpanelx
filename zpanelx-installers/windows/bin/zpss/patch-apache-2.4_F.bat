@echo off
echo.
echo Stoping Apache
net stop apache
echo.
echo Update Apache
cd F:\zpanel\apache\
xcopy *.* c:\zpanel\bin\apache /s /y
rd F:\zpanel\bin\vcredist /S /Q
rd F:\zpanel\bin\hmailserver\INSTALL /S /Q
echo.
echo Starting Apache
net start apache
"F:\zpanel\delete.bat"