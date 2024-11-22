<?php

namespace Drupal\Tests\computed_field\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Tests that computed fields are registered with the field system.
 *
 * @group computed_field
 */
class ComputedFieldAttachingKernelTest extends KernelTestBase {

  /**
   * The modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'system',
    'user',
    'field',
    'entity_test',
    'computed_field',
    'test_computed_field_plugins',
    'test_computed_field_automatic',
  ];

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * The entity display repository service.
   *
   * @var \Drupal\Core\Entity\EntityDisplayRepositoryInterface
   */
  protected $entityDisplayRepository;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installEntitySchema('entity_test_with_bundle');

    $this->entityTypeManager = $this->container->get('entity_type.manager');
    $this->entityFieldManager = $this->container->get('entity_field.manager');
    $this->entityDisplayRepository = $this->container->get('entity_display.repository');

    // Create bundles.
    $entity_test_bundle_storage = $this->entityTypeManager->getStorage('entity_test_bundle');
    foreach (['alpha', 'beta'] as $bundle) {
      $entity_test_bundle_storage->create([
        'id' => $bundle,
      ])->save();
    }
  }

  /**
   * Tests that computed fields are registered as entity fields.
   */
  public function testComputedFields() {
    // Test field from config entity.
    $computed_field_storage = $this->entityTypeManager->getStorage('computed_field');

    $computed_field = $computed_field_storage->create([
      'field_name' => 'test_bundle',
      'label' => 'Test',
      'plugin_id' => 'test_string',
      'entity_type' => 'entity_test_with_bundle',
      'bundle' => 'alpha',
    ]);
    $computed_field->save();

    $this->assertEquals('entity_test_with_bundle.alpha.test_bundle', $computed_field->id());

    $this->assertEquals([0 => 'entity_test.entity_test_bundle.alpha'], $computed_field->getDependencies()['config']);
    $this->assertEquals([0 => 'test_computed_field_plugins'], $computed_field->getDependencies()['module']);

    $this->assertArrayHasKey('test_bundle', $this->entityFieldManager->getFieldDefinitions('entity_test_with_bundle', 'alpha'));
    $this->assertArrayNotHasKey('test_bundle', $this->entityFieldManager->getFieldDefinitions('entity_test_with_bundle', 'beta'));

    $this->assertArrayHasKey('test_bundle', $this->entityFieldManager->getFieldMap()['entity_test_with_bundle']);

    // Automatic plugins base fields.
    $this->assertArrayHasKey('test_automatic_base', $this->entityFieldManager->getFieldDefinitions('entity_test_with_bundle', 'alpha'));
    $this->assertArrayHasKey('test_automatic_base', $this->entityFieldManager->getFieldDefinitions('entity_test_with_bundle', 'beta'));

    $this->assertArrayNotHasKey('test_automatic_base_unused', $this->entityFieldManager->getFieldDefinitions('entity_test_with_bundle', 'alpha'));
    $this->assertArrayNotHasKey('test_automatic_base_unused', $this->entityFieldManager->getFieldDefinitions('entity_test_with_bundle', 'beta'));

    $this->assertArrayHasKey('test_automatic_base', $this->entityFieldManager->getFieldMap()['entity_test_with_bundle']);

    // Automatic plugins bundle fields.
    $this->assertArrayHasKey('test_automatic_bundle', $this->entityFieldManager->getFieldDefinitions('entity_test_with_bundle', 'alpha'));
    $this->assertArrayNotHasKey('test_automatic_bundle', $this->entityFieldManager->getFieldDefinitions('entity_test_with_bundle', 'beta'));

    // Won't work due to this core bug: https://www.drupal.org/project/drupal/issues/3045509
    // $this->assertArrayHasKey('test_automatic_bundle', $this->entityFieldManager->getFieldMap()['entity_test_with_bundle']);

    $this->assertArrayNotHasKey('test_automatic_bundle_unused', $this->entityFieldManager->getFieldDefinitions('entity_test_with_bundle', 'alpha'));
    $this->assertArrayNotHasKey('test_automatic_bundle_unused', $this->entityFieldManager->getFieldDefinitions('entity_test_with_bundle', 'beta'));

    // Dynamically attaching plugin bundle fields.
    $this->assertArrayNotHasKey('test_dynamic_base', $this->entityFieldManager->getFieldDefinitions('entity_test_with_bundle', 'alpha'));
    $this->assertArrayNotHasKey('test_dynamic_base', $this->entityFieldManager->getFieldDefinitions('entity_test_with_bundle', 'beta'));

    // Create a uri field.
    $field_name = $this->randomMachineName();
    $field_storage = $this->entityTypeManager->getStorage('field_storage_config')->create([
      'field_name' => $field_name,
      'entity_type' => 'entity_test_with_bundle',
      'type' => 'uri',
    ]);
    $field_storage->save();
    $field = $this->entityTypeManager->getStorage('field_config')->create([
      'field_name' => $field_name,
      'entity_type' => 'entity_test_with_bundle',
      'bundle' => 'alpha',
      'label' => 'Label',
    ]);
    $field->save();

    // There is now a dynamic field matching the uri field.
    $expected_computed_field_name = $field_name . '_computed';
    $this->assertArrayHasKey($expected_computed_field_name, $this->entityFieldManager->getFieldDefinitions('entity_test_with_bundle', 'alpha'));
    $this->assertArrayNotHasKey($expected_computed_field_name, $this->entityFieldManager->getFieldDefinitions('entity_test_with_bundle', 'beta'));
  }

}
