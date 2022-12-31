<?php

namespace Drupal\pluggable_entity_view_builder_group;

use Drupal\user\Entity\User;
use Drupal\Core\Entity\EntityInterface;

/**
 * Trait SubscribeBuilderTrait.
 *
 * Helper method for building a tag.
 */
trait SubscribeBuilderTrait {

  /**
   * Build a Subscribe greeting for potential member.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string $field
   *   Optional; The name of the field. Defaults to "og_group".
   *
   * @return array
   *   Render array.
   */
  protected function buildSubscribe(EntityInterface $entity, string $field = 'og_group'): array {

    $options = ['label' => 'hidden'];
    $user = User::load(\Drupal::currentUser()->id());
    if (empty($entity->get($field)->view($options)[0]['#url']) || empty($user->get('name')->value)) {
      // Field doesn't exist, or empty.
      return [];
    }
    return [
      '#theme' => 'pluggable_entity_view_builder_group_subscribe',
      '#label' => $entity->title->value,
      '#link' => $entity->toUrl('canonical')->toString(),
      '#url' => $entity->get($field)->view($options)[0]['#url']->toString(),
      '#name' => $user->get('name')->value,
    ];
  }

}
