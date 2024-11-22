<?php

namespace Drupal\computed_field\Field;

use Drupal\Core\Cache\UnchangingCacheableDependencyTrait;
use Drupal\Core\Entity\FieldableEntityInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\Field\RequiredFieldStorageDefinitionInterface;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\OptionsProviderInterface;

/**
 * A class for defining field storage definitions.
 *
 * Taken from patch at https://www.drupal.org/project/drupal/issues/2280639.
 * The only difference is the additional method isLocked().
 *
 * A field storage definition is designed to encapsulate information required
 * for systems to be able to persist field data and for consumers to query the
 * information related to the storage of a field.
 *
 * This builder object may be used directly when implementing
 * hook_entity_field_storage_info(). A storage definition can be used in
 * combination with \Drupal\Core\Field\FieldDefinition, to define both storage
 * and non-storage components of a field.
 *
 * @todo: When https://www.drupal.org/project/drupal/issues/2280639 is fixed,
 * change this class to inherit the matching class from the patch.
 *
 * @see \Drupal\Core\Field\FieldDefinition
 * @see hook_entity_field_storage_info()
 * @see \Drupal\Core\Field\FieldStorageDefinitionInterface
 */
class FieldStorageDefinition extends DataDefinition implements FieldStorageDefinitionInterface, RequiredFieldStorageDefinitionInterface {

  use UnchangingCacheableDependencyTrait;

  /**
   * The field type.
   *
   * @var string
   */
  protected $type;

  /**
   * An array of field property definitions.
   *
   * @var \Drupal\Core\TypedData\DataDefinitionInterface[]
   *
   * @see \Drupal\Core\TypedData\ComplexDataDefinitionInterface::getPropertyDefinitions()
   */
  protected $propertyDefinitions;

  /**
   * The field item class name.
   *
   * @var string
   */
  protected $itemClass;

  /**
   * The field type manager.
   *
   * @var \Drupal\Core\Field\FieldTypePluginManagerInterface
   */
  protected $fieldTypeManager;

  /**
   * The field schema.
   *
   * @var array[]
   */
  protected $schema;

  /**
   * Creates a new field storage definition.
   *
   * @param string $type
   *   The type of the field.
   *
   * @return static
   *   A new field storage definition object.
   */
  public static function create($type) {
    $storage_definition = new static([]);
    $storage_definition->type = $type;
    // Create a definition for the items, and initialize it with the default
    // settings for the field type.
    $default_settings = \Drupal::service('plugin.manager.field.field_type')->getDefaultStorageSettings($type);
    $storage_definition->setSettings($default_settings);
    return $storage_definition;
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->definition['field_name'];
  }

  /**
   * Sets the field name.
   *
   * @param string $name
   *   The field name to set.
   *
   * @return $this
   */
  public function setName($name) {
    $this->definition['field_name'] = $name;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return $this->type;
  }

  /**
   * {@inheritdoc}
   */
  public function isTranslatable() {
    return !empty($this->definition['translatable']);
  }

  /**
   * Sets whether the field is translatable.
   *
   * @param bool $translatable
   *   Whether the field is translatable.
   *
   * @return $this
   */
  public function setTranslatable($translatable) {
    $this->definition['translatable'] = $translatable;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isRevisionable() {
    // Multi-valued fields are always considered revisionable.
    return !empty($this->definition['revisionable']) || $this->isMultiple();
  }

  /**
   * Sets whether the field is revisionable.
   *
   * @param bool $revisionable
   *   Whether the field is revisionable.
   *
   * @return $this
   */
  public function setRevisionable($revisionable) {
    $this->definition['revisionable'] = $revisionable;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isQueryable() {
    @trigger_error('FieldStorageDefinitionInterface::isQueryable() is deprecated in Drupal 8.4.0 and will be removed before Drupal 9.0.0. Instead, you should use ::hasCustomStorage(). See https://www.drupal.org/node/2856563.', E_USER_DEPRECATED);
    return !$this->hasCustomStorage();
  }

  /**
   * {@inheritdoc}
   */
  public function getOptionsProvider($property_name, FieldableEntityInterface $entity) {
    // If the field item class implements the interface, create an orphaned
    // runtime item object, so that it can be used as the options provider
    // without modifying the entity being worked on.
    if (is_subclass_of($this->getItemClass(), OptionsProviderInterface::class)) {
      $items = $entity->get($this->getName());
      return \Drupal::service('plugin.manager.field.field_type')->createFieldItem($items, 0);
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function isMultiple() {
    $cardinality = $this->getCardinality();
    return ($cardinality == static::CARDINALITY_UNLIMITED) || ($cardinality > 1);
  }

  /**
   * {@inheritdoc}
   */
  public function getCardinality() {
    return $this->definition['cardinality'] ?? 1;
  }

  /**
   * Sets the maximum number of items allowed for the field.
   *
   * Possible values are positive integers or
   * FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED.
   *
   * Note that if the entity type that this field is attached to is revisionable
   * and the field has a cardinality higher than 1, the field is considered
   * revisionable by default.
   *
   * @param int $cardinality
   *   The field cardinality.
   *
   * @return $this
   */
  public function setCardinality($cardinality) {
    $this->definition['cardinality'] = $cardinality;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinition($name) {
    if (!isset($this->propertyDefinitions)) {
      $this->getPropertyDefinitions();
    }
    if (isset($this->propertyDefinitions[$name])) {
      return $this->propertyDefinitions[$name];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions() {
    if (!isset($this->propertyDefinitions)) {
      $class = $this->getItemClass();
      $this->propertyDefinitions = $class::propertyDefinitions($this);
    }
    return $this->propertyDefinitions;
  }

  /**
   * {@inheritdoc}
   */
  public function getPropertyNames() {
    return array_keys($this->getPropertyDefinitions());
  }

  /**
   * {@inheritdoc}
   */
  public function getMainPropertyName() {
    $class = $this->getItemClass();
    return $class::mainPropertyName();
  }

  /**
   * Returns the field item class name.
   *
   * @return string
   *   A class name.
   */
  protected function getItemClass() {
    if (!isset($this->itemClass)) {
      $this->itemClass = \Drupal::service('plugin.manager.field.field_type')->getPluginClass($this->type);
    }
    return $this->itemClass;
  }

  /**
   * {@inheritdoc}
   */
  public function getTargetEntityTypeId() {
    return $this->definition['entity_type'];
  }

  /**
   * Sets the ID of the type of the entity this field is attached to.
   *
   * @param string $entity_type_id
   *   The name of the target entity type to set.
   *
   * @return $this
   */
  public function setTargetEntityTypeId($entity_type_id) {
    $this->definition['entity_type'] = $entity_type_id;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSchema() {
    if (!isset($this->schema)) {
      // Get the schema from the field item class.
      $definition = \Drupal::service('plugin.manager.field.field_type')->getDefinition($this->getType());
      $class = $this->getItemClass();
      $schema = $class::schema($this);
      // Fill in default values.
      $schema += [
        'columns' => [],
        'unique keys' => [],
        'indexes' => [],
        'foreign keys' => [],
      ];
      $this->schema = $schema;
    }
    return $this->schema;
  }

  /**
   * {@inheritdoc}
   */
  public function getColumns() {
    $schema = $this->getSchema();
    return $schema['columns'];
  }

  /**
   * {@inheritdoc}
   */
  public function getProvider() {
    return $this->definition['provider'] ?? NULL;
  }

  /**
   * Sets the name of the provider of this field.
   *
   * @param string $provider
   *   The provider name to set.
   *
   * @return $this
   */
  public function setProvider($provider) {
    $this->definition['provider'] = $provider;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function hasCustomStorage() {
    return !empty($this->definition['custom_storage']) || $this->isComputed();
  }

  /**
   * Sets the storage behavior for this field.
   *
   * @param bool $custom_storage
   *   Pass FALSE if the storage takes care of storing the field,
   *   TRUE otherwise.
   *
   * @return $this
   *
   * @throws \LogicException
   *   Thrown if custom storage is to be set to FALSE for a computed field.
   */
  public function setCustomStorage($custom_storage) {
    if (!$custom_storage && $this->isComputed()) {
      throw new \LogicException('Entity storage cannot store a computed field.');
    }
    $this->definition['custom_storage'] = $custom_storage;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isBaseField() {
    return !empty($this->definition['base_field']);
  }

  /**
   * Sets if the storage definition belongs to a base field.
   *
   * @param bool $base_field
   *   TRUE if the storage definition belongs to a base field, FALSE otherwise.
   *
   * @return $this
   */
  public function setBaseField($base_field) {
    $this->definition['base_field'] = $base_field;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getUniqueStorageIdentifier() {
    return $this->getTargetEntityTypeId() . '-' . $this->getName();
  }

  /**
   * {@inheritdoc}
   */
  public function isDeleted() {
    return !empty($this->definition['deleted']);
  }

  /**
   * Sets whether the field storage is deleted.
   *
   * @param bool $deleted
   *   Whether the field storage is deleted.
   *
   * @return $this
   */
  public function setDeleted($deleted) {
    $this->definition['deleted'] = $deleted;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isStorageRequired() {
    if (isset($this->definition['storage_required'])) {
      return !empty($this->definition['storage_required']);
    }
    // Default to the 'required' property of the field.
    return $this->isRequired();
  }

  /**
   * Sets whether the field storage is required.
   *
   * @param bool $required
   *   Whether the field storage is required.
   *
   * @return $this
   */
  public function setStorageRequired($required) {
    $this->definition['storage_required'] = $required;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setSettings(array $settings) {
    // Assign settings individually, in order to keep the current values of
    // settings not specified in $settings.
    foreach ($settings as $setting_name => $setting) {
      $this->setSetting($setting_name, $setting);
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function __sleep() {
    // Do not serialize the statically cached property definitions.
    $vars = get_object_vars($this);
    unset($vars['propertyDefinitions']);
    return array_keys($vars);
  }

  /**
   * Returns whether the field storage is locked or not.
   *
   * This method is additional to the class provided by the patch at
   * https://www.drupal.org/project/drupal/issues/2280639. It is needed because
   * code in Field UI module treats a FieldStorageDefinitionInterface as a
   * FieldStorageConfigInterface.
   *
   * @return bool
   *   TRUE if the field storage is locked.
   */
  public function isLocked() {
    return FALSE;
  }

}
