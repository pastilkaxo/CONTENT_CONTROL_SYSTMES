<?php

namespace Drupal\computed_field\Attribute;

use Drupal\Component\Plugin\Attribute\Plugin;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * Defines a Computed Field attribute object.
 *
 * Plugin namespace: ComputedField.
 */
#[\Attribute(
  \Attribute::TARGET_CLASS,
)]
class ComputedField extends Plugin {

  /**
   * Constructs a ComputedField attribute.
   *
   * @param string $id
   *   The plugin ID.
   * @param \Drupal\Core\StringTranslation\TranslatableMarkup $label
   *   The plugin label.
   * @param string $field_type
   *   The field type of the computed fields this plugin defines. There is no
   *   data stored, but using existing types allows the computed field to use
   *   existing field formatter plugins.
   * @param bool $no_ui
   *   (optional) A boolean that defines whether fields using this plugin can be
   *   created in the UI. Defaults to FALSE.
   * @param int $cardinality
   *   (optional) The cardinality of the field. Defaults to 1.
   * @param array|null $attach
   *   (optional) Automatic attachment definition. If specified, this array must
   *   contain:
   *    - scope: The field scope. One of 'base' or 'bundle'.
   *    - field_name: (optional) The field name. If not specified, the plugin
   *      class must override getFieldName() to provide the field name.
   *    - entity_types: (optional) An array of the entity types and bundles to
   *      attach to. Array keys are entity type IDs. If the scope is 'bundle',
   *      array values are arrays of bundle names. If the scope is 'base', the
   *      array values should be just empty arrays. Entity types and bundles
   *      that do not exist are ignored: it is safe to specify an entity type or
   *      bundles that might not be installed. If not specified, the 'dynamic'
   *      property must be TRUE.
   *    - dynamic: (optional) A boolean specifying that the automatic fields for
   *      this plugin are determined dynamically. If this is set, 'entity_types'
   *      should not be set, and the plugin class must override either of
   *      attachAsBaseField() or attachAsBundleField() to provide its own logic.
   */
  public function __construct(
    public readonly string $id,
    public readonly TranslatableMarkup $label,
    public readonly string $field_type,
    public readonly bool $no_ui = FALSE,
    public readonly int $cardinality = 1,
    public readonly ?array $attach = NULL,
  ) {
  }

}
