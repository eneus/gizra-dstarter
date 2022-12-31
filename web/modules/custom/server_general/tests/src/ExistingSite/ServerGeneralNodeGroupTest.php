<?php

namespace Drupal\Tests\server_general\PEVBGroup;

use Drupal\taxonomy\Entity\Vocabulary;
use Symfony\Component\HttpFoundation\Response;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * Test 'group' content type.
 */
class ServerGeneralNodeGroupTest extends ExistingSiteBase {

  /**
   * {@inheritdoc}
   */
  public function getEntityBundle(): string {
    return 'group';
  }

  /**
   * {@inheritdoc}
   */
  public function getRequiredFields(): array {
    return [
      'body',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getOptionalFields(): array {
    return [
      'field_featured_image',
      'field_tags',
    ];
  }

  /**
   * An test method for group node typr.
   *
   * @throws \Drupal\Core\Entity\EntityStorageException
   * @throws \Drupal\Core\Entity\EntityMalformedException
   * @throws \Behat\Mink\Exception\ExpectationException
   */
  public function testGroup() {
    // Creates a user. Will be automatically cleaned up at the end of the test.
    $author = $this->createUser();
    $assert = $this->assertSession();

    // Create a taxonomy term. Will be automatically cleaned up at the end of
    // the test.
    $vocab = Vocabulary::load('tags');
    $term = $this->createTerm($vocab);

    // Create a "Lamborghini Urus" group. Will be automatically cleaned up at
    // end of test.
    $node = $this->createNode([
      'title' => 'Lamborghini Urus',
      'type' => 'group',
      'field_tags' => [
        'target_id' => $term->id(),
      ],
      'uid' => $author->id(),
    ]);
    $this->assertEquals($author->id(), $node->getOwnerId());

    // We can browse pages.
    $this->drupalGet($node->toUrl());
    $this->assertSession()->statusCodeEquals(200);

    // Check subscribe link for author should be Not Exists.
    $assert->elementNotExists('css', '.subscribe-group');

    // We can login and browse admin pages.
    $this->drupalLogin($author);
    $this->drupalGet($node->toUrl('edit-form'));

    // We can create new user and browse group pages to check links.
    $account = $this->createUser();
    $this->drupalLogin($account);
    $this->drupalGet($node->toUrl());
    $assert->elementExists('css', '.subscribe-group');

    $this->drupalGet(sprintf("group/node/%s/subscribe", $node->id()));
    $this->assertSession()->statusCodeEquals(Response::HTTP_OK);
  }

}
