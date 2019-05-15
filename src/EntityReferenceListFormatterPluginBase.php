<?php

namespace Drupal\wmbert;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Entity\ContentEntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class EntityReferenceListFormatterPluginBase extends PluginBase implements EntityReferenceListFormatterInterface
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

    /**
     * @deprecated Use EntityRepositoryInterface::getTranslationFromContext instead
     */
    protected function getTranslatedEntity(EntityInterface $entity)
    {
        return $this->entityRepository->getTranslationFromContext($entity);
    }
}
