<?php

namespace Drupal\wmbert\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Entity\Element\EntityAutocomplete;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Validation\Plugin\Validation\Constraint\NotNullConstraint;
use Drupal\wmbert\EntityReferenceListFormatterInterface;
use Drupal\wmbert\EntityReferenceListFormatterManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

/**
 * @FieldWidget(
 *   id = "wmbert",
 *   label = @Translation("Better Entity Reference Table"),
 *   multiple_values = TRUE,
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class WmBert extends WidgetBase implements ContainerFactoryPluginInterface
{
    const ADD_SELECTION_SELECT = 'select';
    const ADD_SELECTION_RADIOS = 'radios';
    const ADD_SELECTION_AUTO_COMPLETE = 'auto_complete';
    const ADD_SELECTION_NONE = 'none';

    /** @var AccountProxyInterface */
    protected $currentUser;
    /** @var EntityReferenceListFormatterManager */
    protected $entityReferenceListFormatterManager;
    /** @var EntityTypeManagerInterface */
    protected $entityTypeManager;

    public function __construct(
        $pluginId,
        $pluginDefinition,
        FieldDefinitionInterface $fieldDefinition,
        array $settings,
        array $thirdPartySettings,
        EntityReferenceListFormatterManager $entityReferenceListFormatterManager,
        EntityTypeManagerInterface $entityTypeManager,
        AccountProxyInterface $currentUser
    ) {
        parent::__construct($pluginId, $pluginDefinition, $fieldDefinition, $settings, $thirdPartySettings);
        $this->entityReferenceListFormatterManager = $entityReferenceListFormatterManager;
        $this->entityTypeManager = $entityTypeManager;
        $this->currentUser = $currentUser;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $pluginId, $pluginDefinition)
    {
        return new static(
            $pluginId,
            $pluginDefinition,
            $configuration['field_definition'],
            $configuration['settings'],
            $configuration['third_party_settings'],
            $container->get('plugin.manager.entity_reference_list_formatter'),
            $container->get('entity_type.manager'),
            $container->get('current_user')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function formElement(
        FieldItemListInterface $items,
        $delta,
        array $element,
        array &$form,
        FormStateInterface $formState
    ) {
        $id = 'wmbert';
        $elementId = Html::getUniqueId($id);

        $element['#attached'] = [
            'library' => ['wmbert/default'],
        ];
        $element['#prefix'] = '<div id="' . $elementId . '">';
        $element['#suffix'] = '</div>';

        $element['#theme_wrappers'] = ['form_element'];
        if ($this->getSetting('wrapper')) {
            $element['#theme_wrappers'] = ['fieldset'];
            $element['#wrapper_attributes']['class'][] = 'wmbert';
        }

        $fieldName = $this->fieldDefinition->getName();
        $storageKey = $this->getStorageKey($element['#field_parents'], $fieldName);

        $buttonBaseId = sha1(implode('-', array_merge($element['#field_parents'], [$fieldName])));

        $ajax = [
            'callback' => [static::class, 'ajaxCallback'],
            'wrapper' => $elementId,
        ];

        $button = [
            '#ajax' => $ajax,
            '#limit_validation_errors' => [],
            '#submit' => [
                [static::class, 'submit'],
            ],
            '#type' => 'submit',
            '#unique_base_id' => $buttonBaseId,
        ];

        $entities = $this->getEntities($formState, $items, $storageKey);

        if (!isset($entities[0]) && $this->getSetting('add') !== self::ADD_SELECTION_NONE) {
            $element['add'] = $this->getAdd($entities, $button, $items->getEntity());
            return $element;
        }

        $element['list'] = $this->getList($elementId, $entities, $button, $items->getEntity());

        $cardinality = $this->fieldDefinition->getFieldStorageDefinition()->getCardinality();
        if ($cardinality > 0 && isset($entities[$cardinality - 1])) {
            return $element;
        }

        if ($this->getSetting('add') !== self::ADD_SELECTION_NONE) {
            $element['add'] = $this->getAdd($entities, $button, $items->getEntity());
        }

        return $element;
    }

    /**
     * {@inheritdoc}
     */
    public static function defaultSettings()
    {
        $settings = parent::defaultSettings();
        $settings['list'] = 'title';
        $settings['add'] = 'select';
        $settings['add_placeholder'] = 'Select an entity';
        $settings['disable_duplicate_selection'] = true;
        $settings['disable_parent_entity_selection'] = true;
        $settings['disable_remove'] = false;
        $settings['wrapper'] = true;
        return $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function settingsForm(array $form, FormStateInterface $form_state)
    {
        $form = parent::settingsForm($form, $form_state);
        $id = Html::getUniqueId('wmbert-add');

        $listPlugins = $this->entityReferenceListFormatterManager->getDefinitions();
        $options = [];
        foreach ($listPlugins as $plugin) {
            $options[$plugin['id']] = $plugin['label'];
        }

        $form['list'] = [
            '#default_value' => $this->getSetting('list'),
            '#options' => $options,
            '#title' => $this->t('The list formatter plugin'),
            '#type' => 'select',
        ];

        $form['add'] = [
            '#attributes' => [
                'id' => $id,
            ],
            '#default_value' => $this->getSetting('add'),
            '#options' => $this->getNewSelectionOptions(),
            '#title' => $this->t('Add entities selection'),
            '#type' => 'select',
        ];

        $form['add_placeholder'] = [
            '#default_value' => $this->getSetting('add_placeholder'),
            '#title' => $this->t('Add entities placeholder'),
            '#type' => 'textfield',
            '#states' => [
                'visible' => [
                    ':input[id="' . $id . '"]' => [
                        'value' => static::ADD_SELECTION_AUTO_COMPLETE,
                    ],
                ],
            ],
        ];

        $form['disable_duplicate_selection'] = [
            '#default_value' => $this->getSetting('disable_duplicate_selection'),
            '#title' => $this->t('Disable duplicate selection'),
            '#type' => 'checkbox',
        ];

        $form['disable_parent_entity_selection'] = [
            '#default_value' => $this->getSetting('disable_parent_entity_selection')
                && $this->referencesSameEntityType(),
            '#title' => $this->t('Disable selection of parent entity'),
            '#type' => 'checkbox',
            '#disabled' => !$this->referencesSameEntityType(),
        ];

        $form['disable_remove'] = [
            '#default_value' => $this->getSetting('disable_remove'),
            '#title' => $this->t('Disable remove'),
            '#type' => 'checkbox',
        ];

        $form['wrapper'] = [
            '#default_value' => $this->getSetting('wrapper'),
            '#title' => $this->t('Add a wrapper (fieldset)'),
            '#type' => 'checkbox',
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function settingsSummary() {
        $summary = [];

        if ($value = $this->getSetting('list')) {
            $summary[] = $this->t('List formatter: @value', ['@value' => $value]);
        }

        if ($value = $this->getSetting('add')) {
            $summary[] = $this->t('Selection: @value', ['@value' => $value]);
        }

        if ($value = $this->getSetting('add_placeholder')) {
            $summary[] = $this->t('Placeholder: @value', ['@value' => $value]);
        }

        return $summary;
    }

    /**
     * {@inheritdoc}
     */
    public function massageFormValues(array $values, array $form, FormStateInterface $form_state)
    {
        $ids = [];

        if (empty($values['list'])) {
            return $ids;
        }

        foreach ($values['list'] as $value) {
            if (empty($value['entity'])) {
                continue;
            }
            $entity = $value['entity'];
            if (empty($entity)) {
                continue;
            }
            $ids[] = $entity;
        }
        return $ids;
    }

    public static function submit(array $form, FormStateInterface $formState)
    {
        $formState->setRebuild(true);

        $triggering_element = $formState->getTriggeringElement();
        $button = array_pop($triggering_element['#parents']);
        $parents = array_slice($triggering_element['#parents'], 0, -($triggering_element['#depth']));
        $array_parents = array_slice($triggering_element['#array_parents'], 0, -($triggering_element['#depth'] + 1));
        $element = NestedArray::getValue($form, $array_parents);
        $fieldParents = $element['#field_parents'];
        $fieldName = $element['#field_name'];

        $entities = NestedArray::getValue($formState->getUserInput(), array_merge($parents, ['list'])) ?: [];
        $entities = array_map(function ($entity) {
            return $entity['entity'];
        }, $entities);

        switch ($button) {
            case 'select':
                $entityId = static::getNewEntity($formState, $parents);
                $entities[] = $entityId;
                break;
            case 'auto_complete':
                $entityId = static::getNewEntity($formState, $parents);
                $entityId = EntityAutocomplete::extractEntityIdFromAutocompleteInput($entityId);
                $entities[] = $entityId;
                break;
            case 'remove':
                $index = array_pop($triggering_element['#parents']);
                unset($entities[$index]);
                break;
        }

        NestedArray::setValue($formState->getUserInput(), array_merge($parents, ['add']), null);
        NestedArray::setValue($formState->getStorage(), static::getStorageKey($fieldParents, $fieldName), $entities);
    }

    public static function ajaxCallback(array $form, FormStateInterface $formState)
    {
        $triggering_element = $formState->getTriggeringElement();
        $array_parents = array_slice($triggering_element['#array_parents'], 0, -(1 + $triggering_element['#depth']));
        return NestedArray::getValue($form, $array_parents);
    }

    public static function getStorageKey(array $fieldParents, string $fieldName)
    {
        return array_merge($fieldParents, ['#wmbert'], [$fieldName, 'entities']);
    }

    public static function getNewEntity(FormStateInterface $formState, array $parents)
    {
        return NestedArray::getValue($formState->getUserInput(), array_merge($parents, ['add', 'entity']));
    }

    /**
     * {@inheritdoc}
     */
    public function flagErrors(
        FieldItemListInterface $items,
        ConstraintViolationListInterface $violations,
        array $form,
        FormStateInterface $form_state
    ) {
        // Taken from Drupal\entity_browser\Plugin\Field\FieldWidget\EntityReferenceBrowserWidget
        if ($violations->count() > 0) {
            /* @var \Symfony\Component\Validator\ConstraintViolation $violation */
            foreach ($violations as $offset => $violation) {
                // The value of the required field is checked through the "not null"
                // constraint, whose message is not very useful. We override it here for
                // better UX.
                if ($violation->getConstraint() instanceof NotNullConstraint) {
                    $violations->set($offset, new ConstraintViolation(
                        $this->t('@name field is required.', ['@name' => $items->getFieldDefinition()->getLabel()]),
                        '',
                        [],
                        $violation->getRoot(),
                        $violation->getPropertyPath(),
                        $violation->getInvalidValue(),
                        $violation->getPlural(),
                        $violation->getCode(),
                        $violation->getConstraint(),
                        $violation->getCause()
                    ));
                }
            }
        }

        parent::flagErrors($items, $violations, $form, $form_state);
    }

    /** @return EntityInterface[] */
    protected function getEntities(FormStateInterface $formState, FieldItemListInterface $items, array $storageKey): array
    {
        /* @var \Drupal\Core\Field\EntityReferenceFieldItemListInterface $items */
        if (!NestedArray::keyExists($formState->getStorage(), $storageKey)) {
            $entities = [];
            foreach ($items->referencedEntities() as $entity) {
                $entities[] = $entity->id();
            }
            NestedArray::setValue($formState->getStorage(), $storageKey, $entities);
        }

        $entities = NestedArray::getValue($formState->getStorage(), $storageKey);
        $entityType = $this->getFieldSetting('target_type');
        $storage = $this->entityTypeManager->getStorage($entityType);

        return array_values(array_filter(array_map(function ($id) use ($storage) {
            return $storage->load((string) $id);
        }, $entities)));
    }

    protected function getAdd(array $entities, array $button, EntityInterface $entity): array
    {
        $add = [];
        $ignored = [];

        $entities = array_reduce($entities, function ($result, $entity) {
            /* @var \Drupal\Core\Entity\EntityInterface $entity */
            $result[$entity->id()] = $entity;
            return $result;
        }, []);

        if ($this->getSetting('disable_duplicate_selection')) {
            $ignored = array_merge($ignored, array_keys($entities));
        }

        if (
            $this->getSetting('disable_parent_entity_selection')
            && $this->referencesSameEntityType()
        ) {
            $ignored = array_merge($ignored, [(int) $entity->id()]);
        }

        switch ($this->getSetting('add')) {
            case static::ADD_SELECTION_SELECT:
                $add = $this->getAddBySelect($entity, $button, $entities, $ignored);
                break;
            case static::ADD_SELECTION_AUTO_COMPLETE:
                $add = $this->getAddByAutoComplete($entity, $button, $entities, $ignored);
                break;
            case static::ADD_SELECTION_RADIOS:
                $add = $this->getAddByRadios($entity, $button, $entities, $ignored);
                break;
        }

        return [
                '#type' => 'container',
            ] + $add;
    }

    protected function getList(string $htmlId, array $entities, array $button, FieldableEntityInterface $parent): array
    {
        $tableId = Html::getUniqueId($htmlId . '-table');
        $listPluginDefinition = $this->entityReferenceListFormatterManager->getDefinition($this->getSetting('list'));
        /* @var EntityReferenceListFormatterInterface $listPlugin */
        $listPlugin = $this->entityReferenceListFormatterManager
            ->createInstance($listPluginDefinition['id'])
            ->setParentEntity($parent);

        $list = [
            '#attributes' => [
                'id' => $tableId,
            ],
            '#empty' => t('No items added.'),
            '#type' => 'table',
        ];

        if ($this->fieldDefinition->getFieldStorageDefinition()->isMultiple()) {
            $list['#tabledrag'] = [[
                'action' => 'order',
                'group' => 'wmbert-order-weight',
                'relationship' => 'sibling',
            ]];
        }


        if (!empty($entities) && !empty($listPlugin->getHeader())) {
            $list['#header'] = array_merge([''], $listPlugin->getHeader());
            $suffix = [];

            // Remove column.
            if (!$this->getSetting('disable_remove')) {
                $suffix[] = [];
            }
            // Weight column.
            if (isset($entities[1])) {
                $suffix[] = [];
            }

            if (!empty($suffix)) {
                $list['#header'] = array_merge($list['#header'], $suffix);
            }
        }

        foreach ($entities as $ind => $entity) {

            if (!$entity) {
                continue;
            }

            $row = [
                '#attributes' => [
                    'class' => ['draggable'],
                ],
                'entity' => [
                    '#type' => 'hidden',
                    '#value' => $entity->id(),
                ],
            ];

            $row += $listPlugin->getCells($entity);

            if (!$this->getSetting('disable_remove')) {
                $row['remove'] = [
                        '#depth' => 2,
                        '#name' => 'remove_' . $ind . '_' . $button['#unique_base_id'],
                        '#src' => 'core/misc/icons/000000/ex.svg',
                        '#type' => 'image_button',
                    ] + $button;
            }

            if ($this->fieldDefinition->getFieldStorageDefinition()->isMultiple()) {
                $weight = $ind;
                $row['#weight'] = $weight;
                $row['weight'] = [
                    '#attributes' => ['class' => ['wmbert-order-weight']],
                    '#default_value' => $weight,
                    '#title' => t('Weight for @title', ['@title' => $entity->label()]),
                    '#title_display' => 'invisible',
                    '#type' => 'weight',
                ];
            }

            $list[$ind] = $row;
        }

        return $list;
    }

    protected function getAddBySelect(EntityInterface $entity, array $button, array $entities, array $ignored): array
    {
        $property_names = $this->fieldDefinition->getFieldStorageDefinition()->getPropertyNames();
        $options = $this->fieldDefinition
            ->getFieldStorageDefinition()
            ->getOptionsProvider($property_names[0], $entity)
            ->getSettableOptions($this->currentUser);

        foreach ($ignored as $id) {
            if (isset($options[$id])) {
                unset($options[$id]);
            }
        }

        $options = ['_none' => '- ' . $this->t('None') . ' -'] + $options;

        return [
            'entity' => [
                '#ajax' => [
                        'trigger_as' => [
                            'name' => 'select_add_' . $button['#unique_base_id'],
                        ],
                    ] + $button['#ajax'],
                '#options' => $options,
                '#placeholder' => $this->getSetting('add_placeholder'),
                '#type' => 'select',
            ],
            'select' => [
                    '#ajax' => [
                            'event' => 'autocompleteclose',
                        ] + $button['#ajax'],
                    '#attributes' => [
                        'class' => ['js-hide'],
                    ],
                    '#depth' => 1,
                    '#ignored_entities' => $ignored,
                    '#parent_entity_id' => $entity->id(),
                    '#name' => 'select_add_' . $button['#unique_base_id'],
                    '#value' => $this->t('add'),
                ] + $button,
        ];
    }

    protected function getAddByAutoComplete(EntityInterface $entity, array $button, array $entities, array $ignored): array
    {
        $selectionSettings = $this->getFieldSetting('handler_settings') + [
                'match_operator' => 'CONTAINS',
                'ignored_entities' => $ignored,
            ];

        if ($this->getSetting('disable_duplicate_selection')) {
            $selectionSettings['view']['arguments'][] = implode(',', array_keys($entities));
        }

        return [
            'entity' => [
                '#ajax' => [
                    'event' => 'autocompleteclose',
                    'trigger_as' => [
                        'name' => 'auto_complete_add_' . $button['#unique_base_id'],
                    ],
                ] + $button['#ajax'],
                '#placeholder' => $this->getSetting('add_placeholder'),
                '#type' => 'entity_autocomplete',
                '#target_type' => $this->getFieldSetting('target_type'),
                '#selection_handler' => $this->getFieldSetting('handler'),
                '#selection_settings' => $selectionSettings,
                '#validate_reference' => false,
                '#maxlength' => 1024,
                '#size' => $this->getSetting('size'),
            ],
            'auto_complete' => [
                    '#ajax' => [
                            'event' => 'autocompleteclose',
                        ] + $button['#ajax'],
                    '#attributes' => [
                        'class' => ['js-hide'],
                    ],
                    '#ignored_entities' => $ignored,
                    '#parent_entity_id' => $entity->id(),
                    '#depth' => 1,
                    '#name' => 'auto_complete_add_' . $button['#unique_base_id'],
                    '#value' => $this->t('add'),
                ] + $button,
        ];
    }

    protected function getAddByRadios(EntityInterface $entity, array $button, array $entities, array $ignored): array
    {
        $add = $this->getAddBySelect($entity, $button, $entities, $ignored);
        unset($add['entity']['#options']['_none']);
        $add['entity']['#type'] = 'radios';
        return $add;
    }

    protected function referencesSameEntityType()
    {
        return $this->fieldDefinition->getTargetEntityTypeId()
            === $this->fieldDefinition->getFieldStorageDefinition()->getSetting('target_type');
    }

    private function getNewSelectionOptions(): array
    {
        return [
            static::ADD_SELECTION_AUTO_COMPLETE => $this->t('Auto complete'),
            static::ADD_SELECTION_SELECT => $this->t('Select'),
            static::ADD_SELECTION_RADIOS => $this->t('Radios'),
            static::ADD_SELECTION_NONE => $this->t('None'),
        ];
    }
}
