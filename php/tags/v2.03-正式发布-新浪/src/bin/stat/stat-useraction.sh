#!/bin/sh

date1=`date -d "1 days ago" +%Y%m%d`
#date1='20110517'

prefix='useraction'
tempdir='/home/admin/stat/data/alchemy/kaixin'
rm -rf  ${tempdir}/${prefix}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix}/${date1}
cd ${tempdir}/${prefix}/${date1}

/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix}-${date1}.log.01   http://192.168.0.40/stat-kaixin/${prefix}-${date1}.log
/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix}-${date1}.log.02   http://192.168.0.41/stat-kaixin/${prefix}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix}-${date1}.log   ${prefix}-${date1}.log.01   ${prefix}-${date1}.log.02  

cd /home/admin/website/alchemy/kaixin/bin/
/usr/local/php-cgi/bin/php /home/admin/website/alchemy/kaixin/bin/stat-userAction.php