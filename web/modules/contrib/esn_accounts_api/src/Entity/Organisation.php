<?php

namespace Drupal\esn_accounts_api\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\File\FileExists;
use Drupal\file\FileInterface;

/**
 * Defines a base esn_organisation entity.
 *
 * @ContentEntityType(
 *   id = "esn_organisation",
 *   label = @Translation("ESN Organisation"),
 *   label_collection = @Translation("ESN Organisations"),
 *   label_singular = @Translation("ESN Organisation"),
 *   label_plural = @Translation("ESN Organisations"),
 *   base_table = "esn_organisation",
 *   data_table = "esn_organisation_field_data",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "label" = "title",
 *     "bundle" = "type",
 *   },
 *   bundle_entity_type = "esn_organisation_type",
 *   bundle_label = @Translation("Type"),
 *   admin_permission = "administer site configuration",
 *   fieldable = TRUE,
 * )
 */
class Organisation extends ContentEntityBase implements OrganisationInterface {

  /**
   * The extension.list.module service.
   *
   * @var \Drupal\Core\Extension\ExtensionList
   */
  protected $extensionList;

  /**
   * The file.repository service.
   *
   * @var \Drupal\file\FileRepository
   */
  protected $fileRepository;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $values, $entity_type, $bundle = FALSE, $translations = []) {

    parent::__construct($values, $entity_type, $bundle, $translations);
    $this->extensionList = \Drupal::service('extension.list.module');
    $this->fileRepository = \Drupal::service('file.repository');
  }

  /**
   * {@inheritdoc}
   */
  public function getTitle() {
    return $this->get('title')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setTitle($title) {
    $this->set('title', $title);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getStatus() {
    return $this->get('status')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setStatus($status) {
    $this->set('status', $status);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCode() {
    return $this->get('code')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCode($code) {
    $this->set('code', $code);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCountryCode() {
    return $this->get('country_code')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCountryCode($country_code) {
    $this->set('country_code', $country_code);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getLastUpdate() {
    return $this->get('updated_api')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setLastUpdate($updated) {
    $this->set('updated_api', $updated);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCountry() {
    return $this->get('country')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCountry($country) {
    $this->set('country', $country);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getAddress() {
    return $this->get('address')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setAddress($address) {
    $this->set('address', $address);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getUrlFacebook() {
    return $this->get('facebook')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setUrlFacebook($facebook_url) {
    $this->set('facebook', $facebook_url);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getUrlInstagram() {
    return $this->get('instagram')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setUrlInstagram($instagram_url) {
    $this->set('instagram', $instagram_url);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getUrlTwitter() {
    return $this->get('twitter')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setUrlTwitter($twitter_url) {
    $this->set('twitter', $twitter_url);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getUrlVideo() {
    return $this->get('video')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setUrlVideo($video_url) {
    $this->set('video', $video_url);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getUrlWebsite() {
    return $this->get('website')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setUrlWebsite($website_url) {
    $this->set('website', $website_url);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getState() {
    return $this->get('field_state')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setState($state) {
    return $this->set('field_state', $state);
  }

  /**
   * {@inheritdoc}
   */
  public function getCities() {
    return $this->get('field_cities')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCities($cities) {
    $this->set('field_cities', $cities);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getLocation() {
    return $this->get('field_location')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setLocation($location) {
    $this->set('field_location', $location);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getUniversityName() {
    return $this->get('field_university_name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setUniversityName($university_name) {
    return $this->set('field_university_name', $university_name);
  }

  /**
   * {@inheritdoc}
   */
  public function getUrlUniversity() {
    return $this->get('field_university_website')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setUrlUniversity($university_url) {
    $this->set('field_university_website', $university_url);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getRemoteLogoPath() {
    return $this->get('logo')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getLocalLogo() {
    return $this->get('logo_file')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setLocalLogo($id) {
    $this->set('logo_file', $id);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setLogo($path) {
    $this->set('logo', $path);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function saveLogo() {
    if ($this->getCode() === 'EU-INTL-ESN') {
      $logo = file_get_contents(DRUPAL_ROOT . '/' . $this->extensionList->getPath('esn_core') . '/images/esn_logo_full.png');
      $file = $this->fileRepository->writeData($logo, 'public://EU-INTL-ESN.png', FileExists::Replace);
      $this->setLocalLogo($file->id());
      return $file->id();
    }

    $logo_path = $this->getRemoteLogoPath();
    if ($logo_path !== '') {
      $data = (string) \Drupal::httpClient()->get($logo_path)->getBody();
      $path = \Drupal::service('file_system')->basename(parse_url($logo_path)['path']);
      $path = \Drupal::config('system.file')->get('default_scheme') . '://' . $path;
      $path = \Drupal::service('stream_wrapper_manager')->normalizeUri($path);
      /** @var \Drupal\file\FileInterface $file */
      $file = \Drupal::service('file.repository')->writeData($data, $path, FileExists::Replace);
      $this->setLocalLogo($file->id());
      return $file->id();
    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    // Get the field definitions for 'id' and 'uuid' from the parent.
    $fields = parent::baseFieldDefinitions($entity_type);

    // Title of the procedure.
    $fields['title'] = BaseFieldDefinition::create('string')
      ->setLabel(t("Title"))
      ->setSetting('max_length', 255)
      ->setRequired(TRUE);

    $fields['country_code'] = BaseFieldDefinition::create('string')
      ->setLabel(t("Country code"))
      ->setSetting('max_length', 2)
      ->setRequired(TRUE);

    $fields['country'] = BaseFieldDefinition::create('string')
      ->setLabel(t("Country"))
      ->setSetting('max_length', 255)
      ->setRequired(TRUE);

    $fields['code'] = BaseFieldDefinition::create('string')
      ->setLabel(t("Code of the organisation (Country/Section)"))
      ->setSetting('max_length', 20)
      ->setRequired(TRUE);

    // The address of the organisation, saved as long string.
    $fields['address'] = BaseFieldDefinition::create('string_long')
      ->setLabel(t('Address'))
      ->setDescription(t('The address of the organisation'))
      ->setRequired(TRUE);

    $fields['facebook'] = BaseFieldDefinition::create('uri')
      ->setLabel(t('Facebook Profile'))
      ->setDescription('The uri for the Facebook page of the organisation')
      ->setSetting('max_length', 255)
      ->setSetting('case_sensitive', TRUE);

    $fields['instagram'] = BaseFieldDefinition::create('uri')
      ->setLabel(t('Instagram Profile'))
      ->setDescription('The uri for the Instagram page of the organisation')
      ->setSetting('max_length', 255)
      ->setSetting('case_sensitive', TRUE);

    $fields['twitter'] = BaseFieldDefinition::create('uri')
      ->setLabel(t('Twitter Profile'))
      ->setDescription('The uri for the Twitter page of the organisation')
      ->setSetting('max_length', 255)
      ->setSetting('case_sensitive', TRUE);

    $fields['video'] = BaseFieldDefinition::create('uri')
      ->setLabel(t('Video of organisation Profile'))
      ->setDescription('The uri for a video describing the organisation.')
      ->setSetting('max_length', 255)
      ->setSetting('case_sensitive', TRUE);

    $fields['logo'] = BaseFieldDefinition::create('string')
      ->setLabel(t("Path of the logo of the organisation to ESN Accounts"))
      ->setSetting('max_length', 100);

    $fields['logo_file'] = BaseFieldDefinition::create('file')
      ->setLabel(t("The file entity of the organisation's logo."))
      ->setDefaultValue(NULL);

    // The last time the organisation was updated in Accounts.
    $fields['updated_api'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Timestamp API updated'))
      ->setDescription(t('The last time the organisation was updated in Accounts.'))
      ->setRequired(TRUE);

    $fields['website'] = BaseFieldDefinition::create('uri')
      ->setLabel(t('Organisation website'))
      ->setDescription('The website of the organisation.')
      ->setSetting('max_length', 255)
      ->setSetting('case_sensitive', TRUE);

    $fields['updated'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Entity updated'))
      ->setDescription(t('The last time the organisation entity was updated.'));

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Status'))
      ->setDefaultValue(TRUE);

    return $fields;
  }

  /**
   * {@inheritDoc}
   */
  public function updateLogo(string $path) {

    if ($path !== '') {
      $data = (string) \Drupal::httpClient()->get($path)->getBody();
      $path = \Drupal::service('file_system')->basename(parse_url($path)['path']);
      $path = \Drupal::config('system.file')->get('default_scheme') . '://' . $path;
      $path = \Drupal::service('stream_wrapper_manager')->normalizeUri($path);
      /** @var \Drupal\file\FileInterface $file */
      $file = \Drupal::service('file.repository')->writeData($data, $path, FileExists::Replace);

      if ($file instanceof FileInterface) {
        $this->setLocalLogo($file->id());
        return $file->id();
      }
    }
    return FALSE;
  }

}
