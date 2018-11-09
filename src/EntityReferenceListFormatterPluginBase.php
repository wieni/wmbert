<?php

namespace Drupal\wmbert;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Entity\TranslatableInterface;

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
