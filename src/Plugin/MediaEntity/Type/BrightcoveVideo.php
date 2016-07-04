<?php

namespace Drupal\media_entity_brightcove\Plugin\MediaEntity\Type;

use Drupal\brightcove\BrightcoveUtil;
use Drupal\media_entity\MediaInterface;
use Drupal\media_entity\MediaTypeBase;

/**
 * @MediaType(
 *   id = "media_entity_brightcove",
 *   label = @Translation("Brightcove Video"),
 *   description = @Translation("Provides business logic and metadata for videos.")
 * )
 */
class BrightcoveVideo extends MediaTypeBase {

  /**
   * The used field name.
   */
  const FIELD_NAME = 'field_video';

  /**
   * {@inheritdoc}
   */
  public function providedFields() {
    return [
      'name',
      'complete',
      'description',
      'long_description',
      'reference_id',
      'state',
      'tags',
      'custom_fields',
      'geo',
      'geo.countries',
      'geo.exclude_countries',
      'geo.restricted',
      'schedule',
      'starts_at',
      'ends_at',
      'picture_thumbnail',
      'picture_poster',
      'video_source',
      'economics',
      'partner_channel',
    ];
  }

  /**
   * Returns the data stored on this video media as object.
   *
   * @return \Brightcove\Object\Video\Video|null
   *
   * @todo Decide whether we want to have our own custom domain value object.
   */
  public function getVideo(MediaInterface $media) {
    /** @var \Drupal\brightcove\Entity\BrightcoveVideo $video */
    if ($video = $media->{static::FIELD_NAME}->entity) {
      $cms = BrightcoveUtil::getCMSAPI($video->getAPIClient());
      $brightcove_video = $cms->getVideo($video->getVideoId());
      return $brightcove_video;
    }
  }

  /**
   * Returns the brightcove video entity.
   *
   * @param \Drupal\media_entity\MediaInterface $media
   *   The media
   *
   * @return \Drupal\brightcove\Entity\BrightcoveVideo
   */
  public function getVideoEntity(MediaInterface $media) {
    return $media->{static::FIELD_NAME}->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getField(MediaInterface $media, $name) {
    switch ($name) {
      case 'thumbnail':
        return $this->thumbnail($media);
      case 'name':
        return $this->getVideoEntity($media)->getName();
      case 'complete':
        break;
      case 'description':
        return $this->getVideoEntity($media)->getDescription();
        break;
      case 'long_description':
        return $this->getVideoEntity($media)->getLongDescription();
      case 'reference_id':
        return $this->getVideoEntity($media)->getReferenceID();
      case 'state':
        break;
      case 'tags':
        return $this->getVideoEntity($media)->getTags();
      case 'custom_fields':
        return $this->getVideoEntity($media)->getCustomFieldValues();
      case 'geo':
        break;
      case 'geo.countries':
        return $this->getVideoEntity($media)->geo_countries->value;
      case 'geo.exclude_countries':
        return $this->getVideoEntity($media)->geo_exclude_countries->value;
      case 'geo.restricted':
        return $this->getVideoEntity($media)->geo_restricted->value;
      case 'schedule':
        break;
      case 'starts_at':
        return $this->getVideoEntity($media)->getScheduleStartsAt();
      case 'ends_at':
        return $this->getVideoEntity($media)->getScheduleEndsAt();
      case 'picture_thumbnail':
        return $this->thumbnail($media);
      case 'picture_poster':
        return $this->getVideoEntity($media)->getPoster();
      case 'video_source':
        return $this->getVideoEntity($media)->getVideoUrl();
      case 'economics':
        return $this->getVideoEntity($media)->getEconomics();
      case 'partner_channel':
        break;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function thumbnail(MediaInterface $media) {
    if ($thumbnail_info = $this->getVideoEntity($media)->getThumbnail()) {
      /** @var \Drupal\file\FileInterface $file */
      if ($file = $this->entityTypeManager->getStorage('file')->load($thumbnail_info['target_id'])) {
        return $file->getFileUri();
      }
    }
  }

}
