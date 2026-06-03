<?php

/**
 * @file
 * Satellite Basic page post_update hooks.
 */

/**
 * Implements hook_post_update_NAME().
 */
function satellite_basic_page_post_update_0001_update_pathauto_pattern_id(&$sandbox) {
  $pathauto_config = \Drupal::configFactory()->getEditable('pathauto.pattern.basic_page_pattern');
  $selection_criteria = $pathauto_config->get('selection_criteria');
  $v = reset($selection_criteria);
  if ($v['id'] === 'node_type') {
    $v['id'] = 'entity_bundle_node';
    $selection_criteria[$v['uuid']] = $v;
    $pathauto_config->set('selection_criteria', $selection_criteria)->save();
  }
}

/**
 * Fix pathauto configuration from 0001_update.
 */
function satellite_basic_page_post_update_0002_fix_0001_update_pathauto_pattern_id(&$sandbox) {
  $pathauto_config = \Drupal::configFactory()->getEditable('pathauto.pattern.basic_page_pattern');
  $selection_criteria = $pathauto_config->get('selection_criteria');
  $v = reset($selection_criteria);
  if ($v['id'] === 'entity_bundle_node') {
    $v['id'] = 'entity_bundle:node';
    $selection_criteria[$v['uuid']] = $v;
    $pathauto_config->set('selection_criteria', $selection_criteria)->save();
  }
}
