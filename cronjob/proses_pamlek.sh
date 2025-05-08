cd /var/www/html/ess/cronjob/
dos2unix ftp_get_pamlek.sh
./ftp_get_pamlek.sh

cd /var/www/html/ess/cronjob/
dos2unix ftp_get_pamlek_fr.sh
./ftp_get_pamlek_fr.sh

php /var/www/html/ess/index.php pamlek pamlek get_pamlek
php /var/www/html/ess/index.php pamlek pamlek_to_ess inisialisasi today today all
php /var/www/html/ess/index.php pamlek pamlek_to_ess inisialisasi now now all