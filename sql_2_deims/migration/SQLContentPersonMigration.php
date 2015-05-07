<?php

/**
 * @file
 * Definition of SQLContentPersonMigration.
 */
class SQLContentPersonMigration extends Migration {

  public function __construct($arguments) {
    parent::__construct($arguments);
    $this->description = t('The personnel directory on Konza Prairie LTER');

    $this->map = new MigrateSQLMap(
      $this->machineName,
      array(
        'personnelID' => array('type' => 'int',
                               'unsigned' => TRUE,
                               'not null' => TRUE,
                               'alias' => 'p',
                              )
      ),
      MigrateDestinationNode::getKeySchema()
    );

      $query = Database::getConnection('default', 'mssqlknzmeta')
               ->select('personnel','p')
               ->fields('p', array('personnelID', 'firstName', 'lastName', 'middleName',
                                 'salutation','education','email','lterGroup','deliverpoint',
                                 'city','state','zipcode','phone1','phone1ext','fax',
                                 'onlineurl','interest','active','country'));

    $this->source = new MigrateSourceSQL($query);
    $this->destination = new MigrateDestinationNode('person');

    // Mapped fields

    $this->addFieldMapping('field_list_in_directory')->defaultValue(1);

    $this->addFieldMapping('uid')->defaultValue(1);

    $this->addFieldMapping('sticky')->defaultValue(0);
    $this->addFieldMapping('field_name','firstName');     // subfield
    $this->addFieldMapping('field_name:given','firstName');     // subfield
    $this->addFieldMapping('field_name:family','lastName');      // name subfield
    $this->addFieldMapping('field_name:middle','middleName');    // name subfield
    $this->addFieldMapping('field_name:credentials','education');    // name subfield credentials
    $this->addFieldMapping('field_name:title','salutation');    // name subfield title
    $this->addFieldMapping('field_active','active');   
    $this->addFieldMapping('field_email','email');
    $this->addFieldMapping('field_person_role','lterGroup');
    $this->addFieldMapping('field_address:thoroughfare','deliverpoint');
    $this->addFieldMapping('field_address:locality','city');
    $this->addFieldMapping('field_address:administrative_area','state');
    $this->addFieldMapping('field_address:postal_code','zipcode');
    $this->addFieldMapping('field_address')->defaultValue('US');
    $this->addFieldMapping('field_phone','phone1');     // phone1ext
    $this->addFieldMapping('field_fax','fax');
    $this->addFieldMapping('field_url','onlineurl');
    $this->addFieldMapping('field_person_interest','interest');
    $this->addFieldMapping('field_person_id','personnelID');

    
    // Unmapped source fields
    $this->addUnmigratedSources(array( 
      'phone1ext',
      'salutation',
    ));

    // Unmapped destination fields
    $this->addUnmigratedDestinations(array(
      'field_url:title',
      'field_url:attributes',
      'field_user_account',
      'field_person_title',
      'field_person_title:language',
      'field_address:sub_administrative_area',
      'field_address:dependent_locality',
      'field_address:premise',
      'field_address:sub_premise',
      'field_address:organisation_name',
      'field_address:name_line',
      'field_address:first_name',
      'field_address:last_name',
      'field_address:data',
      'field_name:generational',
      'field_organization',
      'field_person_interest:language', 
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
      'field_associated_biblio_author',
    ));

    if (module_exists('statistics')) {
      $this->addUnmigratedDestinations(
        array('totalcount', 'daycount', 'timestamp'));
    }
  }

  public function prepare($node, $row) {
    $node->auto_entitylabel_applied = TRUE;
    // Remove any empty or illegal delta field values.
    EntityHelper::removeInvalidFieldDeltas('node', $node);
    EntityHelper::removeEmptyFieldValues('node', $node);
  }

}
