<?php

namespace Drupal\wmbert\Plugin\EntityReferenceListFormatter;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\wmbert\EntityReferenceListFormatterPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @EntityReferenceListFormatter(
 *   id = "title_bundle",
 *   label = @Translation("Entity title and bundle"),
 * )
 */
class TitleBundle extends EntityReferenceListFormatterPluginBase implements ContainerFactoryPluginInterface
{
    use StringTranslationTrait;

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
    public function getCells(EntityInterface $entity): array
    {
        $entity = $this->entityRepository->getTranslationFromContext($entity);
        $entityType = $entity->getEntityType();
        $bundle = $entity->get($entityType->getKey('bundle'))->entity;

        return [
            ['#markup' => $entity->label()],
            ['#markup' => $bundle->label()],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader(): array
    {
        return [
            $this->t('Title'),
            $this->t('Type'),
        ];
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
}
