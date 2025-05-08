#!/usr/bin/expect
spawn lftp Administrator@10.30.10.210
expect "Administrator@10.30.10.210's password:"
send "Peruri123!\n"
expect "lftp>"
send "lcd /var/www/html/ess/outbound_sap/MasterData/\n"
expect "lftp>"
send "put *.txt\n"
expect "lftp>"
send "exit\n"
interact

