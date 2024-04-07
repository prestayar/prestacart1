<?php namespace DBSCore\V11Cart;
/**
 * DBSCore      A Great Package For Prestashop Developers!
 * @version		V11Cart
 *
 * @class       DBSModule
 * @website     DBSTheme.com
 * @copyright	(c) 2015 - DBSTHEME Team
 * @author      Ali Shareei <alishareei@gmail.com>
 * @since       30 July 2015
 */
use PaymentModule;
use Db;
use Tab;
use Language;
use Configuration;
use Tools;
use Validate;
use DBSCore\V11Cart\DBSGlobal;

abstract class DBSModule extends PaymentModule {

    /**
     * Create a new module.
     *
     * @return self
     */
	public function __construct()
    {
		$this->baseDomainURL = Configuration::get('PS_SSL_ENABLED') ? _PS_BASE_URL_SSL_ : _PS_BASE_URL_ ;
		$this->baseDomainURL .= __PS_BASE_URI__;
		parent::__construct();
    }
	
	/**
	 * @const DEFAULT_VALUE
	 */
	const DEFAULT_VALUE = -99;

	/**
	 * @var $adminMainTabClass
	 */
	public $baseDomainURL = NULL;
	
	/**
	 * @var $adminMainTabClass
	 */
	private $adminMainTabClass = 'AdminPrestaYarModule';
	
	/**
	 * @var $adminMainTabTitle
	 */
	private $adminMainTabTitle = 'پرستایار';
	
	/**
	 * @var $defaultMainTabModule
	 */
	private $defaultMainTabModule = 'psy_prestayar';
	
	/**
	 * @var $moduleTabClassName
	 */
	public $moduleTabClassName = NULL;
	
	/**
	 * @var $showAdminPanelBtn
	 */
	public $showAdminPanelBtn = TRUE;
	
	/**
	 * @var $configureRedirectToController
	 */
	public $configureRedirectToController = TRUE;
	
	/**
	 * getModuleTabClassName
	 *
     */
	public function getModuleTabClassName()
	{
		if(  isset($this->moduleTabClassName) ) // property_exists($this, 'moduleTabClassName') 
			return $this->moduleTabClassName;
		else
			return 'Admin' . ucfirst($this->name);
	}
	
	public function getIdShop()
	{
		return (int)(Validate::isLoadedObject($this->context->shop) ? $this->context->shop->id : 0);
	}
	
	/**
	 * make Admin Token 
	 *
	 */
	public  function makeTokenByClassName($className)
	{
		return Tools::getAdminToken($className . (int)(Tab::getIdFromClassName($className)) .(int)$this->context->employee->id );
	}
	
	public  function makeAdminControllerLink($className)
	{
		return 'index.php?controller='.$className.'&token='.$this->makeTokenByClassName($className);
	}
	
	/*
	|--------------------------------------------------------------------------
	| SQL Query
	|--------------------------------------------------------------------------
	*/	
	
	/**
	 * run SQL
	 *
     * @param $Sql
     * @return boolean
     */
	public function runSql($Sql)
	{
		if(!Db::getInstance()->execute($Sql)){
			return FALSE;
		}
		return TRUE;
	}
	
	/**
	 * run SQL For Array Results
	 *
     * @param $Sql
     * @return array
     */
	public function runSqlForArray($Sql)
	{
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($Sql, TRUE, FALSE);
	}
	
	/**
	 * run SQL For First Row Matched
	 *
     * @param $Sql
     * @return array
     */
	public function runSqlForRow($Sql)
	{
		return Db::getInstance()->getRow($Sql);
	}
	
	/**
	 * run SQL For Insert Data To A Table
	 *
     * @params $table, $data, $null_values, ...
     * @return array
     */
	public function runSqlForInsert($table, $data, $null_values = TRUE, $use_cache = TRUE, $type = Db::INSERT, $add_prefix = TRUE)
	{
		return Db::getInstance()->insert($table, $data, $null_values, $use_cache, $type, $add_prefix);
	}
	
	/**
	 * run SQL For First Row Matched
	 *
     * @param $Sql
     * @return array
     */
	public function runSqlForDelete($table, $where = '', $limit = 0, $use_cache = true, $add_prefix = true)
	{
		return Db::getInstance()->delete($table, $where, $limit, $use_cache, $add_prefix);
	}
	
	/**
	 * drop if a table already exists.
	 *
     * @param $tableName
     * @return boolean
     */
	public function dropTableByName($tableName)
	{
		$sql = 'DROP TABLE IF EXISTS '._DB_PREFIX_.$tableName;
		if ($this->runSQL($sql))
			return TRUE;
		else
			return FALSE;
	}
	/*
	|--------------------------------------------------------------------------
	| Tab Methods
	|--------------------------------------------------------------------------
	*/	
	
    /**
	 * check if a tab already exists.
	 *
     * @param $tabClassName
     * @return integer
     */
	public function findTabIdByName($tabClassName)
	{
		return Tab::getIdFromClassName($tabClassName);
	}
	
    /**
	 * find parnet id_tab by child id_tab.
	 *
     * @param $childIdTab
     * @return integer
     */
	public function findParentIdTabByChildIdTab($childIdTab)
	{
		$getRow_SQL = 'SELECT `id_parent` FROM `'._DB_PREFIX_.'tab` WHERE `id_tab` = '.(int)$childIdTab;
		$row = $this->runSqlForRow($getRow_SQL);
		return $row['id_parent'];
	}
	
	/**
	 * remove tab from database with id_tab.
	 *
     * @param $idTab
     * @return boolean
     */
	public function deleteTabById($idTab)
	{
		$tab = new Tab($idTab);
        $tab->delete();
		return TRUE;
	}
	
	/**
	 * remove tab from database By Name.
	 *
     * @param $tabClassName
	 * @param $deleteParentTab
     * @return boolean
     */
	public function deleteTabByName($tabClassName, $deleteParentTab = TRUE)
	{
		$idTab = $this->findTabIdByName($tabClassName);	
		if ( $idTab != 0 ){
			$parentId = $this->findParentIdTabByChildIdTab($idTab);
			$this->deleteTabById($idTab);
			if ( $deleteParentTab == TRUE && $parentId ){
				$this->deleteParentTab($parentId);	
			}
			return TRUE;
		}else{ 
			return FALSE;
		}
	}
	
	/**
	 * delete Parent Tab.
	 *
     * @param $idTab
     * @return boolean
     */
	public function deleteParentTab($parentId)
	{
		$getChildsTabId_SQL = 'SELECT id_tab FROM `'._DB_PREFIX_.'tab` WHERE id_parent = '.(int)$parentId;
		$results = $this->runSqlForArray($getChildsTabId_SQL);
		if(count($results) == 0)
			return $this->deleteTabById($parentId);
		else
			return FALSE;
	}
	
	/**
	 * make new Tab.
	 *
     * @param $tabClassName
	 * @param $module
	 * @param $parentId
	 * @param $tabTitle
     * @return integer
     */
	private function _makeNewTab($tabClassName,$module,$parentId,$tabTitle, $active = 1)
	{
		$newTab = new Tab();
        $newTab->class_name = $tabClassName;
        $newTab->module = $module;
        $newTab->id_parent = $parentId;
        $newTab->active = $active;
        $langs = Language::getLanguages(FALSE);
        foreach ($langs as $l) {
            $newTab->name[$l['id_lang']] = $this->l($tabTitle);
        }
        $newTab->add( TRUE, FALSE);
		return $this->findTabIdByName($tabClassName);
	}
	
	/**
	 * Create Admin DBS Main Tab ( Default Parent Tab ).
	 *
     * @return integer
     */
	public function createOrFindDefaultParentTab( $module = self::DEFAULT_VALUE )
	{
		$parentTabId = $this->findTabIdByName($this->adminMainTabClass);
		
		if ( $module == self::DEFAULT_VALUE )
				$module = $this->defaultMainTabModule;
			
		if ( $parentTabId == 0 )
			return $this->_makeNewTab($this->adminMainTabClass,$module,0,$this->adminMainTabTitle);
		else 
			return $parentTabId;
	}
	
	/**
	 * Create new Tab Or Find existing tab with tabClassName ( and in default Create Parent Tab ).
	 *
     * @param $tabClassName
	 * @param $title
	 * @param $parentId
     * @return integer
     */
	public function createTab($tabClassName,$title, $active = 1, $parentId = self::DEFAULT_VALUE)
	{
		$tabId = $this->findTabIdByName($tabClassName);
		if ( $tabId == 0 ){
			
			if ( $parentId == self::DEFAULT_VALUE )
				$parentId = $this->createOrFindDefaultParentTab();
			
			return $this->_makeNewTab($tabClassName,$this->name,$parentId,$title, $active);
		}
		else 
			return $tabId;
	}
	
	/*
	|--------------------------------------------------------------------------
	| Display and style Methods
	|--------------------------------------------------------------------------
	*/	
	
	/**
	 * show bootstrap Alerts
	 *
     * @param $String
	 * @param $type     info,danger,warning,success
     * @return string
     */
	public function showBootstrapAlert($String , $type = 'info')
	{
	 	$output = '
	 	<div class="bootstrap">
			<div class="alert alert-'.$type.'">
				<button type="button" class="close" data-dismiss="alert">&times;</button>
				'.$String.'
			</div>
		</div>';
		return $output;
	}
	
	/**
	 * show Header for Module Configure Page!
	 *
     * @param $url
     * @return string
     */
	public function getConfigureHeader()
	{
		$goToAdminPanelURL = $this->context->link->getAdminLink( $this->getModuleTabClassName() );
		$this->smarty->assign(
			array(
				'show_admin_panel_btn'  => $this->showAdminPanelBtn,
				'go_to_admin_panel_url' => $goToAdminPanelURL,
				'exist_new_version'    => DBSGlobal::existNewVersion($this->version),
				'ws_response'          => DBSGlobal::$webServiceResponse,
			)
		);
		return $this->display($this->name, DBSGlobal::DBSCORE_DIR.'/views/templates/admin/configure_header.tpl');
	}
	
	/**
	 * show Module Buy Link
	 *
     * @param $text
     * @return string
     */
	public function showModuleBuyLink()
	{ 
		if( isset(DBSGlobal::$webServiceResponse['product_info']['buy_link']) && DBSGlobal::$webServiceResponse['product_info']['buy_link'] != NULL )
		{
			$buyLink = DBSGlobal::$webServiceResponse['product_info']['buy_link'];
			return $this->showBootstrapAlert('<a href="'. $buyLink .'">' . $this->l('برای دریافت لایسنس معتبر بر روی اینجا کلیک کنید.') . '</a>', 'warning');
		}
	}
	
	/**
	 * add css styles for admin panel
	 *
     * @return cssStyle!
     */
	public function hookDisplayBackOfficeHeader()
	{
		$this->context->controller->addCss($this->_path. DBSGlobal::DBSCORE_DIR.'/views/css/admin/admin-prestayar-styles.css');
		$this->context->controller->addjs($this->_path. DBSGlobal::DBSCORE_DIR.'/views/js/admin/admin_dbscore.js', 'all');
	}
	
	/*
	|--------------------------------------------------------------------------
	| Configuration Methods
	|--------------------------------------------------------------------------
	*/
	
	/**
	 * delete Configuration
	 *
	 * @param $fields
     * @return boolean
     */
	public static function deleteConfigs(array $fields)
	{
		if ( is_array($fields) && count($fields) > 0 )
		{
			foreach( $fields as $key => $value ) {
				Configuration::deleteByName($key);
			}	
			return TRUE;
		}
		else
			return FALSE;
	}
	
	/**
	 * get and Save Configuration
	 *
	 * @param $fields
     * @return boolean
     */
	public static function getAndSaveConfigs(array $fields)
	{
		if ( is_array($fields) && count($fields) > 0 )
		{
			foreach( $fields as $key => $value ) {
				Configuration::updateValue( $key, strval(Tools::getValue($key)) );
			}	
			return TRUE;
		}
		else 
			return FALSE;
	}
	
	/**
	 * get Configuration
	 *
	 * @param $fields
     * @return boolean
     */
	public static function getSubmitValues(array $fields)
	{
		$submitValues = array();
		if ( is_array($fields) && count($fields) > 0 )
		{
			foreach( $fields as $key => $value ) {
				$submitValues[$key] = strval(Tools::getValue($key));
			}
		}
		
		return $submitValues;
	}
	
	/**
	 * make Hook Name By String (and lowerCased !)
	 *
	 * @param $string
     * @return string
     */
	public static function makeHookNameByString($string, $lowerCase = TRUE)
	{
		$hookName = 'hook'. $string;
		
		if ( $lowerCase === TRUE )
			$hookName = strtolower($hookName);
		
		return $hookName;
	}
	
	/*
	|--------------------------------------------------------------------------
	| Hooks Methods
	|--------------------------------------------------------------------------
	*/
	
	/**
	 * Register and Install Hooks
	 *
	 * @param $config
     * @return string
     */
	public function registerArrayHooks($hooksArray = array() , $addDefaultHooks = TRUE )
	{
		if ( $addDefaultHooks == TRUE )
		{
			$hooksArray[] = 'displayBackOfficeHeader';
		}
		
		foreach( $hooksArray as $hookPlace ) 
		{
			if (Validate::isHookName($hookPlace) && !$this->isRegisteredInHook($hookPlace))
				$this->registerHook($hookPlace);
		}
		
		return TRUE;
	}
	
	/*
	|--------------------------------------------------------------------------
	| License Methods
	|--------------------------------------------------------------------------
	*/
	
	/**
	 * for module settings and configuration module page
	 *
	 */
	public function getContent()
	{
		if( $this->configureRedirectToController === TRUE )
			Tools::redirectAdmin($this->context->link->getAdminLink($this->moduleTabClassName));
		
		$output = null;
		//$output .= $this->configHeader($this->context->link->getAdminLink( $this->getModuleTabClassName() ));
		$output .= $this->getConfigureHeader();
		
		if (Tools::isSubmit('submitLicenseForm'))
		{
			$licenseKey = strval(Tools::getValue(DBSGlobal::getConfigNameForLicense($this->name)));
			if( isset($licenseKey) AND !empty($licenseKey) ){	
				Configuration::updateValue( DBSGlobal::getConfigNameForLicense($this->name), $licenseKey);
				if( DBSGlobal::webServiceChecker($this->name, $this->version) ){
					$output .= $this->displayConfirmation($this->l('لایسنس با موفقیت ثبت و تایید شد.'));
				}
			}
		}
		
		if( DBSGlobal::webServiceChecker($this->name, $this->version) == FALSE )
		{
			$output .= $this->displayError( $this->l('اجازه نامه معتبری برای استفاده از این ماژول بر روی فروشگاه شما شناسایی نشد.') );
			$output .= $this->showModuleBuyLink();
			return $output.$this->displayLicenseForm();
		}
		
		return $this->getConfigureContent($output);
	}
	
	/**
	 * for module Configure Content ( Just For Default - Must be Override in Module )
	 *
	 */
	public function getConfigureContent($output = null)
	{
		return;
	}
	
	/**
	 * for generate License form with HelperForm
	 *
	 */
	public function displayLicenseForm()
	{
		// Init Fields form array
		$licenseFrom[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('درج اجازه نامه معتبر'),
			),
			'input' => array(
				array(
					'type' => 'text',
					'label' => $this->l('کلید لایسنس'),
					'name' => DBSGlobal::getConfigNameForLicense($this->name),
					'size' => 20,
					'required' => TRUE,
					'desc' => $this->l('لطفاً کد لایسنس معتبری که از وب سایت دی بی اس تم دریافت نموده اید اینجا وارد نمایید.'),
				),
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'btn btn-default pull-right'
			)
		);
		
		// Generate Module Settings Form With DBSCore\DBSHelperForm.
		$helper = new DBSHelperForm();
		$helper->setInit($this)->setFieldValue( DBSGlobal::getConfigNameForLicense($this->name) );
		$helper->submit_action = 'submitLicenseForm';
		return $helper->generateForm( $licenseFrom );
	}
	
}
