#!/usr/bin/env sh

# -e = exit when one command returns != 0, -v print each command before executing
set -ev

# find _output folder and add to zip
find tests/ -type d -name "_output" -exec zip -r --exclude="*.gitignore" failure.zip {} +

# add logs to failure.zip
zip -ur failure.zip ${HUMHUB_PATH}/protected/runtime/logs || true

zip -ur failure.zip /tmp/phpserver.log || true

# upload file
curl --upload-file ./failure.zip https://transfer.sh/coinsence-xcoin-travis-${TRAVIS_JOB_NUMBER}.zip
echo "Uploaded to https://transfer.sh/coinsence-xcoin-travis-${TRAVIS_JOB_NUMBER}.zip"


# delete zip
rm failure.zip