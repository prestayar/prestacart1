<?php namespace DBSCore\V11Cart;
/**
 * DBSCore      A Great Package For Prestashop Developers!
 * @version		V11Cart
 *
 * @class       DBSAdminController
 * @website     DBSTheme.com
 * @copyright	(c) 2015 - DBSTHEME Team
 * @author      Ali Shareei <alishareei@gmail.com>
 * @since       29 Aug 2015
 */
 
 use ModuleAdminController;
 use Tools;
 use Tab;
 use Configuration;
 use DBSCore\V11Cart\DBSGlobal;
 use Language;
 use Mail;
 use Validate;

class DBSAdminController extends ModuleAdminController {
	
	/*
	|--------------------------------------------------------------------------
	| Vars And Defaults
	|--------------------------------------------------------------------------
	*/
	
	/**
	 * @vars redirectToAdminConfigureModule
	 */
	protected $redirectToAdminConfigureModule = FALSE;
	
	/**
	 * @vars Templates
	 */
	protected $adminTemplate         = NULL;
	protected $latestTabTemplate     = NULL;
	protected $defaultTabTemplate    = NULL;
	protected $licenseTabTemplate    = NULL;
	protected $contactUsTabTemplate  = NULL;
	protected $notFoundTabTemplate   = NULL;
	
	protected $tempContactUsTabTemplate  = NULL;
	
	/**
	 * @vars feeds URLs !
	 */
	protected $latestNewsFeed     = 'http://dbstheme.com/blog/feed/';
	protected $latestProductsFeed = 'http://dbstheme.com/category/%D9%BE%D8%B1%D8%B3%D8%AA%D8%A7%D8%B4%D8%A7%D9%BE/feed/';
	protected $latestTopicsFeed   = 'http://forum.dbstheme.com/external?type=rss2&nodeid=1'; //'http://first.ph/external.xml';

	/**
	 * @var ajax Tab
	 */
	protected $dbsAjaxDataName = 'dbsAjaxData';
	protected $submitedFlag      = FALSE;
	
	/**
	 * @vars Html Tabs
	 */
	protected $queryStringKeyForTabs  = 'dbs_tab';
	protected $defaultHtmlTab         = 'default';
	protected $thisFormHtmlTab         = 'default';
	protected $htmlTabNotFound        = 'not_found';
	protected $htmlTabPutLicense      = 'put_license';
	protected $showConfigureBtn       =  FALSE;
	protected $numberForHtmlTabs      =  array();
	
	/**
	 * @vars get Current Shop Id
	 */
	public $currentIdShop = NULL;
	
	/**
	 * default Function
	 */
	public function customHtmlTabs()
	{ 
		if( method_exists($this->module,'customHtmlTabs') )
			return $this->module->customHtmlTabs();
		else
			return array(); 
	}
	
	public function customHtmlTabPositions()
	{ 
		if( isset($this->module->customHtmlTabPositions) )
			return $this->module->customHtmlTabPositions;
		else
			return array(); 
	}
	
	/**
	 * @vars Default Html Tabs
	 */
	protected $defaultHtmlTabPositions = array(
		-1 => array(
			'name'      => 'top',
			'html_hr'   => FALSE,
		),
		50 => array(
			'name'      => 'bottom',
			'html_hr'   => TRUE,
		),
	);
	protected $defaultHtmlTabs = array(
		
		// TOP Position
		'default' => array(
			'title' => 'درباره این ماژول',
			'position' => 'top',
			'active' => TRUE,
			'css_icon' => 'icon-home',
			'ajax' => TRUE,
		),
		
		// Bottom Position
		'contact_us' => array(
			'title' => 'درباره پرستایار',
			'position' => 'bottom',
			'active' => TRUE,
			'css_icon' => 'icon-envelope',
			'ajax' => TRUE,
		),
	);
	
	/*
	|--------------------------------------------------------------------------
	| Methods
	|--------------------------------------------------------------------------
	*/
	
	/**
	 * @__construct
	 */
	public function __construct()
	{
		parent::__construct();
		$this->currentIdShop = (int)(Validate::isLoadedObject($this->context->shop) ? $this->context->shop->id : 0);
		
		$this->adminTemplate         = DBSGlobal::DBSCORE_DIR . '/views/templates/admin/dbscore_main.tpl';
		$this->defaultTabTemplate    = DBSGlobal::DBSCORE_DIR . '/views/templates/admin/default_tab.tpl';
		$this->licenseTabTemplate    = DBSGlobal::DBSCORE_DIR . '/views/templates/admin/license_tab.tpl';
		$this->contactUsTabTemplate  = DBSGlobal::DBSCORE_DIR . '/views/templates/admin/contactus_tab.tpl';
		$this->notFoundTabTemplate   = DBSGlobal::DBSCORE_DIR . '/views/templates/admin/notfound_tab.tpl';
		
		$this->tempContactUsTabTemplate  = DBSGlobal::DBSCORE_DIR . '/views/templates/admin/tempory_contactus_tab.tpl';
	}
	
	/**
	 * initialize Content ( module NavBar ) 
	 *
	 */
	public function initContent()
	{
		parent::initContent();
		
		if( $this->redirectToAdminConfigureModule == TRUE )
		{
			$token = Tools::getAdminTokenLite('AdminModules');
			header('Location: index.php?controller=AdminModules&configure=' . $this->module->name . '&token='.$token);
		}
	}
	
	/*
	|--------------------------------------------------------------------------
	| Render Form And List
	|--------------------------------------------------------------------------
	*/
	/**
	 * render List 
	 *
	 */
	public function renderList()
	{
		return $this->renderAdminTemplate();
	}
	
	/**
	 * show List For ActionTabs.
	 */
	public function getRenderList()
	{
		return parent::renderList();
	}
	
	/**
	 * Render Form.
	 */
	public function renderForm()
	{
		$this->defaultHtmlTab = $this->thisFormHtmlTab;
		$this->setRenderForm();
		$form = parent::renderForm();
		return $this->renderAdminTemplate($form);
	}
	
	/**
	 * show Form For ActionTabs.
	 */
	public function getRenderForm()
	{
		$this->setRenderForm();
		return parent::renderForm();
	}
	
	/**
	 * set Render Form ( Default Method If not Set In Controller ).
	 */
	public function setRenderForm()
	{
		return parent::renderForm();
	}
	
	/*
	|--------------------------------------------------------------------------
	| render DBSCore Panel
	|--------------------------------------------------------------------------
	*/
	
	/**
	 * get Html Tabs 
	 *
	 */
	public function getHtmlTabs()
	{
		$htmlTabs = array_merge( $this->defaultHtmlTabs, $this->customHtmlTabs() );
		foreach( $htmlTabs as $tabKey => $tabValue  )
		{
			if( isset($tabValue['controller']) )
				$htmlTabs[$tabKey]['link'] = $this->getAdminControllerLink($tabValue['controller']);
			else
				$htmlTabs[$tabKey]['link'] = $this->getAdminControllerLink();
		}
		return $htmlTabs;
	}
	
	/**
	 * get Html Tab Positions 
	 *
	 */
	public function getHtmlTabPositions()
	{
		$htmlTabPositons = $this->defaultHtmlTabPositions + $this->customHtmlTabPositions();
		ksort( $htmlTabPositons );
		return $htmlTabPositons;
	}
	
	/**
	 * render Admin Template 
	 *
	 */
	public function renderAdminTemplate($showThisContent = NULL)
	{		
		$this->context->smarty->assign(
			array(
				'bootstrap'             => TRUE,
				'tab_content'           => ( $showThisContent )? $showThisContent : $this->getTabContent(),
				'request_tab'           => $this->getRequestTab(),
				'show_configure_btn'    => $this->showConfigureBtn,
				'configure_url'         => $this->getModuleConfigureLink(),
				'current_url'           => $this->getAdminControllerLink(),
				'html_tabs'             => $this->getHtmlTabs(),
				'htmltab_pos'           => $this->getHtmlTabPositions(),
				'number_htmltab'        => $this->numberForHtmlTabs,
				'exist_new_version'     => DBSGlobal::existNewVersion($this->module->version),
				'ws_response'           => DBSGlobal::$webServiceResponse,
			)
		);
		return $this->module->display(_PS_MODULE_DIR_. $this->module->name ,  $this->adminTemplate );
	}
	
	
	/**
	 * Ajax Request for Get Tab Content
	 *
	 */
	public function ajaxProcessDBSActionTabs()
	{		
		die($this->getTabContent());	
	}

	/**
	 * get Request Tab
	 *
	 */
	public function getRequestTab()
	{
		$requestTab = Tools::getValue( $this->queryStringKeyForTabs );
		
		if ( empty($requestTab) )
			$requestTab = $this->defaultHtmlTab;
		
		return $requestTab;
	}
	
	/**
	 * get Tab Content
	 *
	 */
	public function getTabContent()
	{
        $requestTab = $this->getRequestTab();

        $tab_action = $this->htmlTabNotFound;
        $htmlTabs = $this->getHtmlTabs();

        if( isset($htmlTabs[$requestTab]) && ($htmlTabs[$requestTab]['active'] == TRUE ) )
        {
            $tab_action = strval($requestTab);
        }

		$tab_method = 'actionTab_' . $tab_action;
		
		if( !method_exists($this,$tab_method) )
			$tab_method = 'actionTab_' . $this->htmlTabNotFound;
		
		return $this->{$tab_method}();
	}
	
	/**
	 * make Admin Token 
	 *
	 */
	public function makeAdminToken($className)
	{
		return Tools::getAdminToken($className . (int)(Tab::getIdFromClassName($className)) .(int)$this->context->employee->id );
	}
	
	/**
	 * get Module Configure Link 
	 *
	 */
	public function getModuleConfigureLink()
	{
		return $this->context->link->getAdminLink('AdminModules') .'&amp;configure=' . $this->module->name;
	}
	
	/**
	 * get This Controller Link
	 *
	 */
	public function getAdminControllerLink($controllerName = NULL)
	{
		if( $controllerName == NULL  )
			$controllerName = Tools::getValue('controller');
		
		return $this->context->link->getAdminLink($controllerName) .'&amp;'. $this->queryStringKeyForTabs .'=';
	}
	
	/**
	 * get Ajax Data
	 *
	 */
	public function getAjaxData()
	{
		$submitedValues = (string)Tools::getValue($this->dbsAjaxDataName);
		$ajaxData = array();
		parse_str($submitedValues, $ajaxData);
		return $ajaxData;
	}
	
	public function setSmartyAssign( $tplVars = array() )
	{
		$defaultVars = array(
			'bootstrap'      => TRUE,
			'flash_message'  => DBSGlobal::$flashMessage,
			'request_tab'    => $this->getRequestTab(),
		);
		$this->context->smarty->assign( array_merge( $tplVars, $defaultVars ) );
	}
	

	/*
	|--------------------------------------------------------------------------
	| Default Action Tabs
	|--------------------------------------------------------------------------
	*/
	
	/**
	 * show Error 404 for NOTFOUND Tab.
	 *
     * @return string
     */
	public function actionTab_not_found()
	{
		DBSGlobal::$flashMessage = array(
			'css-alert' => 'alert-danger',
			'message' => $this->l('خطای 404 ، چنین برگه ای برای نمایش پیدا نشد !'),
		);
		$this->setSmartyAssign();
		return $this->module->display(_PS_MODULE_DIR_. $this->module->name,  $this->notFoundTabTemplate);
	}
	
	/**
	 * show LICENSE Tab if license is invalid.
	 *
     * @return string
     */
	public function actionTab_put_license()
	{
		DBSGlobal::$flashMessage = array(
			'css-alert' => 'alert-danger',
			'message' => $this->l('اجازه نامه معتبری برای استفاده از این ماژول بر روی فروشگاه شما شناسایی نشد.'),
		);
		
		if (Tools::isSubmit($this->dbsAjaxDataName) AND !$this->submitedFlag )
		{
			$data = $this->getAjaxData();
			$data['tab_request'] = isset($data['tab_request'])? $data['tab_request'] : 'default';
			$userLicense = trim($data[ DBSGlobal::getConfigNameForLicense($this->module->name) ]);
			
			if( isset( $userLicense ) AND !empty( $userLicense ) )
			{
				Configuration::updateValue(DBSGlobal::getConfigNameForLicense($this->module->name), $userLicense );
				$this->submitedFlag = TRUE;
				DBSGlobal::$flashMessage = array(
					'css-alert' => 'alert-success',
					'message' => $this->l('لایسنس با موفقیت ثبت و تایید شد.'),
				);
				return $this->getTabContent( (string)$data['tab_request']);
			}
		}
		
		$this->setSmartyAssign(
			array(
				'licenseName' => DBSGlobal::getConfigNameForLicense($this->module->name),
				'ws_response' => DBSGlobal::$webServiceResponse,
			)
		);
		return $this->module->display(_PS_MODULE_DIR_. $this->module->name,  $this->licenseTabTemplate);
	}
	
	/**
	 * show DEFAULT Tab for DBSCore.
	 *
     * @return string
     */
	public function actionTab_default()
	{
		$wsResponse =  DBSGlobal::$webServiceResponse;
		$dbsOffer = NULL;

		if( isset($wsResponse['product_info']['special_offer']) 
			AND !empty($wsResponse['product_info']['special_offer'])  )
		{
			$dbsOffer = $wsResponse['product_info']['special_offer'];
		}
		elseif( isset($wsResponse['offer']) AND !empty($wsResponse['offer']) )
		{
			$dbsOffer = $wsResponse['offer'];
		}
			
		$this->setSmartyAssign(
			array(
				'module_desc'        => $this->module->description,
				'module_full_desc'   => $this->module->fullDescription,
				'module_name'        => $this->module->displayName . ' ' .$this->module->version,
				'exist_new_version'  => DBSGlobal::existNewVersion($this->module->version),
				'ws_response'        => DBSGlobal::$webServiceResponse,
				'dbs_offer'          => $dbsOffer,
			)
		);
		return $this->module->display(_PS_MODULE_DIR_. $this->module->name,  $this->defaultTabTemplate);
	}
	
	/**
	 * show CONTACT US Tab.
	 *
     * @return string
     */ 
	public function actionTab_contact_us()
	{		
		
		if (Tools::isSubmit($this->dbsAjaxDataName) AND !$this->submitedFlag  )
		{
			$data = $this->getAjaxData();
			
			if( !isset($data['email_message']) OR empty($data['email_message']) )
			{
				DBSGlobal::$flashMessage = array(
					'css-alert' => 'alert-warning',
					'message' => $this->l('لطفا اطلاعات خواسته شده را با دقت وارد نمایید !'),
				);
				$this->submitedFlag = TRUE;
				return $this->getTabContent('contact_us');
			}
		
			 
			
			$templateVars['{shop_name}'] = Configuration::get('PS_SHOP_NAME');
			$templateVars['{shop_domain}'] = $_SERVER['HTTP_HOST']; 
			$templateVars['{shop_email}'] = Configuration::get('PS_SHOP_EMAIL');
			$templateVars['{module_name}'] = $this->module->name; 
			$templateVars['{module_version}'] = $this->module->version; 
			
			$templateVars['{name_family}'] = isset($data['name_family']) ? $data['name_family'] : '[empty]';
			$templateVars['{user_email}'] = isset($data['user_email']) ? $data['user_email'] : '[empty]';
			$templateVars['{department}'] = isset($data['department']) ? $data['department'] : '[empty]';
			$templateVars['{email_message}'] = isset($data['email_message']) ? $data['email_message'] : '[empty]';
			
			$id_land = Language::getIdByIso('fa');
			$template_name = 'contactus_tab'; //Specify the template file name
			$title = Mail::l('درخواست از ماژول ') . $this->module->name ; //Mail subject with translation
			$from = Configuration::get('PS_SHOP_EMAIL');   //Sender's email
			$fromName = Configuration::get('PS_SHOP_NAME'); //Sender's name
			$toName = 'DBSTheme Team';

			$sendMail = Mail::Send(	$id_land, $template_name, $title, 
									$templateVars, DBSGlobal::$contactUsEmail, $toName, 
										$from, $fromName, NULL, NULL, DBSGlobal::getDBSCoreMailsDir() );
			if ($sendMail)
			{
				DBSGlobal::$flashMessage = array(
					'css-alert' => 'alert-success',
					'message' => $this->l('درخواست با موفقیت ثبت گردید. اما توجه داشته باشید که ممکن است به دلایل فنی پیام شما به گروه دی بی اس تم نرسد.'),
				);
				
			} else{
				
				DBSGlobal::$flashMessage = array(
					'css-alert' => 'alert-danger',
					'message' => $this->l('خطایی در ثبت درخواست رخ داده ، لطفا تنظیمات فروشگاهتان را بررسی کنید !'),
				);
			}
			
			$this->submitedFlag = TRUE;
			return $this->getTabContent('contact_us');
			
		}
		
		$this->setSmartyAssign(
			array(
				'name_family'    =>  $this->context->employee->firstname . ' ' . $this->context->employee->lastname,
				'user_email'     => Configuration::get('PS_SHOP_EMAIL'),
			)
		);
		return $this->module->display(_PS_MODULE_DIR_. $this->module->name,  $this->tempContactUsTabTemplate);
	}
	
}