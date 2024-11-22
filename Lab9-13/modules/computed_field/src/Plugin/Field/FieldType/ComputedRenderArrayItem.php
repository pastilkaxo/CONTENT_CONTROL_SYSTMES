<?php

namespace Drupal\computed_field\Plugin\Field\FieldType;

use Drupal\Core\Field\FieldItemBase;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Defines a field type for render arrays.
 *
 * This allows a computed field to return a complete render array for its value.
 *
 * @FieldType(
 *   id = "computed_render_array",
 *   label = @Translation("Computed render array"),
 *   no_ui = TRUE,
 *   default_formatter = "computed_render_array_formatter",
 * )
 */
class ComputedRenderArrayItem extends FieldItemBase {

  /**
   * {@inheritdoc}
   */
  public static function propertyDefinitions(FieldStorageDefinitionInterface $field_definition) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public static function schema(FieldStorageDefinitionInterface $field_definition) {
    return [];
  }

}
