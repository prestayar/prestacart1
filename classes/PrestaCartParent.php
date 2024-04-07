<?php
/**
 * PrestaCart      Cart Module For Prestashop
 *
 * @DBSCore        Compatible with DBSCore V11Cart
 * @website        PrestaYar.com
 * @copyright	   (c) 2017 - PrestaYar Team
 * @author         Hashem Afkhami <hashem_afkhami@yahoo.com>
 * @since          02 Jan 2017
 */

require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'DBSCore/DBSCore.php');
require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'classes/PSFOrderCore.php');

use DBSCore\V11Cart\DBSModule;

class PsCartParent extends DBSModule
{
    /**
     * I don't know!
     */
    protected $_html       = '';
    protected $_postErrors = array();
    protected $_errors     = array();

    /*
    |--------------------------------------------------------------------------
    | construct methods & module constant & module default Values
    |--------------------------------------------------------------------------
    */

    /**
     * Module Vars and Default Values.
     */
    public $moduleTabClassName 			= 'AdminPsCart';
    public $moduleTitle        			= 'Presta Cart';
    public $showAdminPanelBtn  			= TRUE;
    public $moduleStatusCod			= 'PSCA_STATUS_COD';
    public $moduleTypePanelCod		= 'PSCA_TYPE_PANEL_COD';
    public $moduleEmailGuest		= 'PSCA_EMAIL_GUEST';
    public $id_carrier;
    public $cod;

    /**
     * Module content that can be displayed in this Hooks
     */
    public $hooksForContent = array(
        'displayBackOfficeHeader',
        'displayHeader',
        'displayAdminOrder',
        'actionDispatcher',
        'updateCarrier',
        'payment',
        'paymentReturn'
    );

	/*
	|--------------------------------------------------------------------------
	| Global Vars
	|--------------------------------------------------------------------------
	*/
	public $customHtmlTabPositions = array(
		1 => array(
			'name'      => 'config',
			'html_hr'   => TRUE,
		)
	);
	public $customHtmlTabs;

	/**
	 * PsCartParent Constructor.
	 */
	public function __construct()
	{
        $this->name    = 'psf_prestacart';
        $this->tab     = 'payments_gateways';
        $this->version = '1.10.0';
        $this->author        = 'PrestaYar.com';
        $this->bootstrap     = true;
        $this->need_instance = 0;

        $this->displayName = $this->l('Presta Cart','prestacartparent');
        $this->tabName     = $this->l('Presta Cart','prestacartparent');
        $this->description = $this->l('Simple and Quick Shopping Cart','prestacartparent');
        $this->fullDescription  = '<p><span style="color: #000000;">پرستاکارت (<strong>PrestaCart</strong>) پیشرفته ترین، پرامکانات ترین و هوشمندترین سبد خرید طراحی شده در جهان است که توسط پرستایار برای فروشگاه ساز پرستاشاپ آماده شده است ، این سبد ترکیبی است از خلاقیت، بومی سازی اصول علمی فروش و برنامه نویسی پیشرفته. پرستاکارت هر امکانی که برای فروش حرفه ای نیاز داشته باشید در اختیارتان قرار داده است.</span></p>
<p><span style="color: #000000;">نسخه جدید این ماژول ویژه پرستاشاپ ۱.۷ با ساختار کامل بهینه تر و امکانات بیشتر منتشر شده است.</span></p>';

        parent::__construct();

		$cod_config = Configuration::get('PSCA_STATUS_COD');
		$panelCod   = Configuration::get('PSCA_TYPE_PANEL_COD');
		$file_patch = dirname(__FILE__) . DIRECTORY_SEPARATOR .'codpanels/'.$panelCod.'.php';

		if($cod_config == '1' and file_exists($file_patch) )
		{
            require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR .'DBSCodPanel.php');
            require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR .'codpanels/'.$panelCod.'.php');
            $this->cod 		= new PsCartCod();
            $this->options  = $this->cod->getInfo();
		}
		else{
			require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR .'codpanels/Online.php');
			$this->cod = new PsCartCod();
		}
		$this->nameCod = $this->cod->nameCod;
		$this->ajaxPSCart = false;

	}

    /*
    |--------------------------------------------------------------------------
    | Install and UnInstall mehods
    |--------------------------------------------------------------------------
    */

    /**
     * install module method And Register Hooks !
     */
    public function install()
    {
        if ( !parent::install() OR !$this->registerArrayHooks($this->hooksForContent) )
            return FALSE;

        if ( !$this->installConfig() )  return false;
        if ( !$this->installZone() )  return false;
        if ( !$this->installMeta() )  return false;
        if ( !$this->installPostCarrier() )  return false;
        try{
            $this->installDb();
        }catch (PrestaShopDatabaseException $exception){}

        // create module tab  ( And Create Default Parent Tab )
        if( !$this->createTab( $this->getModuleTabClassName(), $this->tabName) )
            return false;

        return TRUE;
    }

    /**
     * uninstall module method
     */
    public function uninstall()
    {
        //$this->uninstallDb();
        if ( !$this->uninstallConfig() ) return false;
        if ( !$this->uninstallMeta() )  return false;

        if ( !parent::uninstall() OR !$this->deleteTabByName($this->getModuleTabClassName()) )
            return FALSE;

        return TRUE;
    }

	/**
	 * install Config
	 */
    public function installConfig()
	{
		$fields = $this->makeFieldsOptions();
		foreach($fields as $key => $field)
		{
			if( $field['html'] ) $default = '';
			else $default = $field['default'];

			Configuration::updateValue($key,$default);
			if( $field['html'] )
				Configuration::updateValue($key,$field['default'],$field['html']);
		}

		$items = $this->cod->getItems();
		foreach($items as $key => $field)
			Configuration::updateValue($key,$field['default']);

		return true;
    }

    public function installZone()
	{
		// add zone and state
		$zonesDBS = array(
			array(
				'name'=>'درون شهری',
				'id_zone'=>null
			),
			array(
				'name'=>'درون استانی',
				'id_zone'=>null
			),
			array(
				'name'=>'برون استانی همجوار',
				'id_zone'=>null
			),
			array(
				'name'=>'برون استانی غیر همجوار',
				'id_zone'=>null
			)
		);
		$zones = Zone::getZones();
		foreach($zonesDBS as $key => $zoneDBS){
			$add = true;
			foreach($zones as $zone)
				if($zone['name'] == $zoneDBS['name']){
					$add = false;
					$zonesDBS[$key]['id_zone'] = $zone['id_zone'];
				}

			if($add){
				$zone = new Zone();
				$zone->name = $zoneDBS['name'];
				$zone->active = '1';
				$zone->add();

				$zonesDBS[$key]['id_zone'] = $zone->id;
			}
		}

		// add iran
		$iranID = Country::getByIso('IR');
		if(!$iranID){
			$country = new Country();
			$country->name['1'] = 'Iran';
			$country->id_zone = '0';
			$country->iso_code = 'IR';
			$country->call_prefix = '98';
			$country->active = '1';
			$country->contains_states = '1';
			$country->need_identification_number = '0';
			$country->need_zip_code = '1';
			$country->zip_code_format = 'NNNNN-NNNNN';
			$country->display_tax_label = '1';

			$languages = Language::getLanguages(true);
			foreach ($languages as $language)
			{
				if ($language['iso_code'] == 'fa')
					$country->name[(int)$language['id_lang']] = 'ایران';
				else
					$country->name[(int)$language['id_lang']] = 'Iran';
			}
			$country->add();
			$iranID	= 	$country->id_country;
		}

		// add zone and state
		$statesDBS = array(
			array(
				'name'=>'درون شهری',
				'iso_code'=>'IR1',
				'id_zone'=>$zonesDBS['0']['id_zone'],
				'id_country'=>$iranID
			),
			array(
				'name'=>'درون استانی',
				'iso_code'=>'IR2',
				'id_zone'=>$zonesDBS['1']['id_zone'],
				'id_country'=>$iranID
			),
			array(
				'name'=>'برون استانی همجوار',
				'iso_code'=>'IR3',
				'id_zone'=>$zonesDBS['2']['id_zone'],
				'id_country'=>$iranID
			),
			array(
				'name'=>'برون استانی غیر همجوار',
				'iso_code'=>'IR4',
				'id_zone'=>$zonesDBS['3']['id_zone'],
				'id_country'=>$iranID
			)
		);

		$states = $this->getStates();
		foreach($statesDBS as $key => $stateDBS)
		{
			$add = true;
			foreach($states as $state)
				if($state['iso_code'] == $stateDBS['iso_code']) $add = false;

			if($add){
				$state = new State();
				$state->name = $stateDBS['name'];
				$state->iso_code = $stateDBS['iso_code'];
				$state->id_zone = $stateDBS['id_zone'];
				$state->id_country = $stateDBS['id_country'];
				$state->active = '0';
				$state->add();
			}
		}

		$zones_fields = array(
			'PSCA_ZONE_CITY'		=>'0',
			'PSCA_ZONE_STATE'		=>'0',
			'PSCA_ZONE_ADJACENT'	=>'0',
			'PSCA_ZONE_UNADJACENT'	=>'0'
		);
		$states = $this->getStates();
		foreach($states as $key => $state)
			switch ( $state['iso_code'] )
			{
				case 'IR1' :
					$zones_fields['PSCA_ZONE_CITY'] = $state['id_state'];
					break;
				case 'IR2' :
					$zones_fields['PSCA_ZONE_STATE'] = $state['id_state'];
					break;
				case 'IR3' :
					$zones_fields['PSCA_ZONE_ADJACENT'] = $state['id_state'];
					break;
				case 'IR4' :
					$zones_fields['PSCA_ZONE_UNADJACENT'] = $state['id_state'];
					break;
			}

		foreach( $zones_fields as $key => $value )
			Configuration::updateValue($key,$value);

		return true;
    }

    public function installMeta()
	{
		$meta	    = new Meta();
		$meta->page = 'module-psf_prestacart-order';
		$meta->configurable = '0';

		$languages = Language::getLanguages(true);
		foreach ($languages as $language)
		{
			$meta->url_rewrite[(int)$language['id_lang']] = 'basket';
			if ($language['iso_code'] == 'fa')
				$meta->title[(int)$language['id_lang']] = 'ثبت سفارش';
			else
				$meta->title[(int)$language['id_lang']] = 'Order';
		}
		$meta->add();
		return true;
    }

    public function installOrderStates()
	{
        $orderStates = $this->cod->getOrderStates();
		foreach($orderStates as $key => $state){
			$flag = Configuration::get($this->nameCod.'_ORDER_STATE_'.$key);
			if (true or $flag == false){
				$orderState[$key] = new OrderState();
				$orderState[$key]->name = array();
                foreach (Language::getLanguages() as $language)
                    $orderState[$key]->name[$language['id_lang']] = $state['options']['name'];
                // status Properties
                $orderState[$key]->color = $state['options']['color'];
				$orderState[$key]->logable = $state['options']['logable'];
				$orderState[$key]->invoice = $state['options']['invoice'];
				$orderState[$key]->hidden = $state['options']['hidden'];
				$orderState[$key]->send_email = $state['options']['send_email'];
                $orderState[$key]->shipped = $state['options']['shipped'];
                $orderState[$key]->paid = $state['options']['paid'];
                $orderState[$key]->delivery = $state['options']['delivery'];

				if ( $state['options']['send_email']){
					$orderState[$key]->template = 'order_changed';
				}
                if ($orderState[$key]->add())
                    Configuration::updateValue($this->nameCod.'_ORDER_STATE_'.$key, (int)$orderState[$key]->id);
			}
		}
        return true;
    }

	public function installPostCarrier()
	{
		$onlineCarrier = $this->makeCarrier();
		$id_pishtaz_online 	= $this->addCarrier( $onlineCarrier );
		Configuration::updateValue('PSCA_ONLINE_CARRIER', $id_pishtaz_online);

        return true;
    }

    public function installDb()
    {
        // Run SQL Query For Create Tables.
        if( !$this->runSql( $this->MakeDbsOrderTable() ) )
            return FALSE;
    }
    public function MakeDbsOrderTable()
    {
        return "CREATE TABLE `" . _DB_PREFIX_ . "dbs_order` (
                    `id_dbs_order` INT(10) NOT NULL AUTO_INCREMENT,
                    `id_order` INT(10) NULL,
					`id_status` INT(3) NOT NULL DEFAULT 0,
                    `cod_tracking_number` CHAR(30) NOT NULL,
					`post_tracking_number` CHAR(30) NOT NULL,
                    `is_change` INT(1) NOT NULL DEFAULT 0,
					`active` INT(1) NOT NULL DEFAULT 1,
					`date_change_cod` CHAR(100) NOT NULL,
					`date_change_state` DATETIME NOT NULL,
					`ensraf` VARCHAR(100) NOT NULL,
					`panel` varchar(200) NOT NULL DEFAULT 'frotel',					
                     primary key(`id_dbs_order`),
                     unique(id_order)) ENGINE = MYISAM COLLATE utf8_general_ci";
    }

    public function uninstallDb()
    {
        if(!$this->dropTableByName('dbs_order') )
            return false;
    }

    public function uninstallMeta()
	{
		$language = Language::getIdByIso('fa');
		$meta = Meta::getMetaByPage('module-psf_prestacart-order',$language);

		$metaObject	= new Meta( $meta['id_meta'] , $language );
		$metaObject->delete();

		return true;
    }

    public function uninstallConfig()
		{
	        $sql = "DELETE FROM `"._DB_PREFIX_."configuration` WHERE `name` LIKE 'PSCA_%'";
	        return Db::getInstance()->execute($sql);
	  }	

	public function makeFieldsOptions()
	{
		$domain = str_replace("www.","", $_SERVER['HTTP_HOST']);
		if($domain == 'localhost') $domain = 'localhost.com';
        $general_fields = array(
			'PSCA_STATUS_AJAX'=>array(
				'default' => '0',
				'html'=>false
			),
			'PSCA_STATUS_COD'=>array(
				'default' => '0',
				'html'=>false
			),
			'PSCA_TYPE_PANEL_COD'=>array(
				'default' => null,
				'html'=>false
			),
            'PSCA_WEIGHT_DEFAULT'=>array(
				'default' => 0,
				'html'=>false
			),
			'PSCA_EMAIL_GUEST'=>array(
				'default' => 'guest@'.$domain,
				'html'=>false
			),
            'PSCA_IS_MOBILE_EMAIL'=>array(
				'default' => 0,
				'html'=>false
			),
			'PSCA_BOX_MESSAGE_ORDER'=>array(
				'default' => '1',
				'html'=>false
			),
			'PSCA_DEBUG'=>array(
				'default' => '0',
				'html'=>false
			),
			'PSCA_TYPE_PAYMENT'=>array(
				'default' => 'Separate',
				'html'=>false
			),
			'PSCA_MODULE_PAYMENT'=>array(
				'default' => 'empty',
				'html'=>false
			),
			'PSCA_MODULE_PAYMENT_BANK'=>array(
				'default' => '',
				'html'=>false
			),
            'PSCA_MODULE_PAYMENT_GATE'=>array(
				'default' => '',
				'html'=>false
			),
            'PSCA_PAYMENT_LINK'=>array(
				'default' => '',
				'html'=>false
			),
			'PSCA_ENABLE_WEIGHT'=>array(
				'default' => '1',
				'html'=>false
			),
			'PSCA_FIELDS_ADDRESS'=>array(
				'default' => '{"id_gender":{"enable":"0","required":"0","enable_virtual":"0","required_virtual":"0","position":"1"},"firstname":{"enable":"1","required":"1","enable_virtual":"1","required_virtual":"1","position":"2"},"lastname":{"enable":"1","required":"1","enable_virtual":"1","required_virtual":"1","position":"3"},"name_merged":{"enable":"0","required":"0","enable_virtual":"0","required_virtual":"0","position":"4"},"email_create":{"enable":"1","required":"0","enable_virtual":"1","required_virtual":"1","position":"5"},"passwd":{"enable":"0","required":"0","enable_virtual":"0","required_virtual":"0","position":"6"},"address1":{"enable":"1","required":"1","enable_virtual":"","required_virtual":"","position":"7"},"postcode":{"enable":"1","required":"0","enable_virtual":"","required_virtual":"","position":"8"},"phone":{"enable":"0","required":"0","enable_virtual":"0","required_virtual":"0","position":"9"},"phone_mobile":{"enable":"1","required":"1","enable_virtual":"1","required_virtual":"1","position":"10"},"dni":{"enable":"0","required":"0","enable_virtual":"0","required_virtual":"0","position":"11"},"newsletter":{"enable":"0","required":"0","enable_virtual":"0","required_virtual":"0","position":"12"},"optin":{"enable":"0","required":"0","enable_virtual":"0","required_virtual":"0","position":"13"}}',
				'html'=>false
			),
			'PSCA_ORDER_STATE'=>array(
				'default' => Configuration::get('PS_OS_PREPARATION'),
				'html'=>false
			),
			'PSCA_SEFARESHI_COD_CARRIER'=>array(
				'default' => null,
				'html'=>false
			),
			'PSCA_PISHTAZ_COD_CARRIER'=>array(
				'default' => null,
				'html'=>false
			),
			'PSCA_ONLINE_CARRIER'=>array(
				'default' => null,
				'html'=>false
			),
            'PSCA_REGISTER_GUEST'=>array(
                'default' => '1',
                'html'=>false
            ),
            'PSCA_ID_STATE_DEFAULT'=>array(
                'default' => '0',
                'html'=>false
            ),
            'PSCA_ID_CITY_DEFAULT'=>array(
                'default' => '0',
                'html'=>false
            ),
            'PSCA_HIDE_COD'=>array(
                'default' => '0',
                'html'=>false
            ),
            'PSCA_CHECK_LANG'=>array(
                'default' => '0',
                'html'=>false
            ),
		);
        $help_fields = array(
			'PSCA_ALERT_LOGIN_FLAG'=>array(
				'default' => '1',
				'html'=>false
			),
			'PSCA_ALERT_LOGIN_TEXT'=>array(
				'default' => '<p>مشتری گرامی چنانچه پیش از این از ما کالایی خریداری نموده اید، کافیست آدرس ایمیل خود و رمز عبوری که برایتان ارسال شده است وارد نمایید. چانچه رمز عبور خود را فراموش کرده اید، <a href="/index.php?controller=password" target="_blank">اینجا کلیک نموده </a>تا رمز عبور جدیدی دریافت نمایید. همچنین شما میتوانید با کلیک بر عبارت "مشتری جدید هستید؟" سفارش خود را در کوتاه ترین زمان بدون ورود به حساب کاربری ثبت نمایید.</p>',
				'html'=>true
			),
			'PSCA_ALERT_GUEST_FLAG'=>array(
				'default' => '1',
				'html'=>false
			),
			'PSCA_ALERT_GUEST_TEXT'=>array(
				'default' => 'مشتری گرامی ثبت آدرس ایمیل الزامی نیست اما چنانچه آدرس ایمیل خود را وارد نمایید، در پایان سفارش بلافاصله یک حساب کاربری پرامکانات به شما اهدا خواهد شد تا با ورود به پنل کاربری از وضعیت سفارش خود مطلع شده و نیز با پشتیبان فروشگاه در ارتباط باشید. ایمیل مشتریان جزو اطلاعات محرمانه ایشان بوده و اطمینان می دهیم هیچ گونه استفاده تجاری یا تبلیغاتی از آن نخواهد شد.  ',
				'html'=>true
			),
			'PSCA_ALERT_CART_FLAG'=>array(
				'default' => '0',
				'html'=>false
			),
			'PSCA_ALERT_CART_TEXT'=>array(
				'default' => '',
				'html'=>true
			),
			'PSCA_ALERT_FLAG_TOP'=>array(
				'default' => '0',
				'html'=>false
			),
			'PSCA_ALERT_TEXT_TOP'=>array(
				'default' => '',
				'html'=>true
			),
			'PSCA_ALERT_CART_STEP3_TEXT'=>array(
				'default' => 'در صورت انتخاب گزینه ی پرداخت آنلاین، به درگاه بانکی ارجاع داده خواهید شد. لطفاً فرایند پرداخت را بطور کامل تکمیل فرمایید.',
				'html'=>true
			),
			'PSCA_ALERT_ORDER_CONFIRMATION_MESSAGE'=>array(
				'default' => '<p>از انتخاب شما سپاس گذاریم</p>سفارش شما با شماره رهگیری زیر با موفقیت ثبت گردید:<br><br><p class="alert alert cod">{rahgiriCod}</p><br>جهت رهگیری سفارش خود می توانید به<a href="{rahgiriUrl}" target="_blank"><b>"رهگیری سفارش"</b></a>مراجعه کنید.',
				'html'=>true
			),
		);
        $skin_fields = array(
			'PSCA_STYLE'=>array(
				'default' => 'default',
				'html'=>false
			),
			'PSCA_TAB_ADDRESS'=>array(
				'default' => '1',
				'html'=>false
			),
			'PSCA_ALERT_COLOR_BOX'=>array(
				'default' => '#fff2db',
				'html'=>false
			),
			'PSCA_ALERT_COLOR_BORDER'=>array(
				'default' => '#ffe4b4',
				'html'=>false
			),
			'PSCA_ALERT_COLOR_TEXT'=>array(
				'default' => '#e89f21',
				'html'=>false
			),
            'PSCA_CSS_CUSTOMIZE'=>array(
				'default' => '',
				'html'=>false
			),
			'PSCA_ALERT_VIRTUAL'=>array(
				'default' => 'شما یک محصول فایل در سبد خرید خود دارید. لطفاً پس از حذف محصول فایل از سبد خرید، ابتدا سفارش محصول فیزیکی را ثبت نموده سپس محصول فایل را بصورت مجزا خریداری نمایید ، سپاسگزاریم.',
				'html'=>true
			),
			'PSCA_TYPE_VIRTUAL'=>array(
				'default' => '0',
				'html'=>false
			),
		);
		$zones_fields = array(
			'PSCA_ZONE_CITY'=>array(
				'default' => '3',
				'html'=>false
			),
			'PSCA_ZONE_STATE'=>array(
				'default' => '3',
				'html'=>false
			),
			'PSCA_ZONE_ADJACENT'=>array(
				'default' => '3',
				'html'=>false
			),
			'PSCA_ZONE_UNADJACENT'=>array(
				'default' => '3',
				'html'=>false
			)
		);
		$fields = array_merge($general_fields,$help_fields,$skin_fields,$zones_fields);
		return $fields;
	}

	public function makeCarrier( $carrier = 'online' )
	{
		$lang = Language::getIsoById(Configuration::get('PS_LANG_DEFAULT'));
		$carriers = array(
			'sefareshi' => array(
				'name' => $this->l('PrestaCart Sefareshi Cod','prestacartparent'),
				'id_tax_rules_group' => 0,
				'active' => true,
				'deleted' => 0,
				'shipping_handling' => true,
				'range_behavior' => 0,
				'delay' => array(
					'en'  => $this->l('Shipping in Sefareshi method and Cod payment','prestacartparent'),
					$lang => $this->l('Shipping in Sefareshi method and Cod payment','prestacartparent')
				),
				'id_zone' => 3,
				'is_module' => true,
				'shipping_external' => true,
				'external_module_name' => $this->name,
				'need_range' => true
			),
			'pishtaz' => array(
				'name' => $this->l('PrestaCart Pishtaz Cod','prestacartparent'),
				'id_tax_rules_group' => 0,
				'active' => true,
				'deleted' => 0,
				'shipping_handling' => true,
				'range_behavior' => 0,
				'delay' => array(
					'en'  => $this->l('Shipping in Pishtaz method and Cod payment','prestacartparent'),
					$lang => $this->l('Shipping in Pishtaz method and Cod payment','prestacartparent'),
				),
				'id_zone' => 3,
				'is_module' => true,
				'shipping_external' => true,
				'external_module_name' => $this->name,
				'need_range' => true
			),
			'online' => array(
				'name' => $this->l('Online payment','prestacartparent'),
				'id_tax_rules_group' => 0,
				'active' => true,
				'deleted' => 0,
				'shipping_handling' => false,
				'range_behavior' => 0,
				'delay' => array(
					'en' => $this->l('Shipping in Pishtaz method','prestacartparent'),
					$lang => $this->l('Shipping in Pishtaz method','prestacartparent'),
					#'fa' => 'پرداخت آنلاین و ارسال با پست پیشتاز'
				),
				'id_zone' => 3,
				'is_module' => true,
				'shipping_external' => false,
				'external_module_name' => $this->name,
				'need_range' => true,
				'is_free'=>false
			)
		);

		return $carriers[$carrier];
	}

	/**
     * custom Html Tabs
     */
	public function customHtmlTabs()
	{
		$cod_config 	= (Configuration::get('PSCA_STATUS_COD') == '1')? true :  false ;
		$debug_config 	= (Configuration::get('PSCA_DEBUG') == '1')? true :  false ;

		return array(
			// Config Tabs
			'module_config' => array(
				'title' => 'پیکربندی',
				'position' => 'config',
				'active' => TRUE,
				'css_icon' => 'icon-gears',
                'controller' => $this->moduleTabClassName,
			),
			'cod_config' => array(
				'title' => 'اطلاعات پنل پستی',
				'position' => 'config',
				'active' => $cod_config,
				'css_icon' => 'icon-list-ul',
                'controller' => $this->moduleTabClassName,
			),
			'carriersPayment_config' => array(
				'title' => 'حامل ها و پرداخت',
				'position' => 'config',
				'active' => TRUE,
				'css_icon' => 'icon-AdminParentShipping',
                'controller' => $this->moduleTabClassName,
			),
			'themes_config' => array(
				'title' => 'تنظیمات قالب سبدخرید',
				'position' => 'config',
				'active' => TRUE,
				'css_icon' => 'icon-html5',
                'controller' => $this->moduleTabClassName,
			),
			'helps_config' => array(
				'title' => 'راهنمای مشتری در سبد خرید',
				'position' => 'config',
				'active' => TRUE,
				'css_icon' => 'icon-folder-close',
                'controller' => $this->moduleTabClassName,
			),
			'fields_config' => array(
				'title' => 'شخصی سازی فیلد های آدرس و ثبت نام',
				'position' => 'config',
				'active' => TRUE,
				'css_icon' => 'icon-AdminParentCustomer',
                'controller' => $this->moduleTabClassName,
			),
			'virtual_config' => array(
				'title' => 'سبد خرید مجازی',
				'position' => 'config',
				'active' => TRUE,
				'css_icon' => 'icon-gears',
                'controller' => $this->moduleTabClassName,
			),

            'debug_config' => array(
                'title' => 'تنظیمات پیشرفته',
                'position' => 'config',
                'active' => $debug_config,
                'css_icon' => 'icon-gears',
                'controller' => $this->moduleTabClassName,
            ),
		);
	}

	public function getJsonOptions($field = 'PSCA_FIELDS_ADDRESS')
	{
		$value = Configuration::get($field);
		$values_fields_address = Tools::jsonDecode($value);
		$fields_address = $this->makeFieldsAddress();
		$position = $result = array();

		foreach($fields_address as $field => $field_info){
			$fields_address[$field]['data']['enable'] = $values_fields_address->{$field}->enable;
			$fields_address[$field]['data']['required'] = $values_fields_address->{$field}->required;
			$fields_address[$field]['data']['enable_virtual'] = $values_fields_address->{$field}->enable_virtual;
			$fields_address[$field]['data']['required_virtual'] = $values_fields_address->{$field}->required_virtual;
			$fields_address[$field]['data']['position'] = $position[$field] = $values_fields_address->{$field}->position;
		}
		asort($position);
		foreach($position as $field => $p ){
			$result[$field] = $fields_address[$field];
		}
		return $result;
	}

	public function setFieldsAddress()
	{
		$fields_address = $this->makeFieldsAddress();
		$json = array();
		foreach($fields_address as $field => $field_info){

		    if ( $field == 'lastname' or $field == 'firstname' ) {
                $data = array(
                    'enable'            => ( Tools::getValue('name_merged_enable') ) ? 0 : Tools::getValue($field.'_enable') ,
                    'required'          => ( Tools::getValue('name_merged_enable') ) ? 0 : Tools::getValue($field.'_required'),
                    'enable_virtual'    => ( Tools::getValue('name_merged_enable_virtual') ) ? 0 : Tools::getValue($field.'_enable_virtual'),
                    'required_virtual'  => ( Tools::getValue('name_merged_enable_virtual') ) ? 0 : Tools::getValue($field.'_required_virtual'),
                    'position'          => Tools::getValue($field.'_position'),
                );
            } else {
                $data = array(
                    'enable'=>Tools::getValue($field.'_enable'),
                    'required'=>Tools::getValue($field.'_required'),
                    'enable_virtual'=>Tools::getValue($field.'_enable_virtual'),
                    'required_virtual'=>Tools::getValue($field.'_required_virtual'),
                    'position'=>Tools::getValue($field.'_position'),
                );
            }


			$json[$field] = $data;
		}
		return Configuration::updateValue('PSCA_FIELDS_ADDRESS',Tools::jsonEncode($json));
	}
	/**
	 * Get States
	 */
    public static function getStates()
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS("
		SELECT `id_state`, `name`, `iso_code`
		FROM `"._DB_PREFIX_."state`
		WHERE `iso_code` IN ('IR1','IR2','IR3','IR4')");
    }

    protected function getStateAddress()
	{
		$newCookie = new Cookie('PSCart');

		$id_city  = $newCookie->__get('PSCart_City');
        $id_state = $newCookie->__get('PSCart_State');

		$origin_id_state = trim(Configuration::get('PSCA_ID_STATE'));
		$origin_id_city  = trim(Configuration::get('PSCA_ID_CITY'));

		if( $id_city == $origin_id_city  )
			return (int)Configuration::get('PSCA_ZONE_CITY');
		elseif( $id_state == $origin_id_state  )
			return (int)Configuration::get('PSCA_ZONE_STATE');
		elseif( $this->cod->isAjacent($origin_id_state,$id_state) )
			return (int)Configuration::get('PSCA_ZONE_ADJACENT');
		else
			return (int)Configuration::get('PSCA_ZONE_UNADJACENT');
	}

	public static function IsAjaxRequest()
	{
		if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' )
			return true;
		return false;
	}

	public static function IsPostRequest()
	{
		if ( $_SERVER['REQUEST_METHOD'] === 'POST' )
			return true;
		return false;
	}

	public function convertNumber( $srting )
	{
		$en_num = array("0","1","2","3","4","5","6","7","8","9");
		$fa_num = array("۰","۱","۲","۳","۴","۵","۶","۷","۸","۹");
		$ar_num = array("٠","١","۲","٣","٤","٥","٦","٧","۸","٩");
		$ap_num = array("٠","١","٢","٣","٤","٥","٦","٧","٨","٩");

		$srting = str_replace($fa_num, $en_num, $srting);
		$srting = str_replace($ar_num, $en_num, $srting);
		$srting = str_replace($ap_num, $en_num, $srting);

		return $srting;
	}

    public function updateContext()
    {
        $customer = new Customer($this->context->cart->id_customer);
		$this->context->customer = $customer;

        $this->context->cookie->id_customer = (int)$customer->id;
        $this->context->cookie->customer_lastname = $customer->lastname;
        $this->context->cookie->customer_firstname = $customer->firstname;
        $this->context->cookie->passwd = $customer->passwd;
        $this->context->cookie->logged = 1;

        $customer->logged = 1;
        $this->context->cookie->email = $customer->email;
        $this->context->cookie->is_guest = $customer->isGuest();
        // Update cart address
        $this->context->cart->secure_key = $customer->secure_key;
    }

	public function actionDebug()
	{
		$debug = Configuration::get('PSCA_DEBUG');
		if ($debug === true) {
			@ini_set('display_errors', 'on');
			@error_reporting(E_ALL | E_STRICT);
		}
	}

	public function addCarrier($config)
	{
        $carrier = new Carrier();
        $carrier->name = $config['name'];
        $carrier->id_tax_rules_group = $config['id_tax_rules_group'];
        $carrier->id_zone = $config['id_zone'];
        $carrier->active = $config['active'];
        $carrier->deleted = $config['deleted'];
        $carrier->delay = $config['delay'];
        $carrier->shipping_handling = $config['shipping_handling'];
        $carrier->range_behavior = $config['range_behavior'];
        $carrier->is_module = $config['is_module'];
        $carrier->shipping_external = $config['shipping_external'];
        $carrier->external_module_name = $config['external_module_name'];
        $carrier->need_range = $config['need_range'];

        $languages = Language::getLanguages(true);
        foreach ($languages as $language)
        {
            if ($language['iso_code'] == 'en')
                $carrier->delay[(int)$language['id_lang']] = $config['delay'][$language['iso_code']];
            if ($language['iso_code'] == Language::getIsoById(Configuration::get('PS_LANG_DEFAULT')))
                $carrier->delay[(int)$language['id_lang']] = $config['delay'][$language['iso_code']];
        }

        if ($carrier->add())
        {
            $groups = Group::getGroups(true);
            foreach ($groups as $group)
                Db::getInstance()->autoExecute(_DB_PREFIX_ . 'carrier_group', array('id_carrier' =>
                        (int)($carrier->id), 'id_group' => (int)($group['id_group'])), 'INSERT');
            // price range
            $rangePrice = new RangePrice();
            $rangePrice->id_carrier = $carrier->id;
            $rangePrice->delimiter1 = '0';
            $rangePrice->delimiter2 = '10000000';
            $rangePrice->add();
            // weight range
            $rangeWeight = new RangeWeight();
            $rangeWeight->id_carrier = $carrier->id;
            $rangeWeight->delimiter1 = '0';
            $rangeWeight->delimiter2 = '100000';
            $rangeWeight->add();

            $zones = Zone::getZones(true);
            foreach ($zones as $zone)
            {
                Db::getInstance()->autoExecute(_DB_PREFIX_ . 'carrier_zone', array('id_carrier' =>
                        (int)($carrier->id), 'id_zone' => (int)($zone['id_zone'])), 'INSERT');
                Db::getInstance()->autoExecuteWithNullValues(_DB_PREFIX_ . 'delivery', array(
                    'id_carrier' => (int)($carrier->id),
                    'id_range_price' => (int)($rangePrice->id),
                    'id_range_weight' => null,
                    'id_zone' => (int)($zone['id_zone']),
                    'price' => '0'), 'INSERT');
                Db::getInstance()->autoExecuteWithNullValues(_DB_PREFIX_ . 'delivery', array(
                    'id_carrier' => (int)($carrier->id),
                    'id_range_price' => null,
                    'id_range_weight' => (int)($rangeWeight->id),
                    'id_zone' => (int)($zone['id_zone']),
                    'price' => '0'), 'INSERT');
            }
            return (int)($carrier->id);
        }
        return false;
    }

    /**
     * Check Guest User PrestaCart To Actions guest-tracking
     *
     */
    public function _checkGuestTracking()
    {
        $hideGuestToCustomer = false;
        $guestEmailUser = trim(Configuration::get('PSCA_EMAIL_GUEST'));

        if( Tools::isSubmit('submitGuestTracking')  )
        {
            $email = Tools::getValue('email');
            if( $guestEmailUser == $email ){
                $hideGuestToCustomer = true;
            }
        }
        elseif( Tools::isSubmit('submitTransformGuestToCustomer')  )
        {
            $email = Tools::getValue('email');
            if( $guestEmailUser == $email ){
                Tools::redirect('index.php?controller=guest-tracking','301 Moved Permanently HTTP/1.1');
            }
        }
        $this->smarty->assign(array('hideGuestToCustomer' => $hideGuestToCustomer));
    }

    /**
     * Set price external carriers Cod
     * @param $params
     * @param $shipping_cost
     * @return float|int
     */
    public function getOrderShippingCost($params, $shipping_cost){
        $dbsCookie = new Cookie('PSCart');
        $shipping_COD = 0 ;
        if ( $this->id_carrier )
        {
            if (Configuration::get('PSCA_PISHTAZ_COD_CARRIER') == $this->id_carrier)
                $shipping_COD = $dbsCookie->__get('Pishtaz_Carrier');

            elseif (Configuration::get('PSCA_SEFARESHI_COD_CARRIER') == $this->id_carrier)
                $shipping_COD = $dbsCookie->__get('Sefareshi_Carrier');

            $dbsCookie->__set('PSCartCarrier', $shipping_COD);
        }

        if( !$shipping_COD and $dbsCookie->__isset('PSCartCarrier') )
            $shipping_COD = $dbsCookie->__get('PSCartCarrier');


        $rial = new Currency( Currency::getIdByIsoCode('IRR') );
        $current_currency = new Currency($this->context->cookie->id_currency);

        if ( $current_currency->id == $rial->id )
            return $shipping_cost + $shipping_COD;
        else
            return $shipping_cost + Tools::convertPriceFull($shipping_COD, $rial, $current_currency);

    }
    public function getOrderShippingCostExternal($params){
        return $this->getOrderShippingCost($params,0);
    }

    public function getError( $numberError = null, $debugCheck = true )
    {
        if($debugCheck)
        {
            $debug = Configuration::get('PSCA_DEBUG');
            if ( !$debug )
                return $this->l('مشکلی در ثبت درخواست شما به وجود آماده است ، لطفا با مدیریت فروشگاه تماس بگیرید.','prestacartparent').'شماره خطا #'.$numberError;
        }

        switch($numberError){
            case '50' :
                $error = $this->l('وزن محصولات نامعتبر است.','prestacartparent');
                break;

            case '100' :
                $error = $this->l('مشکل در ارتباط با وبسرویس پنل پستی','prestacartparent');
                break;

            case '102' :
                $error = $this->l('اطلاعات ارسالی به وبسرویس پنل پستی جهت دریافت هزینه ارسال مشکل دارد','prestacartparent');
                break;

            case '103' :
                $error = $this->l('اطلاعات ارسالی به وبسرویس پستی جهت ثبت سفارش مشکل دارد','prestacartparent');
                break;

            case '200' :
                $error = $this->l('اجازه نامه معتبری برای استفاده از این ماژول بر روی فروشگاه شما شناسایی نشد.','prestacartparent');
                break;

            default :
                $error = false;
        }
        return $error;
    }

    /**
     * management cod orders on page admin
     * @param $params
     * @return string|void
     */
    public function displayAdminOrder($params)
    {
        $id_order = 0;
        if (array_key_exists('id_order', $params))
            $id_order = (int)$params['id_order'];

        if (Tools::isSubmit('submitRigDBS'))
        {
            $result = $this->registerOrderCod($id_order);
            $this->context->smarty->assign(array('result'=>$result));
        }
        elseif (Tools::isSubmit('submitChangeOrderDBS'))
        {
            $result = $this->getOrderState($id_order);
            $this->context->smarty->assign(array('state_order'=>$result));
        }
        elseif (Tools::isSubmit('submitChangeAutoDBS'))
        {
            $orderCod = new PSFOrderCore();
            $dbsOrderCod = $orderCod->getByIdOrder($id_order);
            $active = ($dbsOrderCod['0']['active']=="1")?"0":"1";
            $orderCod->update($id_order,array('active'=>$active));
        }

        $order = new Order($id_order);
        $history = $order->getHistory($this->context->language->id);
        $statusOrder = $history[0]['id_order_state'];

        $panelCod   = Configuration::get('PSCA_TYPE_PANEL_COD');
        if( $panelCod == 'Logito' or $panelCod == 'Raga'  )
            $statusRigester = Configuration::get('PSCA_ORDER_STATE_200');
        else
            $statusRigester = Configuration::get('PSCA_ORDER_STATE_100');

        $panelCod = false;
        if( $statusOrder == $statusRigester )
        {
            $items = $this->cod->getItems();
            $optionsCod = array();
            foreach($items as $key => $item)
            {
                if( $item["type"] == "selectCity" or $item["type"] == "selectState")
                    $optionsCod[$key] = array(
                        'value'=>Configuration::get('PSCA_'.strtoupper($key)),
                        'type'=>$item['type'],
                        'label'=>$item['label'],
                        'html'=>$item['html'],
                        'htmlEdit'=>$item['htmlEdit']
                    );
                else
                    $optionsCod[$key] = array(
                        'value'=>Configuration::get('PSCA_'.strtoupper($key)),
                        'type'=>$item['type'],
                        'label'=>$item['label'],
                    );
            }
            $this->context->smarty->assign(
                array(
                    'panel' => $optionsCod
                )
            );
            $panelCod = true;
        }
        else{
            $orderCod = new PSFOrderCore();
            $dbsOrderCod = $orderCod->getByIdOrder($id_order);
            if( count($dbsOrderCod) )
            {
                $this->context->smarty->assign(
                    array(
                        'dbsOrderCod'=>$dbsOrderCod['0']
                    )
                );
                $panelCod = true;
            }
        }

        if( $panelCod )
        {
            $version5 = ( version_compare(_PS_VERSION_, '1.6.0.0') < 0 ) ? true : false ;
            $this->context->smarty->assign(
                array(
                    'orderId' => $id_order,
                    'statusOrder'=>$statusOrder,
                    'statusRigester'=>$statusRigester,
                    'titleCod'=>$this->options['titleCod'],
                    'dir' => _MODULE_DIR_.$this->name.'/views/',
                    'version5'=>$version5
                )
            );
            return true;
        }
        return false;
    }

    private function registerOrderCod($id_order)
    {
        $order = new Order($id_order);
        $objAddress = new Address((int)$order->id_address_delivery);
        $objCustomer = new Customer((int)$order->id_customer);

        $id_carrier_pishtaz =  Configuration::get('PSCA_PISHTAZ_COD_CARRIER');
        $sendType = ( $id_carrier_pishtaz == $order->id_carrier ) ? 'pishtaz' : 'sefareshi';
        $products = $order->getProducts();
        $options = array(
            'fname'     => $objAddress->firstname,
            'lname'     => $objAddress->lastname,
            'mobile'    => $objAddress->phone_mobile,
            'phone'     => $objAddress->phone,
            'address'   => $objAddress->address1 . $objAddress->address2,
            'email'     => $objCustomer->email,
            'id_gender' => $objCustomer->id_gender,
            'id_state'  => Tools::getValue('PSCA_ID_STATE'),
            'id_city'   => Tools::getValue('PSCA_ID_CITY'),
            'sendType'  => $sendType,
            'postcode'  => $objAddress->postcode,
            'description'=> '',/* #dbs# check version 3.1 // get message order */
            'cartAdmin'=>array(
                'order'         => $order,
                'products'      => $products,
                'id_currency'   => $order->id_currency
            ),
            'objAddress'=> $objAddress
        );

        $cod_config = Configuration::get('PSCA_STATUS_COD');
        if( $cod_config )
            $result = $this->cod->registerOrder( $options );
        else{
            $result = array(
                'hasError' => true,
                'errors' => array('پنل پستی غیرفعال می باشد.')
            );
        }

        if( !$result['hasError']  )
        {
            // save number tracking
            $id_order_carrier = $order->getIdOrderCarrier();
            $order_carrier = new OrderCarrier($id_order_carrier);
            $order_carrier->tracking_number = $result['rahgiriCod'];
            $order_carrier->update();
            $order->shipping_number = $result['rahgiriCod'];

            $codType = Configuration::get('PSCA_TYPE_PANEL_COD');

            $dbsOrder = new PSFOrderCore();
            $dbsOrder->add($id_order,$result['rahgiriCod'],$codType);

            // change order state
            $orderStateId = (int) Configuration::get( 'PSCA_ORDER_STATE_0' );
            // only frotel
            if( !$orderStateId )
                $orderStateId = (int) Configuration::get( 'PSCA_ORDER_STATE_1' );

            $orderState = new OrderState($orderStateId);
            if ( !Validate::isLoadedObject( $orderState ) )
            {
                $result['hasError'] = false;
                $result['errors'][] = 'تغییر وضعیت سفارش با مشکل مواجه شد ، لطفا وضعیت سفارش را بصورت دستی بروز کنید.';
            }
            else{
                $currentOrderState = $order->getCurrentOrderState();
                if ( $currentOrderState->id != $orderState->id )
                {
                    $history = new OrderHistory();
                    $history->id_order = $order->id;
                    $history->id_employee = (int)$this->context->employee->id;

                    $use_existings_payment = false;
                    if ( !$order->hasInvoice() )$use_existings_payment = true;
                    $history->changeIdOrderState( (int)$orderState->id, $order, $use_existings_payment);
                    $history->add();

                    Tools::redirectAdmin('index.php?controller=AdminOrders&id_order='.(int)$order->id.'&vieworder&token='.Tools::getAdminTokenLite('AdminOrders'));
                }
            }
        }
        return $result;
    }
    private function getOrderState( $id_order )
    {
        $order = New Order( (int)$id_order );
        $dbsOrder = new PSFOrderCore();
        $tracking_number = $dbsOrder->getTrackingByIdOrder($id_order);
        if(!$tracking_number){
            $id_order_carrier = $order->getIdOrderCarrier();
            $order_carrier = new OrderCarrier($id_order_carrier);
            $tracking_number = $order_carrier->tracking_number;
        }

        $result = $this->cod->GetStatus($tracking_number);

        if($result['result'])
        {
            // بررسی تغییر وضعیت
            $orderStateIdNew = (int) Configuration::get( 'PSCA_ORDER_STATE_'.$result['state'] );
            if($orderStateIdNew and $orderStateIdNew != $order->current_state )
            {
                $orderStatus = $this->cod->getOrderStates();
                $orderStatusNew = $orderStatus[$result['state']];
                if($orderStatusNew){
                    $result['message'] = 'وضعیت سفارش به <b>'. $orderStatusNew['title'] .'</b> تغییر کرد.';

                    $current_order_state = $order->getCurrentOrderState();
                    if ($current_order_state->id != $orderStateIdNew){
                        $history = new OrderHistory();
                        $history->id_order = $order->id;
                        $history->id_employee = (int)$this->context->employee->id;

                        $use_existings_payment = false;
                        if (!$order->hasInvoice())$use_existings_payment = true;
                        $history->changeIdOrderState((int)$orderStateIdNew, $order, $use_existings_payment);
                        #$history->add();
                        /*{#test#}*/
                        if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING') && $order->shipping_number)
                            //$templateVars = array('{followup}' => str_replace('@', $order->shipping_number, $carrier->url));
                            $templateVars = array('{followup}' => str_replace('@', $order->shipping_number, ''));

                        if ($history->addWithemail(true, $templateVars)){
                            if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'))
                                foreach ($order->getProducts() as $product)
                                    if (StockAvailable::dependsOnStock($product['product_id']))
                                        StockAvailable::synchronize($product['product_id'], (int)$product['id_shop']);
                        }else
                            $this->errors[] = sprintf(Tools::displayError('Cannot change status for order #%d.'), $id_order);
                        /*{#test#}*/
                        $array = array(
                            'id_status'=>$result['state'],
                            'date_change_state'=>date('Y-m-d H:i:s'),
                        );
                        if($result['cod_post']) $array['post_tracking_number']=$result['cod_post'];
                        if($result['date']) $array['date_change_cod']=$result['date'];
                        if($result['ensraf']) $array['ensraf']=$result['ensraf'];

                        $dbsOrder->update(
                            $id_order,
                            $array
                        );

                        Tools::redirectAdmin('index.php?controller=AdminOrders&id_order='.(int)$order->id.'&vieworder&token='.Tools::getAdminTokenLite('AdminOrders'));
                    }

                }else $result['message'] = 'وضعیت این سفارش تغییر کرده است.';
            }
            else{
                $dbsOrder->update(
                    $id_order,
                    array(
                        'date_change_state'=>date('Y-m-d H:i:s'),
                        'id_status'=>$result['state'],
                    )
                );
                $result['message'] = 'وضعیت این سفارش تغییر نکرده است.';
            }
        }
        return $result;
    }

    /*
    |--------------------------------------------------------------------------
    | Hook Methods
    |--------------------------------------------------------------------------
    */
    public function hookDisplayBackOfficeHeader()
    {
        if (method_exists($this->context->controller, 'addJquery'))
            $this->context->controller->addJquery();

        $this->context->controller->addJS($this->_path.'views/js/admin/form.js', 'all');
        $this->context->controller->addCss($this->_path.'/views/css/admin/form.css');
        parent::hookDisplayBackOfficeHeader();
    }

    public function hookDisplayHeader($params)
    {
        $this->page_name = Dispatcher::getInstance()->getController();
        if ( in_array($this->page_name,array('validation','order')) ){
            $this->context->controller->addJS($this->_path.'/views/js/pscart.js');
            $this->context->controller->addCss($this->_path.'/views/css/pscart.css');
            $style = Configuration::get('PSCA_STYLE');

            if ( $style != 'default' )
                $this->context->controller->addCss($this->_path."/views/css/pscart_$style.css");
        }

        // delete box guest to customer for guest user psf_prestacart
        if ($this->page_name == 'guesttracking' ){
            $this->_checkGuestTracking();
            return $this->display('psf_prestacart', 'header.tpl');
        }
    }

    public function hookPayment($params)
    {
        if (!$this->active)
            return ;

        $free_order = false;
        if ($this->context->cart->getOrderTotal() <= 0) {
            $free_order = true;
        }

        $this->smarty->assign(
            array(
                'free_order'  => $free_order
            )
        );

        return $this->display('psf_prestacart', 'payment.tpl');
    }
    public function hookPaymentReturn($params){
        return;
    }
    /**
     * Redirect page order to page pscart
     */
    public function hookActionDispatcher($params)
    {
        $check_lang = (bool) (Configuration::get('PSCA_CHECK_LANG') && $this->context->language->iso_code != 'fa');

        $module = Tools::getValue('module');
        if ($check_lang && $module == $this->name ) {
            $controller = Tools::getValue('controller');
            if($controller == 'order' && $this->context->language->iso_code != 'fa')
            {
                Tools::redirect($this->context->link->getPageLink('order') ,'HTTP/1.1 301 Moved Permanently');
            }
        }

        $controller = $this->context->controller->php_self;
        if($controller == 'order' && !$check_lang)
        {
            $use_routes = (bool)Configuration::get('PS_REWRITING_SETTINGS');
            if ($use_routes)
            {
                $language = Language::getIdByIso('fa');
                $meta = Meta::getMetaByPage('module-'.$this->name.'-order',$language);
                $domain = Tools::getHttpHost(true).__PS_BASE_URI__;

                // callback as modules payment default and revert cart
                $query = '';
                if( count($_GET) ){
                    $query = '?';
                    foreach ( $_GET as $index => $value )
                    {
                        if ( $index == 'controller' ) continue;

                        if( $index == 'step' and $value == '3' )
                            $query .= 'action=step3&';
                        else
                            $query .= $index.'='.$value.'&';
                    }
                    $query = trim($query,'&');
                }

                $url = $domain.$meta['url_rewrite'].$query ;
            }
            else{
                $domain = Tools::getHttpHost(true).__PS_BASE_URI__;
                $url = $domain.'index.php?fc=module&module='.$this->name.'&controller=order';
            }

            Tools::redirect($url ,'HTTP/1.1 301 Moved Permanently');
        }
        $this->actionDebug();
        return;
    }

    /**
     * Update Carriers
     */
    public function hookUpdateCarrier($params)
    {
        // update carriers online
        $selectedIds = explode(',',Configuration::get('PSCA_ONLINE_CARRIER'));
        if ( in_array($params['id_carrier'],$selectedIds))
        {
            $selectedIds[] = $params['carrier']->id;
            $selected = implode(',',$selectedIds);
            Configuration::updateValue('PSCA_ONLINE_CARRIER', $selected);
        }

        // update carriers cod
        if ( $params['id_carrier'] == Configuration::get('PSCA_PISHTAZ_COD_CARRIER') )
            Configuration::updateValue('PSCA_PISHTAZ_COD_CARRIER', $params['carrier']->id );

        if ( $params['id_carrier'] == Configuration::get('PSCA_SEFARESHI_COD_CARRIER') )
            Configuration::updateValue('PSCA_SEFARESHI_COD_CARRIER', $params['carrier']->id );

        // update carriers Restrictions
        $carrierRestrictions = Tools::jsonDecode(Configuration::get('PSCA_CARRIER_RESTRICTIONS'),true);
        foreach ($carrierRestrictions as $key => $carrierRestriction){
            if ( in_array($params['id_carrier'],$carrierRestriction))
                $carrierRestrictions[$key][] = $params['carrier']->id;
        }
        Configuration::updateValue('PSCA_CARRIER_RESTRICTIONS', Tools::jsonEncode($carrierRestrictions));
    }

    /**
     * management cod orders on page admin
     * @param $params
     * @return string|void
     */
    public function hookDisplayAdminOrder($params)
    {
        if( $this->displayAdminOrder($params) ){
            return $this->display('psf_prestacart', 'displayAdminOrder.tpl');
        }
        return;
    }
    /**
     * AdminModules Configure !

    public function getContent()
    {
    return $this->showBootstrapAlert('<a href="'. $this->context->link->getAdminLink( $this->getModuleTabClassName() ) .'">برای پیکربندی و انجام تنظیمات به منوی <b> Presta Cart </b>مراجعه کنید و یا برروی اینجا کلیک کنید.</a>');
    }*/


    public function getMobileCustomer($id_customer, $onlyValid = true)
    {
        if (empty($id_customer))
            return false;

        if (Module::isEnabled('psy_smartlogin')) {
            $smartlogin = Module::getInstanceByName('psy_smartlogin');

            $mobile = $smartlogin->getMobileCustomer($id_customer, $onlyValid);
            if (!empty($mobile))
                return $mobile;
        }

        return false;
    }
}
