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
require_once(t3lib_extMgm::extPath( 'jb_realurl_regeneration' ).'/Classes/class.tx_jbrealurlregeneration_lib.php');

/**
 * TYPO3 cron
 * 
 * @package     TYPO3
 * @subpackage  jb_realurl_regeneration
 */
class tx_jbrealurlregeneration_cron{
	/**
	 * @param integer TSFE_page_id
	 */
	protected $TSFE_page_id = 0;
	/**
	 * @param array more cache tables to truncate after the truncate of realurl
	 */
	protected $tablesToTruncate = array();	
	
	/**
	 * @param integer don't truncate but delate records older than n hours
	 */
	protected $deleteTablesOlderThanHours=0;		
	/**
	 * @param integer realUrlCleanUp
	 */
	protected $realUrlCleanUp = 1;	
	
	/**
	 * @param integer TSFE_page_id is the page id used for typolink
	 */
	public function __construct( $TSFE_page_id=0, $tablesToTruncate='', $deleteTablesOlderThanHours=0, $realUrlCleanUp=1){
		$this->TSFE_page_id = intval($TSFE_page_id);
		$this->deleteTablesOlderThanHours = intval($deleteTablesOlderThanHours);
		
		if( isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'jb_realurl_regeneration' ] ) ) {
			$this->extConf = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'jb_realurl_regeneration' ] );
		}
		
		if($tablesToTruncate){
			$a = explode(',',$tablesToTruncate);
			if(sizeof($a )>0){
				foreach($a as $v){
					$this->tablesToTruncate[] = trim($v); 
				}
			}
		}
		$realUrlCleanUp = intval($realUrlCleanUp);
		if($realUrlCleanUp === 1){
			$this->realUrlCleanUp = 1;
		}else{
			$this->realUrlCleanUp = 0;
		}
	}
	/**
	 * 
	 */
	public function execute(){
		$status = false;
		global $TYPO3_CONF_VARS, $GLOBALS;
		

		
		// generate FE object
		// get the page id for the FE
		$this->create_FE_ENV($this->TSFE_page_id);
		
		
		// realUrl generator lib
		$lib = t3lib_div::makeInstance("tx_jbrealurlregeneration_lib");
		$conf['extConf']= $this->extConf;
		$lib->init($content,$conf,$this->cObj);				
		

		// manage realUrl Tables
		
		if($this->realUrlCleanUp===1){
			// delete realUrl tables
			try{
				$lib->delete_tables($this->deleteTablesOlderThanHours);
			}catch (Exception $e) {
				return false;
			}		
			try{
				if(t3lib_div::inList($this->extConf['tablesToGenerate'] ,'pages') ){
					$counter = $lib->generate_pages_url(0,100000);
				}
				if(t3lib_div::inList($this->extConf['tablesToGenerate'] ,'tt_news') ){
					$counterNews = $lib->generate_tt_news_url(0,100000);
				}		
				if(t3lib_div::inList($this->extConf['tablesToGenerate'] ,'tt_products') ){
					$counterTtProduct = $lib->generate_tt_products_url(0,100000);
				}		
			}catch (Exception $e) {
				return false;
			}
		}
		
		// truncate other cache tables
		try{
			if($this->deleteTablesOlderThanHours > 0){
				$res = $lib->deleteCacheTables($this->tablesToTruncate,$this->deleteTablesOlderThanHours);
			}else{
				$res = $lib->truncateCacheTables($this->tablesToTruncate);
			}
		}catch (Exception $e) {
			return false;
		}
		return true;
	}


		
	/**
	 * copy of fb_realurl_tweak 
	 * generate_page_url()
	 * (c) 2008 Joerg Weller <weller@flagbit.de>
	 * var $id integer id of the page
	 * to do : do not load unused lib
	 */
	private function create_FE_ENV($id){

		global $TYPO3_CONF_VARS;
 
		require_once(PATH_tslib.'class.tslib_fe.php');
 
		if(!is_object($GLOBALS['TSFE'])){
 
			require_once(PATH_t3lib.'class.t3lib_timetrack.php');
			if(!is_object($GLOBALS['TT'])){
				$GLOBALS['TT'] = new t3lib_timeTrack;
				$GLOBALS['TT']->start();
			}
 
			require_once(PATH_t3lib.'class.t3lib_page.php');
			require_once(PATH_t3lib.'class.t3lib_userauth.php');
			require_once(PATH_tslib.'class.tslib_feuserauth.php');
			require_once(PATH_t3lib.'class.t3lib_tstemplate.php');
			require_once(PATH_t3lib.'class.t3lib_cs.php');
			require_once(PATH_tslib.'class.tslib_content.php');
			// require_once(PATH_tslib.'class.tslib_menu.php');
 
			$temp_TSFEclassName = t3lib_div::makeInstanceClassName('tslib_fe');
			$GLOBALS['TSFE'] = new $temp_TSFEclassName(	$TYPO3_CONF_VARS,	$id, 0,	'',	'',	'',	'',	'');
		}

		$GLOBALS['TSFE']->no_cache = true;
		$GLOBALS['TSFE']->id=$id;
		$GLOBALS['TSFE']->initFEuser();
		$GLOBALS['TSFE']->fetch_the_id();		
		// Look up the page
		$GLOBALS['TSFE']->sys_page = t3lib_div::makeInstance('t3lib_pageSelect');
		$GLOBALS['TSFE']->sys_page->init($GLOBALS['TSFE']->showHiddenPage);
		
		// If the page is not found (if the page is a sysfolder, etc), then return no URL, preventing any further processing which would result in an error page.
		// $page = $GLOBALS['TSFE']->sys_page->getPage($id);
		
		
		$GLOBALS['TSFE']->getPageAndRootline();
		$GLOBALS['TSFE']->initTemplate();
		$GLOBALS['TSFE']->forceTemplateParsing = 1;
		
		$GLOBALS['TSFE']->getConfigArray();	
		
		// Find the root template
		$GLOBALS['TSFE']->tmpl->start($GLOBALS['TSFE']->rootLine);

		// cObj	
		$this->cObj = t3lib_div::makeInstance('tslib_cObj');
		$this->cObj->start(array(),'');
		
	}		
		
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jb_realurl_regeneration/Classes/class.tx_jbrealurlregeneration_cron.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jb_realurl_regeneration/Classes/class.tx_jbrealurlregeneration_cron.php']);
}
