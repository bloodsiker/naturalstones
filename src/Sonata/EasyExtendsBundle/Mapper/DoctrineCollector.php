<?php

// Compatibility shim: sonata-project/easy-extends-bundle was removed, but
// sonata-project/page-bundle 3.x still calls its DoctrineCollector with the old
// array-based API. This class bridges the old API to the new OptionsBuilder API.

namespace Sonata\EasyExtendsBundle\Mapper;

use Sonata\Doctrine\Mapper\Builder\OptionsBuilder;
use Sonata\Doctrine\Mapper\DoctrineCollector as NewDoctrineCollector;

class DoctrineCollector
{
    private static ?self $instance = null;

    public static function getInstance(): self
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function addAssociation(string $class, string $type, array $options): void
    {
        $builder = OptionsBuilder::create();
        foreach ($options as $key => $value) {
            $builder->add($key, $value);
        }

        NewDoctrineCollector::getInstance()->addAssociation($class, $type, $builder);
    }

    public function addIndex(string $class, string $name, array $columns): void
    {
        NewDoctrineCollector::getInstance()->addIndex($class, $name, $columns);
    }
}