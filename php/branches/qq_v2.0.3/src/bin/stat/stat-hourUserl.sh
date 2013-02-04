#!/bin/sh
#经营-订单

date1=`date  +%Y%m%d`
#date1=`date -d "1 days ago" +%Y%m%d`
#date1='20110527'

prefix1='userHour'
prefix2='100'
prefix3='payLevel'
prefix4='101'

tempdir='/home/www/stat_log'

#prefix1
rm -rf  ${tempdir}/${prefix1}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix1}/${date1}
cd ${tempdir}/${prefix1}/${date1}

/usr/bin/wget  -q -O  ${prefix1}-${date1}.log.01   http://10.204.148.90/stat-qq/${prefix1}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix1}-${date1}.log   ${prefix1}-${date1}.log.01   
#prefix2
rm -rf  ${tempdir}/${prefix2}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix2}/${date1}
cd ${tempdir}/${prefix2}/${date1}
/usr/bin/wget  -q -O  ${prefix2}-${date1}.log.01   http://10.204.148.90/stat-qq/${prefix2}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix2}-${date1}.log   ${prefix2}-${date1}.log.01  

#prefix3
rm -rf  ${tempdir}/${prefix3}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix3}/${date1}
cd ${tempdir}/${prefix3}/${date1}
/usr/bin/wget  -q -O  ${prefix3}-${date1}.log.01   http://10.204.148.90/stat-qq/${prefix3}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix3}-${date1}.log   ${prefix3}-${date1}.log.01  

#prefix4
rm -rf  ${tempdir}/${prefix4}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix4}/${date1}
cd ${tempdir}/${prefix4}/${date1}
/usr/bin/wget  -q -O  ${prefix4}-${date1}.log.01   http://10.204.148.90/stat-qq/${prefix4}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix4}-${date1}.log   ${prefix4}-${date1}.log.01  
#to php
cd /home/www/alchemy/bin
/usr/local/php-fpm/bin/php /home/www/alchemy/bin/stat-houruser.php
