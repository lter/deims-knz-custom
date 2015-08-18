<?php

/**
 * @file
 * Migrate module integration with the DEIMS Migration modules.
 */

/**
 * Implements hook_migrate_api().
 */
function csv_2_deims_migrate_api() {
  $info = array(
    'api' => 2,
    'groups' => array(
      CSV_MIGRATION_GROUP => array(
        'title' => t('Comma Delimited File Migration'),
      ),
    ),
    'migrations' => array(
      'CsvWatershed' => array(
         'class_name' => 'CsvWatershedMigration',
      ),
    ),
  );

  foreach ($info['migrations'] as &$migration) {
    $migration['group_name'] = CSV_MIGRATION_GROUP;
  }

  // Allow other modules to alter our migration classes.
  $overrides = module_invoke_all('csv_migration_overrides');
  foreach ($overrides as $machine_name => $override) {
    if (isset($info['migrations'][$machine_name])) {
      $info['migrations'][$machine_name] = $override + $info['migrations'][$machine_name];
    }
  }
  return $info;
}
