<?php

/**
 * @file
 * Theme and preprocess functions for views.
 */

/**
 * Implements hook_preprocess_HOOK().
 */
function satellite_theme_preprocess_views_view_unformatted__spotlight__block_1(&$variables) {
  $config = \Drupal::config('system.site');
  $variables['site_name'] = $config->get('name');
// $variables['site_slogan'] = $config->get('slogan');
}
