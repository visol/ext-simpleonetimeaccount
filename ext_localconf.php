<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

/* Registration plugin */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Visol.' . $_EXTKEY,
	'Onetimeaccount',
	array(
		'Account' => 'new,create',

	),
	// non-cacheable actions
	array(
		'Account' => 'new,create'
	)
);