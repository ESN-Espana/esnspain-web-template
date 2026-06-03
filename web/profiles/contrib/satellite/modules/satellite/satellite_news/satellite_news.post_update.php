<?php

/**
 * @file
 * Install, update and uninstall module functions.
 */

use Drupal\Component\Serialization\Yaml;
use Drupal\Core\Config\FileStorage;
use Drupal\Core\Utility\UpdateException;

/**
 * Sets the image loading attribute to lazy.
 */
function satellite_news_post_update_0001_update_image_loading_attribute() {
  /** @var \Drupal\update_helper\Updater $updater */
  $updater = \Drupal::service('update_helper.updater');

  // Execute configuration update definitions with logging of success.
  $updater->executeUpdate('satellite_news', 'satellite_news_post_update_0001_update_image_loading_attribute');

  // Output logged messages to related channel of update execution.
  return $updater->logger()->output();
}

/**
 * Implements hook_post_update_NAME().
 */
function satellite_news_post_update_0002_update_pathauto_pattern_id(&$sandbox) {
  $pathauto_config = \Drupal::configFactory()->getEditable('pathauto.pattern.news_pattern');
  $selection_criteria = $pathauto_config->get('selection_criteria');
  $v = reset($selection_criteria);
  if ($v['id'] === 'node_type') {
    $v['id'] = 'entity_bundle_node';
    $selection_criteria[$v['uuid']] = $v;
    $pathauto_config->set('selection_criteria', $selection_criteria)->save();
  }
}

/**
 * Fix pathauto configuration from 0002_update.
 */
function satellite_news_post_update_0003_fix_0002_update_pathauto_pattern_id(&$sandbox) {
  $pathauto_config = \Drupal::configFactory()->getEditable('pathauto.pattern.news_pattern');
  $selection_criteria = $pathauto_config->get('selection_criteria');
  $v = reset($selection_criteria);
  if ($v['id'] === 'entity_bundle_node') {
    $v['id'] = 'entity_bundle:node';
    $selection_criteria[$v['uuid']] = $v;
    $pathauto_config->set('selection_criteria', $selection_criteria)->save();
  }
}

/**
 * Add filters and sorts on News view displays.
 */
function satellite_news_post_update_0004_add_filters_sort_news_views(&$sandbox) {
  $view_config = \Drupal::configFactory()->getEditable('views.view.news');
  $module_path = \Drupal::service('extension.list.module')->getPath('satellite_news');

  $sort_yml = file_get_contents($module_path . '/config/update/satellite_news_post_update_0004_sorts.yml');
  $filter_yml = file_get_contents($module_path . '/config/update/satellite_news_post_update_0004_filters.yml');
  $block_format_yml = <<<YML
type: html_list
options:
  row_class: p-4
  default_row_class: false
  uses_fields: false
  type: ul
  wrapper_class: ''
  class: 'row row-cols-1 row-cols-sm-2 row-cols-md-4 list-unstyled'
YML;

  $block_footer_yml = <<<YML
display_link:
  id: display_link
  table: views
  field: display_link
  relationship: none
  group_type: group
  admin_label: ''
  plugin_id: display_link
  label: 'View all news'
  empty: false
  display_id: page_1
YML;

  $row_yml = <<<YML
type: 'entity:node'
options:
  relationship: none
  view_mode: teaser
YML;

  $yml_sort_php_array = Yaml::decode($sort_yml);
  $yml_filter_php_array = Yaml::decode($filter_yml);
  $block_format_php_array = Yaml::decode($block_format_yml);
  $block_footer_php_array = Yaml::decode($block_footer_yml);
  $row_php_array = Yaml::decode($row_yml);

  $sort_options = $view_config->get('display.default.display_options.sorts');
  $view_config->set('display.default.display_options.sorts', array_merge($sort_options, $yml_sort_php_array));

  $filter_options = $view_config->get('display.block_1.display_options');
  $view_config->set('display.block_1.display_options', array_merge($filter_options, $yml_filter_php_array));

  $view_config->set('display.block_1.display_options.style', $block_format_php_array);
  $view_config->set('display.block_1.display_options.footer', $block_footer_php_array);

  $v = $view_config->get('display.block_1.display_options.defaults');
  $v['footer'] = FALSE;
  $v['style'] = FALSE;
  $v['row'] = FALSE;
  $view_config->set('display.block_1.display_options.defaults', $v);

  $view_config->set('display.block_1.display_options.row', $row_php_array);

  $view_config->save();
}

/**
 * Migrate field_image to field_image_media on node type news.
 */
function satellite_news_post_update_0005_migrate_field_image_field_image_media(array &$sandbox): void {
  if (!\Drupal::moduleHandler()->moduleExists('image_field_to_media')) {
    throw new UpdateException('image_field_to_media module is not installed.');
  }
  \Drupal::moduleHandler()->loadInclude('image_field_to_media', 'inc', 'image_field_to_media.batch');
  image_field_to_media_populate_media_field('node', ['news'], 'field_image', 'field_image_media', $sandbox);
  $sandbox['#finished'] = $sandbox['finished'];
}

/**
 * Fix issue with 5.0.0 update on news form.
 */
function satellite_news_post_update_0006_fix_news_form_display(array &$sandbox) {
  $config_storage = \Drupal::service('config.storage');
  $config_storage->delete('core.entity_form_display.node.news.default');
  $config_storage->delete('core.entity_view_display.node.news.teaser');
  $config_storage->delete('core.entity_view_display.node.news.default');

  $config_path = \Drupal::service('extension.list.module')->getPath('satellite_news') . '/config/optional';
  $source = new FileStorage($config_path);
  $config_storage = \Drupal::service('config.storage');

  $config_storage->write('core.entity_view_display.node.news.default', $source->read('core.entity_view_display.node.news.default'));
}

/**
 * Fix issue with 5.0.1 update on news form.
 */
function satellite_news_post_update_0007_fix_news_display(array &$sandbox) {
  // Redo it because 5.0.1 didn't work.

  $config_storage = \Drupal::service('config.storage');
  $config_storage->delete('core.entity_form_display.node.news.default');
  $config_storage->delete('core.entity_view_display.node.news.teaser');
  $config_storage->delete('core.entity_view_display.node.news.default');

  $config_path = \Drupal::service('extension.list.module')->getPath('satellite_news') . '/config/optional';
  $source = new FileStorage($config_path);
  $config_storage = \Drupal::service('config.storage');

  $config_storage->write('core.entity_form_display.node.news.default', $source->read('core.entity_form_display.node.news.default'));
  $config_storage->write('core.entity_view_display.node.news.default', $source->read('core.entity_view_display.node.news.default'));
  $config_storage->write('core.entity_view_display.node.news.teaser', $source->read('core.entity_view_display.node.news.teaser'));
}
