<?php

/* * *************************************************************
 * Extension Manager/Repository config file for ext: "reint_mailtask_example"
 *
 * Manual updates:
 * Only the data in the array - anything else is removed by next write.
 * "version" and "dependencies" must not be touched!
 * ************************************************************* */

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Scheduler mail task example',
	'description' => 'An example scheduler task with mail send out with Fluid-templates and multilanguage support.',
	'category' => 'plugin',
	'author' => 'Ephraim Härer',
	'author_email' => 'ephraim.haerer@renolit.com',
	'state' => 'test',
	'internal' => '',
	'uploadfolder' => '0',
	'createDirs' => '',
	'clearCacheOnLoad' => 0,
	'version' => '0.5.1',
	'constraints' => array(
		'depends' => array(
			'typo3' => '6.2.0-7.4.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);
