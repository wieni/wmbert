<?php

namespace Drupal\wmbert\Plugin\EntityReferenceListFormatter;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\wmbert\EntityReferenceListFormatterPluginBase;

/**
 * @EntityReferenceListFormatter(
 *     id = "title_bundle",
 *     label = @Translation("Entity title and bundle"),
 * )
 */
class TitleBundle extends EntityReferenceListFormatterPluginBase implements ContainerFactoryPluginInterface
{
    use StringTranslationTrait;

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

    public function getHeader(): array
    {
        return [
            $this->t('Title'),
            $this->t('Type'),
        ];
    }
}
