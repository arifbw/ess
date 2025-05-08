cd /var/www/html/ess/cronjob/
dos2unix ftp_get_master_data.sh
./ftp_get_master_data.sh
cd /var/www/html/ess/outbound_sap/MasterData
chmod 644 *.*
php /var/www/html/ess/index.php outbound_sap outbound_master_data get_master_data