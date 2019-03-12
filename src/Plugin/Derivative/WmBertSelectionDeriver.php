<?php

namespace Drupal\wmbert\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\Plugin\EntityReferenceSelection\PhpSelection;

/**
 * Provides derivative plugins for the WmBertSelection plugin.
 *
 * @see \Drupal\wmbert\Plugin\EntityReferenceSelection\WmBertSelection
 */
class WmBertSelectionDeriver extends DeriverBase implements ContainerDeriverInterface
{
    use StringTranslationTrait;

    /** @var EntityTypeManagerInterface */
    protected $entityTypeManager;

    public function __construct(
        EntityTypeManagerInterface $entityTypeManager
    ) {
        $this->entityTypeManager = $entityTypeManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, $base_plugin_id)
    {
        return new static(
            $container->get('entity_type.manager')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getDerivativeDefinitions($base_plugin_definition)
    {
        foreach ($this->entityTypeManager->getDefinitions() as $entity_type_id => $entity_type) {
            $this->derivatives[$entity_type_id] = $base_plugin_definition;
            $this->derivatives[$entity_type_id]['entity_types'] = [$entity_type_id];
            $this->derivatives[$entity_type_id]['label'] = $this->t('@entity_type wmbert selection', ['@entity_type' => $entity_type->getLabel()]);
            $this->derivatives[$entity_type_id]['base_plugin_label'] = (string) $base_plugin_definition['label'];

            // If the entity type doesn't provide a 'label' key in its plugin
            // definition, we have to use the alternate PhpSelection class as default
            // plugin, which allows filtering the target entities by their label()
            // method. The major downside of PhpSelection is that it is more expensive
            // performance-wise than SelectionBase because it has to load all the
            // target entities in order to perform the filtering process, regardless
            // of whether a limit has been passed.
            // @see \Drupal\Core\Entity\Plugin\EntityReferenceSelection\PhpSelection
            if (!$entity_type->hasKey('label')) {
                $this->derivatives[$entity_type_id]['class'] = PhpSelection::class;
            }
        }

        return parent::getDerivativeDefinitions($base_plugin_definition);
    }
}
