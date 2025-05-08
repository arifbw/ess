cd /var/www/html/ess/cronjob/
./inbound_sap_absence_cuti.sh

cd /var/www/html/ess/cronjob/
./inbound_sap_cico.sh

cd /var/www/html/ess/cronjob/
./inbound_sap_substitution.sh

cd /var/www/html/ess/cronjob/
./inbound_sap_overtime.sh

cd /var/www/html/ess/cronjob/
./inbound_sap_absence_attendance.sh

#cd /var/www/html/ess/cronjob/
#./proses_delete.sh

cd /var/www/html/ess/cronjob/
dos2unix ftp_put_absence_cuti.sh
./ftp_put_absence_cuti.sh

cd /var/www/html/ess/cronjob/
dos2unix ftp_put_cico.sh
./ftp_put_cico.sh

cd /var/www/html/ess/cronjob/
dos2unix ftp_put_substitution.sh
./ftp_put_substitution.sh

cd /var/www/html/ess/cronjob/
dos2unix ftp_put_overtime.sh
./ftp_put_overtime.sh

cd /var/www/html/ess/cronjob/
dos2unix ftp_put_absence_attendance.sh
./ftp_put_absence_attendance.sh