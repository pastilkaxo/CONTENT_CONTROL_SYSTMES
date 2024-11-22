<?php

namespace Drupal\computed_field_ui\Form;

use Drupal\Core\Entity\EntityDeleteForm;
use Drupal\field_ui\FieldUI;

/**
 * Provides the delete form handler for the Computed Field entity.
 */
class ComputedFieldDeleteForm extends EntityDeleteForm {

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return FieldUI::getOverviewRouteInfo($this->entity->getTargetEntityTypeId(), $this->entity->getTargetBundle());
  }

}
