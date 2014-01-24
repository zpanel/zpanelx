# update translation
mysqlrootpass=`cat /root/mysqlrootpass`
checktranslation=`mysql -u root -p$mysqlrootpass -e "SELECT COUNT(*) FROM zpanel_core.x_translations;" | grep "828"`
translationline="828"
if [ "$checktranslation" == "$translationline" ]
then
echo ""
else
echo "update translation"
wget -q https://github.com/ZPanelFR/zpxfrtrad/raw/master/uninstall.sql
mysql -u root -p$mysqlrootpass < uninstall.sql
rm -f uninstall.sql
fi

# Security enhancement for MySQL.
sed -i "/ssl-key=/a \secure-file-priv = /var/tmp" /etc/mysql/my.cnf

if ! grep -q "apache ALL=NOPASSWD: /etc/zpanel/panel/bin/zsudo" /etc/sudoers; then sed -i "s|apache ALL=NOPASSWD: /etc/zpanel/panel/bin/zsudo|www-data ALL=NOPASSWD: /etc/zpanel/panel/bin/zsudo|"  ; fi

# Double check fixing permissions for CRON jobs.
chmod -R 644 /var/spool/cron/
chmod -R 644 /etc/cron.d/


