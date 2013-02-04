#!/bin/sh

date1=`date -d "1 days ago" +%Y%m%d`
#date1='20110517'

prefix='cLoadTm'
tempdir='/home/admin/data/stat-data/alchemy'
rm -rf  ${tempdir}/${prefix}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix}/${date1}
cd ${tempdir}/${prefix}/${date1}
/usr/bin/wget -q -O  ${prefix}-${date1}.log.01   http://192.168.90.41/debug/${prefix}-${date1}.log
/usr/bin/wget -q -O  ${prefix}-${date1}.log.02   http://192.168.90.42/debug/${prefix}-${date1}.log
/usr/bin/wget -q -O  ${prefix}-${date1}.log.03   http://192.168.90.43/debug/${prefix}-${date1}.log
/usr/bin/wget -q -O  ${prefix}-${date1}.log.04   http://192.168.90.44/debug/${prefix}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix}-${date1}.log   ${prefix}-${date1}.log.01  ${prefix}-${date1}.log.02  ${prefix}-${date1}.log.03  ${prefix}-${date1}.log.04

prefix1='noflash'
/usr/bin/wget -q -O  ${prefix1}-${date1}.log.01   http://192.168.90.41/debug/${prefix1}-${date1}.log
/usr/bin/wget -q -O  ${prefix1}-${date1}.log.02   http://192.168.90.42/debug/${prefix1}-${date1}.log
/usr/bin/wget -q -O  ${prefix1}-${date1}.log.03   http://192.168.90.43/debug/${prefix1}-${date1}.log
/usr/bin/wget -q -O  ${prefix1}-${date1}.log.04   http://192.168.90.44/debug/${prefix1}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix1}-${date1}.log   ${prefix1}-${date1}.log.01  ${prefix1}-${date1}.log.02  ${prefix1}-${date1}.log.03  ${prefix1}-${date1}.log.04

prefix2='nocookie'
/usr/bin/wget -q -O  ${prefix2}-${date1}.log.01   http://192.168.90.41/debug/${prefix2}-${date1}.log
/usr/bin/wget -q -O  ${prefix2}-${date1}.log.02   http://192.168.90.42/debug/${prefix2}-${date1}.log
/usr/bin/wget -q -O  ${prefix2}-${date1}.log.03   http://192.168.90.43/debug/${prefix2}-${date1}.log
/usr/bin/wget -q -O  ${prefix2}-${date1}.log.04   http://192.168.90.44/debug/${prefix2}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix2}-${date1}.log   ${prefix2}-${date1}.log.01  ${prefix2}-${date1}.log.02  ${prefix2}-${date1}.log.03  ${prefix2}-${date1}.log.04

cd /home/admin/website/alchemy/bin/
nohup /home/admin/apps/php-cgi/bin/php -f /home/admin/website/alchemy/bin/stat-cLoadTm.php &