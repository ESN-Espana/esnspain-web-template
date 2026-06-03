<?php

/**
 * @file
 * Satellite theme post_update hooks.
 */

/**
 * Empty hook in order to clear the cache.
 */
function satellite_theme_post_update_clear_theme_cache(&$sandbox) {
  // Empty post_update in order to clear the cache.
}

/**
 * Create a new block entity for status messages and place it to header region.
 */
function satellite_theme_post_update_add_status_messages_to_header_region(&$sandbox) {
  // Create a block entity.
  $entity = \Drupal::entityTypeManager()->getStorage('block')
    ->create([
      'plugin' => 'system_messages_block',
      'theme' => 'satellite_theme',
      'region' => 'header',
      'id' => 'messages',
    ]);

  $entity->save();
}

/**
 * Change the block configuration to expand sub-menus.
 */
function satellite_theme_post_update_expand_menu_sublevels(&$sandbox) {
  $menu_block = \Drupal::entityTypeManager()->getStorage('block')->load('mainnavigation');
  $settings = $menu_block->get('settings');
  $settings['expand_all_items'] = TRUE;
  $menu_block->set('settings', $settings);
  $menu_block->save();
}

/**
 * Create a new block entity for local tabs and place it to header region.
 */
function satellite_theme_post_update_add_local_tabs_to_header_region(&$sandbox) {
  // Create a block entity.
  $entity = \Drupal::entityTypeManager()->getStorage('block')
    ->create([
      'plugin' => 'local_tasks_block',
      'theme' => 'satellite_theme',
      'region' => 'header',
      'id' => 'tabs',
    ]);

  $entity->save();
}
