#!/bin/sh
#经营-订单

#date1=`date  +%Y%m%d`
date1=`date -d "1 days ago" +%Y%m%d`
#date1='20110527'

prefix='401'
prefix1='402'
prefix2='403'
prefix3='225'
prefix4='226'
prefix5='227'
prefix6='228'
prefix7='229'
prefix8='220'
prefix9='221'
prefix10='222'
prefix11='223'
prefix12='351'
prefix13='352'
prefix14='353'
prefix15='354'
prefix16='355'
statdb='kaixin_alchemy_log_stat'
tempdir='/home/admin/stat/data/alchemy/kaixin'

#prefix
rm -rf  ${tempdir}/${prefix}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix}/${date1}
cd ${tempdir}/${prefix}/${date1}

/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix}-${date1}.log.01   http://192.168.0.40/stat-kaixin/${prefix}-${date1}.log
/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix}-${date1}.log.02   http://192.168.0.41/stat-kaixin/${prefix}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix}-${date1}.log   ${prefix}-${date1}.log.01   ${prefix}-${date1}.log.02 

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

#prefix6
rm -rf  ${tempdir}/${prefix6}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix6}/${date1}
cd ${tempdir}/${prefix6}/${date1}

/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix6}-${date1}.log.01   http://192.168.0.40/stat-kaixin/${prefix6}-${date1}.log
/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix6}-${date1}.log.02   http://192.168.0.41/stat-kaixin/${prefix6}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix6}-${date1}.log   ${prefix6}-${date1}.log.01   ${prefix6}-${date1}.log.02  

#prefix7
rm -rf  ${tempdir}/${prefix7}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix7}/${date1}
cd ${tempdir}/${prefix7}/${date1}

/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix7}-${date1}.log.01   http://192.168.0.40/stat-kaixin/${prefix7}-${date1}.log
/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix7}-${date1}.log.02   http://192.168.0.41/stat-kaixin/${prefix7}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix7}-${date1}.log   ${prefix7}-${date1}.log.01   ${prefix7}-${date1}.log.02  

#prefix8
rm -rf  ${tempdir}/${prefix8}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix8}/${date1}
cd ${tempdir}/${prefix8}/${date1}

/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix8}-${date1}.log.01   http://192.168.0.40/stat-kaixin/${prefix8}-${date1}.log
/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix8}-${date1}.log.02   http://192.168.0.41/stat-kaixin/${prefix8}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix8}-${date1}.log   ${prefix8}-${date1}.log.01   ${prefix8}-${date1}.log.02  

#prefix9
rm -rf  ${tempdir}/${prefix9}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix9}/${date1}
cd ${tempdir}/${prefix9}/${date1}

/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix9}-${date1}.log.01   http://192.168.0.40/stat-kaixin/${prefix9}-${date1}.log
/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix9}-${date1}.log.02   http://192.168.0.41/stat-kaixin/${prefix9}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix9}-${date1}.log   ${prefix9}-${date1}.log.01   ${prefix9}-${date1}.log.02  

#prefix10
rm -rf  ${tempdir}/${prefix10}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix10}/${date1}
cd ${tempdir}/${prefix10}/${date1}

/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix10}-${date1}.log.01   http://192.168.0.40/stat-kaixin/${prefix10}-${date1}.log
/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix10}-${date1}.log.02   http://192.168.0.41/stat-kaixin/${prefix10}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix10}-${date1}.log   ${prefix10}-${date1}.log.01   ${prefix10}-${date1}.log.02  

#prefix11
rm -rf  ${tempdir}/${prefix11}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix11}/${date1}
cd ${tempdir}/${prefix11}/${date1}

/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix11}-${date1}.log.01   http://192.168.0.40/stat-kaixin/${prefix11}-${date1}.log
/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix11}-${date1}.log.02   http://192.168.0.41/stat-kaixin/${prefix11}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix11}-${date1}.log   ${prefix11}-${date1}.log.01   ${prefix11}-${date1}.log.02  

#prefix12
rm -rf  ${tempdir}/${prefix12}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix12}/${date1}
cd ${tempdir}/${prefix12}/${date1}

/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix12}-${date1}.log.01   http://192.168.0.40/stat-kaixin/${prefix12}-${date1}.log
/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix12}-${date1}.log.02   http://192.168.0.41/stat-kaixin/${prefix12}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix12}-${date1}.log   ${prefix12}-${date1}.log.01   ${prefix12}-${date1}.log.02  

#prefix13
rm -rf  ${tempdir}/${prefix13}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix13}/${date1}
cd ${tempdir}/${prefix13}/${date1}

/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix13}-${date1}.log.01   http://192.168.0.40/stat-kaixin/${prefix13}-${date1}.log
/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix13}-${date1}.log.02   http://192.168.0.41/stat-kaixin/${prefix13}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix13}-${date1}.log   ${prefix13}-${date1}.log.01   ${prefix13}-${date1}.log.02  

#prefix14
rm -rf  ${tempdir}/${prefix14}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix14}/${date1}
cd ${tempdir}/${prefix14}/${date1}

/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix14}-${date1}.log.01   http://192.168.0.40/stat-kaixin/${prefix14}-${date1}.log
/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix14}-${date1}.log.02   http://192.168.0.41/stat-kaixin/${prefix14}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix14}-${date1}.log   ${prefix14}-${date1}.log.01   ${prefix14}-${date1}.log.02  

#prefix15
rm -rf  ${tempdir}/${prefix15}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix15}/${date1}
cd ${tempdir}/${prefix15}/${date1}

/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix15}-${date1}.log.01   http://192.168.0.40/stat-kaixin/${prefix15}-${date1}.log
/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix15}-${date1}.log.02   http://192.168.0.41/stat-kaixin/${prefix15}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix15}-${date1}.log   ${prefix15}-${date1}.log.01   ${prefix15}-${date1}.log.02  

#prefix16
rm -rf  ${tempdir}/${prefix16}/${date1}
mkdir -p -m 777  ${tempdir}/${prefix16}/${date1}
cd ${tempdir}/${prefix16}/${date1}

/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix16}-${date1}.log.01   http://192.168.0.40/stat-kaixin/${prefix16}-${date1}.log
/usr/bin/wget --header="HOST: ackx.happyfishgame.com.cn" -q -O  ${prefix16}-${date1}.log.02   http://192.168.0.41/stat-kaixin/${prefix16}-${date1}.log
/bin/sort -m -t " " -k 1 -o all-${prefix16}-${date1}.log   ${prefix16}-${date1}.log.01   ${prefix16}-${date1}.log.02  


#to php
cd /home/admin/website/alchemy/kaixin/bin
/usr/local/php-cgi/bin/php /home/admin/website/alchemy/kaixin/bin/stat-2xx.php
