<?php

namespace Drupal\wmbert;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class EntityReferenceLabelFormatterPluginBase extends PluginBase implements EntityReferenceLabelFormatterInterface
{
    /** @var ContentEntityInterface */
    protected $parentEntity;
    /** @var EntityRepositoryInterface */
    protected $entityRepository;

    public function __construct(
        array $configuration,
        string $pluginId,
        $pluginDefinition,
        EntityRepositoryInterface $entityRepository
    ) {
        parent::__construct($configuration, $pluginId, $pluginDefinition);
        $this->entityRepository = $entityRepository;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(
        ContainerInterface $container,
        array $configuration,
        $pluginId,
        $pluginDefinition
    ) {
        return new static(
            $configuration,
            $pluginId,
            $pluginDefinition,
            $container->get('entity.repository')
        );
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
