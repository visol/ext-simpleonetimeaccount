<?php
namespace Visol\Simpleonetimeaccount\Controller;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Visol\Simpleonetimeaccount\Utility\Algorithms;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

class AccountController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var array
	 */
	protected $fieldConfiguration;

	/**
	 * @var \TYPO3\CMS\Core\Database\DatabaseConnection
	 */
	protected $databaseConnection;

	public function initializeAction() {
		$this->databaseConnection = $GLOBALS['TYPO3_DB'];

		$fieldListArray = GeneralUtility::trimExplode(',', $this->settings['fieldList']);
		$mandatoryFieldsArray = GeneralUtility::trimExplode(',', $this->settings['mandatoryFields']);

		foreach ($fieldListArray as $fieldName) {
			$this->fieldConfiguration[$fieldName]['fieldName'] = $fieldName;
			$this->fieldConfiguration[$fieldName]['mandatory'] = in_array($fieldName, $mandatoryFieldsArray) ? TRUE : FALSE;
		}
	}

	/**
	 */
	public function newAction() {
		if ($this->request->hasArgument('fieldConfiguration')) {
			// we used the passed fieldConfiguration with values if existing
			$this->view->assign('fieldConfiguration', $this->request->getArgument('fieldConfiguration'));
			// if this is the case, we have an error
			$this->view->assign('hasError', TRUE);
		} else {
			$this->view->assign('fieldConfiguration', $this->fieldConfiguration);
		}

	}

	/**
	 * Do the non-domain-model validation for the provided frontend user data and redirect back
	 * if there are errors
	 *
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
	 * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
	 */
	public function initializeCreateAction() {
		$fieldConfigurationWithValues = $this->fieldConfiguration;
		if ($this->request->hasArgument('frontendUser')) {
			$frontendUserData = $this->request->getArgument('frontendUser');
			$errors = 0;
			foreach ($frontendUserData as $fieldName => $value) {
				// XSS sanitation
				$value = htmlspecialchars(strip_tags(trim($value)));
				if (array_key_exists($fieldName, $this->fieldConfiguration)) {
					// the field is allowed
					$fieldConfigurationWithValues[$fieldName]['value'] = $value;
					if ($this->fieldConfiguration[$fieldName]['mandatory']) {
						// the field is mandatory, thus it must not be empty
						if (empty($value)) {
							// Error: Empty mandatory field
							$fieldConfigurationWithValues[$fieldName]['cssClasses'] = 'has-error';
							$errors++;
						} else {
							// Validate e-mail address
							if ($fieldName === 'email' && GeneralUtility::validEmail($value) === FALSE) {
								// Error: Invalid e-mail address
								$fieldConfigurationWithValues[$fieldName]['cssClasses'] = 'has-error';
								$errors++;
							}
						}
					}
				} else {
					// Unset fields that are not allowed for security reasons
					unset($frontendUserData[$fieldName]);
				}
			}
			if ($errors > 0) {
				$this->forward('new', NULL, NULL, array('fieldConfiguration' => $fieldConfigurationWithValues));
			} else {
				$this->forward('addAccountAndLogin', NULL, NULL, array('frontendUserData' => $frontendUserData));
			}
		} else {
			$this->forward('new');
		}

	}

	/**
	 * Dummy method, only the initializeAction is used
	 */
	public function createAction() {
	}

	/**
	 * @param array $frontendUserData
	 */
	public function addAccountAndLoginAction($frontendUserData = array()) {
		foreach ($frontendUserData as $fieldName => $value) {
			$frontendUserData[$fieldName] = $this->databaseConnection->escapeStrForLike(htmlspecialchars(strip_tags(trim($value))), 'fe_users');
		}
		// add additional data
		$frontendUserData['username'] = Algorithms::generateUUID();
		$frontendUserData['password'] = Algorithms::generateRandomToken(20);
		$frontendUserData['usergroup'] = $this->settings['userGroups'];
		$frontendUserData['pid'] = $this->settings['userFolder'];
		$frontendUserData['crdate'] = time();
		$frontendUserData['tstamp'] = time();
		$this->databaseConnection->exec_INSERTquery('fe_users', $frontendUserData);
		$frontendUserUid = $this->databaseConnection->sql_insert_id();

		if ($frontendUserUid > 0) {
			// user was successfully created
			/** @var $frontendUser \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication */
			$frontendUser = $GLOBALS['TSFE']->fe_user;
			$frontendUser->checkPid = FALSE;

			$authenticationData = $GLOBALS['TSFE']->fe_user->getAuthInfoArray();
			$userData = $frontendUser->fetchUserRecord($authenticationData['db_user'], $frontendUserData['username']);
			$frontendUser->user = $userData;
			$frontendUser->createUserSession($userData);
			$frontendUser->setKey('user', 'onetimeaccount', TRUE);
			// fake a session entry to ensure the Core actually creates the session and sends the FE cookie
			$frontendUser->setKey('ses', 'onetimeaccount_dummy', TRUE);
			$frontendUser->storeSessionData();
			$redirectUri = $this->uriBuilder->setTargetPageUid((int)$this->settings['targetPage'])->build();
			$this->redirectToUri($redirectUri);
		} else {
			$this->forward('new');
		}
	}

}