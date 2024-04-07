<?php namespace DBSCore\V11Cart;
/**
 * DBSCore      A Great Package For Prestashop Developers!
 * @version		V11Cart
 *
 * @class       DBSGlobal
 * @website     DBSTheme.com
 * @copyright	(c) 2015 - DBSTHEME Team
 * @author      Ali Shareei <alishareei@gmail.com>
 * @since       29 Aug 2015
 */
use Configuration;
 
class DBSGlobal {
	
	/*
	|--------------------------------------------------------------------------
	| Constans
	|--------------------------------------------------------------------------
	*/

	const DBSCORE_DIR = 'DBSCore'; 
	
	/*
	|--------------------------------------------------------------------------
	| Static Vars
	|--------------------------------------------------------------------------
	*/
	
	public static $contactUsEmail = 'comment@dbstheme.com';
	public static $flashMessage = array();
	public static $webServiceResponse =  array();
	
	/*
	|--------------------------------------------------------------------------
	| Static Methods
	|--------------------------------------------------------------------------
	*/
	
	/**
	 * get DBSCore Mails Directory
	 *
	 */
	public static function getDBSCoreMailsDir()
	{
		return dirname(__FILE__).'/mails/';
	}
	
	/**
	 * get Configuration Name For License
	 *
	 */
	public static function getConfigNameForLicense($salt = NULL)
	{
		$configName = 'DBSTHEME_LICENSE';
		if( $salt != NULL )
			$configName .= '_' . strtolower(trim($salt));
		
		return $configName;
	}
	
	/**
	 * get Url Content With CURL
	 *
	 */	
	public static function getUrlContent($url = NULL)
	{
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		$response = curl_exec($curl);
		if ($response === FALSE) {
			$info = curl_getinfo($curl);
			curl_close($curl);
			return FALSE;
		}
		curl_close($curl);
		return $response;
    }
	
	/**
	 * get Feed Array
	 *
	 */	
	public static function getFeedArray($feedUrl , $onlyItemTitleAndLink = TRUE)
	{
		$feedContent = self::getUrlContent($feedUrl);
		$xml = @simplexml_load_string($feedContent);
		$feedArray = json_decode( json_encode($xml), TRUE);
		
		if( $feedArray === NULL )
			return NULL;
		
		if ( $onlyItemTitleAndLink == TRUE )
		{
			$items = array();
			if ( isset($feedArray['channel']['item']) )
			{
				foreach( $feedArray['channel']['item'] as $item )
				{
					$items[$item['title']] = $item['link'];
				}
				return $items;
			}
			
		}else{
			
			return $feedArray ;
		}
	}

	/**
	 * web Service Checker
	 *
	 */
    public static function webServiceChecker($moduleName, $moduleVersion, $domainName = null)
    {
        return true;
    }

	/**
	 * check realeased new Version
	 *
	 */
	public static function existNewVersion($currentModuleVersion)
	{
		if( isset( self::$webServiceResponse['product_info']['latest_version']) )
		{
			$latestVersion = (float) self::$webServiceResponse['product_info']['latest_version'];
			if( $latestVersion > (float)$currentModuleVersion )
				return TRUE;
		}
		return FALSE;
	}
}
