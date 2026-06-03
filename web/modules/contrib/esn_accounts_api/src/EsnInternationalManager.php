<?php

namespace Drupal\esn_accounts_api;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Class that adds the ESN International entity.
 */
class EsnInternationalManager implements EsnInternationalManagerInterface {

  // For using $this->t() later.
  use StringTranslationTrait;

  /**
   * The Entity Order Storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $accountsApiStorage;

  /**
   * AccountsApiSync constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {

    $this->accountsApiStorage = $entity_type_manager->getStorage('esn_organisation');
  }

  /**
   * {@inheritdoc}
   */
  public function createEsnIntlEntity() {

    $address = 'Rue Joseph II / Jozef II-straat 120
1000 Brussels, Belgium';

    $values = [
      'type' => 'international',
      'code' => 'EU-INTL-ESN',
      'title' => 'ESN International',
      'country' => 'Europe',
      'country_code' => 'EU',
      'address' => $address,
      'facebook' => 'https://www.facebook.com/esn',
      'instagram' => 'https://www.instagram.com/esn_int/',
      'twitter' => 'https://twitter.com/esn_int',
      'website' => 'https://esn.org/',

      'status' => TRUE,
    ];
    $e = $this->accountsApiStorage->create($values);
    return $e->save();
  }

  /**
   * {@inheritdoc}
   */
  public function checkAddNotFound() {

    $esnInternational = $this->accountsApiStorage->loadByProperties(['code' => 'EU-INTL-ESN']);
    if (!$esnInternational) {
      // Return the value of save().
      $return = $this->createEsnIntlEntity();
      if ($return) {
        return [
          'type' => 'status',
          'message' => $this->t('Created ESN International entity.')->render(),
        ];
      }

      return [
        'type' => 'error',
        'message' => $this->t('Something happened. Error when adding ESN International.')
          ->render(),
      ];
    }
    // Not added, returns FALSE.
    return [
      'type' => 'status',
      'message' => $this->t('ESN International already exists. Not synced.')
        ->render(),
    ];
  }

}
