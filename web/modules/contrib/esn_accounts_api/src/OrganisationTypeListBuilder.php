<?php

namespace Drupal\esn_accounts_api;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of organisation type entities.
 *
 * @see \Drupal\esn_accounts_api\Entity\OrganisationType
 */
class OrganisationTypeListBuilder extends ConfigEntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['title'] = $this->t('Label');

    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['title'] = [
      'data' => $entity->label(),
      'class' => ['menu-label'],
    ];

    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build = parent::render();

    $build['table']['#empty'] = $this->t(
      'No organisation types available. <a href=":link">Add organisation type</a>.',
      [':link' => Url::fromRoute('entity.organisation_type.add_form')->toString()]
    );

    return $build;
  }

}
