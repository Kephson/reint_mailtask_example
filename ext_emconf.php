<?php /** @noinspection PhpUndefinedVariableInspection */

/* * *************************************************************
 * Extension Manager/Repository config file for ext: "reint_mailtask_example"
 * ************************************************************* */

$EM_CONF[$_EXTKEY] = array(
    'title' => 'Scheduler mail task example',
    'description' => 'An example scheduler task with mail send out with Fluid-templates and multilanguage support.',
    'version' => '2.0.1',
    'category' => 'plugin',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-12.99.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'author' => 'Ephraim Härer',
    'author_email' => 'ephraim.haerer@renolit.com',
    'state' => 'test',
    'clearCacheOnLoad' => 0,
);
