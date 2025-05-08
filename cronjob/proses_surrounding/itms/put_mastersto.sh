#!/usr/bin/expect

#DEV
#spawn sftp itmsdbdev@10.30.11.48
#expect "itmsdbdev@10.30.11.48's password:"
#send "P3rur123!\n"
#expect "sftp>"
#send "lcd /var/www/html/ess/outbound_sap/MasterSTO/\n"
#expect "sftp>"
#send "cd /home/itmsdbdev/itms/master_sto/\n"
#expect "sftp>"
#send "put *.txt\n"
#expect "sftp>"
#send "exit\n"
#interact

#PRD
spawn sftp itmsdbprod@10.30.11.46
expect "itmsdbprod@10.30.11.46's password:"
send "P3rur123!\n"
expect "sftp>"
send "lcd /var/www/html/ess/outbound_sap/MasterSTO/\n"
expect "sftp>"
send "cd /home/itmsdbprod/itms/master_sto\n"
expect "sftp>"
send "put *.txt\n"
expect "sftp>"
send "exit\n"
interact