<?php
    require __DIR__ . "/../bootstrap.php";

    use Tester\Assert;


    $cache = new \Stolfam\Cache\Cache(__DIR__ . "/../../tmp");

    $o = new stdClass();
    $o->key = 123;
    $o->val = "test";

    $key = new \Stolfam\Cache\Key($o->key);

    $cache->add($key, $o);

    Assert::same("test", $cache->get($key)->val);

    $cache->notifyChange($key);

    Assert::null($cache->get($key));