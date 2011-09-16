<?php

########################################################################
# Extension Manager/Repository config file for ext "jb_realurl_regeneration".
#
# Auto generated 12-05-2011 15:56
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'JB RealURL Regeneration',
	'description' => 'Let\'s you delete all RealURL records and create new ones. It\'s useful after renaming pages.',
	'category' => 'plugin',
	'shy' => 1,
	'version' => '3.2.2',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'vd_typo3 (Jean-Luc Thirot),Jan Bednarik',
	'author_email' => 'support.typo3@vd.ch',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:26:{s:9:"ChangeLog";s:4:"5b86";s:10:"README.txt";s:4:"ee2d";s:16:"ext_autoload.php";s:4:"c800";s:21:"ext_conf_template.txt";s:4:"df5a";s:12:"ext_icon.gif";s:4:"038e";s:17:"ext_localconf.php";s:4:"9d9c";s:14:"ext_tables.php";s:4:"8866";s:13:"locallang.php";s:4:"e870";s:16:"locallang_db.php";s:4:"9686";s:47:"Classes/class.tx_jbrealurlregeneration_cron.php";s:4:"da93";s:46:"Classes/class.tx_jbrealurlregeneration_lib.php";s:4:"6831";s:57:"Classes/class.tx_jbrealurlregeneration_scheduler_task.php";s:4:"eb07";s:64:"Classes/class.tx_jbrealurlregeneration_scheduler_task_fields.php";s:4:"db73";s:23:"Configuration/setup.txt";s:4:"9a4e";s:14:"doc/manual.sxw";s:4:"b3f9";s:13:"doc/notes.txt";s:4:"52a2";s:19:"doc/wizard_form.dat";s:4:"2543";s:20:"doc/wizard_form.html";s:4:"e6c7";s:45:"hook/class.tx_jbrealurlregeneration_hooks.php";s:4:"de6a";s:18:"lang/locallang.xml";s:4:"00ac";s:14:"pi1/ce_wiz.gif";s:4:"1182";s:42:"pi1/class.tx_jbrealurlregeneration_pi1.php";s:4:"e564";s:50:"pi1/class.tx_jbrealurlregeneration_pi1_wizicon.php";s:4:"9e59";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.php";s:4:"1082";s:15:"scripts/cli.php";s:4:"f2bd";}',
);

?>