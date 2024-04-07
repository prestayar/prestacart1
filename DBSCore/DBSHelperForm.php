<?php namespace DBSCore\V11Cart;
/**
 * DBSCore      A Great Package For Prestashop Developers!
 * @version		V11Cart
 *
 * @class       DBSHelperForm
 * @website     DBSTheme.com
 * @copyright	(c) 2015 - DBSTHEME Team
 * @author      Ali Shareei <alishareei@gmail.com>
 * @since       3 Aug 2015
 */
 use HelperForm;
 use Module;
 use Tools;
 use Configuration;
 use AdminController;
 
class DBSHelperForm extends HelperForm {
	
	private $moduleObject = NULL;

    /**
     * Create a new HelperForm.
     *
     * @return self
     */
	public function __construct()
    {
		parent::__construct();
    }
	
    /**
     * Set Initialize with Set Module(By Object Or Name) and Set Default Values !
     *
	 * @param  $ObjectOrName
     * @return object
     */
	public function setInit($ObjectOrName , $setDefaults = TRUE )
	{
		if( is_object($ObjectOrName) && ($ObjectOrName instanceof Module) )
			$this->moduleObject = $ObjectOrName;
		else
			$this->moduleObject = Module::getInstanceByName($ObjectOrName);
		//if (Module::isInstalled($module_name) && Module::isEnabled($module_name))
			
		if( $setDefaults )
			$this->setDefaultValues();
			
		return $this;
	}
	
    /**
     * ser Default Values.
     *
     * @return object
     */
	private function setDefaultValues()
	{
		$modObject = $this->moduleObject;
		
		$this->module = $modObject;
		$this->name_controller = $modObject->name;
		$this->token = Tools::getAdminTokenLite('AdminModules');
		$this->currentIndex = AdminController::$currentIndex.'&configure='.$modObject->name;
		 
		// Language
		$default_lang = (int)Configuration::get('PS_LANG_DEFAULT'); // Get default Language
		$this->default_form_language = $default_lang;
		$this->allow_employee_form_lang = $default_lang;
		 
		// Title and toolbar
		$this->title = $modObject->displayName;
		$this->show_toolbar = TRUE;        // FALSE -> remove toolbar
		$this->toolbar_scroll = FALSE;      // yes - > Toolbar is always visible on the top of the screen.
		$this->submit_action = 'submit'.$modObject->name;
		$this->toolbar_btn = array(
			'save' =>
			array(
				'desc' => $modObject->l('Save'),
				'href' => AdminController::$currentIndex.'&configure='.$modObject->name.'&save'.$modObject->name.
				'&token='.Tools::getAdminTokenLite('AdminModules'),
			),
			'back' => array(
				'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
				'desc' => $modObject->l('Back to list')
			)
		);
		
		return $this;
	}

    /**
     * set Fields Value By Fielads Array List.
     *
	 * @param  $fields
     * @return object
     */
	public function setFieldsValue( $fields = array() )
	{
		if( is_array($fields) && count($fields) > 0 )
		{
			foreach( $fields as $key => $value )
			{
				$this->fields_value[$key] = Configuration::get($key);
			}	
		}
		
		return $this;
	}
	
    /**
     * set Field Value By String Field.
     *
	 * @param  string $field
     * @return object
     */
	public function setFieldValue($field , $id_lang = null, $id_shop_group = null, $id_shop = null)
	{
		if (is_string($field))
		{
			$this->fields_value[$field] = Configuration::get($field, $id_lang, $id_shop_group, $id_shop);
		}
		return $this;
	}
	
}
