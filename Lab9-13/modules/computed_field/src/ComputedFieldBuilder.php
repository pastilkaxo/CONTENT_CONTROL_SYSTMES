<?php

namespace Drupal\computed_field;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Security\TrustedCallbackInterface;
use Drupal\Core\Render\PlaceholderGeneratorInterface;

/**
 * Lazy builder for viewing a computed field.
 */
class ComputedFieldBuilder implements TrustedCallbackInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The placeholder generator service.
   *
   * @var \Drupal\Core\Render\PlaceholderGeneratorInterface
   */
  protected $placeholderGenerator;

  /**
   * Creates a ComputedFieldBuilder instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Render\PlaceholderGeneratorInterface $placeholder_generator
   *   The placeholder generator service.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    PlaceholderGeneratorInterface $placeholder_generator
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->placeholderGenerator = $placeholder_generator;
  }

  /**
   * Lazy builder callback for a computed field.
   *
   * A computed field's render array is replaced with a lazy builder with this
   * method as its builder in computed_field_entity_view_alter().
   *
   * @param string $entity_type_id
   *   The type of the entity the field is on.
   * @param int $entity_id
   *   The ID of the entity being rendered.
   * @param string $field_name
   *   The name of the computed field.
   * @param string $view_mode
   *   The view mode being displayed.
   *
   * @return array
   *   The build array for the lazy builder.
   */
  public function viewField(string $entity_type_id, int $entity_id, string $field_name, string $view_mode): array {
    /** @var \Drupal\Core\Entity\FieldableEntityInterface $entity */
    $entity = $this->entityTypeManager->getStorage($entity_type_id)->load($entity_id);

    // Need to nest so render caching works. Setting cache keys on $build
    // doesn't work.
    $build['field'] = $entity->get($field_name)->view($view_mode);

    /** @var \Drupal\computed_field\Field\ComputedFieldDefinitionWithValuePluginInterface $field_definition */
    $field_definition = $entity->getFieldDefinition($field_name);

    // Apply the cacheability supplied by the computed value plugin to the
    // field's render array.
    $computed_field_plugin = $field_definition->getFieldValuePlugin();
    $cacheability = $computed_field_plugin->getCacheability($entity, $field_definition);
    if ($cacheability) {
      $cacheability->applyTo($build['field']);
    }

    // Use the placeholder generator to determine whether the field's render
    // array can itself be cached. The shouldAutomaticallyPlaceholder() returns
    // TRUE if the render array is considered *un*cacheable, so we take the
    // opposite value.
    if (!$this->placeholderGenerator->shouldAutomaticallyPlaceholder($build)) {
      $build['field']['#cache']['keys'] = ['computed_field-view:' . implode(':', [
        $entity_type_id,
        $entity_id,
        $field_name,
        $view_mode,
      ])];
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public static function trustedCallbacks() {
    return ['viewField'];
  }

}
