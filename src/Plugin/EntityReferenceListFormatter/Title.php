<?php

namespace Drupal\wmbert\Plugin\EntityReferenceListFormatter;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\wmbert\EntityReferenceListFormatterPluginBase;

/**
 * @EntityReferenceListFormatter(
 *   id = "title",
 *   label = @Translation("Entity title"),
 * )
 */
class Title extends EntityReferenceListFormatterPluginBase implements ContainerFactoryPluginInterface
{
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
}
