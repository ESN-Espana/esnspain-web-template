<?php

namespace Drupal\esn_accounts_api;

/**
 * Interface for the SyncDefaultEntity class.
 */
interface SyncDefaultEntityInterface {

  /**
   * Sync all the countries and sections into out entities.
   *
   * If the country/section fetched is new, a new entity will be created.
   * Otherwise, if exists, will check if the data is the same and therefore
   * proceed to update it.
   *
   * @return array
   *   Returns an array with message type and the message with the results of
   *   the operation performed.
   */
  public function syncAllIntoEntities();

  /**
   * Sync all the countries into entities.
   *
   * If the country fetched is new, a new entity will be created.
   * Otherwise, if exists, will check if the data is the same and therefore
   * proceed to update it.
   *
   * @param bool $forced
   *   Flag to force the synchronisation of the data if TRUE. If FALSE, the
   *   sync will follow the established conditions for synchronisation.
   *
   * @return array
   *   Returns an array with message type and the message with the results of
   *   the operation performed.
   */
  public function syncCountriesIntoEntity(bool $forced = FALSE): array;

  /**
   * Sync all the sections into groups.
   *
   * If the country fetched is new, a group will be created.
   * Otherwise, if exists, will check if the data is the same and therefore
   * proceed to update it.
   *
   * @param bool $forced
   *   Flag to force the synchronisation of the data if TRUE. If FALSE, the
   *   sync will follow the established conditions for synchronisation.
   *
   * @return array
   *   Returns an array with message type and the message with the results of
   *   the operation performed.
   */
  public function syncSectionsIntoEntity(bool $forced = FALSE): array;

}
