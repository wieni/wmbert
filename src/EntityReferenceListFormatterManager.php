<?php

namespace Drupal\wmbert;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\wmbert\Annotation\EntityReferenceListFormatter;
use Traversable;

class EntityReferenceListFormatterManager extends DefaultPluginManager
{
    public function __construct(
        Traversable $namespaces,
        CacheBackendInterface $cacheBackend,
        ModuleHandlerInterface $moduleHandler
    ) {
        parent::__construct(
            'Plugin/EntityReferenceListFormatter',
            $namespaces,
            $moduleHandler,
            EntityReferenceListFormatterInterface::class,
            EntityReferenceListFormatter::class
        );
        $this->setCacheBackend($cacheBackend, 'wmbert_entity_reference_list_formatter');
    }
}
