<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "onetimeaccount".
 *
 * Auto generated 17-04-2014 16:26
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Simple One-time FE account',
	'description' => 'This extension allows users to create a one-time FE account to which they will be automatically logged in (without having to enter a user name or password). This extension also supports saltedpasswords and rsaauth.',
	'category' => 'plugin',
	'author' => 'Lorenz Ulrich',
	'author_email' => 'lorenz.ulrich@visol.ch',
	'shy' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => 'visol digitale Dienstleistungen GmbH',
	'version' => '1.0.0',
	'constraints' => array(
		'depends' => array(
			'php' => '5.3.0-5.6.99',
			'typo3' => '6.0.0-6.2.99',
		),
	),
);
