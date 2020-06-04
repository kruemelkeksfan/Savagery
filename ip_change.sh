#!/bin/sh

ip = $(hostname -i | awk '{print $1}')

sed -i -e "s/localhost/$ip/g" PHP/MOGUL/HttpHelper.php

sed -i -e "s/localhost/$ip/g" api/MongoDB.php
