<?php

namespace Drupal\test_computed_field_output\Plugin\ComputedField;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\computed_field\Attribute\ComputedField;
use Drupal\computed_field\Field\ComputedFieldDefinitionWithValuePluginInterface;
use Drupal\computed_field\Plugin\ComputedField\ComputedFieldBase;
use Drupal\Core\Entity\EntityInterface;

/**
 * Computed field which outputs a multiple value which is cacheable.
 */
#[ComputedField(
  id: 'test_multiple_string_attached',
  label: new TranslatableMarkup('Test multiple string'),
  field_type: 'string',
  attach: [
    'scope' => 'base',
    'field_name' => 'test_multiple_string',
    'entity_types' => ['entity_test' => []],
  ],
)]
class TestMultipleString extends ComputedFieldBase {

  /**
   * {@inheritdoc}
   */
  public function computeValue(EntityInterface $host_entity, ComputedFieldDefinitionWithValuePluginInterface $computed_field_definition): array {
    return [
      'cake!',
      'pie!',
    ];
  }

}
