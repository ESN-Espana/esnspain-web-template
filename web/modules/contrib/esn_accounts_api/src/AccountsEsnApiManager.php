<?php

namespace Drupal\esn_accounts_api;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Plugin\Discovery\ContainerDerivativeDiscoveryDecorator;
use Drupal\Core\Plugin\Discovery\YamlDiscovery;
use Drupal\Core\Plugin\Factory\ContainerFactory;

/**
 * Defines an endpoint plugin manager to deal with API endpoints.
 *
 * Extension can define endpoints in a EXTENSION_NAME.endpoints.yml file
 * contained in the extension's base directory. Each endpoint has the
 * following structure:
 * @code
 *   MACHINE_NAME:
 *     label: STRING
 *     endpoint: STRING
 *     base_uri: STRING
 *     version: STRING
 * @endcode
 *
 * @see \Drupal\esn_accounts_api\AccountsEsnApiEndpoint
 * @see \Drupal\esn_accounts_api\AccountsEsnApiEndpointInterface
 * @see plugin_api
 */
class AccountsEsnApiManager extends DefaultPluginManager implements AccountsEsnApiManagerInterface {

  /**
   * Provides default values for all endpoints plugins.
   *
   * @var array
   */
  protected $defaults = [
    'endpoint' => '',
    'label' => '',
    'base_uri' => '',
    'version' => '',
    'class' => 'Drupal\esn_accounts_api\AccountsEsnApiEndpoint',
  ];

  /**
   * Constructs a new AccountsEsnApiManager object.
   *
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {

    $this->factory = new ContainerFactory($this);
    // Let others alter definitions with hook_esn_accounts_api_alter().
    $this->moduleHandler = $module_handler;
    $this->alterInfo('esn_accounts_api');
    $this->setCacheBackend($cache_backend, 'esn_accounts_api', ['esn_accounts_api']);
  }

  /**
   * {@inheritdoc}
   */
  protected function getDiscovery() {
    if (!$this->discovery) {
      // Check for files named MODULE.endpoints.yml.
      $this->discovery = new YamlDiscovery('endpoints', $this->moduleHandler->getModuleDirectories());
      $this->discovery->addTranslatableProperty('label', 'label_context');
      $this->discovery = new ContainerDerivativeDiscoveryDecorator($this->discovery);
    }
    return $this->discovery;
  }

  /**
   * {@inheritdoc}
   */
  public function processDefinition(&$definition, $plugin_id) {
    parent::processDefinition($definition, $plugin_id);

    // You can add validation of the plugin definition here.
    if (!isset($definition['base_uri'])) {
      throw new InvalidPluginDefinitionException($plugin_id, "The plugin definition of the mapper '$plugin_id' does not contain a base_uri.");
    }
    if (!isset($definition['version'])) {
      throw new InvalidPluginDefinitionException($plugin_id, "The plugin definition of the mapper '$plugin_id' does not contain a version.");
    }
    if (!isset($definition['endpoint'])) {
      throw new InvalidPluginDefinitionException($plugin_id, "The plugin definition of the mapper '$plugin_id' does not contain a endpoint.");
    }
  }

  /**
   * {@inheritdoc}
   */
  public function endpoint($endpoint) {

    return $this->createInstance($endpoint);
  }

}
