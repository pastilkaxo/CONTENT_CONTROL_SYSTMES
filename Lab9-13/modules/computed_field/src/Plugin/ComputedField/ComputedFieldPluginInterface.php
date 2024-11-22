<?php

namespace Drupal\computed_field\Plugin\ComputedField;

use Drupal\Component\Plugin\DerivativeInspectionInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\computed_field\Field\ComputedFieldDefinition;
use Drupal\computed_field\Field\ComputedFieldDefinitionWithValuePluginInterface;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Interface for Computed Field plugins.
 *
 * Computed field plugins can be one or both of:
 *   - Automatically attached: The plugin's definition declares which entity
 *     types and bundles it is attached to. There is no configuration.
 *   - Configured attaching: The plugin's definition doesn't declare how it is
 *     attached. The plugin is used in conjunction with a computed_field config
 *     entity. Plugins of this type may be configurable.
 *
 * A plugin is declared as being automatically attached by specifying the
 * 'attach' annotation properties.
 *
 * Configurable attachable plugins may be configurable in the admin UI. To do
 * this, the plugin class must implement both of:
 *   - \Drupal\Component\Plugin\ConfigurableInterface
 *   - \Drupal\Core\Plugin\PluginFormInterface
 */
interface ComputedFieldPluginInterface extends PluginInspectionInterface, DerivativeInspectionInterface {

  /**
   * Gets the field type for this plugin.
   *
   * @return string
   *   The field type plugin ID.
   */
  public function getFieldType(): string;

  /**
   * Gets the field name if this plugin attaches automatically.
   *
   * @return string|null
   *   The field name, or NULL if it does not attach automatically.
   */
  public function getFieldName(): ?string;

  /**
   * Gets the field label.
   *
   * @return string
   *   The field label.
   */
  public function getFieldLabel(): string;

  /**
   * Gets the field cardinality.
   *
   * @return int
   *   The field cardinality.
   */
  public function getFieldCardinality(): int;

  /**
   * Gets values for field storage definition settings.
   *
   * This allows plugins to influence the settings of a computed field.
   *
   * @return array
   *   An array of settings. If settings are omitted from this, the default
   *   storage settings value from the field type plugin will be used.
   *
   * @see \Drupal\Core\Field\FieldItemInterface::defaultStorageSettings()
   */
  public function getStorageDefinitionSettings(): array;

  /**
   * Gets values for field definition settings.
   *
   * This allows plugins to influence the settings of a computed field.
   *
   * @return array
   *   An array of settings. If settings are omitted from this, the default
   *   field settings value from the field type plugin will be used.
   *
   * @see \Drupal\Core\Field\FieldItemInterface::defaultFieldSettings()
   */
  public function getFieldDefinitionSettings(): array;

  /**
   * Returns the value for a computed field.
   *
   * This value can be output with a lazy builder if it is uncacheable or has
   * different cacheing requirements from the host entity. To indicate that a
   * lazy builder should be used, implement static::getCacheability() and
   * return the cacheability data.
   *
   * Use SingleValueTrait to implement this method if a plugin is returning only
   * a single value for a field.
   *
   * @param \Drupal\Core\Entity\EntityInterface $host_entity
   *   The entity the field is on.
   * @param \Drupal\computed_field\Field\ComputedFieldDefinitionWithValuePluginInterface $computed_field_definition
   *   The computed field definition. If this plugin is configured attachable
   *   rather than automatically attached, this parameter will be the
   *   computed_field config entity.
   *
   * @return array
   *   An array of field values, indexed by the numeric field delta. If the
   *   field type has only one property, the values can be scalars. For example,
   *   for a string field:
   *   @code
   *   return [
   *     0 => 'one',
   *     1 => 'two',
   *   ];
   *   @code
   *   For a field type with several properties, the values can be scalars to
   *   return only the primary property. To return data for several properties,
   *   the values should be arrays whose keys are property names. For example,
   *   for a text field:
   *   @code
   *   return [
   *     0 => [
   *       'value' => 'one',
   *       'format' => 'full_html',
   *     ],
   *     1 => [
   *       'value' => 'two',
   *       'format' => 'full_html',
   *     ],
   *   ];
   *   @code
   */
  public function computeValue(EntityInterface $host_entity, ComputedFieldDefinitionWithValuePluginInterface $computed_field_definition): array;

  /**
   * Determines whether the field's render array should use a lazy builder.
   *
   * @param \Drupal\Core\Entity\EntityInterface $host_entity
   *   The entity the field is on.
   * @param \Drupal\computed_field\Field\ComputedFieldDefinitionWithValuePluginInterface $computed_field_definition
   *   The computed field definition. If this plugin is configured attachable
   *   rather than automatically attached, this parameter will be the
   *   computed_field config entity.
   *
   * @return bool
   *   Whether the computed field's render array should be switched to use a
   *   lazy builder.
   *
   * @see computed_field_entity_view_alter()
   */
  public function useLazyBuilder(EntityInterface $host_entity, ComputedFieldDefinitionWithValuePluginInterface $computed_field_definition): bool;

  /**
   * Returns the cacheability data for the field value.
   *
   * @param \Drupal\Core\Entity\EntityInterface $host_entity
   *   The entity that the computed field is being displayed on.
   * @param \Drupal\computed_field\Field\ComputedFieldDefinitionWithValuePluginInterface $computed_field_definition
   *   The computed field definition. If this plugin is configured attachable
   *   rather than automatically attached, this parameter will be the
   *   computed_field config entity.
   *
   * @return \Drupal\Core\Cache\CacheableMetadata|null
   *   The cacheability metadata for the field value, or NULL if the field
   *   value can be cached with the host entity.
   */
  public function getCacheability(EntityInterface $host_entity, ComputedFieldDefinitionWithValuePluginInterface $computed_field_definition): ?CacheableMetadata;

  /**
   * Adds base field definitions based on computed field plugins.
   *
   * This is only called if either:
   *  - a plugin's definition specifies that it is to automatically attach to
   *    the given entity type
   *  - a plugin's definition has the 'dynamic' property set to TRUE. In this
   *    case, the plugin class should override this method to declare base
   *    fields.
   *
   * This is called from the alter hook so that plugins that attach
   * automatically can examine existing fields to determine where and how to
   * attach.
   *
   * @param array $fields
   *   The array of fields passed to hook_entity_base_field_info_alter().
   *   Passed by reference. Computed field definitions should be added to this
   *   array.
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type whose field definitions are being altered.
   */
  public function attachAsBaseField(&$fields, EntityTypeInterface $entity_type): void;

  /**
   * Adds bundle field definitions based on computed field plugins.
   *
   * This is only called if either:
   *  - a plugin's definition specifies that it is to automatically attach to
   *    the given entity type
   *  - a plugin's definition has the 'dynamic' property set to TRUE. In this
   *    case, the plugin class should override this method to declare bundle
   *    fields.
   *
   * This is called from the alter hook so that plugins that attach
   * automatically can examine existing fields to determine where and how to
   * attach.
   *
   * @param array &$fields
   *   The array of fields passed to hook_entity_bundle_field_info_alter().
   *   Passed by reference. Computed field definitions should be added to this
   *   array.
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type whose field definitions are being altered.
   * @param string $bundle
   *   The bundle whose field definitions are being altered.
   */
  public function attachAsBundleField(&$fields, EntityTypeInterface $entity_type, string $bundle): void;

}
