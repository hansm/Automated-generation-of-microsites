<?php

require './Environment.php';

use UT\Hans\AutoMicrosite\MappingsGenerator\Factory as MappingsGeneratorFactory;

if (empty($_GET['noheader'])) header( "content-type: application/xml; charset=UTF-8" );

$generator = MappingsGeneratorFactory::build('OpenAjaxMetadata');
print_r($generator->getMappings('Widgets/OrganizationInfo/OrganizationInfo.oam.xml'));

