<?php

namespace Drupal\wmbert\Plugin\EntityReferenceSelection;

use Drupal\Component\Utility\Html;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Session\AccountInterface;
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
    /** @var LanguageManagerInterface */
    protected $languageManager;
    /** @var EntityReferenceLabelFormatterManager */
    protected $entityReferenceLabelFormatterManager;

    public function __construct(
        array $configuration,
        $pluginId,
        $pluginDefinition,
        EntityManagerInterface $entityManager,
        ModuleHandlerInterface $moduleHandler,
        AccountInterface $currentUser,
        LanguageManagerInterface $languageManager,
        EntityReferenceLabelFormatterManager $entityReferenceLabelFormatterManager
    ) {
        parent::__construct($configuration, $pluginId, $pluginDefinition, $entityManager, $moduleHandler, $currentUser);
        $this->languageManager = $languageManager;
        $this->entityReferenceLabelFormatterManager = $entityReferenceLabelFormatterManager;
    }

    public static function create(
        ContainerInterface $container,
        array $configuration,
        $pluginId,
        $pluginDefinition
    ) {
        return new static(
            $configuration,
            $pluginId,
            $pluginDefinition,
            $container->get('entity.manager'),
            $container->get('module_handler'),
            $container->get('current_user'),
            $container->get('language_manager'),
            $container->get('plugin.manager.entity_reference_label_formatter')
        );
    }

    public function defaultConfiguration()
    {
        return [
            'ignored_entities' => [],
            'same_language_only' => false,
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
                $this->entityReferenceLabelFormatterManager->getDefinitions()
            ),
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
        $entities = $this->entityManager->getStorage($target_type)->loadMultiple($result);
        $formatter = $this->entityReferenceLabelFormatterManager->createInstance($configuration['label_formatter']);

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
        $entityType = $this->entityManager->getDefinition($configuration['target_type']);

        $ignored = $configuration['ignored_entities'];
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
}
