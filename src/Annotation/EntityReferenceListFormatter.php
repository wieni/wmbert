<?php

namespace Drupal\wmbert\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * @Annotation
 */
class EntityReferenceListFormatter extends Plugin
{
    /** @var string */
    protected $id;
    /** @var string */
    protected $label;
}
