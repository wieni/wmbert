<?php

namespace Drupal\wmbert\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;

/**
 * @EntityReferenceSelection(
 *   id = "wmbert",
 *   label = @Translation("WmBert selection"),
 *   group = "wmbert",
 *   weight = 1,
 *   deriver = "Drupal\wmbert\Plugin\Derivative\WmBertSelectionDeriver"
 * )
 */
class WmBertSelection extends DefaultSelection
{
    public function defaultConfiguration()
    {
        return [
            'ignored_entities' => [],
        ] + parent::defaultConfiguration();
    }

    protected function buildEntityQuery($match = NULL, $match_operator = 'CONTAINS')
    {
        $query = parent::buildEntityQuery($match, $match_operator);
        $configuration = $this->getConfiguration();
        $entityType = $this->entityManager->getDefinition($configuration['target_type']);
        $ignored = $configuration['ignored_entities'];

        if ($entityType instanceof EntityTypeInterface && !empty($ignored)) {
            $query->condition($entityType->getKey('id'), $ignored, 'NOT IN');
        }

        return $query;
    }
}
