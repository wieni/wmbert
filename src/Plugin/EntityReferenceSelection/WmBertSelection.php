<?php

namespace Drupal\wmbert\Plugin\EntityReferenceSelection;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\wmbert\EntityReferenceLabelFormatterManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @EntityReferenceSelection(
 *     id = "wmbert",
 *     label = @Translation("WmBert selection"),
 *     group = "wmbert",
 *     weight = 1,
 *     deriver = "Drupal\wmbert\Plugin\Derivative\WmBertSelectionDeriver"
 * )
 */
class WmBertSelection extends DefaultSelection
{
    /** @var RouteMatchInterface */
    protected $routeMatch;
    /** @var LanguageManagerInterface */
    protected $languageManager;
    /** @var EntityTypeManagerInterface */
    protected $entityTypeManager;
    /** @var EntityReferenceLabelFormatterManager */
    protected $labelFormatterManager;

    public static function create(
        ContainerInterface $container,
        array $configuration,
        $pluginId,
        $pluginDefinition
    ) {
        $instance = parent::create($container, $configuration, $pluginId, $pluginDefinition);
        $instance->routeMatch = $container->get('current_route_match');
        $instance->languageManager = $container->get('language_manager');
        $instance->entityTypeManager = $container->get('entity_type.manager');
        $instance->labelFormatterManager = $container->get('plugin.manager.entity_reference_label_formatter');

        return $instance;
    }

    public function defaultConfiguration()
    {
        return [
            'ignored_entities' => [],
            'same_language_only' => false,
            'published_only' => false,
            'disable_parent_entity_selection' => false,
            'label_formatter' => 'title',
            'result_amount' => 0,
        ] + parent::defaultConfiguration();
    }

    public function buildConfigurationForm(array $form, FormStateInterface $form_state)
    {
        $form = parent::buildConfigurationForm($form, $form_state);
        $configuration = $this->getConfiguration();

        $form['label_formatter'] = [
            '#default_value' => $configuration['label_formatter'],
            '#title' => $this->t('Label formatter plugin'),
            '#type' => 'select',
            '#options' => array_map(
                function (array $definition) {
                    return $definition['label'];
                },
                $this->labelFormatterManager->getDefinitions()
            ),
        ];

        $form['result_amount'] = [
            '#default_value' => $configuration['result_amount'],
            '#title' => $this->t('Number of results'),
            '#description' => $this->t('The number of suggestions that will be listed. Use <em>0</em> to remove the limit.'),
            '#type' => 'number',
            '#min' => 0,
        ];

        $form['same_language_only'] = [
            '#default_value' => $configuration['same_language_only'],
            '#title' => $this->t('Same language only'),
            '#description' => $this->t('Only include entities with the same language as the active content language.'),
            '#type' => 'checkbox',
        ];

        $form['published_only'] = [
            '#default_value' => $configuration['published_only'],
            '#title' => $this->t('Published only'),
            '#description' => $this->t('Only include published entities (if the entity has a publishing state).'),
            '#type' => 'checkbox',
        ];

        $form['disable_parent_entity_selection'] = [
            '#default_value' => $configuration['disable_parent_entity_selection']
                && $this->referencesSameEntityType(),
            '#title' => $this->t('Disable selection of parent entity'),
            '#description' => $this->t('Prevent the entity this field is attached to to be referenced.'),
            '#type' => 'checkbox',
            '#disabled' => !$this->referencesSameEntityType(),
        ];

        $form['sort']['field']['#options'] = [
            '_label' => $this->t('Entity label'),
        ] + $form['sort']['field']['#options'];
        $form['sort']['field']['#sort_start'] = 2;

        return $form;
    }

    /** @see \Drupal\node\Plugin\EntityReferenceSelection\NodeSelection */
    public function createNewEntity($entity_type_id, $bundle, $label, $uid)
    {
        $entity = parent::createNewEntity($entity_type_id, $bundle, $label, $uid);

        if ($entity_type_id === 'node') {
            // In order to create a referenceable node, it needs to published.
            /** @var \Drupal\node\NodeInterface $node */
            $entity->setPublished();
        }

        return $entity;
    }

    public function getReferenceableEntities($match = null, $match_operator = 'CONTAINS', $limit = 0)
    {
        $configuration = $this->getConfiguration();

        if (isset($configuration['result_amount'])) {
            $limit = $configuration['result_amount'];
        }

        $target_type = $configuration['target_type'];

        $query = $this->buildEntityQuery($match, $match_operator);
        if ($limit > 0) {
            $query->range(0, $limit);
        }

        $result = $query->execute();

        if (empty($result)) {
            return [];
        }

        $options = [];
        $entities = $this->entityTypeManager->getStorage($target_type)->loadMultiple($result);
        $formatter = $this->labelFormatterManager->createInstance($configuration['label_formatter']);

        foreach ($entities as $entity_id => $entity) {
            $bundle = $entity->bundle();
            $options[$bundle][$entity_id] = Html::escape($formatter->getLabel($entity));
        }

        if ($configuration['sort']['field'] === '_label') {
            foreach ($options as $bundle => &$optionsPerBundle) {
                uasort($optionsPerBundle, 'strnatcasecmp');
            }
        }

        return $options;
    }

    protected function buildEntityQuery($match = null, $match_operator = 'CONTAINS')
    {
        $configuration = $this->getConfiguration();
        $targetType = $configuration['target_type'];
        $entityType = $this->entityTypeManager->getDefinition($targetType);
        $ignored = $configuration['ignored_entities'];
        $entity = $configuration['entity'];

        $query = $this->entityTypeManager->getStorage($targetType)->getQuery();

        // If 'target_bundles' is NULL, all bundles are referenceable, no further
        // conditions are needed.
        if (is_array($configuration['target_bundles'])) {
            // If 'target_bundles' is an empty array, no bundle is referenceable,
            // force the query to never return anything and bail out early.
            if ($configuration['target_bundles'] === []) {
                $query->condition($entityType->getKey('id'), null, '=');
                return $query;
            } else {
                $query->condition($entityType->getKey('bundle'), $configuration['target_bundles'], 'IN');
            }
        }

        if (isset($match) && $label_key = $entityType->getKey('label')) {
            $query->condition($label_key, $match, $match_operator);
        }

        // Add entity-access tag.
        $query->addTag($targetType . '_access');

        // Add the Selection handler for system_query_entity_reference_alter().
        $query->addTag('entity_reference');
        $query->addMetaData('entity_reference_selection_handler', $this);

        // Add the sort option.
        if (!in_array($configuration['sort']['field'], ['_none', '_label'], true)) {
            $query->sort($configuration['sort']['field'], $configuration['sort']['direction']);
        }

        if ($entity && !$entity->isNew() && $configuration['disable_parent_entity_selection']) {
            $ignored[] = $entity->id();
        }

        if ($entityType instanceof EntityTypeInterface && !empty($ignored)) {
            $query->condition($entityType->getKey('id'), $ignored, 'NOT IN');
        }

        if ($configuration['same_language_only']) {
            $query->condition(
                $entityType->getKey('langcode'),
                $this->languageManager->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)->getId()
            );
        }

        if ($configuration['published_only'] && $publishedKey = $entityType->getKey('published')) {
            $query->condition($publishedKey, true);
        }

        return $query;
    }

    protected function referencesSameEntityType(): bool
    {
        $configuration = $this->getConfiguration();

        if (!empty($configuration['entity'])) {
            $entityTypeId = $configuration['entity']->getEntityTypeId();
        } else {
            $entityTypeId = $this->routeMatch->getParameter('entity_type_id');
        }

        if (!$entityTypeId) {
            // Unsure, so just show the option
            return true;
        }

        return $entityTypeId === $configuration['target_type'];
    }
}
