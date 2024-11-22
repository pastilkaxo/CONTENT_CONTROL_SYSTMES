<?php

namespace Drupal\computed_field_ui\Form;

use Drupal\computed_field\Entity\ComputedFieldInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityDisplayRepositoryInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\field_ui\FieldUI;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Field\FormatterPluginManager;

/**
 * Provides the default form handler for the Computed Field entity.
 */
class ComputedFieldForm extends EntityForm {

  /**
   * The name of the entity type.
   *
   * @var string
   */
  protected $entityTypeId;

  /**
   * The entity bundle.
   *
   * @var string
   */
  protected $bundle;

  /**
   * The field type plugin manager.
   *
   * @var \Drupal\Core\Field\FieldTypePluginManagerInterface
   */
  protected $fieldTypePluginManager;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * The entity type bundle info service.
   *
   * @var \Drupal\Core\Entity\EntityTypeBundleInfoInterface
   */
  protected $entityTypeBundleInfo;

  /**
   * The formatter manager.
   *
   * @var \Drupal\Core\Field\FormatterPluginManager
   */
  protected $formatterManager;

   /**
   * The entity display repository.
   *
   * @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface
   */
  protected $entityDisplayRepository;

  /**
   * Constructs a new ComputedFieldForm object.
   *
   * @param \Drupal\Core\Field\FieldTypePluginManagerInterface $field_type_plugin_manager
   *   The field type plugin manager.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The entity field manager.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $entity_type_bundle_info
   *   The entity type bundle info service.
   * @param \Drupal\Core\Field\FormatterPluginManager $formatter_manager
   *   The formatter manager.
   * @param \Drupal\Core\Entity\EntityRepositoryInterface $entity_repository
   *   The entity repository service.
   */
  public function __construct(
    FieldTypePluginManagerInterface $field_type_plugin_manager,
    EntityFieldManagerInterface $entity_field_manager,
    EntityTypeBundleInfoInterface $entity_type_bundle_info,
    FormatterPluginManager $formatter_manager,
    EntityDisplayRepositoryInterface $entity_display_repository,
  ) {
    $this->fieldTypePluginManager = $field_type_plugin_manager;
    $this->entityFieldManager = $entity_field_manager;
    $this->entityTypeBundleInfo = $entity_type_bundle_info;
    $this->formatterManager = $formatter_manager;
    $this->entityDisplayRepository = $entity_display_repository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.field.field_type'),
      $container->get('entity_field.manager'),
      $container->get('entity_type.bundle.info'),
      $container->get('plugin.manager.field.formatter'),
      $container->get('entity_display.repository')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $entity_type_id = NULL, $bundle = NULL) {
    if (!$form_state->get('entity_type_id')) {
      $form_state->set('entity_type_id', $entity_type_id);
    }
    if (!$form_state->get('bundle')) {
      $form_state->set('bundle', $bundle);
    }

    $this->entityTypeId = $form_state->get('entity_type_id');
    $this->bundle = $form_state->get('bundle');

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    if (!$this->entity->isNew()) {
      $bundles = $this->entityTypeBundleInfo->getBundleInfo($this->entity->getTargetEntityTypeId());
      $form['#title'] = $this->t('%field settings for %bundle', [
        '%field' => $this->entity->getLabel(),
        '%bundle' => $bundles[$this->entity->getTargetBundle()]['label'],
      ]);
    }

    $form['label'] = [
      '#type' => "textfield",
      '#title' => $this->t("Label"),
      '#description' => $this->t("The label for this computed field."),
      '#default_value' => $this->entity->get('label'),
    ];

    $field_prefix = $this->config('computed_field.settings')->get('field_prefix');

    // @todo Show this on existing fields to allow changing the machine name.
    // Doing this is currently buggy because the #field_prefix gets re-added
    // each time the form is saved. This is maybe a bug in the machine name
    // element?
    if ($this->entity->isNew()) {
      $form['field_name'] = [
        '#type' => "machine_name",
        '#field_prefix' => $field_prefix,
        '#default_value' => $this->entity->getName(),
        '#title' => $this->t("Name"),
        '#description' => $this->t("A unique machine-readable name for this computed field. It must only contain lowercase letters, numbers, and underscores."),
        '#machine_name' => [
          'exists' => [$this, 'fieldNameExists'],
          'source' => ['label'],
        ],
      ];
    }

    // Gather valid field types.
    $field_type_options = [];
    foreach ($this->fieldTypePluginManager->getGroupedDefinitions($this->fieldTypePluginManager->getUiDefinitions()) as $category => $field_types) {
      foreach ($field_types as $name => $field_type) {
        $field_type_options[$category][$name] = $field_type['label'];
      }
    }

    $form['plugin'] = [
      '#type' => "computed_field",
      '#title' => $this->t("Computed value source"),
      '#plugins_method' => 'getUiDefinitions',
      '#required' => TRUE,
      '#default_value' => [
        'plugin_id' => $this->entity->get('plugin_id'),
        'plugin_configuration' => $this->entity->get('plugin_config'),
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);

    $actions['submit']['#value'] = $this->t('Save settings');

    if (!$this->entity->isNew()) {
      $target_entity_type = $this->entityTypeManager->getDefinition($this->entity->getTargetEntityTypeId());
      $route_parameters = [
        'computed_field' => $this->entity->id(),
      ] + FieldUI::getRouteBundleParameter($target_entity_type, $this->entity->getTargetBundle());
      $url = new Url('entity.computed_field.' . $target_entity_type->id() . '_field_delete_form', $route_parameters);

      if ($this->getRequest()->query->has('destination')) {
        $query = $url->getOption('query');
        $query['destination'] = $this->getRequest()->query->get('destination');
        $url->setOption('query', $query);
      }
      $actions['delete'] = [
        '#type' => 'link',
        '#title' => $this->t('Delete'),
        '#url' => $url,
        '#access' => $this->entity->access('delete'),
        '#attributes' => [
          'class' => ['button', 'button--danger'],
        ],
      ];
    }

    return $actions;
  }

  /**
   * Checks if a field machine name is taken.
   *
   * @param string $value
   *   The machine name, not prefixed.
   * @param array $element
   *   An array containing the structure of the 'field_name' element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return bool
   *   Whether or not the field machine name is taken.
   */
  public function fieldNameExists($value, array $element, FormStateInterface $form_state) {
    // Add the field prefix.
    $field_prefix = $this->config('computed_field.settings')->get('field_prefix');
    $field_name = $field_prefix . $value;

    $field_storage_definitions = $this->entityFieldManager->getFieldDefinitions($this->entityTypeId, $form_state->get('bundle'));
    return isset($field_storage_definitions[$field_name]);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    // Add the field prefix on a new field.
    if ($this->entity->isNew()) {
      $field_name = $form_state->getValue('field_name');
      $field_prefix = $this->config('computed_field.settings')->get('field_prefix');

      $field_name = $field_prefix . $field_name;
      $form_state->setValueForElement($form['field_name'], $field_name);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    if ($this->entity->isNew()) {
      $this->configureEntityViewDisplay($this->entity->get('field_name'));
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function copyFormValuesToEntity(EntityInterface $entity, array $form, FormStateInterface $form_state) {
    parent::copyFormValuesToEntity($entity, $form, $form_state);

    $entity->set('entity_type', $form_state->get('entity_type_id'));

    $entity->set('bundle', $form_state->get('bundle'));

    if ($form_state->getValue(['plugin', 'plugin_id'])) {
      $entity->set('plugin_id', $form_state->getValue(['plugin', 'plugin_id']));
      $entity->set('plugin_config', $form_state->getValue(['plugin', 'plugin_configuration']) ?? []);

      // Get the field type from the plugin definition.
      $computed_field_plugin = $entity->getFieldValuePlugin();
      $entity->set('field_type', $computed_field_plugin->getFieldType());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $saved = parent::save($form, $form_state);

    $this->messenger()->addStatus($this->t('Saved %label configuration.', ['%label' => $this->entity->getLabel()]));

    $form_state->setRedirectUrl(FieldUI::getOverviewRouteInfo($this->entity->getTargetEntityTypeId(), $this->entity->getTargetBundle()));

    return $saved;
  }

  /**
   * Configures the field for the default view mode.
   *
   * This follows the same pattern as config fields in
   * \Drupal\field_ui\Form\FieldStorageAddForm.
   *
   * @param string $field_name
   *   The field name.
   */
  protected function configureEntityViewDisplay($field_name) {
    // Make sure the field is displayed in the 'default' view mode (using
    // default formatter and settings). It stays hidden for other view
    // modes until it is explicitly configured.
    // We need to call this ourselves, as the view display's setComponent()
    // method won't set defaults.
    $options = $this->formatterManager->prepareConfiguration($this->entity->getType(), []);

    $this->entityDisplayRepository->getViewDisplay($this->entityTypeId, $this->bundle)
      ->setComponent($field_name, $options)
      ->save();
  }

}
