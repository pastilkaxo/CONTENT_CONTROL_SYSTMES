<?php

namespace Drupal\computed_field;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\computed_field\Annotation\ComputedField as ComputedFieldAnnotation;
use Drupal\computed_field\Attribute\ComputedField;
use Drupal\computed_field\Plugin\ComputedField\ComputedFieldPluginInterface;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Manages discovery and instantiation of Computed Field plugins.
 */
class ComputedFieldManager extends DefaultPluginManager {

  /**
   * Constructs a new ComputedFieldManagerManager.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   The cache backend.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(
    \Traversable $namespaces,
    CacheBackendInterface $cache_backend,
    ModuleHandlerInterface $module_handler
  ) {
    parent::__construct(
      'Plugin/ComputedField',
      $namespaces,
      $module_handler,
      ComputedFieldPluginInterface::class,
      ComputedField::class,
      ComputedFieldAnnotation::class
    );

    $this->alterInfo('computed_field_info');
    $this->setCacheBackend($cache_backend, 'computed_field_plugins');
  }

  /**
   * Gets the definitions of plugins to show in the UI for creating fields.
   *
   * @return array
   *   An array in the same format as static::getDefinitions(), containing only
   *   the definitions of plugins which are available to show in the UI.
   */
  public function getUiDefinitions(): array {
    return array_filter($this->getDefinitions(), function ($definition) {
      return empty($definition['no_ui']);
    });
  }

  /**
   * Gets instances of all automatically attaching plugins.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type to get plugins for.
   *
   * @return array
   *   An array of plugin instances.
   */
  public function getAutomaticPlugins(EntityTypeInterface $entity_type): array {
    $automatic_base_computed_field_plugins = [];
    foreach ($this->getDefinitions() as $plugin_id => $definition) {
      if (!isset($definition['attach'])) {
        continue;
      }

      if (empty($definition['attach']['dynamic']) && !isset($definition['attach']['entity_types'][$entity_type->id()])) {
        continue;
      }

      $automatic_base_computed_field_plugins[$plugin_id] = $this->createInstance($plugin_id);
    }

    return $automatic_base_computed_field_plugins;
  }

}
