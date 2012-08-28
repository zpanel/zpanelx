 SpamAssassin for Win32 v3.2.3.5
=================================
Includes DCC v1.3.61 and Vipul's Razor v2.84


SpamAssassin (http://spamassassin.apache.org/) is a free and powerful
spam filter. This build includes support for online spam databases
DCC (http://www.rhyolite.com/anti-spam/dcc/) and Razor/Cloudmark
(http://razor.sf.net/).


INSTALLATION
============


Unpack the archive into any folder, keeping the file structure.

If you are upgrading from the previous version, it is strongly recommended
that you copy the file etc\dcc\map to "%USERPROFILE%\.spamassassin\map" to
avoid the risk of the incompatibility of the formats.



USAGE
=====


Full documentation is in "doc" subfolder. Here is a brief summary.

spamassassin.exe takes incoming mail from STDIN and returns the
filtered message to STDOUT. Thus, you could run it as follows:

  spamassassin.exe < file-input > file-output

To capture debugging messages, you can run it like this:

  spamassassin.exe -D < file-input > file-output 2> file-debugging

Note that spamd.exe and spamc.exe are native Windows applications, so
they do not open a console window unless you redirect STDIN/STDOUT/STDERR.

If you are planning to run the filter on a mail server, it is better
to start spamd.exe daemon and then check individual mail messages with
spamc.exe (this method reduces CPU load):

  spamd.exe --syslog="file-log"

  spamc.exe < file-input > file-output

You can also run spamcc.exe instead of spamc.exe. The only difference
between them is that spamcc.exe is a console application and command
prompt will wait until it finishes, while spamc.exe is a native
Windows application that does not open a console window. You can easily
capture the exit code of spamcc.exe, while for spamc.exe it is tricky.

Note that spamd.exe permanently resides in memory, therefore, you
can shut it down only by ending 'spamd' process in Windows Task Manager.

Also, you have to specify the log file or to redirect STDERR, otherwise
spamd will not run. If you do not need the log, redirect STDERR to nul:

  spamd.exe 2> nul:

Finally, remember that some of the online blacklists and databases that
are free for personal use may request for paid subscription if your
usage of their resources becomes high.


ADVANCED CONFIGURATION
======================


Again, read the documentation in the "doc" subfolder. For basic use
you need no additional configuration, just run the programs.

One of the most useful switches is -m, which sets the maximum number of messages
that can be scanned simultaneously. The default value is five, however, if
each message takes 30 seconds and you get over 10 messages per second, then
obviously you need to increase the number, for example, -m 10.

A few things that can be very useful:
(1) configure "trusted" networks for SpamAssassin;
(2) configure whitelists for SpamAssassin, DCC and Razor;
(3) classify some of *your* mail as spam and not spam (over 200 messages
    in each group) and feed them into sa-learn, which you can download
    from my website as well.

Locations of configuration files:

SpamAssassin (global configuration): etc\spamassassin
SpamAssassin (user files): %USERPROFILE%\.spamassassin
DCC: %USERPROFILE%\.spamassassin
Razor: %USERPROFILE%\.razor

Here USERPROFILE environment variable is for the user who launches
spamassassin or spamd.

If you are running spamd/spamc, they ignore SpamAssassin user
configuration file %USERPROFILE%\.spamassassin\user_prefs.


RUNNING spamd AS A SERVICE
==========================


In order for Windows to execute spamd automatically on startup, it
should be configured to run as a Windows service. You can use the tools
like NTRunner (http://www.winsite.com/bin/Info?500000023101) or
Microsoft's instsrv.exe / srvany.exe, see examples at
http://www.electrasoft.com/srvany/srvany.htm and at
http://www.henry.it/xmail/xspamc/spamd_windows_service.htm


KNOWN ISSUES
============


Experimental DKIM and DomainKeys plugins occasionally consume too much time,
which causes spamc to time out and the scan to cancel. Therefore, these plugins
are disabled (commented out) by default. To enable them, edit the files v310.pre
and v312.pre located in etc\spamassassin subfolder.


HOME PAGE
=========


http://sawin32.sourceforge.net/

You may consider downloading additional tools from this webpage:

sa-learn - a utility that will allow you to train the Bayes filter
sa-update - a utility that updates the spam-detecting rules
