php /var/www/html/ess/index.php outbound_ess outbound_karyawan generate_karyawan 1
php /var/www/html/ess/index.php outbound_ess outbound_karyawan generate_karyawan 2
php /var/www/html/ess/index.php outbound_ess outbound_karyawan generate_karyawan 3
dos2unix ftp_put_ess_karyawan.sh
chmod 777 *.sh
./ftp_put_ess_karyawan.sh