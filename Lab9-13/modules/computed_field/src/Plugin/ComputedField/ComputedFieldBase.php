<?php

namespace Drupal\computed_field\Plugin\ComputedField;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\PluginBase;
use Drupal\computed_field\Field\ComputedFieldDefinition;
use Drupal\computed_field\Field\ComputedFieldDefinitionWithValuePluginInterface;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Base class for Computed Field plugins.
 */
abstract class ComputedFieldBase extends PluginBase implements ComputedFieldPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function getFieldType(): string {
    return $this->pluginDefinition['field_type'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldName(): ?string {
    return $this->pluginDefinition['attach']['field_name'] ?: NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldLabel(): string {
    return $this->pluginDefinition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function getStorageDefinitionSettings(): array {
    return [];
    }

  /**
   * {@inheritdoc}
   */
  public function getFieldDefinitionSettings(): array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function useLazyBuilder(EntityInterface $host_entity, ComputedFieldDefinitionWithValuePluginInterface $computed_field_definition): bool {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheability(EntityInterface $host_entity, ComputedFieldDefinitionWithValuePluginInterface $computed_field_definition): ?CacheableMetadata {
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldCardinality(): int {
    return $this->pluginDefinition['cardinality'];
  }

  /**
   * {@inheritdoc}
   */
  public function attachAsBaseField(&$fields, EntityTypeInterface $entity_type): void {
    if (!isset($this->pluginDefinition['attach']['scope'])) {
      throw new InvalidPluginDefinitionException($this->pluginId, "The 'scope' key must be specified in the 'attach' array.");
    }

    // Only work with base fields.
    if ($this->pluginDefinition['attach']['scope'] != 'base') {
      return;
    }

    $fields[$this->getFieldName()] = $this->createComputedFieldDefinition($entity_type);
  }

  /**
   * {@inheritdoc}
   */
  public function attachAsBundleField(&$fields, EntityTypeInterface $entity_type, string $bundle): void {
    if (!isset($this->pluginDefinition['attach']['scope'])) {
      throw new InvalidPluginDefinitionException($this->pluginId, "The 'scope' key must be specified in the 'attach' array.");
    }

    // Only work with bundle fields.
    if ($this->pluginDefinition['attach']['scope'] != 'bundle') {
      return;
    }

    // The base class implementation of this method only works with explicitly
    // specified entity types. If using the 'dynamic' property in the plugin
    // definition, this method must be overriden to change the logic.
    if (!isset($this->pluginDefinition['attach']['entity_types'])) {
      throw new InvalidPluginDefinitionException($this->pluginId, "The 'entity_types' key must be specified in the 'attach' array unless overriding the attachAsBundleField() method.");
    }

    // Only attach if the bundle is requested.
    if (!in_array($bundle, $this->pluginDefinition['attach']['entity_types'][$entity_type->id()])) {
      return;
    }

    $fields[$this->getFieldName()] = $this->createComputedFieldDefinition($entity_type, $bundle);
  }

  /**
   * Helper to create a computed field definition for attaching plugins.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type to create the computed field on.
   * @param string $bundle
   *   (optional) The bundle on which to create the field, if this is a bundle
   *   field. If omitted, a base field definition is returned.
   *
   * @return \Drupal\computed_field\Field\ComputedFieldDefinition
   *   The computed field definition.
   */
  protected function createComputedFieldDefinition(EntityTypeInterface $entity_type, string $bundle = NULL): ComputedFieldDefinition {
    return ComputedFieldDefinition::create($this->getFieldType())
      ->setLabel($this->getFieldLabel())
      ->setFieldValuePlugin($this)
      ->setCardinality($this->getFieldCardinality())
      ->setSetting('scope', $bundle ? 'bundle' : 'base')
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE)
      // Set the minimum for the display options to ensure that field is set to
      // be visible, with the default formatter.
      ->setDisplayOptions('view', [
        'weight' => 0,
      ])
      // Because automatically attached fields are declared in the alter hooks,
      // the name, target entity type, and target bundle are not filled in
      // automatically.
      ->setName($this->getFieldName())
      ->setTargetEntityTypeId($entity_type->id())
      ->setTargetBundle($bundle);
  }

}
