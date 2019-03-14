<?php

namespace Drupal\wmbert;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Entity\FieldableEntityInterface;

abstract class EntityReferenceListFormatterPluginBase extends PluginBase implements EntityReferenceListFormatterInterface
{
    /** @var FieldableEntityInterface */
    protected $parentEntity;

    public function getHeader(): array
    {
        return [];
    }

    /** @return FieldableEntityInterface|null */
    public function getParentEntity()
    {
        return $this->parentEntity;
    }

    /** @return $this */
    public function setParentEntity(FieldableEntityInterface $parentEntity)
    {
        $this->parentEntity = $parentEntity;
        return $this;
    }
}
