php /var/www/html/ess/index.php inbound_sap inbound_overtime create_file today today

# Define the expected output file path
OUTPUT_FILE="/var/www/html/ess/inbound_sap/inbound_overtime/ESS_OVERTIME.txt"

# Wait until the file exists and has a non-zero size
while [ ! -s "$OUTPUT_FILE" ]; do
    sleep 5  # Wait for 5 seconds before checking again
done

cd /var/www/html/ess/cronjob/
dos2unix ftp_put_overtime.sh
./ftp_put_overtime.sh
