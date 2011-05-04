<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 jean-luc.thirot2vd.ch
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
 * Classes 'Delete old and create new RealURL links at once.' for the 'jb_realurl_regeneration' extension.
 * Code copied from jb_realurl_regeneration
 *
 * @author	original author Jan Bednarik <info@bednarik.org>
 */


require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Class for deleting and generating url
 
 * The cli/cron mode with Typo3 4.2.8 doesn't work
 * A fatal error is thrown when generating the typolin()
 * Fatal error: Call to a member function pageNotFoundAndExit() on a non-object in /typo3conf/ext/realurl/class.tx_realurl.php on line 2411
 *  
 */
class tx_jbrealurlregeneration_lib extends tslib_pibase {
	var $prefixId = 'tx_jbrealurlregeneration_lib';		// Same as class name
	var $scriptRelPath = 'Classes/class.tx_jbrealurlregeneration_lib.php';	// Path to this script relative to the extension dir.
	var $extKey = 'jb_realurl_regeneration';	// The extension key.
	
	var $languages = array(); // array of languages
	var $cObj;
	
	/**
	 * [Put your description here]
	 */
	function init($content,$conf, $cObj)	{
		$this->conf = $conf;
		$this->cObj = $cObj;
		
		return true;
	}
	
	
	/**
	 * Generate pages url via typolink
	 * @return integer counter	 
	 */
	
	function generate_pages_url($Lbegin=0,$Lmax=100){
		// timer start
    $starttime = $this->timer(1,0);

		// languages
		$this->get_languages();
		// table pages
		$counter_1 = 0;
		$counter_2 = 0;

		$Lbegin=intval($Lbegin);
		$Lmax=intval($Lmax);		
		
		$pages = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,title','pages','doktype<10 AND hidden=0 AND deleted=0','','',"$Lbegin,$Lmax");

		while ($r = mysql_fetch_row($pages)) {
			$counter_1 += 1;
			// languages
			
			if(($this->languages)){
				foreach ($this->languages as $l) {
					$this->cObj->typolink($r[1],Array('parameter'=>$r[0],'additionalParams'=>'&L='.$l)).', ';
					$counter_2 += 1;
				}
			}else{
				$this->cObj->typolink($r[1],Array('parameter'=>$r[0])).', ';
				$counter_2 += 1;
			}
		}	
		// timer end
		$endtime = $this->timer(0,1);
		
		return $counter_2;
	}	
	
	
	/**
	 * Generate tt_news url via typolink
	 * @return integer counter	 
	 */
	
	function generate_tt_news_url($Lbegin=0,$Lmax=100){
	
	
		// timer start
    $starttime = $this->timer(1,0);
		
		// languages
		$this->get_languages();
		
		$Lbegin=intval($Lbegin);
		$Lmax=intval($Lmax);

		$counter_1 = 0;
		$counter_2 = 0;

		// To do add language for tt_news. 
		// do not select records with language but use the parent
		$records = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,title','tt_news','hidden=0 AND deleted=0 AND l18n_parent=\'\' ','','',"$Lbegin,$Lmax");
		
		// the page is not important but must exist
		$params = array(
			'parameter' => $GLOBALS['TSFE']->id,
			'useCacheHash' =>1,
		);			
		
		// sys_language_uid 	l18n_parent 
		
		while ($r = mysql_fetch_row($records)) {
			// default language
			$params['additionalParams'] = '&tx_ttnews[tt_news]=' . $r[0];
			$this->cObj->typolink($r[1],$params).', ';
		 	// languages
			if(isset($this->languages)){
				foreach ($this->languages as $l) {
					// create the uniqualias
					$params['additionalParams'] = '&tx_ttnews[tt_news]=' . $r[0].'&L='.$l;
					$this->cObj->typolink($r[1],$params).', ';
				}
			}
			
			$counter_1++;
		}			

		// timer end
		$endtime = $this->timer(0,1);
		
		return $counter_1;
	}		


	/**
	 * Generate tt_products url via typolink
	 * @return integer counter
	 */
	
	function generate_tt_products_url($Lbegin=0,$Lmax=100){
		// timer start
    $starttime = $this->timer(1,0);
		
		// languages
		$this->get_languages();

		$Lbegin=intval($Lbegin);
		$Lmax=intval($Lmax);		

		
		$counter_1 = 0;
		$counter_2 = 0;

		// To do add language for tt_products. 
		$records = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,title','tt_products','hidden=0 AND deleted=0','','',"$Lbegin,$Lmax");
		
		// the page is not important but must exist
		$params = array(
			'parameter' => $GLOBALS['TSFE']->id,
			'useCacheHash' =>1,
		);			
		
		while ($r = mysql_fetch_row($records)) {
			// default language
			$params['additionalParams'] = '&tt_products[product]=' . $r[0];
			$this->cObj->typolink($r[1],$params).', ';		
		 	// languages
			if(isset($this->languages)){
				foreach ($this->languages as $l) {
					// create the uniqualias
					$params['additionalParams'] = '&tt_products[product]=' . $r[0].'&L='.$l;
					$this->cObj->typolink($r[1],$params).', ';
				}
			}
			$counter_1++;
		}			
		
		// timer end
		$endtime = $this->timer(0,1);
		
		return $counter_1;
	}		

	
	
	/**
	 * Delete realurl cache tables
	 * @return void
	 */
	
	function delete_tables(){
		try{
			$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_realurl_pathcache','');
			$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_realurl_chashcache','');
			$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_realurl_urldecodecache','');
			$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_realurl_urlencodecache','');
					
			// delete news in tx_realurl_uniqalias
			if($this->conf['extConf']['deleteUniqAlias']){
				$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_realurl_uniqalias','');	
			}elseif($this->conf['extConf']['deleteUniqAliasTables']){

					$tables = t3lib_div::trimExplode (',',$this->conf['extConf']['deleteUniqAliasTables'],1);					
					foreach($tables as $t){
						$tc =  $GLOBALS['TYPO3_DB']->fullQuoteStr($t,'tx_realurl_uniqalias');
						$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_realurl_uniqalias','tablename='.$tc);	
					}
			}
			
			// 
		} catch (Exception $e) {
			throw $e;
		}
		return true;
	}	
	
	/**
	 * Very simple. We expect that all pages have all languages
	 * @return array of languages
	 */
	
	function get_languages(){

		if(!isset($this->languages)){
			$languages = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid','sys_language','hidden=0');
			$langs = Array();
			while ($r = mysql_fetch_row($languages)) {
				$langs[] = $r[0];
			}
			$this->languages = $langs;
		}else{
			$this->languages = NULL;
		}
		return $this->languages;
	}		


	/**
	 * function to truncate tables
	 * param array of string cache tables
	 */

	function truncateCacheTables($tables) {
			if (count($tables) > 0) {
					foreach($tables as $table) {
							$GLOBALS['TYPO3_DB']->sql_query('TRUNCATE `' . mysql_real_escape_string($table) . '`;');
							$GLOBALS['TYPO3_DB']->sql_query('OPTIMIZE TABLE `' . mysql_real_escape_string($table) . '`;');
					}
			}
	}
	


	
	/**
	 * Timer
	 */	
	function timer($starttime=0,$endtime=0){
	
		if($starttime == 1){
			$mtime = microtime();
			$mtime = explode(' ', $mtime);
			$mtime = $mtime[1] + $mtime[0];
			$starttime = $mtime;
			return $starttime;
		}elseif($endtime == 1){
			$mtime = microtime();
			$mtime = explode(" ", $mtime);
			$mtime = $mtime[1] + $mtime[0];
			$endtime = $mtime;
			
			return $endtime;
		}	
	}
	
}



?>
