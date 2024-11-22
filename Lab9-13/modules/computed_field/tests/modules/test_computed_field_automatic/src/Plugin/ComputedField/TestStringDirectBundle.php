<?php

namespace Drupal\test_computed_field_automatic\Plugin\ComputedField;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\computed_field\Attribute\ComputedField;
use Drupal\computed_field\Field\ComputedFieldDefinitionWithValuePluginInterface;
use Drupal\computed_field\Plugin\ComputedField\SingleValueTrait;
use Drupal\computed_field\Plugin\ComputedField\ComputedFieldBase;
use Drupal\Core\Entity\EntityInterface;

/**
 * Automatic bundle field.
 */
#[ComputedField(
  id: 'test_string_automatic_bundle',
  label: new TranslatableMarkup('Test String Automatic'),
  field_type: 'string',
  no_ui: TRUE,
  attach: [
    'scope' => 'bundle',
    'field_name' => 'test_automatic_bundle',
    'entity_types' => ['entity_test_with_bundle' => ['alpha']]
  ],
)]
class TestStringDirectBundle extends ComputedFieldBase {

  use SingleValueTrait;

  /**
   * {@inheritdoc}
   */
  public function singleComputeValue(EntityInterface $host_entity, ComputedFieldDefinitionWithValuePluginInterface $computed_field_definition): mixed {
    return 'cake!';
  }

}
