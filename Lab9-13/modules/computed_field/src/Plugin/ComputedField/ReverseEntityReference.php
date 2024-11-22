<?php

namespace Drupal\computed_field\Plugin\ComputedField;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Component\Plugin\ConfigurableInterface;
use Drupal\computed_field\Attribute\ComputedField;
use Drupal\computed_field\Field\ComputedFieldDefinitionWithValuePluginInterface;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Provides a reverse entity reference computed field.
 */
#[ComputedField(
  id: 'reverse_entity_reference',
  label: new TranslatableMarkup('Reverse entity reference'),
  field_type: 'entity_reference',
)]
class ReverseEntityReference extends ComputedFieldBase implements PluginFormInterface, ConfigurableInterface {

  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function computeValue(EntityInterface $host_entity, ComputedFieldDefinitionWithValuePluginInterface $computed_field_definition): array {
    [$referencing_entity_type_id, $field_name] = explode('-', $this->configuration['reference_field']);

    $query = \Drupal::service('entity_type.manager')->getStorage($referencing_entity_type_id)->getQuery();
    $query
      ->accessCheck(TRUE)
      ->condition($field_name, $host_entity->id());
    $result = $query->execute();

    return array_values($result);
  }

  /**
   * {@inheritdoc}
   */
  public function useLazyBuilder(EntityInterface $host_entity, ComputedFieldDefinitionWithValuePluginInterface $computed_field_definition): bool {
    // Our field has a cache dependency not only on the entities it shows, but
    // on the referring entity type as a whole (that is, the ENTITY_TYPE_list
    // cache tag) because a new entity might need to be included.
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheability(EntityInterface $host_entity, ComputedFieldDefinitionWithValuePluginInterface $computed_field_definition): ?CacheableMetadata {
    $cacheability = new CacheableMetadata();

    [$referencing_entity_type_id, $field_name] = explode('-', $this->configuration['reference_field']);
    $referencing_entity_type = \Drupal::service('entity_type.manager')->getDefinition($referencing_entity_type_id);

    $cacheability->addCacheTags($referencing_entity_type->getListCacheTags());

    foreach ($host_entity->get($computed_field_definition->getName())->referencedEntities() as $entity) {
      $cacheability->addCacheableDependency($entity);
    }

    return $cacheability;
  }

  /**
   * {@inheritdoc}
   */
  public function getStorageDefinitionSettings(): array {
    [$referencing_entity_type_id, ] = explode('-', $this->configuration['reference_field']);

    return [
      'target_type' => $referencing_entity_type_id,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    // Find all fields that point to the computed entity's host entity bundle.
    // We have to inspect the computed_field entity, and first get it from the
    // form object. This makes the assumption that this plugin is being used
    // within a ComputedEntityForm.
    $form_object = $form_state->getFormObject();
    $computed_field_entity = $form_object->getEntity();
    $referencing_entity_type_id = $computed_field_entity->getTargetEntityTypeId();
    $entity_type_manager = \Drupal::service('entity_type.manager');

    $field_map = \Drupal::service('entity_field.manager')->getFieldMapByFieldType('entity_reference');
    $options = [];
    foreach ($field_map as $entity_type_id => $entity_type_map_data) {
      $field_storage_definitions = \Drupal::service('entity_field.manager')->getFieldStorageDefinitions($entity_type_id);
      foreach ($entity_type_map_data as $field_name => $field_map_data) {
        if (!isset($field_storage_definitions[$field_name])) {
          continue;
        }

        $field_storage_definition = $field_storage_definitions[$field_name];

        if ($field_storage_definition->getSettings()['target_type'] != $referencing_entity_type_id) {
          continue;
        }

        $options[$entity_type_id . '-' . $field_name] = $this->t("%field on %entity-type entities", [
          // @todo Get the label from one of the field configs if the storage is
          // a config entity.
          '%field' => $field_storage_definition->getLabel(),
          '%entity-type' => $entity_type_manager->getDefinition($entity_type_id)->getLabel(),
        ]);
      }
    }

    natcasesort($options);

    $form['reference_field'] = [
      '#type' => 'select',
      '#title' => $this->t("Entity reference field"),
      '#description' => $this->t("The reference field that will be queried for entities that point to the current entity."),
      '#options' => $options,
      '#empty_value' => '',
      '#required' => TRUE,
    ];
    if (empty($options)) {
      $form['reference_field']['#empty_option'] = $this->t("There are no reference fields that point to this entity bundle.");
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {

  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      // The referencing field, in the format HOST_ENTITY_TYPE-FIELD_NAME. For
      // example, 'node-uid'.
      'reference_field' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    $this->configuration = $configuration + $this->defaultConfiguration();
  }

}
