#!/usr/bin/expect
spawn sftp peruri@10.60.10.43
expect "Password:"
send "asd1234\n"
expect "sftp>"
send "lcd /var/www/html/ess/outbound_sap/MasterSTO/\n"
expect "sftp>"
send "cd /backup/interface/prd/interface_CICO_checking/MasterSTO\n"
expect "sftp>"
send "get *.txt\n"
expect "sftp>"
send "exit\n"
interact