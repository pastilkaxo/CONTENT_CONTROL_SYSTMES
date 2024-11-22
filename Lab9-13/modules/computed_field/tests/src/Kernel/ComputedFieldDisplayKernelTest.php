<?php

namespace Drupal\Tests\computed_field\Kernel;

use Drupal\KernelTests\KernelTestBase;

/**
 * Tests output and caching of computed fields.
 *
 * @group computed_field
 */
class ComputedFieldDisplayKernelTest extends KernelTestBase {

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
    'test_computed_field_output',
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
    // We don't need to test bundles, so use an entity type without them.
    $this->installEntitySchema('entity_test');

    $this->entityTypeManager = $this->container->get('entity_type.manager');
    $this->entityFieldManager = $this->container->get('entity_field.manager');
    $this->entityDisplayRepository = $this->container->get('entity_display.repository');
  }

  /**
   * Tests that computed fields are displayed on an entity.
   */
  public function testComputedFieldsOutput() {
    // Sanity check.
    $field_definitions = $this->entityFieldManager->getFieldDefinitions('entity_test', 'entity_test');
    $this->assertArrayHasKey('test_string', $field_definitions);
    $this->assertArrayHasKey('test_current_user', $field_definitions);
    $this->assertArrayHasKey('test_request_timestamp', $field_definitions);

    $entity_test_storage = $this->entityTypeManager->getStorage('entity_test');
    $view_builder = $this->entityTypeManager->getHandler('entity_test', 'view_builder');

    $alpha_entity = $entity_test_storage->create([]);
    $alpha_entity->save();

    // Build the entity view render array, including the pre_render callback to
    // fill in the fields' render arrays.
    $build = $view_builder->view($alpha_entity);
    $build = $view_builder->build($build);

    $this->assertArrayHasKey('test_string', $build);
    $this->assertArrayNotHasKey('#lazy_builder', $build['test_string']);

    $this->assertArrayHasKey('test_current_user', $build);
    $this->assertEquals('computed_field.computed_field_builder:viewField', $build['test_current_user']['#lazy_builder'][0]);

    $this->assertArrayHasKey('test_request_timestamp', $build);
    $this->assertEquals('computed_field.computed_field_builder:viewField', $build['test_request_timestamp']['#lazy_builder'][0]);

    // Render the build array.
    $html = $this->render($build);

    // Can't test test_current_user as we've not set one up!
    $this->assertStringContainsString('cake!', $html);
    $this->assertStringContainsString((string) \Drupal::time()->getRequestTime(), $html);
  }

}
