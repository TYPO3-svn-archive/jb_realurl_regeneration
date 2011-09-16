<?php
/***************************************************************
*  Copyright notice
*
*  (c) 20100 vd.ch jean-luc.thirot@vd.ch
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
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
***************************************************************/


// DEBUG ONLY - Sets the error reporting level to the highest possible value
#error_reporting( E_ALL | E_STRICT );

/**
 * TYPO3 scheduler task
 * 
 * @author      
 * @version     1.0
 * @package     TYPO3
 * @subpackage  jb_realurl_regeneration
 */
class tx_jbrealurlregeneration_scheduler_task extends tx_scheduler_Task
{

    /**
     * main page used for genarating typolinks
     */
    public $TSFE_page_id = '';
		public $tablesToTruncate; // cache_* tables to truncate
		public $deleteTablesOlderThanHours; // if not empty don't truncate but delete tabels older than
		public $realUrlCleanUp = 1; // clean realurl tables
    /**
     * 
     */
    public function execute()
    {
			 	// No pageid defined, just log the task
			 t3lib_div::sysLog('[scheduler: jb_realurlregeneration]: Start ', 'jb_realurlregeneration', 0);
       $cron = new tx_jbrealurlregeneration_cron ($this->TSFE_page_id, $this->tablesToTruncate, $this->deleteTablesOlderThanHours, $this->realUrlCleanUp );
			 $res = $cron->execute();
			 t3lib_div::sysLog('[scheduler: jb_realurlregeneration]: End '.' status:'.$res, 'jb_realurlregeneration', 0);
       return $res;
			 
    }
    
    /**
     * 
     */
    public function getAdditionalInformation()
    {
        return '';
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jb_realurl_regeneration/Classes/class.tx_jbrealurlregeneration_scheduler_task.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jb_realurl_regeneration/Classes/class.tx_jbrealurlregeneration_scheduler_task.php']);
}
