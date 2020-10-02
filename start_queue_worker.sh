#!/bin/bash

COMMAND=${1-start}


if [ $COMMAND = start ]
then
        php $(dirname $0)/artisan queue:work --tries 3
else
	killall laravel-echo-server
	killall php
fi

