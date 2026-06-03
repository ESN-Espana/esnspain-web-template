<?php

namespace Drupal\esn_accounts_api\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Config\TypedConfigManagerInterface;
use Drupal\Core\CronInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\State\StateInterface;
use Drupal\esn_accounts_api\EsnInternationalManagerInterface;
use Drupal\esn_accounts_api\SyncDefaultEntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Form with examples on how to use cron.
 */
class AccountSyncForm extends ConfigFormBase {

  use MessengerTrait;
  use LoggerChannelTrait;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    TypedConfigManagerInterface $typedConfigManager,
    protected AccountInterface $currentUser,
    protected CronInterface $cron,
    protected StateInterface $state,
    protected SyncDefaultEntityInterface $esnSyncEntities,
    protected EsnInternationalManagerInterface $esnInternationalManager,
    ) {
    parent::__construct($config_factory, $typedConfigManager);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('config.typed'),
      $container->get('current_user'),
      $container->get('cron'),
      $container->get('state'),
      $container->get('esn_accounts_api.sync_all_entities'),
      $container->get('esn_accounts_api.esn_international'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'esn_accounts_api';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->configFactory->get('esn_accounts_api.settings');

    $form['configuration'] = [
      '#type' => 'details',
      '#title' => $this->t('Configuration of Accounts API synchronisation'),
      '#open' => TRUE,
    ];
    $form['configuration']['must_run'] = [
      '#type' => 'checkbox',
      '#title' => $this->t("Sync data automatically during cron."),
      '#default_value' => $config->get('must_run'),
    ];
    $form['configuration']['sync_countries'] = [
      '#type' => 'checkbox',
      '#title' => $this->t("Sync countries data from the endpoint each time the synchronisation is performed."),
      '#default_value' => $config->get('sync_countries'),
      '#description' => $this->t("Warning: disabling this option when data has been previously synced will cause its deletion. (FEATURE NOT IMPLEMENTED YET)."),
    ];
    $form['configuration']['sync_sections'] = [
      '#type' => 'checkbox',
      '#title' => $this->t("Sync sections data from the endpoint each time the synchronisation is performed."),
      '#default_value' => $config->get('sync_sections'),
      '#description' => $this->t("Warning: disabling this option when data has been previously synced will cause its deletion. (FEATURE NOT IMPLEMENTED YET)."),
    ];

    if ($this->currentUser->hasPermission('administer esn_accounts sync')) {
      $form['sync_manual'] = [
        '#type' => 'details',
        '#title' => $this->t('Sync data manually'),
        '#open' => TRUE,
      ];
      $form['sync_manual']['info'] = [
        '#type' => 'item',
        '#markup' => $this->t('Manual sync all the data (according to the configuration from above), forcing its update with the data from the endpoint.'),
      ];
      $form['sync_manual']['actions'] = ['#type' => 'actions'];
      $form['sync_manual']['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Force manual sync'),
        '#submit' => [[$this, 'syncManual']],
      ];
    }

    return parent::buildForm($form, $form_state);
  }

  /**
   * Allow user to directly execute cron, optionally forcing it.
   */
  public function syncManual(array &$form, FormStateInterface $form_state) {

    $config = $this->configFactory->get('esn_accounts_api.settings');

    $sync_countries = $config->get('sync_countries');
    $sync_sections = $config->get('sync_sections');
    $messages = [];
    // Then the info will be fetched and updated each time cron() is executed.
    $messages[] = $this->esnInternationalManager->checkAddNotFound();
    if ($sync_countries) {
      $messages[] = $this->esnSyncEntities->syncCountriesIntoEntity(TRUE);
    }
    if ($sync_sections) {
      $messages[] = $this->esnSyncEntities->syncSectionsIntoEntity(TRUE);
    }

    foreach ($messages as $message) {
      if ('status' === $message['type']) {
        $this->messenger()->addStatus($message['message']);
        $this->logger('esn_accounts_api')->info($message['message']);
      }
      elseif ('error' === $message['type']) {
        $this->messenger()->addError($message['message']);
        $this->logger('esn_accounts_api')->error($message['message']);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Update if the sync must be executed when cron() is. This will be read
    // when the .module hook_cron function fires and will be used to ensure
    // that action is taken only if this value is TRUE.
    $this->configFactory->getEditable('esn_accounts_api.settings')
      ->set('must_run', $form_state->getValue('must_run'))
      ->set('sync_countries', $form_state->getValue('sync_countries'))
      ->set('sync_sections', $form_state->getValue('sync_sections'))
      ->save();

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['esn_accounts_api.settings'];
  }

}
