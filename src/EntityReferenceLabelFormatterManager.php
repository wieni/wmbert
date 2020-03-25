<?php

namespace Drupal\wmbert;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\wmbert\Annotation\EntityReferenceLabelFormatter;
use Traversable;

class EntityReferenceLabelFormatterManager extends DefaultPluginManager
{
    public function __construct(
        Traversable $namespaces,
        CacheBackendInterface $cacheBackend,
        ModuleHandlerInterface $moduleHandler
    ) {
        parent::__construct(
            'Plugin/EntityReferenceLabelFormatter',
            $namespaces,
            $moduleHandler,
            EntityReferenceLabelFormatterInterface::class,
            EntityReferenceLabelFormatter::class
        );
        $this->alterInfo('wmbert_entity_reference_label_formatter');
        $this->setCacheBackend($cacheBackend, 'wmbert_entity_reference_label_formatter');
    }
}
