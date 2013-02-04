#!/bin/sh

date1=`date -d "1 days ago" +%Y%m%d`
#date1='20110517'

prefix='cLoadTm'
tempdir='/home/admin/stat/data/alchemy/kaixin'
rm -rf  ${tempdir}/${prefix}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix}/${date1}
cd ${tempdir}/${prefix}/${date1}

/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix}-${date1}.log.01   http://192.168.0.40/stat-kaixin/${prefix}-${date1}.log
/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix}-${date1}.log.02   http://192.168.0.41/stat-kaixin/${prefix}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix}-${date1}.log   ${prefix}-${date1}.log.01   ${prefix}-${date1}.log.02  

prefix1='noflash'
/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix1}-${date1}.log.01   http://192.168.0.40/stat-kaixin/${prefix1}-${date1}.log
/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix1}-${date1}.log.02   http://192.168.0.41/stat-kaixin/${prefix1}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix1}-${date1}.log   ${prefix1}-${date1}.log.01   ${prefix1}-${date1}.log.02  

prefix2='nocookie'
/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix2}-${date1}.log.01   http://192.168.0.40/stat-kaixin/${prefix2}-${date1}.log
/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix2}-${date1}.log.02   http://192.168.0.41/stat-kaixin/${prefix2}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix2}-${date1}.log   ${prefix2}-${date1}.log.01   ${prefix2}-${date1}.log.02  

prefix3='flashloadlog'
/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix3}-${date1}.log.01   http://192.168.0.40/stat-kaixin/${prefix3}-${date1}.log
/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix3}-${date1}.log.02   http://192.168.0.41/stat-kaixin/${prefix3}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix3}-${date1}.log   ${prefix3}-${date1}.log.01   ${prefix3}-${date1}.log.02  

cd /home/admin/website/alchemy/kaixin/bin/
nohup /usr/local/php-cgi/bin/php -f /home/admin/website/alchemy/kaixin/bin/stat-cLoadTm.php &