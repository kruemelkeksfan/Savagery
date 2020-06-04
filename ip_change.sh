#!/bin/sh

my_ip=`hostname -i`

echo $my_ip

sed -i -e "s/localhost/$my_ip/g" PHP/MOGUL/HttpHelper.php

sed -i -e "s/localhost/$my_ip/g" api/MongoDatabase.php
