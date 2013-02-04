#!/bin/sh
#合成物品信息

#date1=`date  +%Y%m%d`
date1=`date -d "1 days ago" +%Y%m%d`
#date1='20110527'

prefix='341'
statdb='qq_alchemy_log_stat'
tempdir='/home/www/stat_log'

#prefix
rm -rf  ${tempdir}/${prefix}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix}/${date1}
cd ${tempdir}/${prefix}/${date1}

/usr/bin/wget  -q -O  ${prefix}-${date1}.log.01   http://10.204.148.90/stat-qq/${prefix}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix}-${date1}.log   ${prefix}-${date1}.log.01      


#to php
cd /home/www/alchemy/bin
/usr/local/php-fpm/bin/php /home/www/alchemy/bin/stat-${prefix}.php
