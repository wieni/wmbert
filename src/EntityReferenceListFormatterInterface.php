<?php

namespace Drupal\wmbert;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Entity\EntityInterface;

interface EntityReferenceListFormatterInterface extends PluginInspectionInterface
{
    public function getCells(EntityInterface $entity): array;

    public function getHeader(): array;
}
