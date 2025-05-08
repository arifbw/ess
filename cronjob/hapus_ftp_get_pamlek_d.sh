#!/usr/bin/expect

#Dcodex
spawn lftp -u peruri app.heatrax.id:9021
expect "Password:"
send "peruriftp\n"
expect "lftp sftp@app.heatrax.id:9021~>"
send "lcd /var/www/html/ess/outbound_pamlek_fr\n"
expect "lftp sftp@app.heatrax.id:9021~>"
send "mget -c *.tk\n"
expect "lftp sftp@app.heatrax.id:9021~>"
send "exit\n"
interact
