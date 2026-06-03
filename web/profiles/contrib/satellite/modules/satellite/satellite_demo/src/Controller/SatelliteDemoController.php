<?php

namespace Drupal\satellite_demo\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\migrate\Plugin\MigrationPluginManager;
use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Core\Controller\ControllerBase;
use Drupal\migrate\MigrateExecutable;
use Drupal\migrate\Plugin\MigrationInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for Satellite demo routes.
 */
class SatelliteDemoController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * {@inheritDoc}
   */
  public function __construct(protected MigrationPluginManager $migrationPluginManager) {}

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('plugin.manager.migration')
    );
  }

  /**
   * Run the node_migration_no_migrate_drupal test migration.
   *
   * @return array
   *   A renderable array.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   */
  public function execute() {
    $definitions = $this->migrationPluginManager->getDefinitions();
    $demo_migration_ids = ['menus', 'paragraphs', 'blocks', 'files'];
    foreach ($demo_migration_ids as $demo_migration_id) {
      if (!isset($definitions[$demo_migration_id])) {
        throw new InvalidPluginDefinitionException($demo_migration_id);
      }
    }

    $migrations = $this->migrationPluginManager->createInstancesByTag('Satellite Demo');
    foreach ($migrations as $migration) {
      $result = (new MigrateExecutable($migration))->import();
      if ($result !== MigrationInterface::RESULT_COMPLETED) {
        throw new \RuntimeException('Migration failed');
      }
    }

    $build['content'] = [
      '#type' => 'item',
      '#markup' => $this->t('Migration successful!'),
    ];
    $this->state()->set('satellite_demo_migrations_have_run', 1);

    return $build;
  }

}
