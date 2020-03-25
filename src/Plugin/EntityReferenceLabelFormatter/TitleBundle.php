<?php

namespace Drupal\wmbert\Plugin\EntityReferenceLabelFormatter;

use Drupal\Core\Entity\EntityInterface;
use Drupal\wmbert\EntityReferenceLabelFormatterPluginBase;

/**
 * @EntityReferenceLabelFormatter(
 *     id = "title_bundle",
 *     label = @Translation("Entity title and bundle"),
 * )
 */
class TitleBundle extends EntityReferenceLabelFormatterPluginBase
{
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
