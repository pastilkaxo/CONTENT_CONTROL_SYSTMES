<?php

namespace Drupal\test_computed_field_automatic\Plugin\ComputedField;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\computed_field\Attribute\ComputedField;
use Drupal\computed_field\Field\ComputedFieldDefinitionWithValuePluginInterface;
use Drupal\computed_field\Plugin\ComputedField\ComputedFieldBase;
use Drupal\computed_field\Plugin\ComputedField\SingleValueTrait;
use Drupal\Core\Entity\EntityInterface;

/**
 * Automatic base field.
 */
#[ComputedField(
  id: 'test_string_automatic_base',
  label: new TranslatableMarkup('Test String Automatic'),
  field_type: 'string',
  no_ui: TRUE,
  attach: [
    'scope' => 'base',
    'field_name' => 'test_automatic_base',
    'entity_types' => ['entity_test_with_bundle' => []],
  ],
)]
class TestStringDirectBase extends ComputedFieldBase {

  use SingleValueTrait;

  /**
   * {@inheritdoc}
   */
  public function singleComputeValue(EntityInterface $host_entity, ComputedFieldDefinitionWithValuePluginInterface $computed_field_definition): mixed {
    return 'cake!';
  }

}
