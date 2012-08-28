@echo off
echo.
echo Stoping Apache
net stop apache
echo.
echo Update Apache
cd E:\zpanel\apache\
xcopy *.* c:\zpanel\bin\apache /s /y
rd E:\zpanel\bin\vcredist /S /Q
rd E:\zpanel\bin\hmailserver\INSTALL /S /Q
echo.
echo Starting Apache
net start apache
"E:\zpanel\delete.bat"