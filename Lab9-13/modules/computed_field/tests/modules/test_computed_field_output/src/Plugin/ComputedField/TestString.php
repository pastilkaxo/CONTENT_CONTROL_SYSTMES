<?php

namespace Drupal\test_computed_field_output\Plugin\ComputedField;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\computed_field\Attribute\ComputedField;
use Drupal\computed_field\Field\ComputedFieldDefinitionWithValuePluginInterface;
use Drupal\computed_field\Plugin\ComputedField\ComputedFieldBase;
use Drupal\computed_field\Plugin\ComputedField\SingleValueTrait;
use Drupal\Core\Entity\EntityInterface;

/**
 * Computed field which outputs a string which can be cached with the entity.
 */
#[ComputedField(
  id: 'test_string_attached',
  label: new TranslatableMarkup('Test string'),
  field_type: 'string',
  attach: [
    'scope' => 'base',
    'field_name' => 'test_string',
    'entity_types' => [
      'entity_test' => [],
    ],
  ],
)]
class TestString extends ComputedFieldBase {

  use SingleValueTrait;

  /**
   * {@inheritdoc}
   */
  public function singleComputeValue(EntityInterface $host_entity, ComputedFieldDefinitionWithValuePluginInterface $computed_field_definition): mixed {
    return 'cake!';
  }

}
