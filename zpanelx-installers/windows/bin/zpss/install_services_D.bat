@echo off
REM set DIR=%CD%
REM set DIR="D:\zpanel\bin"

echo Installing Microsoft Visual Studio 2005 Runtime..
D:\zpanel\bin\vcredist\vcredist_2005_x86.exe /q

echo Installing Microsoft Visual Studio 2008 Runtime..
D:\zpanel\bin\vcredist\vcredist_2008_x86.exe /q

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