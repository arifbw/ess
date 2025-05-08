#!/usr/bin/expect
spawn sftp root@10.30.11.10
expect "root@10.30.11.10's password:"
send "P@ssw0rd123!\n"
expect "sftp>"
send "lcd /var/www/html/ess/outbound_sap/MasterData/\n"
expect "sftp>"
send "cd /var/Estandar/\n"
expect "sftp>"
send "put *.txt\n"
expect "sftp>"
send "exit\n"
interact