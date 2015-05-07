<?php

/**
 * @file
 * Definition of SQLContentPagesMigration.
 */
class SQLContentPagesMigration extends Migration {

  public function __construct($arguments) {
    parent::__construct($arguments);
    $this->description = t('The pages on Konza Prairie LTER');

    $this->map = new MigrateSQLMap(
      $this->machineName,
      array(
        'menuID' => array('type' => 'int',
                               'unsigned' => TRUE,
                               'not null' => TRUE,
                               'alias' => 'pc',
                              )
      ),
      MigrateDestinationNode::getKeySchema()
    );

      $query = Database::getConnection('default', 'mssqlknzmeta')
               ->select('pagecontents','pc')
               ->fields('pc', array('menuID', 'pageName', 'URL', 'content'));

    $this->source = new MigrateSourceSQL($query);
    $this->destination = new MigrateDestinationNode('page');

    // Mapped fields
    $this->addFieldMapping('uid')->defaultValue(1);
    $this->addFieldMapping('sticky')->defaultValue(0);
    $this->addFieldMapping('title','pageName');
    $this->addFieldMapping('body','content');
    $this->addFieldMapping('body:format')->defaultValue('full_html');
    $this->addFieldMapping('field_url','URL');

    
    // No Unmapped source fields

    // Unmapped destination fields
    $this->addUnmigratedDestinations(array(
      'body:summary',
      'body:language',
      'field_url:title',
      'field_url:attributes',
      'changed',
      'comment',
      'created',
      'is_new',
      'language',
      'log',
      'promote',
      'revision',
      'revision_uid',
      'status',
      'tnid',
      'translate',
      'field_files',   //	Files (file)
      'field_files:file_class',	//Option: Implementation of MigrateFile to use (link is external)
      'field_files:language',	//Subfield: Language for the field
      'field_files:preserve_files',	//Option: Boolean indicating whether files should be preserved or deleted on rollback (link is external)
      'field_files:destination_dir',	//Subfield: Path within Drupal files directory to store file (link is external)
      'field_files:destination_file',	//Subfield: Path within destination_dir to store the file. (link is external)
      'field_files:file_replace',	//Option: Value of $replace in that file function. Defaults to FILE_EXISTS_RENAME. (link is external)
      'field_files:source_dir',	//Subfield: Path to source file. (link is external)
      'field_files:urlencode',	//Option: Encode all segments of the incoming path (defaults to TRUE). (link is external)
      'field_files:description',	//Subfield: String to be used as the description value (link is external)
      'field_files:display',	//Subfield: String to be used as the display value (link is external)
      'field_images',	// Images (image)
      'field_images:file_class',	//Option: Implementation of MigrateFile to use (link is external)
       'field_images:language',	//Subfield: Language for the field
      'field_images:preserve_files',  //	Option: Boolean indicating whether files should be preserved or deleted on rollback (link is external)
      'field_images:destination_dir',	//Subfield: Path within Drupal files directory to store file (link is external)
      'field_images:destination_file',	//Subfield: Path within destination_dir to store the file. (link is external)
      'field_images:file_replace',	//Option: Value of $replace in that file function. Defaults to FILE_EXISTS_RENAME. (link is external)
      'field_images:source_dir',	//Subfield: Path to source file. (link is external)
      'field_images:urlencode',	//Option: Encode all segments of the incoming path (defaults to TRUE). (link is external)
      'field_images:alt',	//Subfield: String to be used as the alt value (link is external)
      'field_images:title',
    ));

    if (module_exists('statistics')) {
      $this->addUnmigratedDestinations(
        array('totalcount', 'daycount', 'timestamp'));
    }
  }

  public function prepare($node, $row) {
    // Remove any empty or illegal delta field values.
    EntityHelper::removeInvalidFieldDeltas('node', $node);
    EntityHelper::removeEmptyFieldValues('node', $node);
  }

}
