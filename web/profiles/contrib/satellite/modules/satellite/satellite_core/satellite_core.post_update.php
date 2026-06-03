<?php

/**
 * @file
 * Install, update and uninstall module functions.
 */

use Drupal\Core\File\FileExists;

/**
 * Enable admin theme when editing content.
 *
 * Implements hook_post_update_NAME().
 */
function satellite_core_post_update_enable_admin_theme_edit(&$sandbox) {
  \Drupal::configFactory()->getEditable('node.settings')->set('use_admin_theme', TRUE)->save();
}

/**
 * Upgrade to CKEditor 5.
 */
function satellite_core_post_update_0001_upgrade_to_ckeditor_5() {
  /** @var \Drupal\update_helper\Updater $updater */
  $updater = \Drupal::service('update_helper.updater');

  // Execute configuration update definitions with logging of success.
  $updater->executeUpdate('satellite_core', 'satellite_core_post_update_0001_upgrade_to_ckeditor_5');

  // Output logged messages to related channel of update execution.
  return $updater->logger()->output();
}

/**
 * Update permissions to sites&#039;s roles.
 */
function satellite_core_post_update_0002_update_permissions() {
  /** @var \Drupal\update_helper\Updater $updater */
  $updater = \Drupal::service('update_helper.updater');

  // Execute configuration update definitions with logging of success.
  $updater->executeUpdate('satellite_core', 'satellite_core_post_update_0002_update_permissions');

  // Output logged messages to related channel of update execution.
  return $updater->logger()->output();
}

/**
 * Add Media and Media Library integration.
 */
function satellite_core_post_update_0003_add_media_integration() {
  /** @var \Drupal\update_helper\Updater $updater */
  $updater = \Drupal::service('update_helper.updater');

  // Execute configuration update definitions with logging of success.
  $updater->executeUpdate('satellite_core', 'satellite_core_post_update_0003_add_media_integration');

  // Output logged messages to related channel of update execution.
  return $updater->logger()->output();
}

/**
 * Allow Content and Site managers to use the admin toolbar.
 */
function satellite_core_post_update_0004_add_toolbar_permissions() {
  /** @var \Drupal\update_helper\Updater $updater */
  $updater = \Drupal::service('update_helper.updater');

  // Execute configuration update definitions with logging of success.
  $updater->executeUpdate('satellite_core', 'satellite_core_post_update_0004_add_toolbar_permissions');

  // Output logged messages to related channel of update execution.
  return $updater->logger()->output();
}

/**
 * Re-enable admin theme when editing content.
 *
 * Implements hook_post_update_NAME().
 */
function satellite_core_post_update_0005_reenable_admin_theme_edit(&$sandbox) {
  \Drupal::configFactory()->getEditable('node.settings')
    ->set('use_admin_theme', TRUE)->save();
}

/**
 * Fix file IDs issue.
 */
function satellite_core_post_update_0006_fix_media_issue_file_id(&$sandbox) {
  $entityTypeManager = \Drupal::entityTypeManager();
  $file_repository = \Drupal::service('file.repository');

  /** @var \Drupal\file\FileInterface[] $esnstar_file */
  $esnstar_file = $entityTypeManager->getStorage('file')->loadByProperties([
    'filename' => 'spotlight.png',
  ]);

  if (!empty($esnstar_file)) {
    $esnstar_file = reset($esnstar_file);
    $esnstar_file
      ->set('filename', 'esnstar.png')
      ->set('uri', 'public://esnstar.png')
      ->save();
  }
  else {
    $theme_path = \Drupal::service('extension.list.theme')->getPath('satellite_theme');

    $image_data = file_get_contents("$theme_path/ESNstar_digital_full_colour.png");
    $esnstar_file = $file_repository->writeData($image_data, "public://esnstar.png", FileExists::Replace);

    /** @var \Drupal\media\MediaInterface[] $esnstar_media */
    $esnstar_media = $entityTypeManager->getStorage('media')->loadByProperties([
      'uuid' => '9916b40b-d139-4866-b350-599bf75479f4',
    ]);

    $esnstar_media = reset($esnstar_media);

    $esnstar_media->set('field_media_image', [
      'target_id' => $esnstar_file->id(),
      'alt' => t('Logo of ESN. Colorful star'),
    ]);
    $esnstar_media->save();
  }

  /** @var \Drupal\file\FileInterface[] $esnstar_file */
  $spotlight_file = $entityTypeManager->getStorage('file')->loadByProperties([
    'filename' => 'spotlight.png',
  ]);

  // That's supposed to be true.
  if (empty($spotlight_file)) {
    $demo_path = \Drupal::service('extension.list.module')->getPath('satellite_demo');
    $image_data = file_get_contents("$demo_path/files/spotlight.png");
    $spotlight_file = $file_repository->writeData($image_data, "public://spotlight.png", FileExists::Replace);

    /** @var \Drupal\block_content\BlockContentInterface[] $spotlight_block */
    $spotlight_block = $entityTypeManager->getStorage('block_content')->loadByProperties([
      'type' => 'spotlight',
    ]);
    $spotlight_block = reset($spotlight_block);

    $spotlight_block->set('field_image', [
      'target_id' => $spotlight_file->id(),
    ]);
    $spotlight_block->save();

  }

}

/**
 * Fix theme logo path.
 */
function satellite_core_post_update_0007_fix_theme_logo_path(&$sandbox) {
  $theme_config = \Drupal::configFactory()->getEditable('satellite_theme.settings');
  $logo_path = $theme_config->get('logo.path');
  if (str_starts_with($logo_path, '/sites')) {
    $logo_path = ltrim($logo_path, '/');
  }
  $theme_config->set('logo.path', $logo_path)->save();
}

/**
 * Fix user and role settings.
 */
function satellite_core_post_update_0008_fix_user_settings_oauth2(&$sandbox) {
  $user_config = \Drupal::configFactory()->getEditable('user.settings');
  $user_config->set('register', 'admin_only')->save();

  $content_manager_role = \Drupal::entityTypeManager()->getStorage('user_role')->load('contentmanager');
  $new_permissions = [
    'bypass node access',
    'create image media',
    'create media',
    'delete all revisions',
    'delete any image media',
    'delete any image media revisions',
    'delete media',
    'delete own image media',
    'edit any image media',
    'edit own image media',
    'revert all revisions',
    'update media',
    'view all revisions',
    'view all taxonomy revisions',
    'view any image media revisions',
    'view own unpublished media',
    'view vocabulary labels',
  ];

  foreach ($new_permissions as $permission) {
    $content_manager_role->grantPermission($permission);
  }
  $content_manager_role->save();

}
