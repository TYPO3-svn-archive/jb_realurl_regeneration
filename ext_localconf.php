<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','
	tt_content.CSS_editor.ch.tx_jbrealurlregeneration_pi1 = < plugin.tx_jbrealurlregeneration_pi1.CSS_editor
',43);


t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_jbrealurlregeneration_pi1.php','_pi1','list_type',0);


// add hook for realurl. Add host for CLI mode
$TYPO3_CONF_VARS['EXTCONF']['realurl']['getHost'][] = 'EXT:jb_realurl_regeneration/hook/class.tx_jbrealurlregeneration_hooks.php:&tx_jbrealurlregeneration_hooks->getHost';

// CLI and Scheduler

$GLOBALS[ 'TYPO3_CONF_VARS' ][ 'SC_OPTIONS' ][ 'scheduler' ][ 'tasks' ][ 'tx_jbrealurlregeneration_scheduler_task' ] = array
(
    'extension'        => $_EXTKEY,
    'title'            => 'LLL:EXT:' . $_EXTKEY . '/lang/locallang.xml:task.name',
    'description'      => 'LLL:EXT:' . $_EXTKEY . '/lang/locallang.xml:task.description',
    'additionalFields' => 'tx_jbrealurlregeneration_scheduler_task_fields'
);

$TYPO3_CONF_VARS[ 'SC_OPTIONS' ][ 'GLOBAL' ][ 'cliKeys' ][ $_EXTKEY ] = array
(
    'EXT:' . $_EXTKEY . '/scripts/cli.php',
    '_CLI_jbrealurlregeneration'
);


?>