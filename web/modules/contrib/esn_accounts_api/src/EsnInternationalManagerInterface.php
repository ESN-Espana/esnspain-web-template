<?php

namespace Drupal\esn_accounts_api;

/**
 * Interface of AddEsnInternational class.
 */
interface EsnInternationalManagerInterface {

  /**
   * Creates the ESN International entity if requested.
   *
   * @return int
   *   Returns the result of the save() function.
   */
  public function createEsnIntlEntity();

  /**
   * Checks if ESN International exists or not.
   *
   * If not found, then inserts a new entry in the database.
   *
   * @return array
   *   Returns a message with the result of the operation.
   */
  public function checkAddNotFound();

}
