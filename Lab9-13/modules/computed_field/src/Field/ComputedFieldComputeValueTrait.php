<?php

namespace Drupal\computed_field\Field;

/**
 * Trait for our field classes.
 *
 * This is needed because we need to supply different field classes for
 * different field types, which inherit from different classes, but whose
 * implementation of computeValue() does not need to differ.
 */
trait ComputedFieldComputeValueTrait {

  /**
   * Implements \Drupal\Core\TypedData\ComputedItemListTrait::computeValue().
   */
  protected function computeValue() {
    /** @var \Drupal\computed_field\Field\ComputedFieldDefinitionWithValuePluginInterface $field_definition */
    $field_definition = $this->getFieldDefinition();

    $computed_field_plugin = $field_definition->getFieldValuePlugin();

    $host_entity = $this->getParent()->getValue();

    // The field definition is either a computed field entity, or a
    // ComputedFieldDefinition.
    $field_values = $computed_field_plugin->computeValue($host_entity, $field_definition);

    foreach ($field_values as $index => $value) {
      $this->list[$index] = $this->createItem($index, $value);
    }
  }

}
