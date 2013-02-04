#!/bin/sh
#经营-订单

#date1=`date  +%Y%m%d`
date1=`date -d "1 days ago" +%Y%m%d`
#date1='20121213'

prefix1='adddemond'
statdb='sinawb_alchemy_log_stat'
tempdir='/home/admin/stat/data/alchemy/weibo'

#prefix1
rm -rf  ${tempdir}/${prefix1}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix1}/${date1}
cd ${tempdir}/${prefix1}/${date1}

/usr/bin/wget  -q -O  ${prefix1}-${date1}.log.01   http://192.168.0.40/stat-weibo/${prefix1}-${date1}.log
/usr/bin/wget  -q -O  ${prefix1}-${date1}.log.02   http://192.168.0.41/stat-weibo/${prefix1}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix1}-${date1}.log   ${prefix1}-${date1}.log.01   ${prefix1}-${date1}.log.02  

#to php
cd /home/admin/website/alchemy/weibo/bin
/usr/local/php-cgi/bin/php /home/admin/website/alchemy/weibo/bin/stat-addDemond.php
