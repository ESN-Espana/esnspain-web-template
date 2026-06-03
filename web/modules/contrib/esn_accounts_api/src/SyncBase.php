<?php

namespace Drupal\esn_accounts_api;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Class that fetches the JSON from a generic endpoint from Accounts.
 */
class SyncBase {

  // For using $this->t() later.
  use StringTranslationTrait;

  /**
   * The countries plugin endpoint data.
   *
   * @var AccountsEsnApiEndpointInterface
   */
  protected AccountsEsnApiEndpointInterface $countriesEndpoint;

  /**
   * The sections plugin endpoint data.
   *
   * @var AccountsEsnApiEndpointInterface
   */
  protected AccountsEsnApiEndpointInterface $sectionsEndpoint;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected TimeInterface $time;

  /**
   * SyncBase constructor.
   *
   * @param AccountsEsnApiManagerInterface $esn_api_endpoint_manager
   *   The Api Sync Endpoint service used for sections.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Component\Datetime\TimeInterface $time
   *   The time manager service.
   */
  public function __construct(AccountsEsnApiManagerInterface $esn_api_endpoint_manager, EntityTypeManagerInterface $entity_type_manager, TimeInterface $time) {

    // This is the service, we need to fetch the data later.
    $this->countriesEndpoint = $esn_api_endpoint_manager->endpoint('accounts.countries');
    $this->sectionsEndpoint = $esn_api_endpoint_manager->endpoint('accounts.sections');
    $this->entityTypeManager = $entity_type_manager;
    $this->time = $time;
  }

  /**
   * Return the result of the operation for debugging purposes.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   A string with the debug message.
   */
  public function debug() {
    return $this->t("@countries countries fetched; @sections sections fetched", [
      '@countries' => $this->countriesEndpoint->countElements(),
      '@sections' => $this->sectionsEndpoint->countElements(),
    ]);
  }

}
