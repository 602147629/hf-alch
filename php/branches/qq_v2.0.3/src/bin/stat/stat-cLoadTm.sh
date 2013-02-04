#!/bin/sh

date1=`date -d "1 days ago" +%Y%m%d`
#date1='20110517'

prefix='cLoadTm'
tempdir='/home/www/stat_log'
rm -rf  ${tempdir}/${prefix}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix}/${date1}
cd ${tempdir}/${prefix}/${date1}

/usr/bin/wget  -q -O  ${prefix}-${date1}.log.01   http://10.204.148.90/stat-qq/${prefix}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix}-${date1}.log   ${prefix}-${date1}.log.01      

prefix1='noflash'
/usr/bin/wget  -q -O  ${prefix1}-${date1}.log.01   http://10.204.148.90/stat-qq/${prefix1}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix1}-${date1}.log   ${prefix1}-${date1}.log.01    

prefix2='nocookie'
/usr/bin/wget  -q -O  ${prefix2}-${date1}.log.01   http://10.204.148.90/stat-qq/${prefix2}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix2}-${date1}.log   ${prefix2}-${date1}.log.01    

prefix3='flashloadlog'
/usr/bin/wget  -q -O  ${prefix3}-${date1}.log.01   http://10.204.148.90/stat-qq/${prefix3}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix3}-${date1}.log   ${prefix3}-${date1}.log.01    

cd /home/www/alchemy/bin/
 /usr/local/php-fpm/bin/php -f /home/www/alchemy/bin/stat-cLoadTm.php &
