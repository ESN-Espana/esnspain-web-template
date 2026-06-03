<?php

namespace Drupal\esn_accounts_api\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBundleBase;

/**
 * Defines the organisation type entity.
 *
 * @ConfigEntityType(
 *   id = "esn_organisation_type",
 *   label = @Translation("ESN Organisation Type"),
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *   },
 *   config_prefix = "esn_organisation_type",
 *   config_export = {
 *     "id",
 *     "label",
 *     "description",
 *   },
 *   bundle_of = "esn_organisation",
 *   admin_permission = "administer site configuration"
 * )
 */
class OrganisationType extends ConfigEntityBundleBase implements OrganisationTypeInterface {

  /**
   * The machine name of the organisation type.
   *
   * @var string
   */
  protected $id;

  /**
   * The human-readable name of the organisation type.
   *
   * @var string
   */
  protected $label;

  /**
   * A brief description of the organisation type.
   *
   * @var string
   */
  protected $description;

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->id;
  }

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription($description) {
    $this->description = $description;
    return $this;
  }

}
