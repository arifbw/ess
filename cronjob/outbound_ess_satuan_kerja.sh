php /var/www/html/ess/index.php outbound_ess outbound_satuan_kerja generate_satuan_kerja 1
php /var/www/html/ess/index.php outbound_ess outbound_satuan_kerja generate_satuan_kerja 2
dos2unix ftp_put_ess_satuan_kerja.sh
chmod 777 *.sh
./ftp_put_ess_satuan_kerja.sh