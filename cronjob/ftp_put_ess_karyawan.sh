#!/usr/bin/expect
spawn sftp root@portal.peruri.co.id
expect "root@portal.peruri.co.id's password:"
send "Sur@tN0t@#2023\n"
expect "sftp>"
send "lcd /var/www/html/ess/outbound_ess/karyawan/\n"
expect "sftp>"
send "cd /data/sinkronisasi_ess/karyawan\n"
expect "sftp>"
send "put *.txt\n"
expect "sftp>"
send "exit\n"
interact