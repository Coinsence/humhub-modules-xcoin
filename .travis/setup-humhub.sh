#!/usr/bin/env sh

# -e = exit when one command returns != 0, -v print each command before executing
set -ev

old=$(pwd)
DEFAULTASSETNAME='Decimal Space'

mkdir ${HUMHUB_PATH}
cd ${HUMHUB_PATH}

git clone --depth 1 https://github.com/Coinsence/humhub.git .
composer install --prefer-dist --no-interaction
composer require --dev php-coveralls/php-coveralls

npm install
grunt build-assets

cd ${HUMHUB_PATH}/protected/humhub/tests

sed -i -e "s|'installed' => true,|'installed' => true,\n\t'moduleAutoloadPaths' => ['$(dirname $old)'],\n\t'defaultAssetName' => '$DEFAULTASSETNAME'|g" config/common.php
sed -i -e "s|humhub\\\modules\\\queue\\\driver\\\Instant|humhub\\\modules\\\queue\\\driver\\\MySQL|g" config/common.php
cat config/common.php

mysql -e 'CREATE DATABASE humhub_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;'
php codeception/bin/yii migrate/up --includeModuleMigrations=1 --interactive=0
mysql -e 'USE humhub_test; INSERT INTO module_enabled (module_id) VALUES ("xcoin");'
php codeception/bin/yii migrate/up --includeModuleMigrations=1 --interactive=0
php codeception/bin/yii installer/auto
php codeception/bin/yii search/rebuild

php ${HUMHUB_PATH}/protected/vendor/bin/codecept build