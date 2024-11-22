<?php

namespace Drupal\test_computed_field_automatic\Plugin\ComputedField;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\computed_field\Attribute\ComputedField;
use Drupal\computed_field\Field\ComputedFieldDefinitionWithValuePluginInterface;
use Drupal\computed_field\Plugin\ComputedField\ComputedFieldBase;
use Drupal\computed_field\Plugin\ComputedField\SingleValueTrait;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Automatic bundle field with dynamic attaching.
 *
 * This defines a computed field alongside any field of type 'uri'.
 */
#[ComputedField(
  id: 'test_string_dynamic_base',
  label: new TranslatableMarkup('Test String Automatic Dynamic'),
  field_type: 'string',
  no_ui: TRUE,
  attach: [
    'scope' => 'bundle',
    'dynamic' => TRUE,
  ],
)]
class TestStringDirectDynamicBundle extends ComputedFieldBase {

  use SingleValueTrait;

  /**
   * {@inheritdoc}
   */
  public function attachAsBundleField(&$fields, EntityTypeInterface $entity_type, string $bundle): void {
    /** @var \Drupal\Core\Field\FieldDefinitionInterface $field */
    foreach ($fields as $field) {
      if ($field->getType() == 'uri') {
        $derived_computed_field = $this->createComputedFieldDefinition($entity_type, $bundle);

        $derived_computed_field_name = $field->getName() . '_computed';
        // Override the name, as createComputedFieldDefinition() will have set a
        // dummy value.
        $derived_computed_field->setName($derived_computed_field_name);

        $fields[$derived_computed_field_name] = $derived_computed_field;
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldName(): ?string {
    // Gets replaced in attachAsBundleField().
    return 'dummy';
  }

  /**
   * {@inheritdoc}
   */
  public function singleComputeValue(EntityInterface $host_entity, ComputedFieldDefinitionWithValuePluginInterface $computed_field_definition): mixed {
    return 'cake!';
  }

}
