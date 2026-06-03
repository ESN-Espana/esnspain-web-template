<?php

namespace Drupal\esn_accounts_api;

/**
 * Defines an interface for the class AccountsEsnApiEndpoint.
 */
interface AccountsEsnApiEndpointInterface {

  /**
   * Returns the data fetched from the endpoint.
   *
   * @return array
   *   The array with all the elements from the endpoint, already decoded.
   */
  public function getData();

  /**
   * Checks if there has been an error with the API.
   *
   * @return bool
   *   TRUE if there is an error message. FALSE otherwise.
   */
  public function isError();

  /**
   * In case there is an error, returns the message.
   *
   * @return string
   *   The error catched (if any). Empty otherwise.
   */
  public function getError();

  /**
   * Counts the elements fetched and stored from the endpoint.
   *
   * @return int
   *   Returns the number of elements in the array.
   */
  public function countElements();

  /**
   * Gets the endpoint that is being used.
   *
   * @return string
   *   The endpoint that is being used.
   */
  public function getEndpoint();

}
