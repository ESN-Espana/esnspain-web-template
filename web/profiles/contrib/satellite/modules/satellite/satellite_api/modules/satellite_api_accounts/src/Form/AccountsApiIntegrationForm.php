<?php

namespace Drupal\satellite_api_accounts\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StreamWrapper\StreamWrapperManagerInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides the site configuration form.
 */
class AccountsApiIntegrationForm extends ConfigFormBase {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected EntityTypeManagerInterface $entityTypeManager;

  /**
   * The stream wrapper interface.
   *
   * @var \Drupal\Core\StreamWrapper\StreamWrapperManagerInterface
   */
  protected StreamWrapperManagerInterface $streamWrapperManager;

  /**
   * The file URL generator interface.
   *
   * @var \Drupal\Core\File\FileUrlGeneratorInterface
   */
  protected FileUrlGeneratorInterface $fileUrlGenerator;

  /**
   * Constructs a ModuleConfigureForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\Core\StringTranslation\TranslationInterface $translation
   *   The string translation service.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager service.
   * @param \Drupal\Core\StreamWrapper\StreamWrapperManagerInterface $streamWrapperManager
   *   The stream wrapper manager service.
   * @param \Drupal\Core\File\FileUrlGeneratorInterface $fileUrlGenerator
   *   The file URL generator service.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    TranslationInterface $translation,
    EntityTypeManagerInterface $entityTypeManager,
    StreamWrapperManagerInterface $streamWrapperManager,
    FileUrlGeneratorInterface $fileUrlGenerator,
  ) {
    parent::__construct($config_factory);
    $this->stringTranslation = $translation;
    $this->entityTypeManager = $entityTypeManager;
    $this->streamWrapperManager = $streamWrapperManager;
    $this->fileUrlGenerator = $fileUrlGenerator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('string_translation'),
      $container->get('entity_type.manager'),
      $container->get('stream_wrapper_manager'),
      $container->get('file_url_generator'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'satellite_accounts_api_integration';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'satellite_api_accounts.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $form = parent::buildForm($form, $form_state);

    $config = $this->config('satellite_api_accounts.settings');

    $form['#title'] = $this->t('Add extra information');

    $form['description'] = [
      '#type' => 'item',
      '#markup' => $this->t('Please enter the following required information.'),
    ];

    $form['group_select_organisation'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Fill in your ESN details'),
    ];

    $form['group_select_organisation']['level'] = [
      '#type' => 'select',
      '#title' => $this->t('Select your organisation level'),
      '#description' => $this->t('Please select if your site will be used on international, national or local level'),
      '#default_value' => $config->get('level'),
      '#required' => TRUE,
      '#ajax' => [
        'callback' => [$this, 'setOptionsSection'],
        'event' => 'change',
        'wrapper' => 'edit-esn-section',
      ],
      '#options' => [
        'international' => $this->t('International'),
        'national' => $this->t('National'),
        'local' => $this->t('Local'),
      ],
    ];

    $country_allowed_levels = ['national', 'local'];

    $country_entities = $this->entityTypeManager->getStorage('esn_organisation')
      ->loadByProperties(['type' => 'country']);
    $countries = [];
    /** @var \Drupal\esn_accounts_api\Entity\Organisation $country_entity */
    foreach ($country_entities as $country_entity) {
      $countries += [$country_entity->getCode() => $country_entity->label()];
    }

    $form['group_select_organisation']['country'] = [
      '#type' => 'select',
      '#title' => $this->t('Select your country'),
      '#description' => $this->t('Please select your ESN country.'),
      '#default_value' => in_array($config->get('level'), $country_allowed_levels, TRUE) ? $config->get('country') : NULL,
      '#options' => $countries,
      '#ajax' => [
        'callback' => [$this, 'setOptionsSection'],
        'event' => 'change',
        'wrapper' => 'edit-esn-section',
      ],
      '#states' => [
        'visible' => [
          ':input[name="level"]' => [
            ['value' => 'local'],
            'or',
            ['value' => 'national'],
          ],
        ],
        'required' => [
          ':input[name="level"]' => [
            ['value' => 'local'],
            'or',
            ['value' => 'national'],
          ],
        ],
      ],
    ];

    $section_entities = $this->entityTypeManager->getStorage('esn_organisation')
      ->loadByProperties(['type' => 'section']);
    $sections = [];
    /** @var \Drupal\esn_accounts_api\Entity\Organisation $section_entity */
    foreach ($section_entities as $section_entity) {
      $sections += [$section_entity->getCode() => $section_entity->label()];
    }

    $form['group_select_organisation']['section'] = [
      '#type' => 'select',
      '#title' => $this->t('Select your section.'),
      '#description' => $this->t('Please select your section.'),
      '#default_value' => $config->get('level') === 'local' ? $config->get('selected_organisation_code') : NULL,
      '#options' => $sections,
      '#states' => [
        'visible' => [
          ':input[name="level"]' => ['value' => 'local'],
        ],
      ],
      '#prefix' => '<div id="edit-esn-section">',
      '#suffix' => '</div>',
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
   * An AJAX callback to save the value of the country field to state.
   *
   * @param array $form
   *   The form array.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state object.
   */
  public function setOptionsSection(array &$form, FormStateInterface $form_state) {

    $response = new AjaxResponse();
    if ($form_state->getValue('level') === 'local') {
      $country = $form_state->getValue('country');
      if (!is_null($country)) {

        /** @var \Drupal\esn_accounts_api\Entity\Organisation[] $array */
        $array = $this->entityTypeManager->getStorage('esn_organisation')
          ->loadByProperties(['country_code' => $country]);
        $country_code = reset($array)->getCountryCode();

        $filter_criteria = [
          'type' => 'section',
          'country_code' => $country_code,
        ];

        $section_entities = $this->entityTypeManager->getStorage('esn_organisation')
          ->loadByProperties($filter_criteria);
        $sections = [];
        /** @var \Drupal\esn_accounts_api\Entity\Organisation $section_entity */
        foreach ($section_entities as $section_entity) {
          $sections += [$section_entity->getCode() => $section_entity->label()];
        }

        $form['group_select_organisation']['section']['#options'] = $sections;

        $response->addCommand(new ReplaceCommand('#edit-esn-section', $form['group_select_organisation']['section']));

      }

    }
    return $response;

  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    $config = $this->config('satellite_api_accounts.settings');
    // Reset the configuration.
    $config->delete();
    $level = $form_state->getValue('level');
    $config->set('level', $level);
    if ($level === 'local') {
      $selected_organisation_code = $form_state->getValue('section');
      $config->set('country', $form_state->getValue('country'));
    }
    elseif ($level === 'national') {
      $selected_organisation_code = $form_state->getValue('country');
      $config->set('country', $form_state->getValue('country'));
    }
    else {
      $selected_organisation_code = current($this->entityTypeManager->getStorage('esn_organisation')
        ->loadByProperties(['type' => 'international']))->getCode();
    }

    /** @var \Drupal\esn_accounts_api\Entity\Organisation $organisation */
    $organisation = current($this->entityTypeManager->getStorage('esn_organisation')
      ->loadByProperties(['code' => $selected_organisation_code]));

    $logo_id = $organisation->saveLogo();
    $var = $this->entityTypeManager->getStorage('file')
      ->load($logo_id)
      ->getFileUri();
    $var = $this->streamWrapperManager->normalizeUri($var);
    $relative_path = $this->fileUrlGenerator->transformRelative($var);

    $config->set('selected_organisation_code', $selected_organisation_code);
    $config->save();

    $this->configFactory()
      ->getEditable('system.site')
      ->set('name', $organisation->label())
      ->save();
    $this->configFactory()
      ->getEditable('satellite_theme.settings')
      ->set('logo.use_default', FALSE)
      ->set('logo.path', $relative_path)
      ->save();

    $this->messenger()
      ->addStatus($this->t('Configuration updated successfully!'));
  }

}
