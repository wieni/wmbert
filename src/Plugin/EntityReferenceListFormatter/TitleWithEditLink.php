<?php

namespace Drupal\wmbert\Plugin\EntityReferenceListFormatter;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\Exception\UndefinedLinkTemplateException;
use Drupal\Core\Render\Markup;
use Drupal\wmbert\EntityReferenceListFormatterPluginBase;

/**
 * @EntityReferenceListFormatter(
 *     id = "title_with_edit_link",
 *     label = @Translation("Entity title (with edit link)"),
 * )
 */
class TitleWithEditLink extends EntityReferenceListFormatterPluginBase
{
    public function getCells(EntityInterface $entity): array
    {
        $entity = $this->entityRepository->getTranslationFromContext($entity);

        try {
            return [[
                '#type' => 'link',
                '#title' => Markup::create($entity->label()),
                '#url' => $entity->toUrl('edit-form'),
                '#attributes' => [
                    'target' => '_blank',
                    'rel' => 'noreferrer noopener',
                ],
            ]];
        } catch (UndefinedLinkTemplateException $e) {
            return [
                ['#markup' => Markup::create($entity->label())],
            ];
        }
    }
}
