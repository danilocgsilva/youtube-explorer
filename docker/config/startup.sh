#!/bin/bash

a2enmod rewrite
service apache2 start
while : ; do sleep 1000; done