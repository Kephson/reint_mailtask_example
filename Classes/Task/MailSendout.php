<?php

namespace RENOLIT\ReintMailtaskExample\Task;

/* * *************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 Ephraim Härer <ephraim.haerer@renolit.com>, RENOLIT SE
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Utility\DebugUtility;
use \TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use \TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

class MailSendout {

	/*
	 * array for default options
	 * @var array
	 */

	protected $defaultConfig = array(
		'extKey' => 'reint_mailtask_example',
		'lFilePath' => 'LLL:EXT:reint_mailtask_example/Resources/Private/Language/locallang.xlf:',
		'forceFrontendLink' => 1, // has to be set to generate frontend links in backend
	);

	/**
	 * executes the task function
	 * 
	 * @param string $link
	 * @param string $transLang
	 * @param string $receiver_email
	 * @param string $receiver_name
	 * @param string $sender_email
	 * @param string $sender_name
	 * 
	 * @return boolean
	 */
	public function run($link, $transLang, $receiver_email, $receiver_name, $sender_email, $sender_name) {

		$this->defaultConfig['link'] = $link;
		$this->defaultConfig['transLanguage'] = $transLang;
		$receiver = array($receiver_email => $receiver_name);
		$sender = array($sender_email => $sender_name);

		// change language if other language is set in scheduler task
		$GLOBALS['LANG'] = GeneralUtility::makeInstance('\\TYPO3\\CMS\\Lang\\LanguageService');
		$GLOBALS['LANG']->init($transLang);

		$subject = LocalizationUtility::translate($this->defaultConfig['lFilePath'] . 'subject', $this->defaultConfig['extKey']);
		$body = $this->renderMailContent();
		$mailSent = $this->sendMail($receiver, $sender, $subject, $body);

		return $mailSent;
	}

	/**
	 * renders a fluid mail template
	 * 
	 * @param string $templateName
	 * 
	 * @return string
	 */
	protected function renderMailContent($templateName = 'Example') {
		$view = GeneralUtility::makeInstance('\\TYPO3\\CMS\\Fluid\\View\\StandaloneView');
		$view->getRequest()->setControllerExtensionName($this->defaultConfig['extKey']); // path the extension name to get translation work
		$view->setPartialRootPath(ExtensionManagementUtility::extPath($this->defaultConfig['extKey']) . 'Resources/Private/Partials/');
		$view->setLayoutRootPath(ExtensionManagementUtility::extPath($this->defaultConfig['extKey']) . 'Resources/Private/Layouts/');
		$view->setTemplatePathAndFilename(ExtensionManagementUtility::extPath($this->defaultConfig['extKey']) . 'Resources/Private/Templates/Mail/' . $templateName . '.html');
		$view->assign('config', $this->defaultConfig);
		return $view->render();
	}

	/**
	 * sends an email
	 * 
	 * @param array $receiver Array with receiver
	 * @param array $sender Array with sender
	 * @param string $subject Subject of mail
	 * @param string $body Body content for mail
	 * @param string $attachment Path to a file
	 * @param string $bodyType text/html or text/plain
	 * 
	 * return boolean
	 */
	protected function sendMail($receiver = array(), $sender = array(), $subject = '', $body = '', $attachment = '', $bodyType = 'text/html') {

		$mail = GeneralUtility::makeInstance('\\TYPO3\\CMS\\Core\\Mail\\MailMessage');

		// add attachment
		if ($attachment !== '') {
			if (is_file($attachment)) {
				$swiftAttachment = \Swift_Attachment::fromPath($attachment);
				$mail->attach($swiftAttachment);
			}
		}

		if (!empty($receiver) && !empty($sender)) {
			return $mail->setFrom($sender)
							->setTo($receiver)
							->setSubject($subject)
							->setBody($body, $bodyType)
							->send();
		} else {
			return false;
		}
	}

}
