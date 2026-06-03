<?php

namespace Drupal\esn_accounts_api\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\EntityDescriptionInterface;

/**
 * Interface for the class OrganisationType.
 */
interface OrganisationTypeInterface extends ConfigEntityInterface, EntityDescriptionInterface {

  /**
   * Gets the description.
   *
   * @return string
   *   The description of this organisation type.
   */
  public function getDescription();

  /**
   * Sets the description.
   *
   * @param mixed $description
   *   The description of this organisation type.
   *
   * @return \Drupal\esn_accounts_api\Entity\OrganisationTypeInterface
   *   Returns this object.
   */
  public function setDescription($description);

}
