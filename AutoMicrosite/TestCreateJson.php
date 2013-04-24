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

//$widgetsLocation = 'http://localhost/Automated-generation-of-microsites/AutoMicrosite/widgets/';

/*
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
));*/
/*
$widgetsLocation = 'http://localhost/Automated-generation-of-microsites/AutoMicrosite/widgets/Liisi/';
$widgetsLocation = 'http://deepweb.ut.ee/CreditManager/widgets/';

echo urlencode(json_encode(
		array(
			'title'		=>	'My Mashup',
			'widgets'	=>	array(
				array(
					'url'			=>	$widgetsLocation . 'Charts/PieChart.oam.xml'
				),
				array(
					'url'			=>	$widgetsLocation . 'Charts/LineChart.oam.xml'
				),
				array(
					'url'			=>	$widgetsLocation . 'Table/Table.oam.xml'
				)
			)
		)
));
*/
/*
$widgetsLocation = 'http://localhost/Automated-generation-of-microsites/AutoMicrosite/widgets/Liisi/';
$widgetsLocation = 'http://deepweb.ut.ee/CreditManager/widgets/';

echo urlencode(json_encode(
		array(
			'title'		=>	'My Mashup',
			'widgets'	=>	array(
				array(
					'url'			=>	$widgetsLocation . 'Charts/PieChart.oam.xml'
				),
				array(
					'url'			=>	$widgetsLocation . 'Charts/PieChart.oam.xml'
				),
				array(
					'url'			=>	$widgetsLocation . 'Charts/BarChart.oam.xml'
				),
				array(
					'url'			=>	$widgetsLocation . 'Table/Table.oam.xml'
				)
			)
		)
));
*/

$widgetsLocation = 'http://localhost/Automated-generation-of-microsites/AutoMicrosite/widgets/';

echo urlencode(json_encode(
		array(
			'title'		=>	'My Mashup',
			'widgets'	=>	array(
				array(
					'url'			=>	$widgetsLocation . 'GoogleMaps/GoogleMaps.oam.xml'
				),
				array(
					'url'			=>	$widgetsLocation . 'OrganizationDeptInfo/OrganizationDeptInfo.oam.xml'
				),
				array(
					'url'			=>	$widgetsLocation . 'OrganizationInfo/OrganizationInfo.oam.xml'
				),
				array(
					'url'			=>	'http://automicrosite.maesalu.com/BusinessRegister/BusinessRegisterQuery.oam.xml',
					'properties'	=>	array(
						'name'	=>	'EVETERM OÜ'
					)
				),
				array(
					'url'			=>	$widgetsLocation . 'TransformerWidget/TransformerWidget.oam.xml'
				),
			)
		)
));