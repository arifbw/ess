#!/usr/bin/expect
spawn sftp root@10.30.10.215
expect "root@10.30.10.215's password:"
send "P3rur1DSS@2024!!\n"
expect "sftp>"
send "lcd /var/www/html/ess/outbound_dwh/sikesper/dwh_hr_hcm_donor_darah/\n"
expect "sftp>"
send "cd /home/interface/prd/dwh/sap_hcm_last/donor_darah/\n"
expect "sftp>"
send "get *.csv\n"
expect "sftp>"
send "exit\n"
interact
