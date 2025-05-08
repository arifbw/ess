#!/usr/bin/expect
spawn sftp peruri@10.60.10.43
expect "Password:"
send "asd1234\n"
expect "sftp>"
send "lcd /var/www/html/ess/inbound_sap/inbound_substitution/\n"
expect "sftp>"
send "cd /backup/interface/prd/interface_CICO_checking/Subtitution\n"
expect "sftp>"
send "put *.txt\n"
expect "sftp>"
send "exit\n"
interact