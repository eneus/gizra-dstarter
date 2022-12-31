<?php

namespace Drupal\pluggable_entity_view_builder_group;

use Drupal\user\Entity\User;
use Drupal\Core\Entity\EntityInterface;

/**
 * Trait ByOnBuilderTrait.
 *
 * Helper method for building a tag.
 */
trait ByOnBuilderTrait {

  /**
   * Build a By [author] On [date].
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity.
   * @param string $field
   *   Optional; The name of the field. Defaults to "created".
   *
   * @return array
   *   Render array.
   */
  protected function buildByOne(EntityInterface $entity, string $field = 'created'): array {

    $user = User::load($entity->getOwnerId());
    $timestamp = $entity->get($field)->first()->getValue();
    return [
      '#theme' => 'pluggable_entity_view_builder_group_byon',
      '#date' => date('j M Y', $timestamp['value']),
      '#author' => $user->get('name')->value,
    ];
  }

}
