<?php

namespace Drupal\computed_field_ui\Entity\Handler;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeBundleInfoInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldTypePluginManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the list builder handler for the Computed Field entity.
 */
class ComputedFieldListBuilder extends ConfigEntityListBuilder {

  /**
   * An array of information about field types.
   *
   * @var array
   */
  protected $fieldTypes;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * An array of entity bundle information.
   *
   * @var array
   */
  protected $bundles;

  /**
   * The field type manager.
   *
   * @var \Drupal\Core\Field\FieldTypePluginManagerInterface
   */
  protected $fieldTypeManager;

  /**
   * Constructs a new FieldStorageConfigListBuilder object.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Field\FieldTypePluginManagerInterface $field_type_manager
   *   The 'field type' plugin manager.
   * @param \Drupal\Core\Entity\EntityTypeBundleInfoInterface $bundle_info_service
   *   The bundle info service.
   */
  public function __construct(
    EntityTypeInterface $entity_type,
    EntityTypeManagerInterface $entity_type_manager,
    FieldTypePluginManagerInterface $field_type_manager,
    EntityTypeBundleInfoInterface $bundle_info_service,
  ) {
    parent::__construct($entity_type, $entity_type_manager->getStorage($entity_type->id()));

    $this->entityTypeManager = $entity_type_manager;
    $this->bundles = $bundle_info_service->getAllBundleInfo();
    $this->fieldTypeManager = $field_type_manager;
    $this->fieldTypes = $this->fieldTypeManager->getDefinitions();
    $this->limit = FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity_type.manager'),
      $container->get('plugin.manager.field.field_type'),
      $container->get('entity_type.bundle.info')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header = [];
    $header['name'] = $this->t('Field name');
    $header['entity_type'] = $this->t('Entity type');
    $header['field_type'] = $this->t('Field type');
    $header['plugin'] = $this->t('Plugin');
    return $header;
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\computed_field\Entity\ComputedFieldInterface Drupal\field\FieldConfigInterface $entity */
    $row = [];
    $row['name'] = $entity->label();
    $row['entity_type'] = $this->entityTypeManager->getDefinition($entity->getTargetEntityTypeId())->getLabel();
    $row['field_type'] = $this->fieldTypes[$entity->getType()]['label'];
    $row['plugin'] = $entity->getFieldValuePlugin()->getPluginDefinition()['label'];
    return $row;
  }

}
