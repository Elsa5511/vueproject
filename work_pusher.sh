#!/bin/bash

COMMAND=${1-start}

if [ $COMMAND = start ]
then
	 ./artisan websockets:serve --port=8080 & ./artisan queue:work --tries 3 &
else
	#killall laravel-echo-server
	killall php
fi
