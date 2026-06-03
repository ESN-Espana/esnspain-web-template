<?php

namespace Drupal\esn_accounts_api;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Http\ClientFactory;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use GuzzleHttp\Exception\ConnectException;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class that fetches the JSON from a certain endpoint of an API.
 */
class AccountsEsnApiEndpoint extends PluginBase implements AccountsEsnApiEndpointInterface, ContainerFactoryPluginInterface {

  // For using $this->t() later.
  use StringTranslationTrait;

  /**
   * The base url of the website where the API will be called.
   *
   * @var string
   */
  protected $baseUri;

  /**
   * The API version path without the endpoints.
   *
   * @var string
   */
  protected $apiVersion;

  /**
   * The client factory.
   *
   * @var \GuzzleHttp\Client
   */
  protected $client;

  /**
   * The data fetched from the API.
   *
   * @var array
   */
  protected $data = [];

  /**
   * An error happened. Display the error message.
   *
   * @var string
   */
  protected $error = '';

  /**
   * The endpoint.
   *
   * @var string
   */
  protected $endpoint;

  /**
   * Constructs a EsnApiEndpoint.
   *
   * @param string $plugin_id
   *   The config mapper plugin ID.
   * @param mixed $plugin_definition
   *   An array of plugin information as defined in EsnApiEndpointManager.
   * @param \Drupal\Core\Http\ClientFactory $http_client_factory
   *   The client factory.
   */
  public function __construct($plugin_id, $plugin_definition, ClientFactory $http_client_factory) {

    $this->pluginId = $plugin_id;
    $this->pluginDefinition = $plugin_definition;
    // Set the data from the plugin yml file.
    $this->baseUri = $this->pluginDefinition['base_uri'];
    $this->apiVersion = $this->pluginDefinition['version'];
    $this->endpoint = $this->pluginDefinition['endpoint'];
    // Initialise the client with the baseUri as option.
    $this->client = $http_client_factory->fromOptions([
      'base_uri' => $this->baseUri,
    ]);
    // And fetch the data directly in the constructor.
    $this->fetchEndpoint();
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $container->get('http_client_factory')
    );
  }

  /**
   * Fetchs the data from a certain endpoint already set.
   */
  private function fetchEndpoint() {

    if (!$this->endpoint) {
      $this->error = $this->t('API sync error. No endpoint provided.');
    }
    else {
      // We have endpoint.
      try {
        // Try to fetch the data from the full url now we have the endpoint.
        $response = $this->client->get($this->apiVersion . $this->endpoint, ['exceptions' => FALSE]);
        if ($response->getStatusCode() === 200) {
          // We want only code 200, the others will return error or empty.
          $this->data = Json::decode($response->getBody());
        }
        else {
          $this->error = $this->t('API sync error. Response returned error (code: @code).', [
            '@code' => $response->getStatusCode(),
          ]);
        }
      }
      catch (ConnectException $e) {
        // Error during connection, we catch the exception.
        $this->error = $this->t('API sync error. Unable to stablish a connection with the API.');
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getData() {

    return $this->data;
  }

  /**
   * {@inheritdoc}
   */
  public function isError() {

    return !empty($this->error);
  }

  /**
   * {@inheritdoc}
   */
  public function getError() {

    return $this->error;
  }

  /**
   * {@inheritdoc}
   */
  public function countElements() {

    return count($this->data);
  }

  /**
   * {@inheritdoc}
   */
  public function getEndpoint() {

    return $this->endpoint;
  }

}
