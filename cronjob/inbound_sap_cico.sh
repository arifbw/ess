php /var/www/html/ess/index.php inbound_sap inbound_cico create_file today

cd /var/www/html/ess/cronjob/
dos2unix ftp_put_cico.sh
./ftp_put_cico.sh

