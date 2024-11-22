<?php

namespace Drupal\Tests\computed_field\Kernel;

use Drupal\computed_field\ComputedFieldManager;
use Drupal\link\LinkItemInterface;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceModifierInterface;
use Drupal\KernelTests\KernelTestBase;

/**
 * Tests that computed fields return the correct field settings.
 *
 * @group computed_field
 */
class ComputedFieldTypeSettingsKernelTest extends KernelTestBase implements ServiceModifierInterface {

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
    'link',
    'test_computed_field_plugins',
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
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $service_definition = $container->getDefinition('plugin.manager.computed_field');
    $service_definition->setClass(TestComputedFieldManager::class);
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installEntitySchema('entity_test');

    $this->entityTypeManager = $this->container->get('entity_type.manager');
    $this->entityFieldManager = $this->container->get('entity_field.manager');
  }

  /**
   * Tests that field settings are returned from computed field definitions.
   *
   * This needs to test both automatically attached fields and fields defined
   * in config, as they use different definition classes.
   */
  public function testFieldSettings() {
    $computed_field_storage = $this->entityTypeManager->getStorage('computed_field');

    // Test link field defined in code.
    $this->assertArrayHasKey('automatic_test_link', $this->entityFieldManager->getFieldDefinitions('entity_test', 'entity_test'));
    $field_definition = $this->entityFieldManager->getFieldDefinitions('entity_test', 'entity_test')['automatic_test_link'];

    // We have to check both ways of getting the setting: getSettings() and
    // getSetting($name).
    $field_settings = $field_definition->getSettings();
    $this->assertEquals(DRUPAL_OPTIONAL, $field_settings['title']);
    $this->assertEquals(DRUPAL_OPTIONAL, $field_definition->getSetting('title'));
    $this->assertEquals(LinkItemInterface::LINK_GENERIC, $field_settings['link_type']);
    $this->assertEquals(LinkItemInterface::LINK_GENERIC, $field_definition->getSetting('link_type'));

    // Test link field defined with config computed field.
    $computed_field = $computed_field_storage->create([
      'field_name' => 'config_test_link',
      'label' => 'Test',
      'plugin_id' => 'test_link',
      'entity_type' => 'entity_test',
      'bundle' => 'entity_test',
    ]);
    $computed_field->save();

    $this->assertArrayHasKey('config_test_link', $this->entityFieldManager->getFieldDefinitions('entity_test', 'entity_test'));
    $field_definition = $this->entityFieldManager->getFieldDefinitions('entity_test', 'entity_test')['config_test_link'];

    $field_settings = $field_definition->getSettings();
    $this->assertEquals(DRUPAL_OPTIONAL, $field_settings['title']);
    $this->assertEquals(DRUPAL_OPTIONAL, $field_definition->getSetting('title'));
    $this->assertEquals(LinkItemInterface::LINK_GENERIC, $field_settings['link_type']);
    $this->assertEquals(LinkItemInterface::LINK_GENERIC, $field_definition->getSetting('link_type'));

    // Test reverse entity reference field defined in config.
    $computed_field = $computed_field_storage->create([
      'field_name' => 'test_rer',
      'label' => 'Test',
      'plugin_id' => 'reverse_entity_reference',
      'plugin_config' => [
        'reference_field' => 'entity_test-user_id',
      ],
      'entity_type' => 'user',
      'bundle' => 'user',
    ]);
    $computed_field->save();

    $this->assertArrayHasKey('test_rer', $this->entityFieldManager->getFieldDefinitions('user', 'user'));
    $field_definition = $this->entityFieldManager->getFieldDefinitions('user', 'user')['test_rer'];

    $field_settings = $field_definition->getSettings();
    $this->assertEquals('entity_test', $field_settings['target_type']);
    $this->assertEquals('entity_test', $field_definition->getSetting('target_type'));
    $this->assertEquals('default', $field_settings['handler']);
    $this->assertEquals('default', $field_definition->getSetting('handler'));
    $this->assertEquals([], $field_settings['handler_settings']);
    $this->assertEquals([], $field_definition->getSetting('handler_settings'));

    // Test cardinality with a field defined in code.
    $this->assertArrayHasKey('automatic_test_string_multiple', $this->entityFieldManager->getFieldDefinitions('entity_test', 'entity_test'));
    $field_definition = $this->entityFieldManager->getFieldDefinitions('entity_test', 'entity_test')['automatic_test_string_multiple'];
    $this->assertEquals(2, $field_definition->getFieldStorageDefinition()->getCardinality());


    // Test cardinality with a field defined in config.
    $computed_field = $computed_field_storage->create([
      'field_name' => 'test_string_multiple',
      'label' => 'Test',
      'plugin_id' => 'test_string_multiple',
      'entity_type' => 'entity_test',
      'bundle' => 'entity_test',
    ]);
    $computed_field->save();

    $this->assertArrayHasKey('test_string_multiple', $this->entityFieldManager->getFieldDefinitions('entity_test', 'entity_test'));
    $field_definition = $this->entityFieldManager->getFieldDefinitions('entity_test', 'entity_test')['test_string_multiple'];
    $this->assertEquals(2, $field_definition->getFieldStorageDefinition()->getCardinality());
  }

}

/**
 * Overrides the plugin manager to provide automatically attaching plugins.
 */
class TestComputedFieldManager extends ComputedFieldManager {

  protected function alterDefinitions(&$definitions) {
    parent::alterDefinitions($definitions);

    $definitions['automatic_test_link'] = [
      'id' => 'automatic_test_link',
      'attach' => [
        'scope' => 'base',
        'field_name' =>'automatic_test_link',
        'entity_types' => [
          'entity_test' => [],
        ],
      ],
    ]
    + $definitions['test_link'];

    $definitions['automatic_reverse_entity_reference'] = [
      'id' => 'automatic_reverse_entity_reference',
      'attach' => [
        'scope' => 'base',
        'field_name' =>'automatic_reverse_entity_reference',
        'entity_types' => [
          'user' => [],
        ],
      ],
      'plugin_config' => [
        'reference_field' => 'entity_test-user_id',
      ],
    ]
    + $definitions['reverse_entity_reference'];

    $definitions['automatic_test_string_multiple'] = [
      'id' => 'automatic_test_string_multiple',
      'attach' => [
        'scope' => 'base',
        'field_name' =>'automatic_test_string_multiple',
        'entity_types' => [
          'entity_test' => [],
        ],
      ],
    ]
    + $definitions['test_string_multiple'];
  }

}

