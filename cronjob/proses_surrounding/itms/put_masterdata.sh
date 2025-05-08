#!/usr/bin/expect
spawn sftp itmsdbprod@10.30.11.46
expect "itmsdbprod@10.30.11.46's password:"
send "P3rur123!\n"
expect "sftp>"
send "lcd /var/www/html/ess/outbound_sap/MasterData/\n"
expect "sftp>"
send "cd /home/itmsdbprod/itms/master_karyawan\n"
expect "sftp>"
send "put *.txt\n"
expect "sftp>"
send "exit\n"
interact