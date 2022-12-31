<?php

namespace Drupal\pluggable_entity_view_builder_group\Plugin\EntityViewBuilder;

use Drupal\node\NodeInterface;
use Drupal\pluggable_entity_view_builder\EntityViewBuilderPluginAbstract;
use Drupal\pluggable_entity_view_builder_group\ElementContainerTrait;
use Drupal\pluggable_entity_view_builder_group\ProcessedTextBuilderTrait;
use Drupal\pluggable_entity_view_builder_group\TagBuilderTrait;
use Drupal\pluggable_entity_view_builder_group\SubscribeBuilderTrait;
use Drupal\pluggable_entity_view_builder_group\ByOnBuilderTrait;

/**
 * The "Node Group" plugin.
 *
 * @EntityViewBuilder(
 *   id = "node.group",
 *   label = @Translation("Group"),
 *   description = "Node view builder for Group bundle."
 * )
 */
class NodeGroup extends EntityViewBuilderPluginAbstract {

  use ElementContainerTrait;
  use ProcessedTextBuilderTrait;
  use TagBuilderTrait;
  use SubscribeBuilderTrait;
  use ByOnBuilderTrait;

  /**
   * Build full view mode.
   *
   * @param array $build
   *   The existing build.
   * @param \Drupal\node\NodeInterface $entity
   *   The entity.
   *
   * @return array
   *   Render array.
   */
  public function buildFull(array $build, NodeInterface $entity): array {

    // Created by author on date.
    $build[] = $this->buildByOne($entity);

    // Header.
    $build[] = $this->buildHeroHeader($entity);

    // Subscribe.
    $build[] = $this->buildSubscribe($entity);

    // Body.
    $build[] = $this->buildProcessedText($entity);

    // Tags.
    $build[] = $this->buildContentTags($entity);

    // If Paragraphs group module is enabled, show the paragraphs.
    if ($entity->hasField('field_paragraphs') && !$entity->field_paragraphs->isEmpty()) {
      $build[] = [
        '#theme' => 'pluggable_entity_view_builder_group_cards',
        '#items' => $this->buildReferencedEntities($entity->field_paragraphs, 'full'),
      ];
    }

    // Comments.
    $build[] = $this->buildComment($entity);

    // Load Tailwind CSS framework, so our group are styled.
    $build['#attached']['library'][] = 'pluggable_entity_view_builder_group/tailwind';

    return $build;
  }

  /**
   * Build the Hero Header section, with Title, and Background Image.
   *
   * @param \Drupal\node\NodeInterface $entity
   *   The entity.
   * @param string $image_field_name
   *   Optional; The field name. Defaults to "field_image".
   *
   * @return array
   *   Render array.
   */
  protected function buildHeroHeader(NodeInterface $entity, $image_field_name = 'field_featured_image'): array {
    $image_info = $this->getMediaImageAndAlt($entity, $image_field_name);

    $element = [
      '#theme' => 'pluggable_entity_view_builder_group_hero_header',
      '#title' => $entity->label(),
      '#background_image' => !empty($image_info['url']) ? $image_info['url'] : '',
    ];

    return $this->wrapElementWithContainer($element);
  }

  /**
   * Build the content tags section.
   *
   * @param \Drupal\node\NodeInterface $entity
   *   The entity.
   * @param string $field_name
   *   Optional; The term reference field name. Defaults to "field_tags".
   *
   * @return array
   *   Render array.
   */
  protected function buildContentTags(NodeInterface $entity, string $field_name = 'field_tags'): array {
    $tags = $this->buildTags($entity, $field_name);
    if (!$tags) {
      return [];
    }

    return [
      '#theme' => 'pluggable_entity_view_builder_group_tags',
      '#tags' => $tags,
    ];
  }

  /**
   * Build a list of tags.
   *
   * @param \Drupal\node\NodeInterface $entity
   *   The entity.
   * @param string $field_name
   *   Optional; The term reference field name. Defaults to "field_tags".
   *
   * @return array
   *   Render array.
   */
  protected function buildTags(NodeInterface $entity, string $field_name = 'field_tags'): array {
    if (empty($entity->{$field_name}) || $entity->{$field_name}->isEmpty()) {
      // No terms referenced.
      return [];
    }

    $tags = [];
    foreach ($entity->{$field_name}->referencedEntities() as $term) {
      $tags[] = $this->buildTag($term);
    }

    return $tags;
  }

}
