php /var/www/html/ess/index.php inbound_sap inbound_absence_cuti create_file today

cd /var/www/html/ess/cronjob/
dos2unix ftp_put_absence_cuti.sh
./ftp_put_absence_cuti.sh
