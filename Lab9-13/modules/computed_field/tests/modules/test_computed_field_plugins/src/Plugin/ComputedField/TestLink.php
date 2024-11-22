<?php

namespace Drupal\test_computed_field_plugins\Plugin\ComputedField;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\computed_field\Attribute\ComputedField;
use Drupal\computed_field\Field\ComputedFieldDefinitionWithValuePluginInterface;
use Drupal\computed_field\Plugin\ComputedField\ComputedFieldBase;
use Drupal\computed_field\Plugin\ComputedField\SingleValueTrait;
use Drupal\Core\Entity\EntityInterface;

/**
 * Computed field which outputs a link.
 *
 * Requires link module.
 */
#[ComputedField(
  id: 'test_link',
  label: new TranslatableMarkup('Test link'),
  field_type: 'link',
)]
class TestLink extends ComputedFieldBase {

  use SingleValueTrait;

  /**
   * {@inheritdoc}
   */
  public function singleComputeValue(EntityInterface $host_entity, ComputedFieldDefinitionWithValuePluginInterface $computed_field_definition): mixed {
    return [
      'uri' => 'http://example.com',
      'title' => 'example link',
    ];
  }

}
