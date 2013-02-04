#!/bin/sh
#经营-订单

#date1=`date  +%Y%m%d`
date1=`date -d "1 days ago" +%Y%m%d`
#date1='20110527'

prefix='311'
prefix1='311'
prefix2='312'
prefix3='313'
prefix4='314'
statdb='sinawb_alchemy_log_stat'
tempdir='/home/admin/stat/data/alchemy/weibo'

#prefix1
rm -rf  ${tempdir}/${prefix1}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix1}/${date1}
cd ${tempdir}/${prefix1}/${date1}

/usr/bin/wget  -q -O  ${prefix1}-${date1}.log.01   http://192.168.0.40/stat-weibo/${prefix1}-${date1}.log
/usr/bin/wget  -q -O  ${prefix1}-${date1}.log.02   http://192.168.0.41/stat-weibo/${prefix1}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix1}-${date1}.log   ${prefix1}-${date1}.log.01   ${prefix1}-${date1}.log.02  


#prefix2
rm -rf  ${tempdir}/${prefix2}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix2}/${date1}
cd ${tempdir}/${prefix2}/${date1}

/usr/bin/wget  -q -O  ${prefix2}-${date1}.log.01   http://192.168.0.40/stat-weibo/${prefix2}-${date1}.log
/usr/bin/wget  -q -O  ${prefix2}-${date1}.log.02   http://192.168.0.41/stat-weibo/${prefix2}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix2}-${date1}.log   ${prefix2}-${date1}.log.01   ${prefix2}-${date1}.log.02  



#prefix3
rm -rf  ${tempdir}/${prefix3}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix3}/${date1}
cd ${tempdir}/${prefix3}/${date1}

/usr/bin/wget  -q -O  ${prefix3}-${date1}.log.01   http://192.168.0.40/stat-weibo/${prefix3}-${date1}.log
/usr/bin/wget  -q -O  ${prefix3}-${date1}.log.02   http://192.168.0.41/stat-weibo/${prefix3}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix3}-${date1}.log   ${prefix3}-${date1}.log.01   ${prefix3}-${date1}.log.02  


#prefix4
rm -rf  ${tempdir}/${prefix4}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix4}/${date1}
cd ${tempdir}/${prefix4}/${date1}

/usr/bin/wget  -q -O  ${prefix4}-${date1}.log.01   http://192.168.0.40/stat-weibo/${prefix4}-${date1}.log
/usr/bin/wget  -q -O  ${prefix4}-${date1}.log.02   http://192.168.0.41/stat-weibo/${prefix4}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix4}-${date1}.log   ${prefix4}-${date1}.log.01   ${prefix4}-${date1}.log.02  


#to php
cd /home/admin/website/alchemy/weibo/bin
/usr/local/php-cgi/bin/php /home/admin/website/alchemy/weibo/bin/stat-${prefix}.php
