#!/usr/bin/expect
spawn sftp ftp_ess@10.30.11.35
expect "ftp_ess@10.30.11.35's password:"
send "tracking123!\n"
expect "sftp>"
send "lcd /var/www/html/ess/outbound_sap/MasterSTO/\n"
expect "sftp>"
send "cd /home/trackingpengadaan/ESS/sto-files/\n"
expect "sftp>"
send "put *.txt\n"
expect "sftp>"
send "exit\n"
interact