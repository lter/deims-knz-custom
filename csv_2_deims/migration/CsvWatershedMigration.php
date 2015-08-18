<?php

/**
 * @file
 * Definition of CsvWatershedMigration. For now, this will assume that you downloaded all the datasource files in destination.
 */

class CsvWatershedMigration extends Migration {
  protected $dependencies = array();
  public function __construct(array $arguments) {

    parent::__construct($arguments);

    $options = array(); 
    $options['header_rows'] = 1; 
    $options['delimiter'] = ","; 

    $columns = array(
      0 => array('watsd_name', 'Name of Watershed'),
      1 => array('poly', 'The Polygon'),
   );

    $csv_file = DRUPAL_ROOT . '/' . 'sites/default/imports/knz-polygons.csv';

    $this->source = new MigrateSourceCSV($csv_file, $columns, $options);

    $this->description = t('CSV Migration of the Watershed Locations');

    $this->map = new MigrateSQLMap($this->machineName,
      array(
        'watsd_name' => array(
          'type' => 'varchar',
          'length' => 255,
          'not null' => TRUE,
          'description' => 'watershed code is the key',
        )
      ),
      MigrateDestinationNode::getKeySchema()
    );

    $this->destination = new MigrateDestinationNode('research_site');

//  Save to the default file scheme.

    $this->addFieldMapping('uid')->defaultValue(1);

    $this->addFieldMapping('title','watsd_name');

//  V. Important:
//  change the widget to WKT

    $this->addFieldMapping('field_coordinates','geo')
      ->description('in Prep');

    $this->addFieldMapping('field_coordinates:geo_type')->defaultValue('wkt');
    $this->addFieldMapping('field_coordinates:wkt','poly');
    $this->addFieldMapping('field_coordinates:lat', NULL);
    $this->addFieldMapping('field_coordinates:lon', NULL);
    $this->addFieldMapping('field_coordinates:left',NULL);  //	left
    $this->addFieldMapping('field_coordinates:top', NULL);	  //  top
    $this->addFieldMapping('field_coordinates:right', NULL);   //	right
    $this->addFieldMapping('field_coordinates:bottom',NULL);  //	bottom
    $this->addFieldMapping('field_point:input_format')->defaultValue('wkt');


    $this->addFieldMapping('field_section:ignore_case')->defaultValue(TRUE);
    $this->addFieldMapping('field_section')->defaultValue('Lakes');

    $this->addFieldMapping('promote')->defaultValue(0);
    $this->addFieldMapping('sticky')->defaultValue(0);
    $this->addFieldMapping('revision')->defaultValue(0);
    $this->addFieldMapping('status')->defaultValue(1);
    $this->addFieldMapping('log')->defaultValue('Migrated from CSV - mcmlter LAKE_DESCRIPTIONS lakedsc.dat');

//  need tweaks in term and terms. for keywords, etc.

    $this->addUnmigratedSources(array(
    ));

    $this->addUnmigratedDestinations(array(
      'language',	// Language  (fr, en, ...)
      'tnid',	        // The translation set id for this node
      'translate',      //	A boolean indicating whether this translation page needs to be updated
      'revision_uid',   //	Modified (uid)
      'is_new',
      'created',
      'changed',
      'field_elevation',
      'field_images',
      'field_images:file_class',
      'field_images:preserve_files',
      'field_images:destination_dir',
      'field_images:destination_file',
      'field_images:file_replace',
      'field_images:source_dir',
      'field_images:language', //	Subfield: Language for the field
      'field_images:urlencode', 
      'field_images:alt',
      'field_images:title', 
      'field_description:language',
      'path',
      'field_section:create_term',	//  Set to TRUE to create referenced terms when necessary
      'field_section:source_type',
      'pathauto',
      'comment',
      'field_site_details',
      'field_description:format',      
      'field_core_areas', //	Core Areas (taxonomy_term_reference)
      'field_core_areas:source_type',
      'field_core_areas:create_term',	
      'field_core_areas:ignore_case',
//      'field_coordinates:srid',
//      'field_coordinates:accuracy',	
    ));
  } 

  public function prepareRow($row) {

     parent::prepareRow($row);

     //concatenate stream_name, provenance and description.

     if(!empty($row->valley)){
       $row->description = $row->description . "\n" . ' Valley: ' . $row->valley;
     }

     if (!empty($row->dist_sea)){
       $row->description = $row->description . "\n" . ' Distance to Sea : ' . $row->dist_sea;
     }

     if(!empty($row->length)){
       $row->description = $row->description . "\n" . ' Maximum Length (km): ' . $row->length;
     }

     if(!empty($row->width)){
       $row->description = $row->description . "\n" . ' Maximum Width (km): ' . $row->width;
     }

     if(!empty($row->depth)){
       $row->description = $row->description . "\n" . ' Maximum Depth (m): ' . $row->depth;
     }

     if(!empty($row->surf_avg)){
       $row->description = $row->description . "\n" . ' Surface Area (km^2): ' . $row->surf_avg;
     }

     if(!empty($row->thick_avg)){
       $row->description = $row->description . "\n" . ' Ice Thickness Average Surface (m): ' . $row->thick_avg;
     }

     if(!empty($row->volume)){
       $row->description = $row->description . "\n" . ' Volume (m^3 * 10^6): ' . $row->volume;
     }


    // geos

      $row->geo = 'POINT (' . $row->long . ' ' . $row->lat . ')';
  }

}
