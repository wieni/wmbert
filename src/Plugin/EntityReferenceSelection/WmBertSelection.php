<?php

namespace Drupal\wmbert\Plugin\EntityReferenceSelection;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;
use Drupal\Core\Form\FormStateInterface;
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
        $instance->labelFormatterManager = $container->get('plugin.manager.entity_reference_label_formatter');

        return $instance;
    }

    public function defaultConfiguration()
    {
        return [
            'ignored_entities' => [],
            'same_language_only' => false,
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

        $form['disable_parent_entity_selection'] = [
            '#default_value' => $configuration['disable_parent_entity_selection']
                && $this->referencesSameEntityType(),
            '#title' => $this->t('Disable selection of parent entity'),
            '#description' => $this->t('Prevent the entity this field is attached to to be referenced.'),
            '#type' => 'checkbox',
            '#disabled' => !$this->referencesSameEntityType(),
        ];

        return $form;
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

        return $options;
    }

    protected function buildEntityQuery($match = null, $match_operator = 'CONTAINS')
    {
        $query = parent::buildEntityQuery($match, $match_operator);
        $configuration = $this->getConfiguration();
        $entityType = $this->entityTypeManager->getDefinition($configuration['target_type']);

        $ignored = $configuration['ignored_entities'];

        if ($configuration['disable_parent_entity_selection']) {
            $ignored[] = $configuration['entity']->id();
        }

        if ($entityType instanceof EntityTypeInterface && !empty($ignored)) {
            $query->condition($entityType->getKey('id'), $ignored, 'NOT IN');
        }

        if ($configuration['same_language_only']) {
            $query->condition(
                $entityType->getKey('langcode'),
                $this->languageManager->getCurrentLanguage()->getId()
            );
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
