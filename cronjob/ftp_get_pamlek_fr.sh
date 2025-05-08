#!/usr/bin/expect

spawn lftp -u mitra 10.30.11.38
expect "Password:"
send "m1tr4##\n"
expect "lftp mitra@10.30.11.38:~>"
send "lcd /var/www/html/ess/outbound_pamlek\n"
expect "lftp mitra@10.30.11.38:~>"
send "mirror\n"
expect "lftp mitra@10.30.11.38:~>"
send "exit\n"
interact





