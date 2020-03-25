<?php

namespace Drupal\wmbert;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class EntityReferenceLabelFormatterPluginBase extends PluginBase implements EntityReferenceLabelFormatterInterface, ContainerFactoryPluginInterface
{
    /** @var ContentEntityInterface */
    protected $parentEntity;
    /** @var EntityRepositoryInterface */
    protected $entityRepository;

    public static function create(
        ContainerInterface $container,
        array $configuration,
        $pluginId,
        $pluginDefinition
    ) {
        $instance = new static($configuration, $pluginId, $pluginDefinition);
        $instance->entityRepository = $container->get('entity.repository');

        return $instance;
    }

    /** @return ContentEntityInterface|null */
    public function getParentEntity()
    {
        return $this->parentEntity;
    }

    /** @return $this */
    public function setParentEntity(ContentEntityInterface $parentEntity)
    {
        $this->parentEntity = $parentEntity;
        return $this;
    }
}
