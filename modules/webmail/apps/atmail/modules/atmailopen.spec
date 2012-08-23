Summary: Open Source Webmail Client
Name: AtMail Open
Version: 1.02
Release: 1
Group: Applications/Mail
BuildRoot: /var/tmp/%{name}-buildroot
BuildArch: noarch
AutoReq: no
AutoProv: no
License: Apache 2.0

%define initdir %(if test -d /etc/init.d/. ; then echo /etc/init.d ; else echo /etc/rc.d/init.d ; fi)

%description
AtMail Open is a rich featured Open Source IMAP Webmail client that offers an AJAX interface with integrated
address book and the ability to send video mails.

%prep
rm -rf /usr/local/atmailopen/tmp/*
rm -rf $RPM_BUILD_ROOT
mkdir $RPM_BUILD_ROOT
mkdir -p $RPM_BUILD_ROOT/usr/share/atmailopen
mkdir $RPM_BUILD_ROOT/usr/share/atmailopen/tmp
mkdir -p $RPM_BUILD_ROOT/usr/local/bin
cp -R /usr/local/atmailopen/* $RPM_BUILD_ROOT/usr/share/atmailopen

%install
LANG=C
export LANG
find $RPM_BUILD_ROOT/ -name "Thumbs.db" | sed "s/^\//rm -f \//g" | sh
find $RPM_BUILD_ROOT/ -name "*.bak" | sed "s/^\//rm -f \//g" | sh

if test -f $RPM_BUILD_ROOT/.test
then
rm $RPM_BUILD_ROOT/.test
fi

if test -f $RPM_BUILD_ROOT/.install
then
rm $RPM_BUILD_ROOT/.install
fi

%pre
