#!/usr/bin/expect

#server gateway lama
#spawn lftp -u anonymous 12.5.2.10
#expect "Password:"

#Server Presensi
spawn lftp -u presensi 10.30.10.211
expect "Password:"
send "anonymous\n"
expect "lftp presensi@10.30.10.211:~>"
send "lcd /var/www/html/ess/outbound_pamlek\n"
expect "lftp presensi@10.30.10.211:~>"
send "mget -c *.tk\n"
expect "lftp presensi@10.30.10.211:~>"
send "exit\n"
interact

#host: app.heatrax.id
#port: 9021
#username: peruri
#password: peruriftp
