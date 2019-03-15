<?php

namespace Drupal\wmbert\Plugin\EntityReferenceSelection;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Plugin\EntityReferenceSelection\DefaultSelection;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @EntityReferenceSelection(
 *   id = "wmbert",
 *   label = @Translation("WmBert selection"),
 *   group = "wmbert",
 *   weight = 1,
 *   deriver = "Drupal\wmbert\Plugin\Derivative\WmBertSelectionDeriver"
 * )
 */
class WmBertSelection extends DefaultSelection
{
    /** @var LanguageManagerInterface */
    protected $languageManager;

    public function __construct(
        array $configuration,
        $pluginId,
        $pluginDefinition,
        EntityManagerInterface $entityManager,
        ModuleHandlerInterface $moduleHandler,
        AccountInterface $currentUser,
        LanguageManagerInterface $languageManager
    ) {
        parent::__construct($configuration, $pluginId, $pluginDefinition, $entityManager, $moduleHandler, $currentUser);
        $this->languageManager = $languageManager;
    }

    public function defaultConfiguration()
    {
        return [
            'ignored_entities' => [],
            'same_language_only' => false,
        ] + parent::defaultConfiguration();
    }

    protected function buildEntityQuery($match = NULL, $match_operator = 'CONTAINS')
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

    /**
     * {@inheritdoc}
     */
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
            $container->get('language_manager')
        );
    }
}
