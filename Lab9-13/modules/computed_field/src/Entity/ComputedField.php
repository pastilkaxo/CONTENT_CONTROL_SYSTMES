<?php

namespace Drupal\computed_field\Entity;

use Drupal\computed_field\Field\ComputedFieldSettingsTrait;
use Drupal\computed_field\Field\FieldStorageDefinition;
use Drupal\computed_field\Plugin\ComputedField\ComputedFieldPluginInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\FieldConfigBase;
use Drupal\Core\Plugin\DefaultSingleLazyPluginCollection;
use Drupal\field\FieldConfigInterface;

/**
 * Provides the Computed Field entity.
 *
 * This class implements \Drupal\field\FieldConfigInterface so that the
 * field_config list builder \Drupal\field_ui\FieldConfigListBuilder includes it
 * in 'Manage fields' field UI pages. This requires a few minor hacks in various
 * places to make it work.
 *
 * @ConfigEntityType(
 *   id = "computed_field",
 *   label = @Translation("Computed Field"),
 *   label_collection = @Translation("Computed Fields"),
 *   label_singular = @Translation("computed field"),
 *   label_plural = @Translation("computed fields"),
 *   label_count = @PluralTranslation(
 *     singular = "@count computed field",
 *     plural = "@count computed fields",
 *   ),
 *   handlers = {
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     },
 *     "storage" = "Drupal\computed_field\Entity\Handler\ComputedFieldStorage",
 *   },
 *   admin_permission = "administer computed_field entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "field_name",
 *     "entity_type",
 *     "bundle",
 *     "plugin_id",
 *     "plugin_config",
 *   },
 * )
 */
class ComputedField extends FieldConfigBase implements
    ComputedFieldInterface,
    FieldConfigInterface {

  use ComputedFieldSettingsTrait;

  /**
   * The field ID.
   *
   * The ID consists of the following pieces:
   *  - the entity type
   *  - the bundle
   *  - the field name. For fields created in the admin UI, this is prefixed
   *    with 'computed_'.
   *
   * Example: node.article.computed_myfield, user.user.computed_myfield.
   *
   * @var string
   *
   * @see static::id()
   */
  protected $id;

  /**
   * The field name.
   *
   * @var string
   */
  protected $field_name;

  /**
   * The field lame.
   *
   * @var string
   */
  protected $label = '';

  /**
   * The entity type this field is attached to.
   *
   * @var string
   */
  protected $entity_type = '';

  /**
   * The name of the bundle the field is attached to.
   *
   * @var string
   */
  protected $bundle;

  /**
   * The field value plugin ID.
   *
   * @var string
   */
  protected $plugin_id;

  /**
   * The field value plugin configuration.
   *
   * @var array
   */
  protected $plugin_config = [];

  /**
   * The plugin collection.
   *
   * @var \Drupal\Component\Plugin\DefaultSingleLazyPluginCollection
   */
  protected $pluginCollection = NULL;

  /**
   * {@inheritdoc}
   */
  public function id() {
    // This method *defines* rather than *supplies* the entity ID, as it is
    // called during the entity creation process, by
    // ConfigEntityBase::__construct(). This is the same pattern used by the
    // field_config and field_storage_config entity types.
    return $this->entity_type . '.' . $this->bundle . '.' . $this->field_name;
  }

  /**
   * {@inheritdoc}
   */
  public function getTargetEntityTypeId(): string {
    return $this->entity_type;
  }

  /**
   * {@inheritdoc}
   */
  public function getTargetBundle(): string {
    return $this->bundle;
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return $this->getFieldValuePlugin()->getFieldType();
  }

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->field_name;
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldValuePlugin(): ComputedFieldPluginInterface {
    return $this->getPluginCollection()->get($this->plugin_id);
  }

  /**
   * {@inheritdoc}
   */
  public function getPluginCollections() {
    $collections = [];
    if ($this->getPluginCollection()) {
      $collections['plugin_config'] = $this->getPluginCollection();
    }
    return $collections;
  }

  /**
   * Encapsulates the creation of the computed field's plugin collection.
   *
   * @return \Drupal\Component\Plugin\DefaultSingleLazyPluginCollection
   *   The computed field's plugin collection.
   */
  protected function getPluginCollection() {
    if (!$this->pluginCollection && $this->plugin_id && !is_null($this->plugin_config)) {
      $this->pluginCollection = new DefaultSingleLazyPluginCollection(
        \Drupal::service('plugin.manager.computed_field'),
        $this->plugin_id,
        $this->plugin_config
      );
    }
    return $this->pluginCollection;
  }

  /**
   * {@inheritdoc}
   */
  public function postCreate(EntityStorageInterface $storage) {
    // DIRTY HACK! DO NOTHING!
    // This is needed because when going to the 'add' form, there is no plugin
    // yet, but parent::postCreate() expects one to exist.
    // This will break things if EntityBase::postCreate() ever adds code.
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    // Notify the entity storage. This ensures the field is added to the field
    // map.
    if ($this->isNew()) {
      \Drupal::service('field_definition.listener')->onFieldDefinitionCreate($this);
    }
    else {
      \Drupal::service('field_definition.listener')->onFieldDefinitionUpdate($this, $this->original);
    }

    parent::preSave($storage);
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    // We rely on the parent class's postSave() to clear field definitions.
    parent::postSave($storage, $update);
  }

  /**
   * {@inheritdoc}
   */
  public static function postDelete(EntityStorageInterface $storage, array $entities) {
    // The parent class doesn't handle clearing caches on delete.
    // See https://www.drupal.org/project/drupal/issues/3336639.
    // Clear the cache upfront, to refresh the results of getBundles().
    \Drupal::service('entity_field.manager')->clearCachedFieldDefinitions();

    // Notify the entity storage.
    foreach ($entities as $entity) {
      \Drupal::service('field_definition.listener')->onFieldDefinitionDelete($entity);
    }

    // @todo Deleting a computed field entity leaves behind the configuration in
    // the entity view display. See
    // https://www.drupal.org/project/drupal/issues/3016895.
  }

  /**
   * {@inheritdoc}
   */
  public function getClass() {
    return \Drupal::service('computed_field.computed_field_class_factory')->getFieldItemClass($this->getType());
  }

  /**
   * {@inheritdoc}
   */
  public function isComputed() {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function isReadOnly() {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getDisplayOptions($display_context) {
    // Hide configurable fields by default. The display option is set to the
    // default formatter when a new computed_field entity is saved in
    // \Drupal\computed_field\Form\ComputedFieldForm\configureEntityViewDisplay().
    return ['region' => 'hidden'];
  }

  /**
   * {@inheritdoc}
   */
  public function isDisplayConfigurable($context) {
    return match ($context) {
      'view' => TRUE,
      'form' => FALSE,
    };
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldStorageDefinition() {
    $plugin = $this->getFieldValuePlugin();
    // Create a dummy with just the essentials.
    $definition = FieldStorageDefinition::create($this->getType())
      ->setName($this->getName())
      ->setComputed(TRUE)
      ->setCardinality($plugin->getFieldCardinality())
      ->setTargetEntityTypeId($this->entity_type);

    // Get settings from the computed field plugin.
    $settings = $this->getSettings();
    $definition->setSettings($settings);

    return $definition;
  }

  /**
   * {@inheritdoc}
   */
  public function getUniqueIdentifier() {
    return $this->getTargetEntityTypeId() . '-' . $this->getTargetBundle() . '-' . $this->getName();
  }

  /**
   * {@inheritdoc}
   */
  public function isDeleted() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  protected function linkTemplates() {
    $link_templates["{$this->entity_type}-field-edit-form"] = 'entity.field_config.' . $this->entity_type . '_field_edit_form';
    $link_templates["{$this->entity_type}-field-delete-form"] = 'entity.field_config.' . $this->entity_type . '_field_delete_form';

    // This link template needs to exist because
    // \Drupal\field_ui\FieldConfigListBuilder assumes its presence.
    $link_templates["{$this->entity_type}-storage-edit-form"] = 'entity.field_config.' . $this->entity_type . '_storage_edit_form';

    return $link_templates;
  }

  /**
   * {@inheritdoc}
   */
  protected function urlRouteParameters($rel) {
    $parameters = parent::urlRouteParameters($rel);
    $entity_type = \Drupal::entityTypeManager()->getDefinition($this->entity_type);
    $bundle_parameter_key = $entity_type->getBundleEntityType() ?: 'bundle';
    $parameters[$bundle_parameter_key] = $this->bundle;
    return $parameters;
  }

}
