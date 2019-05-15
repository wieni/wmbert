<?php

namespace Drupal\wmbert;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Entity\EntityInterface;

interface EntityReferenceLabelFormatterInterface extends PluginInspectionInterface
{
    public function getLabel(EntityInterface $entity): string;
}
