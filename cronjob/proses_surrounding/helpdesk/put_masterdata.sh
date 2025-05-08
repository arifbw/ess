#!/usr/bin/expect
spawn sftp devhelpdesk@10.30.11.42
expect "devhelpdesk@10.30.11.42's password:"
send "S1h3p12024!\n"
expect "sftp>"
send "lcd /var/www/html/ess/outbound_sap/MasterData/\n"
expect "sftp>"
send "cd /var/www/html/helpdesk/karyawan/\n"
expect "sftp>"
send "put *.txt\n"
expect "sftp>"
send "exit\n"
interact
