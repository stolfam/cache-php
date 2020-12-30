# cache-php
Wrapped PHP classes around Nette Framework cache.
### Dependency
That is very useful for multi-level caching of data structures. For example: Imagine that you have a data object called X compounded from smaller data objects A, B and C coming from different resources. For a better speed of a system it is good to use cache for each small object A, B and C and to create dependency between these small objects and the big one X, which would be in the cache as well. This will cause that if change any of the small objects - only that object would be need to reload. After it, the big one will be created from two small from the cache and third (now reloaded) also from cache. At the end the big one X will be stored in the cache as well again until any of the smaller objects will change.
#### Example
```
// defining dependencies with IKey
$cache->createDependency($childKey, $parentKey);
$cache->createDependency($parentKey, $grandParentKey);

// automatic update of parent and grandparent when child changed
$cache->notifyChange($childKey)
```
Because of a child changed, all dependent data (a child, a parent and a grandparent) have become invalid and will be overridden with a newer version of data.