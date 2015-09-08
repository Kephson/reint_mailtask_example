<?php

if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

// registering scheduler task
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['RENOLIT\\ReintMailtaskExample\\Task\\MailSendoutTask'] = array(
	'extension' => $_EXTKEY,
	'title' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xlf:mail_sendout_task_h',
	'description' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang.xlf:mail_sendout_task_t',
	'additionalFields' => 'RENOLIT\\ReintMailtaskExample\\Task\\MailSendoutFields'
);
