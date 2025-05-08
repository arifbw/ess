### cd /var/www/backup/ess/database/
cd /var/www/backup_storage/app_ess
[ -d $(date +%F) ] || mkdir $(date +%F)
mysqldump -u ess -pperuri2018ess ess > $(date +%F)/ess_$(date +%F-%H-%M).sql
if [ $(date +%H) == 18 ]; then zip -r $(date +%F).zip $(date +%F); rm -rf $(date +%F); fi

php /var/www/html/ess/index.php proses_backup insert_status_proses today
