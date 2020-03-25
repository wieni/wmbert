<?php

namespace Drupal\wmbert;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class EntityReferenceListFormatterPluginBase extends PluginBase implements EntityReferenceListFormatterInterface, ContainerFactoryPluginInterface
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

    public function getHeader(): array
    {
        return [];
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

    /** @deprecated Use EntityRepositoryInterface::getTranslationFromContext instead */
    protected function getTranslatedEntity(EntityInterface $entity)
    {
        return $this->entityRepository->getTranslationFromContext($entity);
    }
}
