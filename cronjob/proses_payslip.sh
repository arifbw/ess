cd /var/www/html/ess/cronjob/
dos2unix ftp_get_payslip.sh
./ftp_get_payslip.sh
cd /var/www/html/ess/outbound_sap/E_PAYSLIP
chmod 644 *.*
php /var/www/html/ess/index.php outbound_sap outbound_payslip get_payslip