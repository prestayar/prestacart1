<?php namespace DBSCore\V11Cart;
/**
 * DBSCore      A Great Package For Prestashop Developers!
 * @version		V11Cart
 *
 * @class       DBSTemplateEngine
 * @website     DBSTheme.com
 * @copyright	(c) 2015 - DBSTHEME Team
 * @author      Ali Shareei <alishareei@gmail.com>
 * @since       29 Sep 2015
 */
 
class DBSTemplateEngine {

	/**
	 * @var $template 	raw string template
	 */
	public static $template;
	
	/**
	 * @var $vars	template vars
	 */
	public static $vars;
	
	/**
	 * @var $compiledString	 compiled template string
	 */
	public static $compiledString;
	
	/*
	|--------------------------------------------------------------------------
	| DBSTemplateEngine Methods
	|--------------------------------------------------------------------------
	*/
	/**
	 * construct Object and Set properties
	 */
	public static function publish($stringTpl,$arrayVars)
	{
		self::$template 		= NULL;
		self::$vars 			= NULL;
		self::$compiledString 	= NULL;
		
		self::setTemplate($stringTpl);
		self::setVars($arrayVars);
		self::compile();
		return self::fetchCompiled();
	}
	
	/**
	 * setTemplate
	 *
     */
	public static function setTemplate($tpl)
	{
		if( is_string($tpl) && !empty($tpl) )
			self::$template = $tpl;
		else
			self::$template = '';
			//die("Template is Invalid...");
	}
	
	/**
	 * setVars
	 *
     */
	public static function setVars($arrVars)
	{
		if( is_array($arrVars) )
			self::$vars = $arrVars;
		else
			self::$vars = array();
			// die("Vars is Invalid...");
	}
	

	/**
	 * compile
	 *
     */
	public static function compile()
	{
		foreach(self::$vars as $var => $content)
		{
			//self::$template = str_replace('{$' . $var . '}', $content, self::$template);
			self::$template = str_replace( $var, $content, self::$template);
		}
	}
	
	/**
	 * fetch and published string
	 *
     */
	public static function fetchCompiled()
	{
		return self::$template;
	}
	
}