#!/bin/sh

ip = $(hostname -i | awk '{print $1}')

sed -i -e "s/localhost/$ip/g" MOGUL/HttpHelper.php
