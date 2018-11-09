<?php

namespace Drupal\wmbert;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\FieldableEntityInterface;

interface EntityReferenceListFormatterInterface extends PluginInspectionInterface
{
    public function getCells(EntityInterface $entity): array;

    public function getHeader(): array;

    /** @return FieldableEntityInterface|null */
    public function getParentEntity();

    /** @return $this */
    public function setParentEntity(FieldableEntityInterface $parentEntity);
}
