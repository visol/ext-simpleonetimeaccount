<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

/* Registration plugin with FlexForm */
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Onetimeaccount',
	'LLL:EXT:simpleonetimeaccount/Resources/Private/Language/locallang.xlf:pluginLabel',
	'EXT:simpleonetimeaccount/ext_icon.gif'
);

$pluginSignature = str_replace('_','',$_EXTKEY) . '_onetimeaccount';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForm/flexform_onetimeaccount.xml');

/* TypoScript configuration */
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Simple One-time FE account');
