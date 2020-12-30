<?php
    require __DIR__ . '/../vendor/autoload.php';

    Tester\Environment::setup();
    date_default_timezone_set('Europe/Prague');

    define('TMP_DIR', '/tmp/demo-app-tests');

    // type this command to console to start tests: vendor/bin/tester tests/