#!/bin/sh
#小时数据统计

#date1=`date  +%Y%m%d`
date1=`date -d "0 days ago" +%Y%m%d`
date2=`date -d "1 days ago" +%Y%m%d`

#date1='20110527'

prefix='361'
statdb='qq_alchemy_log_stat'
tempdir='/home/www/stat_log'

#prefix
#date1
rm -rf  ${tempdir}/${prefix}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix}/${date1}
cd ${tempdir}/${prefix}/${date1}

rm -rf all-${prefix}-${date1}.log
/usr/bin/wget  -q -O  ${prefix}-${date1}.log.01   http://10.204.148.90/stat-qq/100-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix}-${date1}.log   ${prefix}-${date1}.log.01      

#date2
rm -rf  ${tempdir}/${prefix}/${date2}
mkdir -p -m 777  ${tempdir}/${prefix}/${date2}
cd ${tempdir}/${prefix}/${date2}

rm -rf all-${prefix}-${date2}.log
/usr/bin/wget  -q -O  ${prefix}-${date2}.log.01   http://10.204.148.90/stat-qq/100-${date2}.log
/bin/sort -m -t " " -k 1 -o all-${prefix}-${date2}.log   ${prefix}-${date2}.log.01    

#to php
cd /home/www/alchemy/bin
/usr/local/php-fpm/bin/php /home/www/alchemy/bin/stat-${prefix}.php $1

