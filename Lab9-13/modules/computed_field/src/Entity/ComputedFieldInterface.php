<?php

namespace Drupal\computed_field\Entity;

use Drupal\computed_field\Field\ComputedFieldDefinitionWithValuePluginInterface;
use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\EntityWithPluginCollectionInterface;

/**
 * Interface for Computed Field entities.
 */
interface ComputedFieldInterface extends ConfigEntityInterface, EntityWithPluginCollectionInterface, ComputedFieldDefinitionWithValuePluginInterface {

}
