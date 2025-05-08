cd /var/www/html/ess/cronjob/
dos2unix ftp_get_approver.sh
./ftp_get_approver.sh
cd /var/www/html/ess/outbound_sap/Approver
chmod 644 *.*
php /var/www/html/ess/index.php outbound_sap outbound_approver get_approver