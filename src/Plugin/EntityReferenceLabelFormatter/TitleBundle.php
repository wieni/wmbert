<?php

namespace Drupal\wmbert\Plugin\EntityReferenceLabelFormatter;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\wmbert\EntityReferenceLabelFormatterPluginBase;

/**
 * @EntityReferenceLabelFormatter(
 *   id = "title_bundle",
 *   label = @Translation("Entity title and bundle"),
 * )
 */
class TitleBundle extends EntityReferenceLabelFormatterPluginBase implements ContainerFactoryPluginInterface
{
    /**
     * {@inheritdoc}
     */
    public function getLabel(EntityInterface $entity): string
    {
        $entity = $this->entityRepository->getTranslationFromContext($entity);
        $entityType = $entity->getEntityType();
        $bundle = $entity->get($entityType->getKey('bundle'))->entity;

        return sprintf(
            '%s (%s)',
            $entity->label(),
            $bundle->label()
        );
    }
}
