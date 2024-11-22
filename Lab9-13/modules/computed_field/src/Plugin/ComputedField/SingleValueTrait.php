<?php

namespace Drupal\computed_field\Plugin\ComputedField;

use Drupal\computed_field\Field\ComputedFieldDefinitionWithValuePluginInterface;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a method to return a single value for a computed field.
 */
trait SingleValueTrait {

  /**
   * Implements computeValue().
   */
  public function computeValue(EntityInterface $host_entity, ComputedFieldDefinitionWithValuePluginInterface $computed_field_definition): array {
    return [
      0 => $this->singleComputeValue($host_entity, $computed_field_definition),
    ];
  }

  /**
   * Returns the single value for a computed field.
   *
   * @param \Drupal\Core\Entity\EntityInterface $host_entity
   *   The entity the field is on.
   * @param \Drupal\computed_field\Field\ComputedFieldDefinitionWithValuePluginInterface $computed_field_definition
   *   The computed field definition. If this plugin is configured attachable
   *   rather than automatically attached, this parameter will be the
   *   computed_field config entity.
   *
   * @return mixed
   *   The computed field value. This has the same format as a single delta
   *   value in the return of computeValue().
   */
  abstract public function singleComputeValue(EntityInterface $host_entity, ComputedFieldDefinitionWithValuePluginInterface $computed_field_definition): mixed;

}
