<?php
    declare(strict_types=1);

    namespace Stolfam\Cache;

    use Nette\SmartObject;


    /**
     * Class Dependency
     * @package Stolfam\Cache
     * @property-read string $id
     */
    class Dependency implements IKey, \Countable
    {
        use SmartObject;


        private IKey $key;

        /** @var Dependency[] */
        private array $parents = [];

        /**
         * Dependency constructor.
         * @param IKey $key
         */
        public function __construct(IKey $key)
        {
            $this->key = $key;
        }

        public function addParent(Dependency $dependency): void
        {
            $this->parents[$dependency->getId()] = $dependency;
        }

        public function removeDependency(Dependency $dependency): void
        {
            unset($this->parents[$dependency->getId()]);
        }

        public function getId(): string
        {
            return Cache::getKey($this->key);
        }

        public function getPrefix(): ?string
        {
            return "dependency";
        }

        /**
         * @return Dependency[]
         */
        public function listParents(): array
        {
            return $this->parents;
        }

        /**
         * @return IKey
         */
        public function getKey(): IKey
        {
            return $this->key;
        }

        public function count(): int
        {
            return count($this->parents);
        }
    }