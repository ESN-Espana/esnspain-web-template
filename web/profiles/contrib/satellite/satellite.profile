<?php

/**
 * @file
 * Functions for the ESN Satellite installation profile.
 *
 * Code related to optional feature and default content management contained in
 *   this file has been taken from the
 * {@link https://www.drupal.org/project/social Open Social distribution} and
 *   adapted by the
 * {@link https://it.esn.org IT Committee of ESN International} to the
 *   requirements of the ESN Satellite 5 distribution. *
 * //phpcs:disable
 * @author {@link https://www.drupal.org/open-social Open Social}
 * //phpcs:enable
 * @copyright 2021 Open Social
 * @license https://spdx.org/licenses/GPL-2.0-or-later.html
 */

use Drupal\satellite\Installer\Form\ModuleConfigureForm;
use Drupal\user\Entity\User;

/**
 * Implements hook_install_tasks().
 */
function satellite_install_tasks(&$install_state) {
  $tasks = [];

  // Runs the function satellite_install_update_users()
  $tasks['satellite_install_update_users'] = [];

  // If the user has selected that demo content should be installed then we add
  // this as an extra install step.
  if (\Drupal::state()->get('satellite_install_demo_content', 0) === 1) {
    $tasks['satellite_install_demo_content'] = [
      'display_name' => t('Install demo content'),
      'display' => TRUE,
      'type' => 'normal',
    ];
  }

  // @todo Add tasks here.
  //   check satellite 4 - what is asking now
  return $tasks;
}

/**
 * Uses the Satellite Demo module to install demo content.
 *
 * Will enable the Satellite Demo module, install the content and then disable
 * the Satellite Demo module again because it's only a helper to create the
 * content.
 *
 * @param array $install_state
 *   The install state.
 *
 * @return void
 */
function satellite_install_demo_content(array &$install_state) {
  // 1. enable satellite_demo module and dependencies
  \Drupal::service('module_installer')->install(['satellite_demo']);

}

/**
 * Add administrator role to user 1.
 */
function satellite_install_update_users(&$install_state) {
  // Assign user 1 the "administrator" role.
  /** @var \Drupal\user\UserInterface $user */
  $user = User::load(1);
  $user->addRole('administrator');
  $user->save();
}

/**
 * Implements hook_install_tasks_alter().
 *
 * Unfortunately we have to alter the verify requirements.
 * This is because of https://www.drupal.org/node/1253774. The dependencies of
 * dependencies are not tested. So adding requirements to our install profile
 * hook_requirements will not work :(. Also take a look at install.inc function
 * drupal_check_profile() it just checks for all the dependencies of our
 * install profile from the info file. And no actually hook_requirements in
 * there.
 */
function satellite_install_tasks_alter(&$tasks, $install_state) {
  // Override the core install_verify_requirements task function.
  $tasks['install_verify_requirements']['function'] = 'satellite_verify_custom_requirements';

  // Allow the user to select optional modules and have Drupal install those for
  // us. To make this work we have to position our optional form right before
  // install_profile_modules.
  $task_keys = array_keys($tasks);
  $insert_before = array_search('install_profile_modules', $task_keys, TRUE);
  $tasks = array_slice($tasks, 0, $insert_before, TRUE) +
    [
      'satellite_module_configure_form' => [
        'display_name' => t('Select optional modules'),
        'type' => 'form',
        'function' => ModuleConfigureForm::class,
      ],
    ] +
    array_slice($tasks, $insert_before, NULL, TRUE);
}

/**
 * Callback for install_verify_requirements, so that we meet custom requirement.
 *
 * @param array $install_state
 *   The current install state.
 *
 * @return array
 *   All the requirements we need to meet.
 */
function satellite_verify_custom_requirements(array &$install_state) {
  // Copy pasted from install_verify_requirements().
  // @todo when composer hits remove this.
  // Check the installation requirements for Drupal and this profile.
  $requirements = install_check_requirements($install_state);

  // Verify existence of all required modules.
  $requirements += drupal_verify_profile($install_state);

  return install_display_requirements($install_state, $requirements);
}
