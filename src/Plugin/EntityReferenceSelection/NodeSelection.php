<?php

namespace Drupal\wmbert\Plugin\EntityReferenceSelection;

use Drupal\node\Plugin\EntityReferenceSelection\NodeSelection as CoreNodeSelection;

/**
 * Provides specific access control for the node entity type.
 *
 * @EntityReferenceSelection(
 *   id = "wmbert:node",
 *   label = @Translation("Node selection"),
 *   entity_types = {"node"},
 *   group = "wmbert",
 *   weight = 1
 * )
 */
class NodeSelection extends CoreNodeSelection
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
        $ignoredEntities = $this->getConfiguration()['ignored_entities'];

        if (!empty($ignoredEntities)) {
            $query->condition('nid', $ignoredEntities, 'NOT IN');
        }

        return $query;
    }
}
