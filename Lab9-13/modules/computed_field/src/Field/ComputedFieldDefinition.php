<?php

namespace Drupal\computed_field\Field;

use Drupal\computed_field\Plugin\ComputedField\ComputedFieldPluginInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Field definition for automatic bundle and base computed fields.
 *
 * This class allows detection of our computed fields in
 * computed_field_entity_view_alter().
 *
 * @todo When https://www.drupal.org/node/2346347 is fixed, this should be split
 * between this class and a new class that inherits from BundleFieldDefinition.
 */
class ComputedFieldDefinition extends BaseFieldDefinition implements ComputedFieldDefinitionWithValuePluginInterface {

  use ComputedFieldSettingsTrait;

  /**
   * The field value plugin ID.
   *
   * @var string
   */
  protected $fieldValuePluginId;

  /**
   * {@inheritdoc}
   */
  public function isBaseField() {
    return ($this->getSetting('scope') == 'base');
  }

  /**
   * Sets the field value plugin for the field.
   *
   * @param \Drupal\computed_field\Plugin\ComputedField\ComputedFieldPluginInterface $plugin
   *   The field value plugin.
   *
   * @return static
   */
  public function setFieldValuePlugin(ComputedFieldPluginInterface $plugin): static {
    $this->fieldValuePluginId = $plugin->getPluginId();

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldValuePlugin(): ComputedFieldPluginInterface {
    // Automatic plugins have no configuration.
    return \Drupal::service('plugin.manager.computed_field')->createInstance($this->fieldValuePluginId);
  }

  /**
   * {@inheritdoc}
   */
  public function isComputed() {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function isReadOnly() {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getClass() {
    return \Drupal::service('computed_field.computed_field_class_factory')->getFieldItemClass($this->getType());
  }

  /**
   * {@inheritdoc}
   */
  public function getProvider() {
    return 'computed_field';
  }

  /**
   * {@inheritdoc}
   */
  public function getDisplayOptions($display_context) {
    // Hide fields on form displays.
    if ($display_context == 'form') {
      return ['region' => 'hidden'];
    }

    return parent::getDisplayOptions($display_context);
  }

  /**
   * {@inheritdoc}
   */
  public function isDisplayConfigurable($context) {
    return match ($context) {
      'view' => TRUE,
      'form' => FALSE,
    };
  }

}
