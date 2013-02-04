#!/bin/sh
#佣兵-雇佣、培养

#date1=`date  +%Y%m%d`
date1=`date -d "1 days ago" +%Y%m%d`
#date1='20110527'

prefix='301'
prefix1='301'
prefix2='302'
prefix3='303'
prefix4='304'
prefix5='305'
statdb='kaixin_alchemy_log_stat'
tempdir='/home/admin/stat/data/alchemy/kaixin'

#prefix1
rm -rf  ${tempdir}/${prefix1}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix1}/${date1}
cd ${tempdir}/${prefix1}/${date1}

/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix1}-${date1}.log.01   http://192.168.0.40/stat-kaixin/${prefix1}-${date1}.log
/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix1}-${date1}.log.02   http://192.168.0.41/stat-kaixin/${prefix1}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix1}-${date1}.log   ${prefix1}-${date1}.log.01   ${prefix1}-${date1}.log.02  


#prefix2
rm -rf  ${tempdir}/${prefix2}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix2}/${date1}
cd ${tempdir}/${prefix2}/${date1}

/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix2}-${date1}.log.01   http://192.168.0.40/stat-kaixin/${prefix2}-${date1}.log
/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix2}-${date1}.log.02   http://192.168.0.41/stat-kaixin/${prefix2}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix2}-${date1}.log   ${prefix2}-${date1}.log.01   ${prefix2}-${date1}.log.02  


#prefix3
rm -rf  ${tempdir}/${prefix3}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix3}/${date1}
cd ${tempdir}/${prefix3}/${date1}

/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix3}-${date1}.log.01   http://192.168.0.40/stat-kaixin/${prefix3}-${date1}.log
/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix3}-${date1}.log.02   http://192.168.0.41/stat-kaixin/${prefix3}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix3}-${date1}.log   ${prefix3}-${date1}.log.01   ${prefix3}-${date1}.log.02  


#prefix4
rm -rf  ${tempdir}/${prefix4}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix4}/${date1}
cd ${tempdir}/${prefix4}/${date1}

/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix4}-${date1}.log.01   http://192.168.0.40/stat-kaixin/${prefix4}-${date1}.log
/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix4}-${date1}.log.02   http://192.168.0.41/stat-kaixin/${prefix4}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix4}-${date1}.log   ${prefix4}-${date1}.log.01   ${prefix4}-${date1}.log.02  



#prefix5
rm -rf  ${tempdir}/${prefix5}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix5}/${date1}
cd ${tempdir}/${prefix5}/${date1}

/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix5}-${date1}.log.01   http://192.168.0.40/stat-kaixin/${prefix5}-${date1}.log
/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix5}-${date1}.log.02   http://192.168.0.41/stat-kaixin/${prefix5}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix5}-${date1}.log   ${prefix5}-${date1}.log.01   ${prefix5}-${date1}.log.02  


#to php
cd /home/admin/website/alchemy/kaixin/bin
/usr/local/php-cgi/bin/php /home/admin/website/alchemy/kaixin/bin/stat-${prefix}.php
