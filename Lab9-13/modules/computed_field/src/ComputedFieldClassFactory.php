<?php

namespace Drupal\computed_field;

use Drupal\computed_field\Field\ComputedFieldClass;
use Drupal\computed_field\Field\ComputedFieldEntityReferenceClass;

/**
 * Lookup for the field class to use for different field types.
 *
 * This is needed because the entity_reference field type uses a different field
 * class, and its formatters expect its interface.
 *
 * If other field types also have specialized classes, consider changing this to
 * a plugin system so it can be extended.
 */
class ComputedFieldClassFactory {

  /**
   * Gets the field item class to use for the given field type.
   *
   * @param string $field_type
   *   The field type.
   *
   * @return string
   *   The name of a class that inherits from \Drupal\Core\Field\FieldItemList
   *   to set as the field class for a computed field.
   */
  public function getFieldItemClass(string $field_type): string {
    return match($field_type) {
      'entity_reference', 'image' => ComputedFieldEntityReferenceClass::class,
      default => ComputedFieldClass::class,
    };
  }

}
