<?php namespace DBSCore\V11Cart;
/**
 * DBSCore      A Great Package For Prestashop Developers!
 * @version		V11Cart
 *
 * @class       DBSValidator
 * @website     DBSTheme.com
 * @copyright	(c) 2015 - DBSTHEME Team
 * @author      Ali Shareei <alishareei@gmail.com>
 * @since       30 July 2015
 */
if (!class_exists('GUMP')) 
	require_once("libs/gump.class.php");

use GUMP;
use Validate;

class DBSValidator extends GUMP {
	
	/*
	|--------------------------------------------------------------------------
	| prestashop Default Validators ( Compatible with DBSValidator )
	|--------------------------------------------------------------------------
	*/

	/**
	 * Validate Prestashop Default Validate::isDate
	 */
	public static function validate_ps_isDate($field, $input, $param = NULL)
	{
		if ( empty($input[$field]) )
			return TRUE;
	   
		if ( Validate::isDate($input[$field]) )
			return TRUE;

	   return array(
			'field' => $field,
			'value' => null,
			'rule' => __FUNCTION__,
			'param' => $param,
		);
	}

	/**
	 * Validate Prestashop Default Validate::isBool
	 */
	public static function validate_ps_isBool($field, $input, $param = NULL)
	{
	   if ( Validate::isBool($input[$field]) )
		   return TRUE;

	   return array(
			'field' => $field,
			'value' => null,
			'rule' => __FUNCTION__,
			'param' => $param,
		);
	}

	/*
	|--------------------------------------------------------------------------
	| DBSValidator Custom vallidator
	|--------------------------------------------------------------------------
	*/
	
	/**
	 * Validate web color ( Hex Color like #FcE23Dd )
	 */
	public static function validate_web_color($field, $input, $param = NULL)
	{
		if ( empty($input[$field]) )
			return TRUE;
	   
		$colorHexPattern = '/^(#[0-9a-f]{3}|#[0-9a-f]{6})?$/i';
		if ( preg_match($colorHexPattern,$input[$field]) )
			return TRUE;

		return array(
			'field' => $field,
			'value' => null,
			'rule' => __FUNCTION__,
			'param' => $param,
		);
	}
	
}