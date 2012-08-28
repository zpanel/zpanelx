##
# $Id: squirrelmail.spec 13537 2009-04-13 16:52:57Z jervfors $
%define spec_release 1

##
# Rebuild with:
# rpmbuild --rebuild --define 'rhl7 1' squirrelmail-1.4.x.src.rpm
# to build for rhl7.

%{!?rhl7:%define rhl7 0}
%if %{rhl7}
    %define webserver apache
    %define rpm_release 0.%{spec_release}.7.x
%else
    %define webserver httpd
    %define rpm_release %{spec_release}
%endif

#------------------------------------------------------------------------------

Summary:        SquirrelMail webmail client
Name:           squirrelmail
Version:        1.4.3
Release:        %{rpm_release}
License:        GPL
URL:            http://squirrelmail.org/
Vendor:         squirrelmail.org
Group:          Applications/Internet
Source:         %{name}-%{version}.tar.bz2
BuildRoot:      %{_tmppath}/%{name}-%{version}-root
BuildArch:      noarch
Requires:       %{webserver}, php >= 4.0.6, perl, tmpwatch >= 2.8, aspell
Requires:       /usr/sbin/sendmail

%description
SquirrelMail is a standards-based webmail package written in PHP. It
includes built-in pure PHP support for the IMAP and SMTP protocols, and
all pages render in pure HTML 4.0 (with no Javascript) for maximum
compatibility across browsers.  It has very few requirements and is very
easy to configure and install. SquirrelMail has all the functionality
you would want from an email client, including strong MIME support,
address books, and folder manipulation.

#------------------------------------------------------------------------------

%prep
%setup -q
%{__rm} -f plugins/make_archive.pl

# Rearrange the documentation
%{__mv} AUTHORS ChangeLog COPYING INSTALL README UPGRADE doc/
%{__mv} ReleaseNotes doc/ReleaseNotes.txt
%{__mv} themes/README.themes doc/
for f in `find plugins -name "README*" -or -name INSTALL \
    -or -name CHANGES -or -name HISTORY`; do
    %{__mkdir_p} doc/`dirname $f`
    %{__mv} $f $_
done
%{__mv} doc/plugins/squirrelspell/doc/README doc/plugins/squirrelspell
%{__rm} -rf doc/plugins/squirrelspell/doc
%{__mv} plugins/squirrelspell/doc/* doc/plugins/squirrelspell
%{__rm} -f doc/plugins/squirrelspell/index.php
%{__rm} -rf plugins/squirrelspell/doc

# Fixup various files
echo "left_refresh=300" >> data/default_pref
for f in contrib/RPM/squirrelmail.cron contrib/RPM/config.php.redhat; do
    %{__perl} -pi \
        -e "s|__ATTDIR__|%{_localstatedir}/spool/squirrelmail/attach/|g;" \
        -e "s|__PREFSDIR__|%{_localstatedir}/lib/squirrelmail/prefs/|g;" $f
done

# Fix the version
%{__perl} -pi -e "s|^(\s*\\\$version\s*=\s*).*|\1'%{version}-%{release}';|g"\
    functions/strings.php

#------------------------------------------------------------------------------

%install
%{__rm} -rf %{buildroot}
%{__mkdir_p} -m 755 %{buildroot}%{_sysconfdir}/squirrelmail
%{__mkdir_p} -m 755 %{buildroot}%{_localstatedir}/lib/squirrelmail/prefs
%{__mkdir_p} -m 755 %{buildroot}%{_localstatedir}/spool/squirrelmail/attach
%{__mkdir_p} -m 755 %{buildroot}%{_datadir}/squirrelmail

# install default_pref into /etc/squirrelmail and symlink to it from data
%{__install} -m 644 data/default_pref \
    %{buildroot}%{_sysconfdir}/squirrelmail/
%{__ln_s} %{_sysconfdir}/squirrelmail/default_pref \
    %{buildroot}%{_localstatedir}/lib/squirrelmail/prefs/default_pref

# install the config files
%{__mkdir_p} -m 755 %{buildroot}%{_datadir}/squirrelmail/config
%{__install} -m 644 contrib/RPM/config.php.redhat \
    %{buildroot}%{_sysconfdir}/squirrelmail/config.php
%{__ln_s} %{_sysconfdir}/squirrelmail/config.php \
    %{buildroot}%{_datadir}/squirrelmail/config/config.php
%{__install} -m 644 config/config_local.php \
    %{buildroot}%{_sysconfdir}/squirrelmail/config_local.php
%{__ln_s} %{_sysconfdir}/squirrelmail/config_local.php \
    %{buildroot}%{_datadir}/squirrelmail/config/config_local.php
%{__rm} -f config/config_local.php config/config.php
%{__install} -m 644 config/*.php %{buildroot}%{_datadir}/squirrelmail/config/
%{__install} -m 755 config/*.pl  %{buildroot}%{_datadir}/squirrelmail/config/

# install index.php
%{__install} -m 644 index.php %{buildroot}%{_datadir}/squirrelmail/

# Copy over the rest
for DIR in class functions help images include locale plugins src themes; do
    %{__cp} -rp $DIR %{buildroot}%{_datadir}/squirrelmail/
done

# install the cron script
%{__mkdir_p} -m 755 %{buildroot}%{_sysconfdir}/cron.daily
%{__install} -m 755 contrib/RPM/squirrelmail.cron \
    %{buildroot}/%{_sysconfdir}/cron.daily/

%if %{rhl7}
    # symlink from /var/www/html/webmail to /usr/share/squirrelmail
    %{__mkdir_p} -m 755 %{buildroot}/var/www/html
    %{__ln_s} %{_datadir}/squirrelmail %{buildroot}/var/www/html/webmail
%else
    # install the config file
    %{__mkdir_p} %{buildroot}%{_sysconfdir}/httpd/conf.d
    %{__install} -m 644 contrib/RPM/squirrelmail.conf \
        %{buildroot}%{_sysconfdir}/httpd/conf.d/
%endif

#------------------------------------------------------------------------------

%clean
%{__rm} -rf %{buildroot}

#------------------------------------------------------------------------------

%files
%defattr(-,root,root)
%config %dir %{_sysconfdir}/squirrelmail
%config(noreplace) %{_sysconfdir}/squirrelmail/*
%if %{rhl7}
  /var/www/html/webmail
%else
  %config(noreplace) %{_sysconfdir}/httpd/conf.d/*.conf
%endif
%doc doc/*
%dir %{_datadir}/squirrelmail
%{_datadir}/squirrelmail/index.php
%{_datadir}/squirrelmail/class
%{_datadir}/squirrelmail/functions
%{_datadir}/squirrelmail/help
%{_datadir}/squirrelmail/images
%{_datadir}/squirrelmail/include
%{_datadir}/squirrelmail/locale
%{_datadir}/squirrelmail/src
%{_datadir}/squirrelmail/themes
%{_datadir}/squirrelmail/config
%dir %{_datadir}/squirrelmail/plugins
%{_datadir}/squirrelmail/plugins/*
%dir %{_localstatedir}/lib/squirrelmail
%dir %{_localstatedir}/spool/squirrelmail
%attr(0770, root, apache) %dir %{_localstatedir}/lib/squirrelmail/prefs
%attr(0730, root, apache) %dir %{_localstatedir}/spool/squirrelmail/attach
%{_localstatedir}/lib/squirrelmail/prefs/default_pref
%{_sysconfdir}/cron.daily/squirrelmail.cron

#------------------------------------------------------------------------------

%changelog
* Wed Apr 07 2004 Konstantin Ryabitsev <icon@duke.edu> 1.4.3-1
- Skipped 1.4.2 because it was built outside of CVS.
- Prepping for 1.4.3
- default_pref is now in /etc/squirrelmail/default_pref with a symlink in
  place
- Probably the last release for 7.x.

* Thu Jul 03 2003 Konstantin Riabitsev <icon@duke.edu> 1.4.1-1
- Build for 1.4.1
- Prefixing the release with "0" so the RPM upgrades cleanly when going to
  rhl > 7.x.

* Tue Mar 26 2003 Konstantin Riabitsev <icon@duke.edu> 1.4.0-1
- Build for 1.4.0

* Thu Feb 13 2003 Konstantin Riabitsev <icon@duke.edu> 1.4.0-0.2pre
- Initial release for 1.4.0 prerelease

* Tue Feb 04 2003 Konstantin Riabitsev <icon@duke.edu> 1.2.11-1
- Upping version number.

* Tue Oct 29 2002 Konstantin Riabitsev <icon@duke.edu> 1.2.9-1
- Upping version number.

* Sat Sep 14 2002 Konstantin Riabitsev <icon@duke.edu> 1.2.8-1
- adopted RH's spec file so we don't duplicate effort. 
- Removed rh'ized splash screen.
- Adding fallbacks for building rhl7 version as well with the same 
  specfile. Makes the spec file not as clean, but hey.
- remove workarounds for #68669 (rh bugzilla), since 1.2.8 works with
  register_globals = Off.
- Hardwiring localhost into the default config file. Makes sense.
- No more such file MIRRORS.
- Adding aspell as one of the req's, since squirrelspell is enabled by
  default
- Added Vendor: line to distinguish ourselves from RH.
- Doing the uglies with the release numbers.

* Tue Aug  6 2002 Preston Brown <pbrown@redhat.com> 1.2.7-4
- replacement splash screen.

* Mon Jul 22 2002 Gary Benson <gbenson@redhat.com> 1.2.7-3
- get rid of long lines in the specfile.
- remove symlink in docroot and use an alias in conf.d instead.
- work with register_globals off (#68669)

* Tue Jul 09 2002 Gary Benson <gbenson@redhat.com> 1.2.7-2
- hardwire the hostname (well, localhost) into the config file (#67635)

* Mon Jun 24 2002 Gary Benson <gbenson@redhat.com> 1.2.7-1
- hardwire the locations into the config file and cron file.
- install squirrelmail-cleanup.cron as squirrelmail.cron.
- make symlinks relative.
- upgrade to 1.2.7.
- more dependency fixes.

* Fri Jun 21 2002 Gary Benson <gbenson@redhat.com>
- summarize the summary, fix deps, and remove some redundant stuff.
- tidy up the prep section.
- replace directory definitions with standard RHL ones.

* Fri Jun 21 2002 Tim Powers <timp@redhat.com> 1.2.6-3
- automated rebuild

* Wed Jun 19 2002 Preston Brown <pbrown@redhat.com> 1.2.6-2
- adopted Konstantin Riabitsev <icon@duke.edu>'s package for Red Hat
  Linux.  Nice job Konstantin!
