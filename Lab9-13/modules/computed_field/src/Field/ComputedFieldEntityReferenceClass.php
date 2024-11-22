<?php

namespace Drupal\computed_field\Field;

use Drupal\Core\Field\EntityReferenceFieldItemList;
use Drupal\Core\TypedData\ComputedItemListTrait;

/**
 * Field item list class for our computed fields of type entity_reference.
 *
 * Hands over to the computed field plugin for the field.
 *
 * @see computed_field_entity_view_alter()
 */
class ComputedFieldEntityReferenceClass extends EntityReferenceFieldItemList {

  use ComputedItemListTrait;

  use ComputedFieldComputeValueTrait;

}
