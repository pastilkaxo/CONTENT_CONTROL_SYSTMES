<?php

namespace Drupal\computed_field_ui\Routing;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Provides admin UI routes for computed fields.
 *
 * This needs to be a route subscriber rather than a route provider because it
 * needs to detect existing routes for entity types.
 */
class ComputedFieldRouteSubscriber extends RouteSubscriberBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Creates a RouteSubscriber instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager
  ) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    foreach ($this->entityTypeManager->getDefinitions() as $entity_type_id => $entity_type) {
      if ($route_name = $entity_type->get('field_ui_base_route')) {
        // Try to get the route from the current collection.
        if (!$entity_route = $collection->get($route_name)) {
          continue;
        }
        $path = $entity_route->getPath();

        $options = $entity_route->getOptions();
        if ($bundle_entity_type = $entity_type->getBundleEntityType()) {
          $options['parameters'][$bundle_entity_type] = [
            'type' => 'entity:' . $bundle_entity_type,
          ];
        }
        // Special parameter used to easily recognize all Field UI routes.
        $options['_field_ui'] = TRUE;

        $defaults = [
          'entity_type_id' => $entity_type_id,
        ];
        // If the entity type has no bundles and it doesn't use {bundle} in its
        // admin path, use the entity type.
        if (strpos($path, '{bundle}') === FALSE) {
          $defaults['bundle'] = !$entity_type->hasKey('bundle') ? $entity_type_id : '';
        }

        $route = new Route(
          "$path/fields/add-computed-field",
          [
            '_entity_form' => 'computed_field.default',
            '_title' => 'Add computed field',
          ] + $defaults,
          ['_permission' => 'administer ' . $entity_type_id . ' fields'],
          $options
        );
        $collection->add("entity.computed_field.computed_field_add_$entity_type_id", $route);

        $route = new Route(
          // We have to use a different path from config fields, as otherwise
          // only one route responds to URLs for both config fields and computed
          // fields, and will fail the parameter upcasting for the entity.
          "$path/fields/computed/{computed_field}",
          [
            '_entity_form' => 'computed_field.edit',
            // Overridden by the form.
            '_title' => 'Edit',
          ] + $defaults,
          ['_entity_access' => 'computed_field.update'],
          $options
        );
        $collection->add("entity.computed_field.{$entity_type_id}_field_edit_form", $route);

        $route = new Route(
          "$path/fields/computed/{computed_field}/delete",
          [
            '_entity_form' => 'computed_field.delete',
          ] + $defaults,
          ['_entity_access' => 'computed_field.delete'],
          $options
        );
        $collection->add("entity.computed_field.{$entity_type_id}_field_delete_form", $route);

        // Dummy route for the benefit of
        // \Drupal\field_ui\FieldConfigListBuilder, which expects this to exist
        // when building operations. The operation for this route is then
        // removed from the list builder's operations array by
        // computed_field_entity_operation_alter(), as it serves no purpose.
        $route = new Route(
          "$path/fields/computed/{computed_field}/storage",
          [
            // Use the computed field edit form as a dummy.
            '_entity_form' => 'computed_field.edit',
          ] + $defaults,
          ['_access' => 'FALSE'],
          $options
        );
        $collection->add("entity.computed_field.{$entity_type_id}_storage_edit_form", $route);
      }
    }
  }

}
