#!/bin/sh
#合成物品信息

#date1=`date  +%Y%m%d`
date1=`date -d "1 days ago" +%Y%m%d`
#date1='20110527'

prefix='newuserlevel'
statdb='sinawb_alchemy_log_stat'
tempdir='/home/admin/stat/data/alchemy/weibo'

#prefix
rm -rf  ${tempdir}/${prefix}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix}/${date1}
cd ${tempdir}/${prefix}/${date1}

/usr/bin/wget  -q -O  ${prefix}-${date1}.log.01   http://192.168.0.40/stat-weibo/${prefix}-${date1}.log
/usr/bin/wget  -q -O  ${prefix}-${date1}.log.02   http://192.168.0.41/stat-weibo/${prefix}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix}-${date1}.log   ${prefix}-${date1}.log.01   ${prefix}-${date1}.log.02  


#to php
cd /home/admin/website/alchemy/weibo/bin
/usr/local/php-cgi/bin/php /home/admin/website/alchemy/weibo/bin/stat-${prefix}.php
