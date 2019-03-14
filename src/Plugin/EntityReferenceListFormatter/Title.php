<?php

namespace Drupal\wmbert\Plugin\EntityReferenceListFormatter;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityRepositoryInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\wmbert\EntityReferenceListFormatterPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @EntityReferenceListFormatter(
 *   id = "title",
 *   label = @Translation("Entity title"),
 * )
 */
class Title extends EntityReferenceListFormatterPluginBase implements ContainerFactoryPluginInterface
{
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

        return [
            ['#markup' => $entity->label()],
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
