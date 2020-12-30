<?php
    declare(strict_types=1);

    namespace Stolfam\Cache;

    use Nette\Caching\Storages\FileStorage;


    /**
     * Class Cache
     * @package Stolfam\Cache
     */
    class Cache
    {
        protected array $objects = [];
        public bool $cache = true;
        protected \Nette\Caching\Cache $cachedStorage;

        public string $defaultExpiration = "2 months";
        public bool $defaultSliding = true;

        /**
         * DataStorage constructor.
         * @param string $tempDir
         * @param bool   $cache
         */
        public function __construct(string $tempDir, bool $cache = true)
        {
            if (!file_exists($tempDir)) {
                mkdir($tempDir);
            }
            $storage = new FileStorage($tempDir);
            $this->cachedStorage = new \Nette\Caching\Cache($storage, "layer.data");
            $this->cache = $cache;
        }

        /**
         * Returns the stored (and cached) object under the $key.
         *
         * @param IKey $key
         * @return mixed|null
         * @throws \Throwable
         */
        public function get(IKey $key)
        {
            $key = self::getKey($key);

            if (isset($this->objects[$key])) {
                return $this->objects[$key];
            }

            if ($this->cache) {
                $o = $this->cachedStorage->load($key);
                if ($o !== null) {
                    return $o;
                }
            }

            if (isset($this->objects[$key])) {
                if ($this->cache) {
                    $this->cachedStorage->save($key, $this->objects[$key], [
                        \Nette\Caching\Cache::EXPIRE  => $this->defaultExpiration,
                        \Nette\Caching\Cache::SLIDING => $this->defaultSliding
                    ]);
                }

                return $this->objects[$key];
            }

            return null;
        }

        /**
         * Adds object to data storage and cached it.
         *
         * @param IKey   $key
         * @param        $o
         * @param string $expireIn
         * @param bool   $cacheSliding
         * @return mixed
         * @throws \Throwable
         */
        public function add(IKey $key, $o, $expireIn = '2 months', $cacheSliding = true)
        {
            $key = self::getKey($key);

            $this->objects[$key] = $o;
            if ($this->cache) {
                $this->cachedStorage->save($key, $o, [
                    \Nette\Caching\Cache::EXPIRE  => $expireIn,
                    \Nette\Caching\Cache::SLIDING => $cacheSliding
                ]);
            }

            return $o;
        }

        /**
         * Call this function when stored data are changed.
         *
         * @param IKey $key
         * @throws \Throwable
         */
        public function notifyChange(IKey $key): void
        {
            $dependency = $this->getDependency($key);

            if ($this->cache) {
                $this->cachedStorage->remove(self::getKey($key));
            }
            unset($this->objects[self::getKey($key)]);

            if ($dependency != null) {
                foreach ($dependency->listParents() as $parent) {
                    $this->notifyChange($parent->getKey());
                    $this->removeDependency($key, $parent->getKey());
                }
            }
        }

        /**
         * @param IKey $key
         * @return string
         */
        public static function getKey(IKey $key): string
        {
            return (!empty($key->getPrefix()) ? $key->getPrefix() . '_' : '') . $key->getId();
        }

        private function getDependency(IKey $child): ?Dependency
        {
            return $this->get(new Dependency($child));
        }

        /**
         * @param IKey $child
         * @param IKey $parent
         * @throws \Throwable
         */
        public function createDependency(IKey $child, IKey $parent): void
        {
            $dependency = $this->get(new Dependency($child));
            if ($dependency == null) {
                $dependency = new Dependency($child);
            }

            $dependency->addParent(new Dependency($parent));

            $this->add($dependency, $dependency);
        }

        /**
         * @param IKey $child
         * @param IKey $parent
         * @throws \Throwable
         */
        protected function removeDependency(IKey $child, IKey $parent): void
        {
            $dependency = $this->get(new Dependency($child));
            if ($dependency != null && $dependency instanceof Dependency) {
                $dependency->removeDependency(new Dependency($parent));

                if ($dependency->count() > 0) {
                    $this->add($dependency, $dependency);
                } else {
                    $this->notifyChange($dependency);
                }
            }
        }
    }