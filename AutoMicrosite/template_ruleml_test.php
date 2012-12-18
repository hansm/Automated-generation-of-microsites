<?php

require './Environment.php';

if (empty($_GET['noheader'])) header( "content-type: application/xml; charset=UTF-8" );
$template = new UT\Hans\AutoMicrosite\Template\MicrodataTemplate('Templates/Simple.html');
$ruleMl = $template->toRuleMl();
print_r($ruleMl->getString());

?>