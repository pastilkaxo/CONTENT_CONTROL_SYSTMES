<?php

namespace Drupal\computed_field\Entity\Handler;

use Drupal\computed_field\Entity\ComputedFieldInterface;
use Drupal\Core\Config\Entity\ConfigEntityStorage;

/**
 * Provides the storage handler for the Computed Field entity.
 */
class ComputedFieldStorage extends ConfigEntityStorage {

  /**
   * Loads a computed field entity based on the entity type and field name.
   *
   * @param string $entity_type_id
   *   ID of the entity type.
   * @param string $bundle
   *   Bundle name.
   * @param string $field_name
   *   Name of the field.
   *
   * @return Drupal\computed_field\Entity\ComputedFieldInterface|null
   *   The computed field entity if one exists for the provided field
   *   name, otherwise NULL.
   */
  public function loadByName(string $entity_type_id, string $bundle, string $field_name): ?ComputedFieldInterface {
    return $this->load($entity_type_id . '.' . $bundle . '.' . $field_name);
  }

}
