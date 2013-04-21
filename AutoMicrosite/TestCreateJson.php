<?php


/*
echo urlencode(json_encode(
		array(
			'title'		=>	'My Mashup',
			'widgets'	=>	array(
				array(
					'url'			=>	'http://deepweb.ut.ee/automicrosite/Widgets/Table/Table.oam.xml',
					'properties'	=>	array(
						'backgroundColor'	=>	'#FFFFFF',
						'foregroundColor'	=>	'#000000'
					),
					'flowOrder'		=>	1
				)
			)
		)
));*/

$widgetsLocation = 'http://localhost/Automated-generation-of-microsites/AutoMicrosite/widgets/';


echo urlencode(json_encode(
		array(
			'title'		=>	'My Mashup',
			'widgets'	=>	array(
				array(
					'url'			=>	$widgetsLocation . 'DataManager/DataManager.oam.xml'
				),
				array(
					'url'			=>	$widgetsLocation . 'Data/Data.oam.xml'
				),
				array(
					'url'			=>	$widgetsLocation . 'Map/Map.oam.xml'
				),
				array(
					'url'			=>	$widgetsLocation . 'Menu/Menu.oam.xml'
				),
				array(
					'url'			=>	$widgetsLocation . 'Summary/Summary.oam.xml'
				),
				array(
					'url'			=>	$widgetsLocation . 'Table/Table.oam.xml'
				),
			)
		)
));