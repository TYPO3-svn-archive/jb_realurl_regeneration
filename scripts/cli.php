<?php


// Security check
if( !defined( 'TYPO3_MODE' ) ) {
    
    // TYPO3 is not running
    trigger_error(
        'This script cannot be used outside TYPO3',
        E_USER_ERROR
    );
}

if( !class_exists( 'jb_realurl_regeneration_cron' ) )
{
    require_once
    (
        t3lib_extMgm::extPath( 'jb_realurl_regeneration' )
      . 'Classes'
      . DIRECTORY_SEPARATOR
      . 'class.tx_jbrealurlregeneration_cron.php'
    );
}


if( isset( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'jb_realurl_regeneration' ] ) ) {

    $CONF     = unserialize( $GLOBALS[ 'TYPO3_CONF_VARS' ][ 'EXT' ][ 'extConf' ][ 'jb_realurl_regeneration' ] );
		$TSFE_page_id     = $CONF[ 'TSFE_page_id' ];

}

$CRON = new tx_jbrealurlregeneration_cron( $TSFE_page_id );

if( $CRON->execute() === false )
{
    print 'Error executing cron for extension jb_realurl_regeneration' . chr( 10 );
}

unset( $CRON );
unset( $CONF );
