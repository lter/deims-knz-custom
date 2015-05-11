<?php

/**
 * @file
 * Definition of SQLContentNewsTickerMigration.
 */
class SQLContentNewsTickerMigration extends Migration {

  public function __construct($arguments) {
    parent::__construct($arguments);
    $this->description = t('The News Ticker articles Konza Prairie LTER');

    $this->map = new MigrateSQLMap(
      $this->machineName,
      array(
        'ItemID' => array('type' => 'int',
                               'unsigned' => TRUE,
                               'not null' => TRUE,
                               'alias' => 'ni',
                              )
      ),
      MigrateDestinationNode::getKeySchema()
    );

      $query = Database::getConnection('default', 'mssqlknzmeta')
               ->select('newsticker','ni')
               ->fields('ni', array('ItemID', 'Abstract','EventDate','Location','Title','isActive'));

    $this->source = new MigrateSourceSQL($query);
    $this->destination = new MigrateDestinationNode('article');

    // Mapped fields
    $this->addFieldMapping('uid')->defaultValue(1);
    $this->addFieldMapping('sticky')->defaultValue(0);
    $this->addFieldMapping('title','Title');
    $this->addFieldMapping('field_article_date','EventDate');

    $this->addFieldMapping('status','isActive');
    $this->addFieldMapping('body','Abstract');
    $this->addFieldMapping('field_section')
     ->defaultValue('News Item');

    // No Unmapped source fields

    // Unmapped destination fields
    $this->addUnmigratedDestinations(array(
      'body:format',
      'body:summary',
      'body:language',
      'field_article_date:timezone',
      'field_article_date:rrule',
      'field_article_date:to',
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
      'field_keywords',	//Keywords (taxonomy_term_reference)
      'field_keywords:source_type',	//Option: Set to 'tid' when the value is a source ID (link is external)
      'field_keywords:create_term',	//Option: Set to TRUE to create referenced terms when necessary (link is external)
      'field_keywords:ignore_case',	//Option: Set to TRUE to ignore case differences between source data and existing term names (link is external)
      'field_related_people',	//Contacts (entityreference)
      'field_related_publications',	//Related publications (entityreference)
      'field_section:source_type',	//Option: Set to 'tid' when the value is a source ID (link is external)
      'field_section:create_term',	//Option: Set to TRUE to create referenced terms when necessary (link is external)
      'field_section:ignore_case',
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
