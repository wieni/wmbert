<?php

namespace Drupal\wmbert\Plugin\EntityReferenceLabelFormatter;

use Drupal\Core\Entity\EntityInterface;
use Drupal\wmbert\EntityReferenceLabelFormatterPluginBase;

/**
 * @EntityReferenceLabelFormatter(
 *     id = "title",
 *     label = @Translation("Entity title"),
 * )
 */
class Title extends EntityReferenceLabelFormatterPluginBase
{
    public function getLabel(EntityInterface $entity): string
    {
        $entity = $this->entityRepository->getTranslationFromContext($entity);

        return $entity->label();
    }
}
