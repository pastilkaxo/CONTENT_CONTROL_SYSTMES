<?php

namespace Drupal\test_computed_field_plugins\Plugin\ComputedField;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Component\Datetime\TimeInterface;
use Drupal\computed_field\Attribute\ComputedField;
use Drupal\computed_field\Field\ComputedFieldDefinitionWithValuePluginInterface;
use Drupal\computed_field\Plugin\ComputedField\ComputedFieldBase;
use Drupal\computed_field\Plugin\ComputedField\SingleValueTrait;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Uncacheable computed field which shows the request time.
 */
#[ComputedField(
  id: 'test_request_time',
  label: new TranslatableMarkup('Test Request Time'),
  field_type: 'datetime',
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
    $datetime = new \DateTime('@' . \Drupal::time()->getRequestTime());
    return $datetime->format(DateTimeItemInterface::DATETIME_STORAGE_FORMAT);
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
