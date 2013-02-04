#!/bin/sh
#道具使用

#date1=`date  +%Y%m%d`
date1=`date -d "1 days ago" +%Y%m%d`
#date1='20110527'

prefix='321'
statdb='test_renren_alchemy_log_stat'
tempdir='/home/admin/stat/stat-data'

#prefix
rm -rf  ${tempdir}/${prefix}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix}/${date1}
cd ${tempdir}/${prefix}/${date1}

/usr/bin/wget -q -O  ${prefix}-${date1}.log.01   http://192.168.1.250/debug/${prefix}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix}-${date1}.log   ${prefix}-${date1}.log.01  


#to php
cd /home/admin/website/alchemy/renren/bin
/home/admin/apps/php-cgi/bin/php /home/admin/website/island/poland/bin/stat-${prefix}.php
