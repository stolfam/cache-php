<?php
    require __DIR__ . "/../bootstrap.php";

    use Tester\Assert;


    $cache = new \Stolfam\Cache\Cache(__DIR__ . "/../../tmp");

    $cache->add(new \Stolfam\Cache\Key(111), new \Stolfam\Env\Pair(12, "Test 1"));
    $cache->add(new \Stolfam\Cache\Key(999), new \Stolfam\Env\Pair(99, "Test 2"));
    $cache->add(new \Stolfam\Cache\Key("ABC"), new \Stolfam\Env\Pair("AB", "Test 3"));
    $cache->add(new \Stolfam\Cache\Key("xxx"), new \Stolfam\Env\Pair("xx", "Test 4"));
    $cache->add(new \Stolfam\Cache\Key("yyy"), new \Stolfam\Env\Pair("yy", "Test 5"));

    $cache->createDependency(new \Stolfam\Cache\Key(111), new \Stolfam\Cache\Key(999));
    $cache->createDependency(new \Stolfam\Cache\Key(999), new \Stolfam\Cache\Key("ABC"));
    $cache->createDependency(new \Stolfam\Cache\Key(111), new \Stolfam\Cache\Key("xxx"));
    $cache->createDependency(new \Stolfam\Cache\Key("ABC"), new \Stolfam\Cache\Key("xxx"));
    $cache->createDependency(new \Stolfam\Cache\Key("xxx"), new \Stolfam\Cache\Key("yyy"));

    Assert::same("Test 4", $cache->get(new \Stolfam\Cache\Key("xxx"))->value);

    $cache->notifyChange(new \Stolfam\Cache\Key(999));
    $cache->notifyChange(new \Stolfam\Cache\Key("ABC"));

    Assert::null($cache->get(new \Stolfam\Cache\Key(999)));
    Assert::null($cache->get(new \Stolfam\Cache\Key("xxx")));
    Assert::null($cache->get(new \Stolfam\Cache\Key("ABC")));
    Assert::null($cache->get(new \Stolfam\Cache\Key("yyy")));

    Assert::same("Test 1", $cache->get(new \Stolfam\Cache\Key(111))->value);

    $cache->notifyChange(new \Stolfam\Cache\Key(111));

    $lastKey = null;
    for ($i = 0; $i < 100; $i++) {
        $key = new \Stolfam\Cache\Key($i);
        $cache->add($key, new \Stolfam\Env\Pair("key_$i", "Test $i"));
        if(isset($lastKey)) {
            $cache->createDependency($key,$lastKey);
        }
        $lastKey = clone $key;
    }

    $cache->notifyChange(new \Stolfam\Cache\Key(98));
    Assert::null($cache->get(new \Stolfam\Cache\Key(0)));
    Assert::same("Test 99", $cache->get(new \Stolfam\Cache\Key(99))->value);

    $cache->notifyChange(new \Stolfam\Cache\Key(99));
    Assert::null($cache->get(new \Stolfam\Cache\Key(99)));