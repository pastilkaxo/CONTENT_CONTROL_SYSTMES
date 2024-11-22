<?php

namespace Drupal\test_computed_field_plugins\Plugin\ComputedField;

use Drupal\computed_field\Attribute\ComputedField;
use Drupal\computed_field\Field\ComputedFieldDefinitionWithValuePluginInterface;
use Drupal\computed_field\Plugin\ComputedField\ComputedFieldBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Computed field which outputs a multi-valued string.
 */
#[ComputedField(
  id: "test_string_multiple",
  label: new TranslatableMarkup("Test string"),
  field_type: "string",
  cardinality: 2,
)]
class TestMultipleString extends ComputedFieldBase {

  /**
   * {@inheritdoc}
   */
  public function computeValue(EntityInterface $host_entity, ComputedFieldDefinitionWithValuePluginInterface $computed_field_definition): array {
    return [
      0 => 'cake',
      1 => 'pie',
    ];
  }

}
