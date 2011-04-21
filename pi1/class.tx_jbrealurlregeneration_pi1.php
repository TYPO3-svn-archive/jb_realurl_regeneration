<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005 Jan Bednarik (info@bednarik.org)
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
 * Plugin 'Delete old and create new RealURL links at once.' for the 'jb_realurl_regeneration' extension.
 * 
 * $Id$
 *
 * @author	Jean-Luc Thirot <jean-luc@bthirot.com> 2011
 * @author	Jan Bednarik <info@bednarik.org>
 */
 
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 */

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath( 'jb_realurl_regeneration' ).'/Classes/class.tx_jbrealurlregeneration_lib.php');


class tx_jbrealurlregeneration_pi1 extends tslib_pibase {
	var $prefixId = 'tx_jbrealurlregeneration_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_jbrealurlregeneration_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey = 'jb_realurl_regeneration';	// The extension key.
	
	/**
	 * [Put your description here]
	 */
	function main($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_USER_INT_obj=1;	// Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
	
	  $GLOBALS['TSFE']->set_no_cache();

		if( isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'jb_realurl_regeneration' ] ) ) {
			$this->extConf = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'jb_realurl_regeneration' ] );
		}
		$conf['extConf']= $this->extConf;
		// lib functions
		$this->lib = t3lib_div::makeInstance("tx_jbrealurlregeneration_lib");
		$this->lib->init($content,$conf,$this->cObj);
		
		// languages
		$this->languages = $this->lib->get_languages();

		// timer
    $starttime =$this->lib->timer(1,0);
			
			
		// GET parameters
		$getParams = t3lib_div::_GET('tx_jbrealurlregeneration_pi1');
		
		$deleteTables   = ($getParams[deletetables]==1) ? $getParams[deletetables] :0;
		$deleteTables = intval($deleteTables);
		
		// Limits
		$Lbegin = $getParams[pages_from] ? $getParams[pages_from] :0;
		$Lmax   = $getParams[pages_max] ? $getParams[pages_max] :0;	
		$Lbegin = intval($Lbegin);
		$Lmax = intval($Lmax);
			
		$params = array(
				'additionalParams' => '&tx_jbrealurlregeneration_pi1[deletetables]=1',
				'parameter' => $GLOBALS['TSFE']->id,
			);
		if($conf['disable_delete']==1){
			$deleteLink = ' Delete tables is disabled .';
		}else{
			$deleteLink = $this->cObj->typolink('DELETE tables',$params);
		}		

		$content .= '<h4>WARNING 1) This link deletes some  tx_realurl_* tables > ' . $deleteLink . ' 
		<br/>Protect this page or add plugin.tx_jbrealurlregeneration_pi1.disable_delete=1. Chek the extension configuration</h4>
		<h4>WARNING 2) You must be not logged in the Backend for generating the cache</h4>';	

		// delete tables
		if($deleteTables===1 && $conf['disable_delete']<>1){
			$q = $GLOBALS['TYPO3_DB']->exec_SELECTquery('COUNT(cache_id)','tx_realurl_pathcache','');
			$content .= '<p>tx_realurl_pathcache : '.str_replace('###X###',mysql_result($q,0),$this->pi_getLL('delete')).'</p>';
			
			$this->lib->delete_tables();
			// to do use tables to deleted
			$content .= '<h4> Tables deleted </h4>';	
		}
		

		// table pages
		
		// table check 
		if(t3lib_div::inList($this->extConf['tablesToGenerate'] ,'pages') ){
			$content .= "<hr/>";
			$content .= '<h2>Table pages</h2>';
			
			$q = $GLOBALS['TYPO3_DB']->exec_SELECTquery('COUNT(uid)','pages','doktype<10 AND hidden=0 AND deleted=0');
			$maxID = $GLOBALS['TYPO3_DB']->exec_SELECTquery('MAX(uid)','pages','1');
					
			$content .= '<p>'.str_replace('###X###',mysql_result($q,0),$this->pi_getLL('create')).' (au total dans la BD à créer.)</p>';
			

			$pages = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,title','pages','doktype<10 AND hidden=0 AND deleted=0','','',"$Lbegin,$Lmax");
			$content .= '<p>';
			if($Lmax>0){
				$counter = $this->lib->generate_pages_url($Lbegin,$Lmax);
			}
			
			$content .= (" <h3>typolinks = $counter</h3> ");
			
			// count records
			$q2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('COUNT(cache_id)','tx_realurl_pathcache','1=1');

			$content .= '<h4>Records in the table tx_realurl_pathcache :' .mysql_result($q2,0) . '</h4>';
			
			// next 
			$LbeginNext = $Lbegin + $Lmax;
			$Lmax = ($Lmax<=1)?100:$Lmax;
			if($LbeginNext < mysql_result($maxID,0) ){
				$params = array(
					'additionalParams' => '&tx_jbrealurlregeneration_pi1[pages_from]=' . $LbeginNext . '&tx_jbrealurlregeneration_pi1[pages_max]=' . $Lmax,
					'parameter' => $GLOBALS['TSFE']->id,
				);
				$content .= '<h4>' . $this->cObj->typolink('generate next links',$params) . '</h4>';
			}
		}else{
			
		}		
		
		// table tt_news
		$content .= $this->show_table('tt_news');
		$content .= $this->show_table('tt_products');
		
		$content .= "<hr/>";

    $endtime =$this->lib->timer(0,1);
		$time = $endtime - $starttime ;
		$content .= " <h3>Time = $time </h3> ";
		
		$content .= '</p>';
		
		return $content;
	}
	/**
	 * Return the view for the table to generate
	 * @param string name of the table
	 * @return string
	 */
	function show_table($table){
	
			
			$content = '';
			// table check 
			if(t3lib_div::inList($this->extConf['tablesToGenerate'] ,$table) ){
			}else{
				return $content;
			}
			
			$getParams = t3lib_div::_GET('tx_jbrealurlregeneration_pi1');
			$content = "<hr/>";
			$content .= '<h2>Table '.$table. '</h2>';
			
			// Limits
			$Lbegin = $getParams[$table.'_from'] ? $getParams[$table.'_from'] :0;
			$Lmax   = $getParams[$table.'_max'] ? $getParams[$table.'_max'] :0;	
			$Lbegin = intval($Lbegin);
			$Lmax = intval($Lmax);
			
			$tableFQ = $GLOBALS['TYPO3_DB']->quoteStr($table, $table);
			$q = $GLOBALS['TYPO3_DB']->exec_SELECTquery('COUNT(uid)',$tableFQ,'hidden=0 AND deleted=0');
			$maxID = $GLOBALS['TYPO3_DB']->exec_SELECTquery('MAX(uid)',$tableFQ,'1');
			$content .= '<p>' . str_replace('###X###',mysql_result($q,0),$this->pi_getLL('create')).' (au total dans la BD à créer.)</p>';

			$counter_1 = 0;
			
			$records = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,title',$tableFQ,'hidden=0 AND deleted=0','','',"$Lbegin,$Lmax");
			$content .= '<p>';			
			if($Lmax>0){
				$counter = $this->lib->{'generate_'.$table.'_url'}($Lbegin,$Lmax);
			}
			
			$content .= (" <h3>Typolinks = $counter</h3> ");
			
			// count records
			$q2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('COUNT(uid)','tx_realurl_uniqalias','tablename=\''.$tableFQ.'\'');
			$content .= '<h4>Records in the table tx_realurl_uniqalias :' .mysql_result($q2,0) . '</h4>';
			// next 
			$LbeginNext = $Lbegin + $Lmax;
			$Lmax = ($Lmax<=1)?100:$Lmax;
			if($LbeginNext < mysql_result($maxID,0) ){
				$params = array(
					'additionalParams' => '&tx_jbrealurlregeneration_pi1['.$table.'_from]=' . $LbeginNext . '&tx_jbrealurlregeneration_pi1['.$table.'_max]=' . $Lmax,
					'parameter' => $GLOBALS['TSFE']->id,
				);
				$content .= '<h4>' . $this->cObj->typolink('generate next links',$params) . '</h4>';
			}		
		
			return $content;
	}
	
	
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jb_realurl_regeneration/pi1/class.tx_jbrealurlregeneration_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jb_realurl_regeneration/pi1/class.tx_jbrealurlregeneration_pi1.php']);
}

?>