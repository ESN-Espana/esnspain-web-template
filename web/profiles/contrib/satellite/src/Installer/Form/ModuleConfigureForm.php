<?php

namespace Drupal\satellite\Installer\Form;

//phpcs:disable
/**
 * @file
 * Contains the module configuration form from the
 * {@link https://www.drupal.org/project/social Open Social distribution}.
 *
 * The code has been adapted by the
 * {@link https://it.esn.org IT Committee of ESN International} to the
 * requirements of the ESN Satellite distribution.
 *
 * @author {@link https://www.drupal.org/open-social Open Social}
 * @copyright 2021 Open Social
 * @license https://spdx.org/licenses/GPL-2.0-or-later.html
 */
//phpcs:enable
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\State\StateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\satellite\Installer\OptionalModuleManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the site configuration form.
 */
class ModuleConfigureForm extends ConfigFormBase {

  use StringTranslationTrait;

  /**
   * The module extension list.
   *
   * @var \Drupal\satellite\Installer\OptionalModuleManager
   */
  protected $optionalModuleManager;

  /**
   * The state.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * Constructs a ModuleConfigureForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\State\StateInterface $state
   *   The state.
   * @param \Drupal\satellite\Installer\OptionalModuleManager $optional_module_manager
   *   The module extension list.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    StateInterface $state,
    OptionalModuleManager $optional_module_manager,
  ) {
    parent::__construct($config_factory);
    $this->optionalModuleManager = $optional_module_manager;
    $this->state = $state;

  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('state'),
      // Create the OptionalModuleManager ourselves because it can not be
      // available as a service yet.
      OptionalModuleManager::create($container)
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'satellite_module_configure_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['#title'] = $this->t('Install optional modules');

    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('All the required modules and configuration will be automatically installed and imported. You can optionally select additional features or generated demo content.'),
    ];

    $form['install_modules'] = [
      '#type' => 'container',
    ];

    // Allow automated installs to easily select all optional modules.
    $form['install_modules']['select_all'] = [
      '#type' => 'checkbox',
      '#label' => 'Install all features',
      '#attributes' => [
        'class' => ['visually-hidden'],
      ],
    ];

    $optional_features = $this->optionalModuleManager->getOptionalModules();
    $feature_options = array_map(
      static function ($info) {
        return $info['name'];
      },
      $optional_features
    );
    $default_features = array_keys(
      array_filter(
        $optional_features,
        static function ($info) {
          return $info['default'];
        }
      )
    );

    // Checkboxes to enable Optional modules.
    $form['install_modules']['optional_modules'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Enable additional features'),
      '#options' => $feature_options,
      '#default_value' => $default_features,
    ];

    $form['install_demo'] = [
      '#type' => 'container',
    ];

    $form['install_demo']['satellite_demo'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Generate demo content and users'),
      '#description' => $this->t('Will generate files, users, groups, events, topics, comments and posts.'),
    ];

    $form['actions'] = ['#type' => 'actions'];
    $form['actions']['save'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save and continue'),
      '#button_type' => 'primary',
      '#submit' => ['::submitForm'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    global $install_state;

    if ($form_state->getValue('select_all')) {
      // Create a simple array with all the possible optional modules.
      $optional_modules = array_keys($this->optionalModuleManager->getOptionalModules());
    }
    else {
      // Filter out the unselected modules.
      $selected_modules = array_filter($form_state->getValue('optional_modules'));
      // Create a simple array of just the module names as values.
      $optional_modules = array_values($selected_modules);
    }

    // Set the modules to be installed by Drupal in the install_profile_modules
    // step.
    $install_modules = array_merge(
      $install_state['profile_info']['install'],
      $optional_modules
    );
    $install_state['profile_info']['install'] = $install_modules;

    // Store whether we need to set up demo content.
    $this->state->set('satellite_install_demo_content', $form_state->getValue('satellite_demo'));
  }

}
