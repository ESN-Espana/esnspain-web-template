<?php

namespace Drupal\esn_accounts_api;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Class that fetches the JSON and import the data into our entities.
 */
class SyncDefaultEntity extends SyncBase implements SyncDefaultEntityInterface {

  /**
   * The ESN International manager.
   *
   * @var \Drupal\esn_accounts_api\EsnInternationalManagerInterface
   */
  protected EsnInternationalManagerInterface $esnInternationalManager;

  /**
   * SyncDefaultEntity constructor.
   *
   * @param AccountsEsnApiManagerInterface $esn_api_endpoint_manager
   *   The Api Sync Endpoint service used for sections.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time manager service.
   * @param \Drupal\esn_accounts_api\EsnInternationalManagerInterface $esn_international_manager
   *   The ESN International manager service.
   */
  public function __construct(AccountsEsnApiManagerInterface $esn_api_endpoint_manager, EntityTypeManagerInterface $entity_type_manager, TimeInterface $time, EsnInternationalManagerInterface $esn_international_manager) {

    parent::__construct($esn_api_endpoint_manager, $entity_type_manager, $time);
    $this->esnInternationalManager = $esn_international_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function syncAllIntoEntities(): array {
    $message = [];
    $message[] = $this->syncCountriesIntoEntity();
    $message[] = $this->syncSectionsIntoEntity();
    $message[] = $this->esnInternationalManager->checkAddNotFound();

    return $message;
  }

  /**
   * {@inheritdoc}
   */
  public function syncCountriesIntoEntity(bool $forced = FALSE): array {

    $entityStorage = $this->entityTypeManager->getStorage('esn_organisation');
    if (!$entityStorage) {
      return [
        'type' => 'error',
        'message' => $this->t('Something happened. Aborting.')->render(),
      ];
    }

    // We fetch the countries from that endpoint by using this object.
    if ($this->countriesEndpoint->isError()) {
      return [
        'type' => 'error',
        'message' => $this->countriesEndpoint->getError(),
      ];
    }

    if ($this->countriesEndpoint->countElements() === 0) {
      return [
        'type' => 'error',
        'message' => $this->t('No countries fetched from the API.')->render(),
      ];
    }

    $countries = $this->countriesEndpoint->getData();
    $current_time = $this->time->getRequestTime();
    $total = $this->countriesEndpoint->countElements();
    $created = 0;
    $updated = 0;
    foreach ($countries as $country) {

      $entity = $entityStorage->loadByProperties(['code' => $country['cc']]);
      // Get the values from the country fetched from the API.
      $values = $this->getDefaultValues($country, $current_time);
      $values += [
        'type' => 'country',
        'code' => $country['cc'],
      ];
      if (!$entity) {
        // New country.
        $organisation = $entityStorage->create($values);
        $organisation->save();
        $created++;
      }
      else {
        // Is an update.
        /** @var \Drupal\esn_accounts_api\Entity\OrganisationInterface $organisation */
        $organisation = reset($entity);
        if ($forced || ($organisation->getLastUpdate() < $country['updated'])) {
          foreach ($values as $field => $value) {
            $organisation->set($field, $value);
            if ($field === 'logo' && !is_null($organisation->getLocalLogo())) {
              $organisation->updateLogo($value);
            }
          }
          $organisation->save();
          $updated++;
        }
      }
    }

    return [
      'type' => 'status',
      'message' => $this->t(
        '@total countries fetched from the API: @created new (created) and @updated updated.',
        [
          '@total' => $total,
          '@created' => $created,
          '@updated' => $updated,
        ]
      )->render(),
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function syncSectionsIntoEntity(bool $forced = FALSE): array {

    $entityStorage = $this->entityTypeManager->getStorage('esn_organisation');
    if (!$entityStorage) {
      return [
        'type' => 'error',
        'message' => $this->t('Something happened. Aborting.')->render(),
      ];
    }

    // We fetch the sections from that endpoint by using this object.
    if ($this->sectionsEndpoint->isError()) {
      return [
        'type' => 'error',
        'message' => $this->sectionsEndpoint->getError(),
      ];
    }

    if ($this->sectionsEndpoint->countElements() === 0) {
      return [
        'type' => 'error',
        'message' => $this->t('No sections fetched from the API.')->render(),
      ];
    }

    $sections = $this->sectionsEndpoint->getData();
    $current_time = $this->time->getRequestTime();
    $total = $this->sectionsEndpoint->countElements();
    $created = 0;
    $updated = 0;
    foreach ($sections as $section) {

      $entity = $entityStorage->loadByProperties(['code' => $section['code']]);
      // Get the values from the country fetched from the API.
      $values = $this->getDefaultValues($section, $current_time);
      $values += [
        'type' => 'section',
        'code' => $section['code'],
        'field_cities' => $this->getSectionCities($section['cities']),
        'field_location' => $this->getSectionLocation($section['geolocation']),
        'field_state' => $section['state'],
        'field_university_name' => $section['university_name'],
        'field_university_website' => ['uri' => $section['university_website']],
      ];
      if (!$entity) {
        // New section.
        /** @var \Drupal\esn_accounts_api\Entity\OrganisationInterface $organisation */
        $organisation = $entityStorage->create($values);
        $organisation->save();

        $created++;
      }
      else {
        // Is an update.
        /** @var \Drupal\esn_accounts_api\Entity\OrganisationInterface $organisation */
        $organisation = reset($entity);
        if ($forced || ($organisation->getLastUpdate() < $section['updated'])) {
          foreach ($values as $field => $value) {
            if ($organisation->hasField($field)) {
              $organisation->set($field, $value);
            }
          }
          $organisation->save();
          $updated++;
        }
      }
    }

    return [
      'type' => 'status',
      'message' => $this->t(
        '@total sections fetched from the API: @created new (created) and @updated updated.',
        [
          '@total' => $total,
          '@created' => $created,
          '@updated' => $updated,
        ]
      )->render(),
    ];
  }

  /**
   * Returns the coordinates of the section as a long string.
   *
   * @param array $location
   *   The location of the section to be converted into string.
   *
   * @return string
   *   The coordinates of a section.
   *
   *   The return format is the following:
   *   @code
   *     "lat:48.2191963
   *      lng:16.4033427
   *      lat_sin:0.74569927197033
   *      lat_cos:0.6662826695802
   *      lng_rad:0.28629233844798"
   *    @endcode
   */
  private function getSectionLocation(array $location): string {
    $string = '';
    foreach ($location as $key => $item) {
      $string .= $key . ':' . $item . PHP_EOL;
    }
    return $string;
  }

  /**
   * Returns the cities of the section as a long string.
   *
   * @param array $cities
   *   The cities of a section to be converted into string.
   *
   * @return string
   *   The string with the cities.
   *
   *   The format of the string is the following:
   *   @code
   *     "0:AT:Vienna"
   *   @endcode
   */
  private function getSectionCities(array $cities): string {
    $string = '';
    foreach ($cities as $id => $city) {
      $string .= $id . ":" . $city['cc'] . ":" . $city['name'] . PHP_EOL;
    }

    return $string;

  }

  /**
   * Returns the values of an organisation as array.
   *
   * @param array $organisation
   *   The organisation to get the values from.
   * @param int $current_time
   *   The current time.
   *
   * @return array
   *   The values of the organisation.
   */
  private function getDefaultValues(array $organisation, int $current_time): array {
    return [
      'title' => $organisation['label'],
      'country' => $organisation['country'],
      'country_code' => $organisation['cc'],
      'updated_api' => $organisation['updated'],
      'address' =>$organisation['address']['street_address'],
      'facebook' => $organisation['facebook'],
      'instagram' => $organisation['instagram'],
      'twitter' => $organisation['twitter'],
      'video' => $organisation['video'],
      'logo' => $organisation['logo'],
      'website' => $organisation['website'],
      'updated' => $current_time,
      'status' => $organisation['status'],
    ];
  }

}
