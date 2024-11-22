<?php

namespace Drupal\test_computed_field_output\Plugin\ComputedField;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\computed_field\Attribute\ComputedField;
use Drupal\computed_field\Field\ComputedFieldDefinitionWithValuePluginInterface;
use Drupal\computed_field\Plugin\ComputedField\ComputedFieldBase;
use Drupal\computed_field\Plugin\ComputedField\SingleValueTrait;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Uncacheable computed field which shows the request time.
 *
 * Uses a string field type, as both the datetime field type requires an extra
 * module to be installed and the timestamp field requires a date format config
 * entity to be created.
 */
#[ComputedField(
  id: 'test_request_timestamp_attached',
  label: new TranslatableMarkup('Test Request Timestamp'),
  field_type: 'string',
  attach: [
    'scope' => 'base',
    'field_name' => 'test_request_timestamp',
    'entity_types' => ['entity_test' => []],
  ],
)]
class TestRequestTime extends ComputedFieldBase implements ContainerFactoryPluginInterface {

  use SingleValueTrait;

  /**
   * The time service.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $time;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('datetime.time'),
    );
  }

  /**
   * Creates a TestRequestTime instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time service.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    TimeInterface $time
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->time = $time;
  }

  /**
   * {@inheritdoc}
   */
  public function singleComputeValue(EntityInterface $host_entity, ComputedFieldDefinitionWithValuePluginInterface $computed_field_definition): mixed {
    return (string) \Drupal::time()->getRequestTime();
  }

  /**
   * {@inheritdoc}
   */
  public function useLazyBuilder(EntityInterface $host_entity, ComputedFieldDefinitionWithValuePluginInterface $computed_field_definition): bool {
    return TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheability(EntityInterface $host_entity, ComputedFieldDefinitionWithValuePluginInterface $computed_field_definition): ?CacheableMetadata {
    $cacheability = new CacheableMetadata();
    $cacheability->setCacheMaxAge(0);
    return $cacheability;
  }

}
