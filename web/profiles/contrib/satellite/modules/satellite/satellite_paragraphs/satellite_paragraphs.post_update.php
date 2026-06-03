<?php

/**
 * @file
 * Satellite Paragraphs post_update hooks.
 */

use Drupal\Core\Utility\UpdateException;

/**
 * Migrate field_image to field_image_media on paragraph type text_image.
 */
function satellite_paragraphs_post_update_0001_migrate_field_image_field_image_media(array &$sandbox): void {
  if (!\Drupal::moduleHandler()->moduleExists('image_field_to_media')) {
    throw new UpdateException('image_field_to_media module is not installed.');
  }
  \Drupal::moduleHandler()->loadInclude('image_field_to_media', 'inc', 'image_field_to_media.batch');
  image_field_to_media_populate_media_field('paragraph', ['text_image'], 'field_image', 'field_image_media', $sandbox);
  $sandbox['#finished'] = $sandbox['finished'];
}
