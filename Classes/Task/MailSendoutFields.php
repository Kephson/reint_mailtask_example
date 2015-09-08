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

use \TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use \TYPO3\CMS\Core\Messaging\FlashMessage;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Utility\DebugUtility;

class MailSendoutFields implements \TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface {

	protected $extKey = 'reint_mailtask_example';
	protected $taskKey = 'mailsendout';
	protected $fieldNamePrefix = '';

	/**
	 * Gets additional fields to render in the form to add/edit a task
	 *
	 * @param array $taskInfo Values of the fields from the add/edit task form
	 * @param \TYPO3\CMS\Scheduler\Task\AbstractTask $task The task object being edited. Null when adding a task!
	 * @param \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $parentObject Reference to the scheduler backend module
	 * @return array A two dimensional array, array('Identifier' => array('fieldId' => array('code' => '', 'label' => '', 'cshKey' => '', 'cshLabel' => ''))
	 */
	public function getAdditionalFields(array &$taskInfo, $task, \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $parentObject) {

		$this->fieldNamePrefix = 'tx_scheduler[' . $this->extKey . '][' . $this->taskKey . ']';
		$additionalFields = array();

		$editEntry = false;
		if ($parentObject->CMD === 'edit') {
			$editEntry = true;
		}

		// write link field
		if (empty($taskInfo[$this->extKey][$this->taskKey]['link'])) {
			if ($editEntry) {
				$taskInfo[$this->extKey][$this->taskKey]['link'] = $task->link;
			} else {
				$taskInfo[$this->extKey][$this->taskKey]['link'] = '';
			}
		}
		$fieldID_0 = 'task_' . $this->taskKey . '_link';
		$fieldCode_0 = $this->getTextField($fieldID_0, 'link', $taskInfo[$this->extKey][$this->taskKey]['link']);
		$additionalFields[$fieldID_0] = array(
			'code' => $fieldCode_0,
			'label' => 'LLL:EXT:' . $this->extKey . '/Resources/Private/Language/locallang.xlf:task_f_0'
		);

		// write language field
		if (empty($taskInfo[$this->extKey][$this->taskKey]['transLanguage'])) {
			if ($editEntry) {
				$taskInfo[$this->extKey][$this->taskKey]['transLanguage'] = $task->translang;
			} else {
				$taskInfo[$this->extKey][$this->taskKey]['transLanguage'] = 'en';
			}
		}
		$fieldID_1 = 'task_' . $this->taskKey . '_transLanguage';
		$fieldCode_1 = $this->getTranslationField($fieldID_1, $taskInfo[$this->extKey][$this->taskKey]['transLanguage']);
		$additionalFields[$fieldID_1] = array(
			'code' => $fieldCode_1,
			'label' => 'LLL:EXT:' . $this->extKey . '/Resources/Private/Language/locallang.xlf:task_f_1'
		);

		// write receiver email field
		if (empty($taskInfo[$this->extKey][$this->taskKey]['receiver_email'])) {
			if ($editEntry) {
				$taskInfo[$this->extKey][$this->taskKey]['receiver_email'] = $task->receiver_email;
			} else {
				$taskInfo[$this->extKey][$this->taskKey]['receiver_email'] = '';
			}
		}
		$fieldID_2 = 'task_' . $this->taskKey . '_receiver_email';
		$fieldCode_2 = $this->getTextField($fieldID_2, 'receiver_email', $taskInfo[$this->extKey][$this->taskKey]['receiver_email']);
		$additionalFields[$fieldID_2] = array(
			'code' => $fieldCode_2,
			'label' => 'LLL:EXT:' . $this->extKey . '/Resources/Private/Language/locallang.xlf:task_f_2'
		);

		// write receiver name field
		if (empty($taskInfo[$this->extKey][$this->taskKey]['receiver_name'])) {
			if ($editEntry) {
				$taskInfo[$this->extKey][$this->taskKey]['receiver_name'] = $task->receiver_name;
			} else {
				$taskInfo[$this->extKey][$this->taskKey]['receiver_name'] = '';
			}
		}
		$fieldID_3 = 'task_' . $this->taskKey . '_receiver_name';
		$fieldCode_3 = $this->getTextField($fieldID_3, 'receiver_name', $taskInfo[$this->extKey][$this->taskKey]['receiver_name']);
		$additionalFields[$fieldID_3] = array(
			'code' => $fieldCode_3,
			'label' => 'LLL:EXT:' . $this->extKey . '/Resources/Private/Language/locallang.xlf:task_f_3'
		);

		// write sender email field
		if (empty($taskInfo[$this->extKey][$this->taskKey]['sender_email'])) {
			if ($editEntry) {
				$taskInfo[$this->extKey][$this->taskKey]['sender_email'] = $task->sender_email;
			} else {
				$taskInfo[$this->extKey][$this->taskKey]['sender_email'] = '';
			}
		}
		$fieldID_4 = 'task_' . $this->taskKey . '_sender_email';
		$fieldCode_4 = $this->getTextField($fieldID_4, 'sender_email', $taskInfo[$this->extKey][$this->taskKey]['sender_email']);
		$additionalFields[$fieldID_4] = array(
			'code' => $fieldCode_4,
			'label' => 'LLL:EXT:' . $this->extKey . '/Resources/Private/Language/locallang.xlf:task_f_4'
		);

		// write sender name field
		if (empty($taskInfo[$this->extKey][$this->taskKey]['sender_name'])) {
			if ($editEntry) {
				$taskInfo[$this->extKey][$this->taskKey]['sender_name'] = $task->sender_name;
			} else {
				$taskInfo[$this->extKey][$this->taskKey]['sender_name'] = '';
			}
		}
		$fieldID_5 = 'task_' . $this->taskKey . '_sender_name';
		$fieldCode_5 = $this->getTextField($fieldID_5, 'sender_name', $taskInfo[$this->extKey][$this->taskKey]['sender_name']);
		$additionalFields[$fieldID_5] = array(
			'code' => $fieldCode_5,
			'label' => 'LLL:EXT:' . $this->extKey . '/Resources/Private/Language/locallang.xlf:task_f_5'
		);

		return $additionalFields;
	}

	/**
	 * Validates the additional fields' values
	 *
	 * @param array $submittedData An array containing the data submitted by the add/edit task form
	 * @param \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $parentObject Reference to the scheduler backend module
	 * @return boolean TRUE if validation was ok (or selected class is not relevant), FALSE otherwise
	 */
	public function validateAdditionalFields(array &$submittedData, \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $parentObject) {

		$errors = array();
		$message = array();

		if (empty($submittedData[$this->extKey][$this->taskKey]['link'])) {
			$errors['link'] = true;
			$message[]['head'] = LocalizationUtility::translate('task_f_0_message_error_head', $this->extKey);
			$message[]['body'] = LocalizationUtility::translate('task_f_0_message_error_body', $this->extKey);
			$submittedData[$this->extKey][$this->taskKey]['link'] = '';
		} else {
			$submittedData[$this->extKey][$this->taskKey]['link'] = trim($submittedData[$this->extKey][$this->taskKey]['link']);
		}

		if (empty($submittedData[$this->extKey][$this->taskKey]['transLanguage'])) {
			$submittedData[$this->extKey][$this->taskKey]['transLanguage'] = 'en';
		} else {
			$submittedData[$this->extKey][$this->taskKey]['transLanguage'] = $submittedData[$this->extKey][$this->taskKey]['transLanguage'];
		}

		if (empty($submittedData[$this->extKey][$this->taskKey]['receiver_email'])) {
			$errors['receiver_email'] = true;
			$message[]['head'] = LocalizationUtility::translate('task_f_2_message_error_head', $this->extKey);
			$message[]['body'] = LocalizationUtility::translate('task_f_2_message_error_body', $this->extKey);
			$submittedData[$this->extKey][$this->taskKey]['receiver_email'] = '';
		} else {
			$submittedData[$this->extKey][$this->taskKey]['receiver_email'] = trim($submittedData[$this->extKey][$this->taskKey]['receiver_email']);
		}

		if (empty($submittedData[$this->extKey][$this->taskKey]['receiver_name'])) {
			$errors['receiver_name'] = true;
			$message[]['head'] = LocalizationUtility::translate('task_f_3_message_error_head', $this->extKey);
			$message[]['body'] = LocalizationUtility::translate('task_f_3_message_error_body', $this->extKey);
			$submittedData[$this->extKey][$this->taskKey]['receiver_name'] = '';
		} else {
			$submittedData[$this->extKey][$this->taskKey]['receiver_name'] = trim($submittedData[$this->extKey][$this->taskKey]['receiver_name']);
		}

		if (empty($submittedData[$this->extKey][$this->taskKey]['sender_email'])) {
			$errors['sender_email'] = true;
			$message[]['head'] = LocalizationUtility::translate('task_f_4_message_error_head', $this->extKey);
			$message[]['body'] = LocalizationUtility::translate('task_f_4_message_error_body', $this->extKey);
			$submittedData[$this->extKey][$this->taskKey]['sender_email'] = '';
		} else {
			$submittedData[$this->extKey][$this->taskKey]['sender_email'] = trim($submittedData[$this->extKey][$this->taskKey]['sender_email']);
		}

		if (empty($submittedData[$this->extKey][$this->taskKey]['sender_name'])) {
			$errors['sender_name'] = true;
			$message[]['head'] = LocalizationUtility::translate('task_f_5_message_error_head', $this->extKey);
			$message[]['body'] = LocalizationUtility::translate('task_f_5_message_error_body', $this->extKey);
			$submittedData[$this->extKey][$this->taskKey]['sender_name'] = '';
		} else {
			$submittedData[$this->extKey][$this->taskKey]['sender_name'] = trim($submittedData[$this->extKey][$this->taskKey]['sender_name']);
		}

		if (!empty($errors)) {
			foreach ($message as $m) {
				$messageOut = GeneralUtility::makeInstance(
								'TYPO3\\CMS\\Core\\Messaging\\FlashMessage', $m['body'], $m['head'], FlashMessage::ERROR, FALSE
				);
				\TYPO3\CMS\Core\Messaging\FlashMessageQueue::addMessage($messageOut);
			}
			return false;
		} else {
			return true;
		}

		return true;
	}

	/**
	 * Takes care of saving the additional fields' values in the task's object
	 *
	 * @param array $submittedData An array containing the data submitted by the add/edit task form
	 * @param \TYPO3\CMS\Scheduler\Task\AbstractTask $task Reference to the scheduler backend module
	 * @return void
	 */
	public function saveAdditionalFields(array $submittedData, \TYPO3\CMS\Scheduler\Task\AbstractTask $task) {
		$task->link = $submittedData[$this->extKey][$this->taskKey]['link'];
		$task->translang = $submittedData[$this->extKey][$this->taskKey]['transLanguage'];
		$task->receiver_email = $submittedData[$this->extKey][$this->taskKey]['receiver_email'];
		$task->receiver_name = $submittedData[$this->extKey][$this->taskKey]['receiver_name'];
		$task->sender_email = $submittedData[$this->extKey][$this->taskKey]['sender_email'];
		$task->sender_name = $submittedData[$this->extKey][$this->taskKey]['sender_name'];
	}

	/**
	 * returns a selectbox with translation options
	 * 
	 * @param string $fieldID
	 * @param string $fieldKey
	 * @param string $value
	 * 
	 * @return string
	 */
	protected function getTextField($fieldID, $fieldKey, $value) {

		return '<input type="text" name="' . $this->fieldNamePrefix . '[' . $fieldKey . ']" id="' . $fieldID . '" value="' . $value . '" size="30" />';
	}

	/**
	 * returns a selectbox with translation options
	 * 
	 * @param string $fieldID
	 * @param string $value
	 * 
	 * @return string
	 */
	protected function getTranslationField($fieldID, $value = 'en') {

		$field = '<select name="' . $this->fieldNamePrefix . '[transLanguage]" id="' . $fieldID . '">';
		$values = array(
			'en' => 'task_en',
			'de' => 'task_de',
		);
		foreach ($values as $k => $r) {
			if ($k === $value) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}
			$field .= '<option value="' . $k . '"' . $selected . '>' . LocalizationUtility::translate($r, $this->extKey) . '</option>';
		}
		$field .= '</select>';
		return $field;
	}

}
