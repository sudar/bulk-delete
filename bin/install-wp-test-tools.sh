#!/usr/bin/env bash

## Install WP Test tools into vendor folder.
## Composer install fails on PHP 5.x and therefore
## WP Test tools package should be manually installed.

if [ -d 'vendor/sudar/wp-plugin-test-tools/' ]; then
    cd vendor/sudar/wp-plugin-test-tools/
    git pull origin master
else
    mkdir -p vendor/sudar/
    cd vendor/sudar
    git clone https://github.com/sudar/wp-plugin-test-tools.git
    cd ../
    echo "require_once sudar/wp-plugin-test-tools/src/Tests/WPCore/bootstrap.php" >> autoload.php
    echo "require_once sudar/wp-plugin-test-tools/src/Tests/WPCore/WPCoreUnitTestCase.php" >> autoload.php
fi
