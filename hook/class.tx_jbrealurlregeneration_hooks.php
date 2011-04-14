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
/**
 * CLI 'Delete old and create new RealURL links at once.' for the 'jb_realurl_regeneration' extension.
 *
 */

 
// DEBUG ONLY - Sets the error reporting level to the highest possible value
// error_reporting( E_ALL | E_STRICT );

// required for CLI
// require_once(t3lib_extMgm::extPath( 'jb_realurl_regeneration' ).'/Classes/class.tx_jbrealurlregeneration_lib.php');

/**
 * TYPO3 hooks
 * 
 * @package     TYPO3
 * @subpackage  jb_realurl_regeneration
 */
class tx_jbrealurlregeneration_hooks{

	public static $Host;
	
	
	/**
	 * Hook for ['TYPO3_CONF_VARS']['EXTCONF']['realurl']['getHost']
	 */
	public function getHost(&$params, &$ref) {
	
		if(self::$Host){
			$host = self::$Host;
		}elseif( isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'jb_realurl_regeneration' ] ) ) {
			$extConf = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'jb_realurl_regeneration' ] );
			$host = self::$Host = $extConf['CLI_HOST'];
		}	
		
		
		if(!$params['host']){
			// CLI mode
			// get hots from CLI mode
			// TO DO choose the host via CLI config
			return $params['host'] = $host;
		}else{
			// web mode
			return $params['host'];
		}
	}
       
}
			 			 
?>