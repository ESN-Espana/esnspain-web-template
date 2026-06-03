<?php

namespace Drupal\satellite_demo\Plugin\migrate\process;

use Drupal\migrate\Attribute\MigrateProcess;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Plugin\migrate\process\EntityExists;
use Drupal\migrate\Row;

/**
 * The plugin checks if a given entity exists and returns the next available ID.
 *
 * Example usage with configuration:
 * @code
 *   field_tags:
 *     plugin: entity_exists
 *     source: tid
 *     entity_type: taxonomy_term
 * @endcode
 */
#[MigrateProcess('custom_file_exists')]
class FileExists extends EntityExists {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    while (parent::transform($value, $migrate_executable, $row, $destination_property)) {
      $value++;
    }
    return (string) $value;
  }

}
