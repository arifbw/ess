cd /var/www/html/ess/cronjob/
dos2unix ftp_get_sto.sh
./ftp_get_sto.sh
cd /var/www/html/ess/outbound_sap/MasterSTO
chmod 644 *.*
php /var/www/html/ess/index.php outbound_sap outbound_sto get_sto