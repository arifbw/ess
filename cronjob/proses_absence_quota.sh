cd /var/www/html/ess/cronjob/
dos2unix ftp_get_absence_quota.sh
./ftp_get_absence_quota.sh
cd /var/www/html/ess/outbound_sap/AbsenceQuota
chmod 644 *.*
php /var/www/html/ess/index.php outbound_sap outbound_absence_quota get_absence_quota