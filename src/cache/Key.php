<?php
    declare(strict_types=1);

    namespace Stolfam\Cache;

    use Nette\SmartObject;


    /**
     * Class Key
     * @package       Stolfam\Cache
     * @property-read mixed   $id
     * @property-read ?string $prefix
     */
    class Key implements IKey
    {
        use SmartObject;


        private string $id;

        private ?string $prefix;

        /**
         * Key constructor.
         * @param string      $id
         * @param string|null $prefix
         */
        public function __construct(string $id, ?string $prefix = null)
        {
            $this->id = $id;
            $this->prefix = $prefix;
        }

        /**
         * @return string
         */
        public function getId(): string
        {
            return $this->id;
        }

        /**
         * @return string|null
         */
        public function getPrefix(): ?string
        {
            return $this->prefix;
        }
    }