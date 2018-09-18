<?php

namespace Drupal\wmbert;

use Drupal\Component\Plugin\PluginBase;

abstract class EntityReferenceListFormatterPluginBase extends PluginBase implements EntityReferenceListFormatterInterface
{
    public function getHeader(): array
    {
        return [];
    }
}
