<?php

namespace Drupal\meida_entity_brightcove;

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
    $field_config_storage = $this->entityTypeManager->getStorage('field_config');
    if (!$field_config_storage->load('media' . '.' . $bundle . '.' . BrightcoveVideo::FIELD_NAME)) {
      $field_config = $field_config_storage->create([
        'entity_type' => 'media',
        'field_name' => BrightcoveVideo::FIELD_NAME,
        'bundle' => $bundle,
        'settings' => [
          'handler' => 'default:brightcove_video',
        ],
      ]);
      $field_config_storage->save($field_config);
    }
  }

  protected function ensureFieldStorage() {
    $field_storage_config_storage = $this->entityTypeManager->getStorage('field_storage_config');
    if (!$field_storage_config_storage->load('media.' . BrightcoveVideo::FIELD_NAME)) {
      $field_storage_config = $field_storage_config_storage->create([
        'entity_type' => 'media',
        'field_name' => BrightcoveVideo::FIELD_NAME,
        'type' => 'entity_reference',
      ]);
      $field_storage_config_storage->save($field_storage_config);
    }
  }

}
