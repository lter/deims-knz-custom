<?php

/**
 * @file
 * Definition of EmlDataFileMigration.
 */

class EmlDataFileMigration extends XMLMigration {

  public function __construct(array $arguments) {

    parent::__construct($arguments);

    $fields = array(
       'datasrcname' => t('The table name in EML'),
       'datasrcdescription' => t('Description of the data source in EML'),
       'headerlines' => t('The number of header lines in the data source'),
       'footerlines' => t('The number of footer lines in the data source'),
       'fieldDelimiter' => t('The field delimiter as expressed in EML'),
       'quoteCharacter' => t('The character used to quote text'),
       'recordDelimiter' => t('The record delimiter, as expressed in EML'),
       'orientation' => t('Whether content is arranged by columns or rows'),
   //   'begindate' => t('The start date for this data source'),
   //    'enddate' => t('The end date for this data source'),
       'datasrcmethods' => t('The methods at the data source level'),
       'datasrcinstrumentation' => t('Instruments at the data source level'),
       'datasrcquality' => t('QA -quality assurance- notes at the data source level'),
   //    'datasrcresearchsiteref' => t('data source research site reference'),
    );
    // The source ID here is the one retrieved from each data item in the XML file, and
    // used to identify specific items
    $this->map = new MigrateSQLMap($this->machineName,
       array(
        'entityName' => array(
          'type' => 'varchar',
          'length' => 255,
          'not null' => TRUE,
          'description' => 'PackageId',
         )
       ),
       MigrateDestinationRole::getKeySchema()
    );

    // This can also be an URL instead of a local file path.
    $xml_folder = DRUPAL_ROOT . '/' .
    drupal_get_path('module', 'eml_2_deims') . '/xml/producers/';
    $items_url = $xml_folder . 'oldemlfilename.xml';

    // the xpath
    $item_xpath = '/eml:eml/dataset/dataTable';  // relative to document

    $item_ID_xpath = 'entityName';          // relative to item_xpath

    $items_class = new MigrateItemsXML($items_url, $item_xpath, $item_ID_xpath);
    $this->source = new MigrateSourceMultiItems($items_class, $fields);

    $this->destination = new MigrateDestinationNode('data_source');

    $this->addFieldMapping('title', 'datasrcname')
      ->xpath('entityName');

    $this->addFieldMapping('field_description', 'datasrcdescription')
      ->xpath('entityDescription');

    $this->addFieldMapping('field_data_source_file', 'datasrcname')
      ->xpath('entityName')
      ->sourceMigration('EmlFile');

    $this->addFieldMapping('field_data_source_file:preserve_files')->defaultValue(TRUE);
    $this->addFieldMapping('field_data_source_file:file_class')->defaultValue('MigrateFileFid');

//@todo methods are of the textType, which are hard to parse as they are complex type.
    $this->addFieldMapping('field_methods', 'datasrcmethods')
       ->xpath('methods/methodStep/description/para/literalLayout');
    $this->addFieldMapping('field_instrumentation', 'datasrcinstrumentation')
       ->xpath('methods/methodStep/instrumentation');
    $this->addFieldMapping('field_quality_assurance', 'datasrcquality')
       ->xpath('methods/methodStep/instrumentation');
  //  $this->addFieldMapping('field_related_sites', 'datasrcresearchsiteref')
  //    ->xpath('entityName')
  //    ->sourceMigration(array('EmlDatasourceResearchSite'));

    $this->addFieldMapping('field_variables')
      ->description('Handled in prepare().');

//  these are the data source properties (aka eml physical/dataFormat)
    $this->addFieldMapping('field_csv_header_lines', 'headerlines')
       ->xpath('physical/dataFormat/textFormat/numHeaderLines');
    $this->addFieldMapping('field_csv_footer_lines', 'footerlines')
       ->xpath('physical/dataFormat/textFormat/numFooterLines');
    $this->addFieldMapping('field_csv_orientation', 'orientation')
       ->xpath('physical/dataFormat/textFormat/attributeOrientation');
    $this->addFieldMapping('field_csv_quote_character', 'quoteCharacter')
       ->xpath('physical/dataFormat/textFormat/simpleDelimited/quoteCharacter');
    $this->addFieldMapping('field_csv_field_delimiter', 'fieldDelimiter')
       ->xpath('physical/dataFormat/textFormat/simpleDelimited/fieldDelimiter');
    $this->addFieldMapping('field_csv_record_delimiter', 'recordDelimiter')
       ->xpath('physical/dataFormat/textFormat/recordDelimiter');
    $this->addFieldMapping('uid')->defaultValue(1);

      //@toDo   Treat this in prepare, since it could be a mix and mash of singleDates
      // and range of dates. also, something is up w/ end date.

 ///  The Data Conn would force you to have a Data in a database.
 ///  No problem if oci8 is working, or we have a mysql-hotcopy.
/**
    $this->addFieldMapping('field_deims_data_explorer')
      ->defaultValue('knz_data');             //    Remote Data Source (schema_reference)
    $this->addFieldMapping('field_deims_data_explorer:table','datasrcname')  //    table
      ->xpath('entityName');
*/
    $this->addUnmigratedDestinations(array(
      'path',    	//      Path alias
      'comment',	//      Whether comments may be posted to the node
      'pathauto',
      'created',       //	Created timestamp
      'changed',       //	Modified timestamp
      'status',	       //       Published
      'promote',       //       Promoted to front page
      'sticky',	       //       Sticky at top of lists
      'revision',      //       Create new revision
      'log',	       //       Revision Log message
      'language',      //       Language (fr, en, ...)
      'tnid',          //	The translation set id for this node
      'translate',     //	A boolean indicating whether this translation page needs to be updated
      'revision_uid',  //	Modified (uid)
      'is_new', 
      'field_data_source_file:language',
      'field_data_source_file:description',
      'field_data_source_file:display',
      'field_methods:language',
      'field_instrumentation:language',
      'field_quality_assurance:language',
      'field_variables:name',
      'field_variables:type',
      'field_variables:definition',
      'field_variables:data',
      'field_variables:missing_values',
      'field_description:format',
      'field_description:language',
      'field_csv_quote_character:language',
      'field_csv_field_delimiter:language',
      'field_csv_record_delimiter:language',
      'field_date_range',
      'field_date_range:to',
      'field_date_range:timezone',
      'field_date_range:rrule',
      'field_instrumentation:format',
      'field_methods:format',
      'field_quality_assurance:format',
    ));
  }

  public function prepareRow($row) {
    parent::prepareRow($row);
  }

  public function prepare($node, $row) {
    // Fetch and prepare the variables field.
    $node->field_variables[LANGUAGE_NONE] = $this->get_variables($node, $row);

    // Remove any empty or illegal delta field values.
    EntityHelper::removeInvalidFieldDeltas('node', $node);
    EntityHelper::removeEmptyFieldValues('node', $node);

    // Perform field validation and check for debugging information.
    if (empty($node->field_variables[LANGUAGE_NONE])) {
      $this->saveMessage('Empty variable field in data file node.', MigrationBase::MESSAGE_INFORMATIONAL);
    }
  }

  public function get_variables($node, $row) {
    // We already have the array of referenced variable nodes in this row variable.
    // First filter out any NULL or empty values before proceeding.
    $field_values = array();

    $attribute_list = $row->xml->attributeList;//->children();

//    foreach ($attribute_list as $child_name=>$variable) {
    foreach ($attribute_list as $variables) {
       foreach ($variables as $variable){
          $value = array();

          // The label value is not required, but node title is.
          if(isset($variable->attributeLabel)){
             $value['label'] = (string) $variable->attributeLabel;
          }
          $value['name'] = (string) $variable->attributeName;
          $value['definition'] = (string) $variable->attributeDefinition;
          $value['data'] = array();

          if (isset($variable->measurementScale->ratio->numericDomain->numberType) ){
            // even numberType is used to determine is a "physical" type, it is not used in DEIMS
            // $number_type = (string) $variable->measurementScale->ratio->numericDomain->numberType;
            $value['type'] = DEIMS_VARIABLE_TYPE_PHYSICAL;
            $stunit = (string) $variable->measurementScale->ratio->unit->standardUnit;
            $customunit = (string) $variable->measurementScale->ratio->unit->customUnit;
            if (strlen($stunit)>0){
                $value['data']['unit'] = $stunit;
            }else{
                $value['data']['unit'] = $customunit;
            }
            $value['data']['minimum'] = (string) $variable->measurementScale->ratio->numericDomain->bounds->minimum;
            $value['data']['maximum'] = (string) $variable->measurementScale->ratio->numericDomain->bounds->maximum;
            $value['data']['precision'] = (string) $variable->measurementScale->ratio->precision;
          } elseif (isset($variable->measurementScale->dateTime->formatString)){
            $format_string =  (string) $variable->measurementScale->dateTime->formatString;
            $value['type'] = DEIMS_VARIABLE_TYPE_DATE;
            $value['data']['pattern'] = $format_string;
          } elseif (isset($variable->measurementScale->nominal->nonNumericDomain->enumeratedDomain->codeDefinition->definition)){
            $value['type'] = DEIMS_VARIABLE_TYPE_CODES;
            // Extract the code-definition pairs from the simpleXmlObject into assoc. array
            $code_values = array();
            $codex = $variable->measurementScale->nominal->nonNumericDomain->enumeratedDomain;
            foreach ($codex as $codedefinitions){
              foreach  ($codedefinitions as $codedefpair){
                $code =  (string) $codedefpair->code;
                $defi = (string) $codedefpair->definition;
                $code_values[$code] = $defi;
              }
            }
            $value['data']['codes'] = $code_values;
          }
          else {
            $value['type'] = DEIMS_VARIABLE_TYPE_NOMINAL;
            $value['definition'] .= (string) $variable->measurementScale->nominal->nonNumericDomain->textDefinition->definition;
          }
           //  get the variables' missing values.
          $missing_values = array();
          if(isset($variable->missingValueCode->code)){
            $missingvalues = $variable->missingValueCode;
            foreach ($missingvalues as $missingpairs) {
               $codeM = (string) $missingpairs->code;
               $defi = (string) $missingpairs->codeExplanation;
               $missing_values[$codeM] = $defi;
            }
          }
          $value['missing_values'] = count($missing_values)>0 ? $missing_values : array();

          $field_values[] = $value;
       }
    }

    return $field_values;
  }
}
