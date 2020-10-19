<?php

namespace Drupal\wmbert\Plugin\EntityReferenceListFormatter;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\wmbert\EntityReferenceListFormatterPluginBase;

/**
 * @EntityReferenceListFormatter(
 *     id = "title",
 *     label = @Translation("Entity title"),
 * )
 */
class Title extends EntityReferenceListFormatterPluginBase
{
    use StringTranslationTrait;

    public function getCells(EntityInterface $entity): array
    {
        $entity = $this->entityRepository->getTranslationFromContext($entity);

        return [
            ['#markup' => $entity->label()],
        ];
    }

    public function getHeader(): array
    {
        return [
            $this->t('Title'),
        ];
    }
}
