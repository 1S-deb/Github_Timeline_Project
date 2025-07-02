#!/bin/bash
# This script should set up a CRON job to run cron.php every 5 minutes.
# You need to implement the CRON setup logic here.
PHP_PATH=$(which php)

CRON_FILE="$(pwd)/cron.php"

# CRON expression for every 5 minutes
CRON_JOB="*/5 * * * * $PHP_PATH $CRON_FILE > /dev/null 2>&1"

#Check if the cron job already exists
(crontab -l 2>/dev/null | grep -F "$CRON_FILE") && {
    echo "CRON job already exists."
    exit 0
}
(crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab -
 echo "CRON job added to run every 5 minutes:$CRON_FILE"
