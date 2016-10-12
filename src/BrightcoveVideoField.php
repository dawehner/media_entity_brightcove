<?php

namespace Drupal\media_entity_brightcove;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\media_entity_brightcove\Plugin\MediaEntity\Type\BrightcoveVideo;

class BrightcoveVideoField {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Creates a new BrightcoveVideoField instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  public function createField($bundle) {
    $this->ensureFieldStorage();

    // Create the field instance on this new media bundle.
    $field_config_storage = $this->entityTypeManager->getStorage('field_config');
    if (!$field_config_storage->load('media' . '.' . $bundle . '.' . BrightcoveVideo::FIELD_NAME)) {
      $field_config = $field_config_storage->create([
        'entity_type' => 'media',
        'field_name' => BrightcoveVideo::FIELD_NAME,
        'bundle' => $bundle,
        'required' => TRUE,
        'label' => 'Brightcove Video',
        'settings' => [
          'handler' => 'default:brightcove_video',
        ],
      ]);
      $field_config_storage->save($field_config);
    }

    // Create (or update) the entity form display for this new media bundle to
    // include this new field with some more sane defaults.
    $entity_form_display_storage = $this->entityTypeManager->getStorage('entity_form_display');
    $entity_form_display = $entity_form_display_storage->load('media.' . $bundle . '.default');
    if (!$entity_form_display) {
      $entity_form_display = $entity_form_display_storage->create([
        'status' => TRUE,
        'targetEntityType' => 'media',
        'bundle' => $bundle,
        'mode' => 'default',
        'content' => [
          BrightcoveVideo::FIELD_NAME => [
            'type' => 'brightcove_inline_entity_form_complex',
            'settings' => [
              'form_mode' => 'default',
              'allow_new' => 1,
              'allow_existing' => 1,
              'match_operator' => 'CONTAINS',
            ],
            'third_party_settings' => [],
            'weight' => 0,
          ],
        ],
      ]);
    }
    $entity_form_display_storage->save($entity_form_display);
  }

  protected function ensureFieldStorage() {
    $field_storage_config_storage = $this->entityTypeManager->getStorage('field_storage_config');
    if (!$field_storage_config_storage->load('media.' . BrightcoveVideo::FIELD_NAME)) {
      $field_storage_config = $field_storage_config_storage->create([
        'entity_type' => 'media',
        'field_name' => BrightcoveVideo::FIELD_NAME,
        'type' => 'entity_reference',
        'settings' => [
          'target_type' => 'brightcove_video',
        ],
      ]);
      $field_storage_config_storage->save($field_storage_config);
    }
  }

}
