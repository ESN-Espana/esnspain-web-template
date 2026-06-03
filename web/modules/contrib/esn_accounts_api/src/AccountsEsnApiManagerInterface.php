<?php

namespace Drupal\esn_accounts_api;

/**
 * Defines an interface for meme_plugin managers.
 */
interface AccountsEsnApiManagerInterface {

  /**
   * Instantiate a plugin that matches the parameter.
   *
   * @param string $endpoint
   *   The endpoint that matches a plugin to instantiate.
   *
   * @return \Drupal\esn_accounts_api\AccountsEsnApiEndpointInterface
   *   An AccountsEsnApiEndpointInterface object to interact with the endpoint.
   */
  public function endpoint($endpoint);

}
