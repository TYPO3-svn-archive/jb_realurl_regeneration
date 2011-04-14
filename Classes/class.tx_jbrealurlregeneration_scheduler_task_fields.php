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
 * @author      Jean-Luc.thirot@vd.ch
 * @version     1.0
 * @package     TYPO3
 * @subpackage  jb_realurl_regeneration
 */
class tx_jbrealurlregeneration_scheduler_task_fields implements tx_scheduler_AdditionalFieldProvider
{
    /**
     * An id for simulating the TSFE
		 * cache_* tables to truncate
     */
    protected $_fields = array
    (
        'TSFE_page_id'   => '',
				'tablesToTruncate' => '',
    );
		
    /**
     * 
     */
    public function __construct()
    {
			
    }
    
    /**
     * 
     */
    public function getAdditionalFields( array &$taskInfo, $task, tx_scheduler_Module $parentObject )
    {
        $fields = array();
        
        foreach( $this->_fields as $field => $defaultValue )
        {
            if( empty( $taskInfo[ $field ] ) )
            {
                if( $parentObject->CMD === 'add' )
                {
                    $taskInfo[ $field ] = $defaultValue;
                }
                elseif( $parentObject->CMD === 'edit' )
                {
                    $taskInfo[ $field ] = $task->$field;
                }
                else
                {
                    $taskInfo[ $field ] = '';
                }
            }
            
            $fields[ 'task_' . $field ] = array
            (
                'code'     => '<input name="tx_scheduler[' . $field . ']" id="task_' . $field . '" type="text" size="20" value="' . $taskInfo[ $field ] . '" />',
                'label'    => 'LLL:EXT:jb_realurl_regeneration/lang/locallang.xml:task.fields.' . $field,
                'cshKey'   => '',
                'cshLabel' => '',
            );
        }
        
        return $fields;
    }
    
    /**
     * 
     */
    public function validateAdditionalFields( array &$submittedData, tx_scheduler_Module $parentObject )
    {
        return true;
    }
    
    /**
     * 
     */
    public function saveAdditionalFields( array $submittedData, tx_scheduler_Task $task )
    {
        foreach( $this->_fields as $field => $void )
        {
            $task->$field = $submittedData[ $field ];
        }
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jb_realurl_regeneration/Classes/class.tx_jbrealurlregeneration_scheduler_task_fields.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jb_realurl_regeneration/Classes/class.tx_jbrealurlregeneration_scheduler_task_fields.php']);
}
