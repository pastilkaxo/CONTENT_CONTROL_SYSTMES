<?php

namespace Drupal\computed_field\Field;

use Drupal\computed_field\Plugin\ComputedField\ComputedFieldPluginInterface;

/**
 * Defines an interface for computed field definitions which use a plugin.
 *
 * This interface allows detection of both of our kinds of computed field
 * definition: config entity or plain definition class. This is needed to switch
 * field formatter render arrays to lazy builders in
 * computed_field_entity_view_alter().
 */
interface ComputedFieldDefinitionWithValuePluginInterface {

  /**
   * Gets the field's field value plugin.
   *
   * @return \Drupal\computed_field\Plugin\ComputedField\ComputedFieldPluginInterface
   *   The field value plugin.
   */
  public function getFieldValuePlugin(): ComputedFieldPluginInterface;

}
