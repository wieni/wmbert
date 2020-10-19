<?php

namespace Drupal\wmbert\Plugin\EntityReferenceListFormatter;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\wmbert\EntityReferenceListFormatterPluginBase;

/**
 * @EntityReferenceListFormatter(
 *     id = "title_publishing",
 *     label = @Translation("Entity title and publishing status"),
 * )
 */
class TitlePublishing extends EntityReferenceListFormatterPluginBase
{
    use StringTranslationTrait;

    public function getCells(EntityInterface $entity): array
    {
        if (!$entity instanceof EntityPublishedInterface) {
            throw new \InvalidArgumentException('Entity must be an instance of \Drupal\Core\Entity\EntityPublishedInterface');
        }

        $entity = $this->entityRepository->getTranslationFromContext($entity);
        $status = $entity->isPublished()
            ? $this->t('Published')
            : $this->t('Unpublished');

        return [
            ['#markup' => $entity->label()],
            ['#markup' => $status],
        ];
    }

    public function getHeader(): array
    {
        return [
            $this->t('Title'),
            $this->t('Status'),
        ];
    }
}
