<?php
    declare(strict_types=1);

    namespace Stolfam\Cache;

    /**
     * Interface IKey
     * @package Stolfam\Cache
     */
    interface IKey
    {
        public function getPrefix(): ?string;

        public function getId(): string;
    }