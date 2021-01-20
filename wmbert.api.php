<?php

use Drupal\wmbert\EntityReferenceLabelFormatterManager;

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Alter label formatter definitions
 * @see EntityReferenceLabelFormatterManager
 */
function hook_wmbert_entity_reference_label_formatter_alter(array &$definitions): void
{
    $definitions['title_bundle']['class'] = 'Drupal\mymodule\Plugin\EntityReferenceLabelFormatter\CustomTitleBundle';
}

/**
 * Alter list formatter definitions
 * @see EntityReferenceLabelFormatterManager
 */
function hook_wmbert_entity_reference_list_formatter_alter(array &$definitions): void
{
    $definitions['title_bundle']['class'] = 'Drupal\mymodule\Plugin\EntityReferenceListFormatter\CustomTitleBundle';
}

/**
 * @} End of "addtogroup hooks".
 */
