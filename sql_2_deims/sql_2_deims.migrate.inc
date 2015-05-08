<?php

/**
 * @file
 * Migrate module integration with the DEIMS D6 Migration module.
 */

/**
 * Implements hook_migrate_api().
 */
function sql_2_deims_migrate_api() {
  $info = array(
    'api' => 2,
    'groups' => array(
      SQL_MIGRATION_GROUP => array(
        'title' => t('MsSql_knzmeta'),
      ),
    ),
    'migrations' => array(
      'sqlFile' => array(
         'class_name' => 'sqlFileMigration',
      ),
      'sqlContentNewsTicker' => array(
         'class_name' => 'sqlContentNewsTickerMigration',
      ),
      'sqlContentNewsItems' => array(
         'class_name' => 'sqlContentNewsItemsMigration',
      ),
      'sqlContentPerson' => array(
         'class_name' => 'sqlContentPersonMigration',
      ),
      'sqlContentPages' => array(
         'class_name' => 'sqlContentPagesMigration',
      ),
    ),
  );

  foreach ($info['migrations'] as &$migration) {
    $migration['group_name'] = SQL_MIGRATION_GROUP;
  }

  // Allow other modules to alter our migration classes.
  $overrides = module_invoke_all('sql_migration_overrides');
  foreach ($overrides as $machine_name => $override) {
    if (isset($info['migrations'][$machine_name])) {
      $info['migrations'][$machine_name] = $override + $info['migrations'][$machine_name];
    }
  }
  return $info;
}
