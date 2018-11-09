<?php

namespace Drupal\wmbert\Plugin\EntityReferenceListFormatter;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\wmbert\EntityReferenceListFormatterPluginBase;

/**
 * @EntityReferenceListFormatter(
 *   id = "title_bundle",
 *   label = @Translation("Entity title and bundle"),
 * )
 */
class TitleBundle extends EntityReferenceListFormatterPluginBase
{
    use StringTranslationTrait;

    /**
     * {@inheritdoc}
     */
    public function getCells(EntityInterface $entity): array
    {
        $entity = $this->getTranslatedEntity($entity);

        return [
            ['#markup' => $entity->label()],
            ['#markup' => $entity->type->entity->label()],
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
}
