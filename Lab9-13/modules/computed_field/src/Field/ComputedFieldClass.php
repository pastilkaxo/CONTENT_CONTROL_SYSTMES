<?php

namespace Drupal\computed_field\Field;

use Drupal\Core\Field\FieldItemList;
use Drupal\Core\TypedData\ComputedItemListTrait;

/**
 * Field item list class for our computed fields.
 *
 * Hands over to the computed field plugin for the field.
 *
 * @see computed_field_entity_view_alter()
 */
class ComputedFieldClass extends FieldItemList {

  use ComputedItemListTrait;

  use ComputedFieldComputeValueTrait;

}
