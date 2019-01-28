<?php

namespace Drupal\Tests\og\Kernel\Migrate;

use Drupal\field\Entity\FieldConfig;
use Drupal\og\Entity\OgMembership;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\Tests\migrate\Kernel\NodeCommentCombinationTrait;
use Drupal\Tests\file\Kernel\Migrate\d7\FileMigrationSetupTrait;
use Drupal\Tests\migrate_drupal\Kernel\d7\MigrateDrupal7TestBase;

/**
 * Tests organic group field migration.
 *
 * @group og_migrate
 */
class OgFieldD7MigrateTest extends MigrateDrupal7TestBase {

  use NodeCommentCombinationTrait;
  use FileMigrationSetupTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'entity_reference',
    'comment',
    'node',
    'system',
    'og',
    'og_ui',
    'og_migrate',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->fileMigrationSetup();

    $this->loadFixture(__DIR__ . '/../../../fixtures/drupal7.php');
    $this->installConfig(static::$modules);
    $this->createNodeCommentCombination('article');
    $this->createNodeCommentCombination('forum', 'comment_forum');
    $this->executeMigrations([
      'd7_field',
      'd7_field_instance',
      'd7_user_role',
      'd7_user',
      'd7_node_type',
      'd7_node',
      'd7_og_group',
      'd7_og_field_instance',
    ]);
  }

  /**
   * Asserts that the og-related fields were migrated properly.
   */
  public function testOgAudienceFields() {
    $nodeField = FieldConfig::load('node.article.og_audience');
    $fieldDefinitions = $this->container->get('entity_field.manager')->getFieldDefinitions('node', 'article');

    $this->assertEquals('og_standard_reference', $nodeField->getType());
    $this->assertArrayHasKey('og_audience', $fieldDefinitions);

    $forumField = FieldConfig::load('node.forum.og_audience');
    $fieldDefinitions = $this->container->get('entity_field.manager')->getFieldDefinitions('node', 'forum');

    $this->assertEquals('og_standard_reference', $forumField->getType());
    $this->assertArrayHasKey('og_audience', $fieldDefinitions);
  }

}
