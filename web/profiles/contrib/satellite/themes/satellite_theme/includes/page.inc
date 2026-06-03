<?php

/**
 * @file
 * Theme and preprocess functions for page.
 */

/**
 * Implements hook_preprocess_HOOK().
 */
function satellite_theme_preprocess_page(&$variables) {
  $config = \Drupal::config('satellite_api_accounts.settings');
  if ($entity_storage = \Drupal::entityTypeManager()
    ->getStorage('esn_organisation')) {
    $organisation_code = $config->get('selected_organisation_code');
    $level = $config->get('level');
    if ($organisation_code) {
      /** @var \Drupal\esn_accounts_api\Entity\Organisation $organisation */
      $organisation = current($entity_storage->loadByProperties(['code' => $organisation_code]));
      // I need address, email, country, social media.
      $data_to_import['level'] = $level;

      $data_to_import += [
        'address' => $organisation->getAddress() ?: NULL,
        'email' => \Drupal::config('system.site')->get('mail'),
        'facebook' => $organisation->getUrlFacebook() ?: NULL,
        'twitter' => $organisation->getUrlTwitter() ?: NULL,
        'instagram' => $organisation->getUrlInstagram() ?: NULL,
      ];

      if ($level === 'local') {
        $data_to_import += [
          'country_name' => current($entity_storage->loadByProperties(['code' => $config->get('country')]))
            ->label(),
          'country_site' => current($entity_storage->loadByProperties(['code' => $config->get('country')]))
            ->getUrlWebsite(),
        ];
      }

      $data_to_import += [
        'logo_url' => \Drupal::service('file_url_generator')
          ->generateString(\Drupal::config('satellite_theme.settings')
            ->get('logo.path')),
      ];

      $variables['esn_data'] = $data_to_import;

    }
  }
}
