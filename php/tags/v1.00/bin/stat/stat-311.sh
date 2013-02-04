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
statdb='test_renren_alchemy_log_stat'
tempdir='/home/admin/stat/stat-data'

#prefix1
rm -rf  ${tempdir}/${prefix1}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix1}/${date1}
cd ${tempdir}/${prefix1}/${date1}

/usr/bin/wget -q -O  ${prefix1}-${date1}.log.01   http://192.168.1.250/debug/${prefix1}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix1}-${date1}.log   ${prefix1}-${date1}.log.01  


#prefix2
rm -rf  ${tempdir}/${prefix2}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix2}/${date1}
cd ${tempdir}/${prefix2}/${date1}

/usr/bin/wget -q -O  ${prefix2}-${date1}.log.01   http://192.168.1.250/debug/${prefix2}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix2}-${date1}.log   ${prefix2}-${date1}.log.01  


#prefix3
rm -rf  ${tempdir}/${prefix3}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix3}/${date1}
cd ${tempdir}/${prefix3}/${date1}

/usr/bin/wget -q -O  ${prefix3}-${date1}.log.01   http://192.168.1.250/debug/${prefix3}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix3}-${date1}.log   ${prefix3}-${date1}.log.01  


#prefix4
rm -rf  ${tempdir}/${prefix3}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix3}/${date1}
cd ${tempdir}/${prefix3}/${date1}

/usr/bin/wget -q -O  ${prefix4}-${date1}.log.01   http://192.168.1.250/debug/${prefix4}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix4}-${date1}.log   ${prefix4}-${date1}.log.01  


#to php
cd /home/admin/website/alchemy/renren/bin
/home/admin/apps/php-cgi/bin/php /home/admin/website/island/poland/bin/stat-${prefix}.php
