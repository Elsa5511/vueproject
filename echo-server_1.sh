#!/bin/bash

COMMAND=${1-start}


if [ $COMMAND = start ]
then
	npm start start --prefix $(dirname $0)/websockets #& php $(dirname $0)/artisan queue:work --tries 3 &
else
	killall laravel-echo-server
	killall php
fi
