<?php

namespace Drupal\wmbert\Plugin\EntityReferenceListFormatter;

use Drupal\Core\Entity\EntityInterface;
use Drupal\wmbert\EntityReferenceListFormatterPluginBase;

/**
 * @EntityReferenceListFormatter(
 *   id = "title",
 *   label = @Translation("Entity title"),
 * )
 */
class Title extends EntityReferenceListFormatterPluginBase
{
    /**
     * {@inheritdoc}
     */
    public function getCells(EntityInterface $entity): array
    {
        $entity = $this->getTranslatedEntity($entity);
        
        return [
            ['#markup' => $entity->label()],
        ];
    }
}
