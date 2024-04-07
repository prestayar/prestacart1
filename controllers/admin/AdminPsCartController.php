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

require_once( dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR .'classes/PSCartParentAdminController.php');
use DBSCore\V11Cart\DBSHelperForm;
use DBSCore\V11Cart\DBSGlobal;

class AdminPsCartController extends PSCartParentAdminController{

	public $carriers = null;
	public $carriers_external = null;
	public $soption = null;

    /**
     * AdminPsCartController Constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->soption = array(
            array(
                'id' => 'active_on',
                'value' => 1,
                'label' => $this->l('Enabled')
            ),
            array(
                'id' => 'active_off',
                'value' => 0,
                'label' => $this->l('Disabled')
            )
        );
    }

    public function getTabContent()
	{
        $parent = parent::getTabContent();
		return $this->checkConfig() . $parent ;
	}
	
	/*
	|--------------------------------------------------------------------------
	| Controller Action Tabs
	|--------------------------------------------------------------------------
	*/
	/**
	 * module Config Action Tabs.
	 */	
	public function actionTab_module_config()
	{

		$output = '';
		$fields = array(
			'PSCA_STATUS_AJAX'=>'',
			'PSCA_STATUS_COD'=>'',
			'PSCA_EMAIL_GUEST'=>'',
			'PSCA_BOX_MESSAGE_ORDER'=>'',
			'PSCA_DEBUG'=>'',
			'PSCA_REGISTER_GUEST'=>'',
			'PSCA_ID_STATE_DEFAULT'=>'',
			'PSCA_ID_CITY_DEFAULT'=>'',
			'PSCA_CHECK_LANG'=>'',
		);
		
		if (Tools::isSubmit('submit'.$this->module->name) )
		{
			$status_cod_first = Configuration::get('PSCA_STATUS_COD');
			foreach($fields as $key=>$field)
				Configuration::updateValue($key, Tools::getValue($key) );

			$output .= $this->module->displayConfirmation($this->l('تنظیمات با موفقیت به روز شد !'));
			
			$status_cod = Configuration::get('PSCA_STATUS_COD');
			if( $status_cod )
			{
				$pishtaz_cod   = Configuration::get('PSCA_PISHTAZ_COD_CARRIER');
				if( !$pishtaz_cod )
				{
					$pishtazCarrier = $this->module->makeCarrier('pishtaz');
					$id_pishtaz_cod = $this->module->addCarrier( $pishtazCarrier );
					Configuration::updateValue('PSCA_PISHTAZ_COD_CARRIER', $id_pishtaz_cod);
				}				

				$sefareshi_cod   = Configuration::get('PSCA_SEFARESHI_COD_CARRIER');
				if( !$sefareshi_cod )
				{
					$sefareshiCarrier = $this->module->makeCarrier('sefareshi');
					$id_sefareshi_cod = $this->module->addCarrier( $sefareshiCarrier );
					Configuration::updateValue('PSCA_SEFARESHI_COD_CARRIER', $id_sefareshi_cod);
				}
			}
			
			if( $status_cod_first != $status_cod   ) {
				Configuration::updateValue('PSCA_ID_STATE',null);
				Configuration::updateValue('PSCA_ID_CITY',null);
			}
		}
        
		// Init Fields form array
		$fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Module settings'),
			),
			'input' => array(
				array(
					'type' => 'switch',
					'label' => $this->l('Ajax Status'),
					'name' => 'PSCA_STATUS_AJAX',
					'default' => 1,
					'values' => $this->soption,
					'required' => true,
					'desc' => $this->l('اگر میخواهید سبد خرید بصورت ایجکس باشد فعال کنید'),
				),			
				array(
					'type' => 'switch',
					'label' => $this->l('پنل پستی'),
					'name' => 'PSCA_STATUS_COD',
					'default' => 1,
					'values' => $this->soption,
					'required' => true,
					'desc' => array(
                        $this->l('در صورتی که از پنل پستی استفاده نمیکنید ، این گزینه را غیرفعال کنید! همچنین حامل های مخصوص پرداخت در محل پنل پستی را حذف و یا غیرفعال کنید و در صورت ویرایش این حامل ها و استفاده به عنوان روش ارسال دیگری مانند پیک موتوری و ... با مشکل مواجه می شوید.'),
                        $this->l('در صورتی که بصورت موقت قصد استفاده از پنل پستی را ندارید بعد از غیرفعال کردن این گزینه ، حامل های مخصوص پرداخت در محل را غیرفعال کنید و از حذف و ویرایش آنها خودداری کنید.')
                    ),
                    'hint' => $this->l('این گزینه برای استفاده از پنل شرکت های واسطه است.'),
				),				
				array(
                    'type' => 'text',
                    'label' => $this->l('ایمیل حساب کاربری مهمان'),
                    'name' => 'PSCA_EMAIL_GUEST',
					'desc' => $this->l('ایمیل حساب کاربری که سفارشات مشتریانی که ایمیل وارد نکنند در آن ثبت خواهد شد.'),
				),
				array(
					'type' => 'switch',
					'label' => $this->l('پیام خریدار'),
					'name' => 'PSCA_BOX_MESSAGE_ORDER',
					'default' => 1,
					'values' => $this->soption,
					'required' => true,
					'desc' => $this->l('با فعال کردن این گزینه فیلدی جهت پیام مشتری در مرحله سوم افزوده میشود.'),
				),
				array(
					'type' => 'switch',
					'label' => $this->l('حالت خطایابی'),
					'name' => 'PSCA_DEBUG',
					'default' => 0,
					'values' => $this->soption,
					'required' => true,
					'desc' => $this->l('این گزینه بصورت پیش فرض غیرفعال باشد و تنها در زمان خطایابی فعال کنید.'),
				),
                array(
					'type' => 'switch',
					'label' => $this->l('حالت مهمان در عضویت اتوماتیک'),
					'name' => 'PSCA_REGISTER_GUEST',
					'default' => 1,
					'values' => $this->soption,
					'required' => true,
					'desc' =>  array(
                        $this->l('در صورت فعال بودن این ویژگی کاربرانی که از طریق ماژول عضو سایت میشوند بصورت مهمان خواهند بود وگرنه بصورت مشتری ثبت میگردند.'),
                        $this->l('حالت مهمان : کاربری که بصورت مهمان ثبت نام شود ، امکان ثبت سفارش بدون نیاز به لاگین به حساب کاربری را خواهد داشت ولی امکان لاگین به حساب کاربری برای کاربر مهمان در پرستاشاپ وجود ندارد.'),
                        $this->l('حالت مشتری : این کاربران می توانند به حساب کاربری خود وارد شوند ولی امکان ثبت سفارش بدون ورود به حساب کاربری را نخواهند داشت و حتما در زمان ثبت سفارش باید لاگین کنند.'),
                    )
				),
                array(
					'type' => 'switch',
					'label' => $this->l('استان پیش فرض'),
					'name' => 'PSCA_ID_STATE_DEFAULT',
					'default' => 1,
					'values' => $this->soption,
					'required' => true,
					'desc' =>  $this->l('استان مبدا بصورت پیش فرض در سبدخرید به حالت انتخاب شده قرار میگیرد.')

				),
                array(
					'type' => 'switch',
                    'label' => $this->l('شهر پیش فرض'),
					'name' => 'PSCA_ID_CITY_DEFAULT',
					'default' => 1,
					'values' => $this->soption,
					'required' => true,
                    'desc' =>  $this->l('شهر مبدا بصورت پیش فرض در سبدخرید به حالت انتخاب شده قرار میگیرد. برای این مورد باید تنظیم استان پیش فرض را هم فعال کنید.')
				),
                array(
                    'type' => 'switch',
                    'label' => $this->l('اجرا سبدخرید فقط در زبان فارسی'),
                    'name' => 'PSCA_CHECK_LANG',
                    'default' => 1,
                    'values' => $this->soption,
                    'required' => true,
                ),
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'btn btn-default pull-right'
			)
		);

		// Generate Module Settings Form With DBSCore\DBSHelperForm.
		$helper = new DBSHelperForm();
		$helper->setInit($this->module)->setFieldsValue($fields);
		$helper->token = Tools::getAdminTokenLite(Tools::getValue('controller'));
		$helper->currentIndex = AdminController::$currentIndex.'&'. $this->queryStringKeyForTabs .'='.Tools::getValue($this->queryStringKeyForTabs);			
		return	$output . $helper->generateForm( $fields_form );
	}

	public function actionTab_cod_config()
	{
		$cod_config = Configuration::get('PSCA_STATUS_COD');
		if(!$cod_config)
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminPsCart').'&dbs_tab=module_config');

		$output = '';
		$fields = array(
			'PSCA_TYPE_PANEL_COD'=>'',
			'PSCA_WEIGHT_DEFAULT'=>'',
            //'PSCA_HIDE_COD'=>'',
        );
		// setting panel cod
		$input = array(
		    0 => array(
                'type' => 'select',
                'label' => $this->l('پنل پستی'),
                'name' => 'PSCA_TYPE_PANEL_COD',
                'col' => '8',
				'options' => array(  
					'query' => $this->getPanelsCod(),
					'id' => 'id',
					'name' => 'name' 
				),
				'required' => true,
				'form_group_class'=>'type-cod',
			),	
		);


        if (!empty(DBSGlobal::$webServiceResponse['companay_partner'])) {
            $companay_partner = DBSGlobal::$webServiceResponse['companay_partner'];
            $output .= $this->module->showBootstrapAlert('لایسنس شما ویژه پنل پستی '. $companay_partner .' صادر شده است و برای استفاده از سایر پنل های پستی باید با بخش پشتیبانی پرستایار تماس بگیرید.', 'warning');
        }

		$items = $this->module->cod->getItems();
		foreach($items as $key => $item){
			if( $item['type'] == 'text' or $item['type'] == 'password'){
				$input[] = array(
                    'type' => $item['type'],
                    'label' => $item['label'],
                    'name' => $key,
					'desc' => ( isset($item['desc']) ? $item['desc'] : $item['label']) ,
				);
				$fields[$key] = '';
			}	
		}

        $input[] = array(
            'type' 	=> 'text',
            'label' => 'وزن پیش فرض (گرم)',
            'name' 	=> 'PSCA_WEIGHT_DEFAULT',
            'desc' 	=> 'در زمان ثبت سفارش در پنل پستی در صورتی که وزن محصولات صفر باشد از وزن پیش فرض استفاده می شود.',
            'class' => 'fixed-width-xl'
        );
        /*$input[] =  array(
            'type' => 'switch',
            'label' => $this->l('عدم توجه به خطا پنل پستی'),
            'name' => 'PSCA_HIDE_COD',
            'default' => 1,
            'values' => $this->soption,
            'required' => true,
            'desc' =>  $this->l('با فعال کردن این گزینه در صورتی که در مرحله اول و هنگام دریافت هزینه پستی خطایی توسط پنل پستی ایجاد شود ، ثبت سفارش ادامه داشته و حامل های پستی برای سفارش نمایش داده نمی شود.'),

        );*/

		$orderStatesCod = $this->module->cod->getOrderStates();
		$orderStates 	= $this->getOrderStates(true);

		foreach($orderStatesCod as $key => $item){
			$input[] = array(
                'type' 	=> 'select',
                'label' => $item['title'],
                'name' 	=> 'PSCA_ORDER_STATE_'.$key,
				'options' => array(  
					'query' => $orderStates,
					'id' => 'id',
					'name' => 'name' 
				),				
				'desc' 	=> ( isset($item['description']) ? $item['description'] : false) ,
			);
			$fields['PSCA_ORDER_STATE_'.$key] = '';
		}

		if (Tools::isSubmit('submit'.$this->module->name) )
		{
			$type_panel = Configuration::get('PSCA_TYPE_PANEL_COD');
			
			foreach($fields as $key => $field)
			{
				if( Tools::getValue($key) == 'newOrderState' )
				{
					$key = str_replace("PSCA_ORDER_STATE_","",$key);
					$this->saveOrderState($key,$orderStatesCod[$key]);
				}
				elseif ( !strpos($key,"PASSWORD") or Tools::getValue($key) != '' )
					Configuration::updateValue($key, Tools::getValue($key) );
			}
			$output .= $this->module->displayConfirmation($this->l('تنظیمات با موفقیت به روز شد !'));

			$this->changePanel($type_panel);
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminPsCart').'&dbs_tab=cod_config');
		}

		// Init Fields form array
		$fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('اطلاعات پنل پستی'),
			),
			'input' => $input,
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'btn btn-default pull-right'
			)
		);

		// Generate Module Settings Form With DBSCore\DBSHelperForm.
		$helper = new DBSHelperForm();
		$helper->setInit($this->module)->setFieldsValue($fields);
		$helper->token = Tools::getAdminTokenLite(Tools::getValue('controller'));
		$helper->currentIndex = AdminController::$currentIndex.'&'. $this->queryStringKeyForTabs .'='.Tools::getValue($this->queryStringKeyForTabs);			

		return	$output . $helper->generateForm( $fields_form );
	}	
	
	public function actionTab_carriersPayment_config()
	{
		$output = '';
		$fields = array(
			'PSCA_TYPE_PAYMENT'=>'',
			'PSCA_MODULE_PAYMENT'=>'',
			'PSCA_MODULE_PAYMENT_BANK'=>'',
			'PSCA_MODULE_PAYMENT_GATE'=>'',
            'PSCA_PAYMENT_LINK'=>''
		);
		
		if (Tools::isSubmit('submit'.$this->module->name) )
		{
			/* carrier online */	
			$selectedIds = Tools::getValue('PSCA_ONLINE_CARRIER_selected');
			$selected = ( $selectedIds !== '' ) ? implode(',',$selectedIds) : $selectedIds ;
			Configuration::updateValue('PSCA_ONLINE_CARRIER', $selected);
			
			foreach($fields as $key=>$field)
				Configuration::updateValue($key, Tools::getValue($key) );
			
			$output .= $this->module->displayConfirmation($this->l('تنظیمات با موفقیت به روز شد !'));
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminPsCart').'&dbs_tab=carriersPayment_config');
		}
		elseif (Tools::isSubmit('submitPSCartCity') )
        {
			$value = Tools::getValue('PSCA_ID_STATE');
			Configuration::updateValue('PSCA_ID_STATE', $value);
			
			$value = Tools::getValue('PSCA_ID_CITY');
			Configuration::updateValue('PSCA_ID_CITY', $value);
			
			$output .= $this->module->displayConfirmation($this->l('تنظیمات با موفقیت به روز شد !'));
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminPsCart').'&dbs_tab=carriersPayment_config');
		}
		elseif (Tools::isSubmit('submitModulecarrier')){
            $this->saveRestrictions();
        }

		// Init Fields form array
		$fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('حامل ها و پرداخت'),
			),
			'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('روش پرداخت'),
                    'name' => 'PSCA_TYPE_PAYMENT',
					'options' => array(  
						'query' => array(
							0 => array('name'=>'ادغام با روش ارسال','id'=>'Merger'),
							1 => array('name'=>'انتخاب جداگانه روش پرداخت','id'=>'Separate')
						),
						'id' => 'id',
						'name' => 'name' 
					),
					'required' => true,
					'form_group_class'=>'type-payment',
                    //'desc' => $this->l('')
				),	
                array(
                    'type' => 'select',
                    'label' => $this->l('ماژول پرداخت'),
                    'name' => 'PSCA_MODULE_PAYMENT',
					'options' => array(  
						'query' => array_merge(
                            $this->getModulesPayment(),
                            array(
                                array(
                                    'name' => 'لینک پرداخت',
                                    'id' => 'paymentLink'
                                )
                            )
                        ),
						'id' => 'id',
						'name' => 'name' 
					),
					'required' => true,
					'form_group_class'=>'module-payment',
                    'desc' => $this->l('ماژولی که حامل های آنلاین (درصورت استفاده از حالت ادغام روش پرداخت با روش ارسال ) براساس آن ثبت شده و پرداخت میشوند ، این ماژول برای پرداخت سفارش محصولات مجازی نیز مورد استفاده قرار می گیرد.'),
				),
                array(
                    'type' => 'select',
                    'label' => $this->l('درگاه بانک'),
                    'name' => 'PSCA_MODULE_PAYMENT_BANK',
					'options' => array(  
						'query' => $this->getBanksPayment(),
						'id' => 'id',
						'name' => 'name' 
					),
					'required' => true,
					'form_group_class'=>'module-payment-bank hide',
				),
                array(
                    'type' => 'select',
                    'label' => $this->l('درگاه بانک'),
                    'name' => 'PSCA_MODULE_PAYMENT_GATE',
					'options' => array(
						'query' => $this->getGatesPayment(),
						'id' => 'id',
						'name' => 'name'
					),
					'required' => true,
					'form_group_class'=>'module-payment-gate hide',
				),
                array(
                    'type' => 'text',
                    'label' => $this->l('لینک پرداخت مستقیم درگاه پرداخت'),
                    'name' => 'PSCA_PAYMENT_LINK',
                    'desc' => $this->l('لینک مستقیم ماژول پرداخت جهت اتصال به درگاه بانک'),
                    'form_group_class'=>'payment-link hide',
                ),
                array(
                    'type' => 'swap',
                    'label' => $this->l('حامل آنلاین'),
                    'name' => 'PSCA_ONLINE_CARRIER',
                    'required' => false,
                    'multiple' => true,
                    'options' => array(
                        'query' => Carrier::getCarriers($this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS),
                        'id' => 'id_carrier',
                        'name' => 'name'
                    ),
                    'hint' =>$this->l('حامل های آنلاین در صورت انتخاب توسط کاربر ، کاربر را برای ثبت نهایی سفارش به ماژول پرداخت انتخاب شده هدایت میکند.'),
                    'desc' => array(
                        $this->l('حامل های آنلاین در صورت انتخاب توسط کاربر ، کاربر را برای ثبت نهایی سفارش به ماژول پرداخت انتخاب شده هدایت میکند.'),
                        $this->l('برای انتخاب باید حامل را در سمت چپ قرار دهید.')
                    ),
					'form_group_class'=>'online-carrier hide',
				)
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'btn btn-default pull-right'
			)
		);

		// Generate Module Settings Form With DBSCore\DBSHelperForm.
		$helper = new DBSHelperForm();
		$helper->setInit($this->module)->setFieldsValue($fields);
		
		// get Carriers
		$selectedIds = explode(',',Configuration::get('PSCA_ONLINE_CARRIER'));
		$helper->fields_value['PSCA_ONLINE_CARRIER'] = $selectedIds;
		$helper->token = Tools::getAdminTokenLite(Tools::getValue('controller'));
		$helper->currentIndex = AdminController::$currentIndex.'&'. $this->queryStringKeyForTabs .'='.Tools::getValue($this->queryStringKeyForTabs);			

		//city_config
		$items = $this->module->cod->getItems();
		foreach($items as $key => $item){
			if( $item["type"] == "selectCity" or $item["type"] == "selectState")
                $items[$key]['value'] = Configuration::get($key);
		}
		$this->setSmartyAssign(
			array(
				'items'	=> $items,
			)
		);		
		
		return	$output .
            $helper->generateForm( $fields_form ) .
            $this->renderCarrierRestrictions().
            $this->module->display(_PS_MODULE_DIR_. $this->module->name,  'views/templates/admin/city_config.tpl');
	}

	public function actionTab_debug_config()
	{
		$debug = Configuration::get('PSCA_DEBUG');
		if(!$debug)
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminPsCart').'&dbs_tab=module_config');
		
		$output = '';
		$fields = array(	
			'PSCA_ZONE_CITY'=>'',
			'PSCA_ZONE_STATE'=>'',
			'PSCA_ZONE_ADJACENT'=>'',
			'PSCA_ZONE_UNADJACENT'=>'',
			'PSCA_ORDER_STATE'=>'',
			'PSCA_PISHTAZ_COD_CARRIER'=>'',
			'PSCA_SEFARESHI_COD_CARRIER'=>''
		);
		
		if (Tools::isSubmit('submit'.$this->module->name) )
		{
			foreach($fields as $key=>$field)
				if( Tools::getValue($key) == 'newCarrier' )
				{
					$sub_pishtaz   = strpos($key,'PISHTAZ');
					$sub_sefareshi = strpos($key,'SEFARESHI');
					
					if($sub_pishtaz) $carrier = 'pishtaz';
					elseif($sub_sefareshi) $carrier = 'sefareshi';
					
					$configCarrier = $this->module->makeCarrier($carrier);
					$id_carrier = $this->module->addCarrier( $configCarrier );
					Configuration::updateValue($key, $id_carrier);					
				}
				else				
					Configuration::updateValue($key, Tools::getValue($key) );
		
			$output .= $this->module->displayConfirmation($this->l('تنظیمات با موفقیت به روز شد !'));
			Tools::redirectAdmin($this->context->link->getAdminLink('AdminPsCart').'&dbs_tab=debug_config');
		}

		// Init Fields form array
        $states = $this->module->getStates();
		$fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('تنظیمات پیشرفته'),
			),
			'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('منطقه درون شهری'),
                    'name' => 'PSCA_ZONE_CITY',
					'options' => array(  
						'query' => $states,
						'id' => 'id_state',
						'name' => 'name' 
					),
					'required' => true,
				),
                array(
                    'type' => 'select',
                    'label' => $this->l('منطقه درون استانی'),
                    'name' => 'PSCA_ZONE_STATE',
					'options' => array(  
						'query' => $states,
						'id' => 'id_state',
						'name' => 'name' 
					),
					'required' => true,
				),
                array(
                    'type' => 'select',
                    'label' => $this->l('منطقه برون استانی همجوار'),
                    'name' => 'PSCA_ZONE_ADJACENT',
					'options' => array(  
						'query' => $states,
						'id' => 'id_state',
						'name' => 'name' 
					),
					'required' => true,
				),
                array(
                    'type' => 'select',
                    'label' => $this->l('منطقه برون استانی غیرهمجوار'),
                    'name' => 'PSCA_ZONE_UNADJACENT',
					'options' => array(  
						'query' => $states,
						'id' => 'id_state',
						'name' => 'name' 
					),
					'required' => true,
				),               
				array(
                    'type' => 'select',
                    'label' => $this->l('وضعیت سفارش ویژه'),
                    'name' => 'PSCA_ORDER_STATE',
					'options' => array(  
						'query' => $this->getOrderStates(),
						'id' => 'id',
						'name' => 'name' 
					),
					'required' => true,
					'desc' => $this->l('وضعیت سفارشاتی که بدون استفاده از ماژول های پرداخت و توسط ماژول سبد خرید ثبت می شوند ، مانند : پیک موتوری و ...'),
				),  
				array(
                    'type' => 'select',
                    'label' => $this->l('حامل پیشتاز پنل پستی'),
                    'name' => 'PSCA_PISHTAZ_COD_CARRIER',
					'options' => array(
						'query' => $this->getCarriers(),
						'id' => 'id_carrier',
						'name' => 'name'
					),
					'required' => true,
					'desc' => $this->l('لطفا حامل پرداخت در محل و ارسال با پست پیشتاز را انتخاب کنید. این حامل باید توسط ماژول سبد خرید ایجاد شده باشد.'),
				),
                array(
                    'type' => 'select',
                    'label' => $this->l('حامل سفارشی پنل پستی'),
                    'name' => 'PSCA_SEFARESHI_COD_CARRIER',
                    'options' => array(
                        'query' => $this->getCarriers(),
                        'id' => 'id_carrier',
                        'name' => 'name'
					),
					'required' => true,
                    'desc' => $this->l('لطفا حامل پرداخت در محل و ارسال با پست سفارشی را انتخاب کنید. این حامل باید توسط ماژول سبد خرید ایجاد شده باشد.'),
				)
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'btn btn-default pull-right'
			)
		);

		// Generate Module Settings Form With DBSCore\DBSHelperForm.
		$helper = new DBSHelperForm();
		$helper->setInit($this->module)->setFieldsValue($fields);
		$helper->token = Tools::getAdminTokenLite(Tools::getValue('controller'));
		$helper->currentIndex = AdminController::$currentIndex.'&'. $this->queryStringKeyForTabs .'='.Tools::getValue($this->queryStringKeyForTabs);			

		return	$output . $helper->generateForm( $fields_form );
	}
		
	public function actionTab_themes_config()
	{
		$output = '';
		$fields = array(
			'PSCA_STYLE'=>'',
			'PSCA_TAB_ADDRESS'=>'',
			'PSCA_ALERT_COLOR_BOX'=>'',
			'PSCA_ALERT_COLOR_BORDER'=>'',
			'PSCA_ALERT_COLOR_TEXT'=>'',
			'PSCA_CSS_CUSTOMIZE'=>'',
		);
		if (Tools::isSubmit('submit'.$this->module->name) ){
			foreach($fields as $key=>$field){
				Configuration::updateValue($key, Tools::getValue($key) );
			}
			$output .= $this->module->displayConfirmation($this->l('تنظیمات با موفقیت به روز شد !'));
		}
		
		// Init Fields form array
		$fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('تنظیمات قالب سبدخرید'),
			),
			'input' => array(
				array(
					'type' => 'switch',
					'label' => $this->l('فعال سازی تب در مرحله دریافت اطلاعات'),
					'name' => 'PSCA_TAB_ADDRESS',
					'default' => 1,
					'values' => $this->soption,
					'required' => true,
					'desc' => $this->l('حالت تب برای قسمت ثبت آدرس و ورود به حساب کاربری در مرحله دوم خرید'),
				),			
                array(
                    'type' => 'select',
                    'label' => $this->l('استایل سبدخرید'),
                    'name' => 'PSCA_STYLE',
					'options' => array(  
						'query' => $this->getSkinsCart(),
						'id' => 'id',
						'name' => 'name' 
					),
					'required' => true,
					'desc' => $this->l('رنگ بندی بخش کاربری صفحه سبد خرید را از بین استایل های متفاوت انتخاب کنید.'),
				),			
				array(
					'type' => 'color',
					'label' => $this->l('رنگ پس زمینه باکس راهنما'),
					'name' => 'PSCA_ALERT_COLOR_BOX',
					'desc' => $this->l('رنگ پس زمینه باکس راهنمای موجود در صفحه سبد خرید را به دلخواه تغییر دهید.'),
				),			
				array(
					'type' => 'color',
					'label' => $this->l('رنگ حاشیه باکس راهنما'),
					'name' => 'PSCA_ALERT_COLOR_BORDER',
					'desc' => $this->l('رنگ حاشیه باکس راهنمای موجود در صفحه سبد خرید را به دلخواه تغییر دهید.'),
				),			
				array(
					'type' => 'color',
					'label' => $this->l('رنگ متن باکس راهنما'),
					'name' => 'PSCA_ALERT_COLOR_TEXT',
					'desc' => $this->l('رنگ متن باکس راهنمای موجود در صفحه سبد خرید را به دلخواه تغییر دهید.'),
				),
                array(
					'type' => 'textarea',
					'label' => $this->l('css سفارشی'),
					'name' => 'PSCA_CSS_CUSTOMIZE',
                    'class' => 'textarea-ltr',
					'desc' => $this->l('کدهای css دلخواه خود را می توانید به صفحه سبد خرید اضافه کنید.'),
				),
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'btn btn-default pull-right'
			)
		);

		// Generate Module Settings Form With DBSCore\DBSHelperForm.
		$helper = new DBSHelperForm();
		$helper->setInit($this->module)->setFieldsValue($fields);
		$helper->token = Tools::getAdminTokenLite(Tools::getValue('controller'));
		$helper->currentIndex = AdminController::$currentIndex.'&'. $this->queryStringKeyForTabs .'='.Tools::getValue($this->queryStringKeyForTabs);			
		return	$output . $helper->generateForm( $fields_form );
	}
		
	public function actionTab_helps_config()
	{
		$output = '';
		$fields = array(
			'PSCA_ALERT_FLAG_TOP'=>'',
			'PSCA_ALERT_TEXT_TOP'=>'',
			'PSCA_ALERT_CART_FLAG'=>'',
			'PSCA_ALERT_CART_TEXT'=>'',
			'PSCA_ALERT_LOGIN_FLAG'=>'',
			'PSCA_ALERT_LOGIN_TEXT'=>'',
			'PSCA_ALERT_GUEST_FLAG'=>'',
			'PSCA_ALERT_GUEST_TEXT'=>'',
			'PSCA_ALERT_CART_STEP3_TEXT'=>'',
			'PSCA_ALERT_CART_STEP3_FLAG'=>'',
			'PSCA_ALERT_ORDER_CONFIRMATION_MESSAGE'=>''
		);
		
		if (Tools::isSubmit('submit'.$this->module->name) )
		{
			foreach($fields as $key=>$field)
                Configuration::updateValue($key, Tools::getValue($key),true);

			$output .= $this->module->displayConfirmation($this->l('تنظیمات با موفقیت به روز شد !'));
		}

		$alertFT = (Configuration::get('PSCA_ALERT_FLAG_TOP') == '1')? '' :  'hide' ;
		$alertFB = (Configuration::get('PSCA_ALERT_CART_FLAG') == '1')? '' :  'hide' ;
		$alertFGU = (Configuration::get('PSCA_ALERT_GUEST_FLAG') == '1')? '' :  'hide' ;
		$alertFLO = (Configuration::get('PSCA_ALERT_LOGIN_FLAG') == '1')? '' :  'hide' ;
        $alertFS3 = (Configuration::get('PSCA_ALERT_CART_STEP3_FLAG') == '1')? '' :  'hide' ;

		// Init Fields form array
		$fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('راهنمای مشتری در صفحه سبد خرید'),
			),
			'input' => array(
				array(
					'type' => 'switch',
                    'label' => $this->l('باکس راهنما در بالای سبدخرید'),
					'name' => 'PSCA_ALERT_FLAG_TOP',
					'default' => 1,
					'values' => $this->soption,
					'desc' => $this->l('در صورت غیرفعال بودن حالت ایجکس تنها در مرحله اول نمایش داده میشود.'),
				),			
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Text Message Top Cart'),
                    'name' => 'PSCA_ALERT_TEXT_TOP',
					'desc' => $this->l(''),
					'autoload_rte' => true,
					'form_group_class'=>'alertFT '.$alertFT,
				),

                array(
                    'type' => 'switch',
                    'label' => $this->l('باکس راهنما در پایین سبدخرید'),
                    'name' => 'PSCA_ALERT_CART_FLAG',
                    'default' => 1,
                    'values' => $this->soption,
                    'desc' => $this->l(''),
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('متن راهنمای پایین سبد خرید'),
                    'name' => 'PSCA_ALERT_CART_TEXT',
                    'desc' => $this->l(''),
                    'autoload_rte' => true,
                    'form_group_class'=>'alertFB '.$alertFB,
                ),
				
				array(
					'type' => 'switch',
					'label' => $this->l('باکس راهنما ثبت آدرس مهمان'),
					'name' => 'PSCA_ALERT_GUEST_FLAG',
					'default' => 1,
					'values' => $this->soption,
					'desc' => $this->l('این باکس در حالتی که کاربر بدون ورود به حساب کاری و بصورت مهمان اقدام به ثبت آدرس میکند ، نمایش داده می شود.'),
				),			
                array(
                    'type' => 'textarea',
                    'label' => $this->l('متن راهنمای ثبت آدرس مهمان'),
                    'name' => 'PSCA_ALERT_GUEST_TEXT',
					'desc' => $this->l(''),
					'autoload_rte' => true,
					'form_group_class'=>'alertFGU '.$alertFGU,
				),

				array(
					'type' => 'switch',
					'label' => $this->l('باکس راهنما ورود به حساب کاربری'),
					'name' => 'PSCA_ALERT_LOGIN_FLAG',
					'default' => 1,
					'values' => $this->soption,
					'desc' => $this->l(''),
				),			
                array(
                    'type' => 'textarea',
                    'label' => $this->l('متن راهنمای بخش ورود به حساب کاربری'),
                    'name' => 'PSCA_ALERT_LOGIN_TEXT',
					'desc' => $this->l(''),
					'autoload_rte' => true,
					'form_group_class'=>'alertFLO '.$alertFLO,
				),

                array(
                    'type' => 'switch',
                    'label' => $this->l('راهنمای مرحله سوم سبد خرید'),
                    'name' => 'PSCA_ALERT_CART_STEP3_FLAG',
                    'default' => 0,
                    'values' => $this->soption,
                    'desc' => $this->l('')
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('متن راهنمای مرحله سوم'),
                    'name' => 'PSCA_ALERT_CART_STEP3_TEXT',
					'desc' => $this->l(''),
					'autoload_rte' => true,
                    'form_group_class'=>'alertFS3 '.$alertFS3,
				),

                array(
                    'type' => 'textarea',
                    'label' => $this->l('متن پیام تایید سفارش'),
                    'name' => 'PSCA_ALERT_ORDER_CONFIRMATION_MESSAGE',
					'desc' => array(
                        $this->l('این متن بعد از ثبت سفارش (در حالتی که نیازی به اتصال به بانک نباشد) نمایش داده میشود. دقت کنید که این قسمت درصورتی که از روش ادغام ارسال و پرداخت استفاده کنید قابل استفاده می باشد.   '),
                        '{referenceOrder} : نمایش کد مرجع سفارش',
                        '{idOrder} : نمایش شناسه عددی سفارش',
                        '{rahgiriCod} : نمایش کد رهگیری پنل پستی',
                    ),
					'autoload_rte' => true
				),					
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'btn btn-default pull-right'
			)
		);

		// Generate Module Settings Form With DBSCore\DBSHelperForm.
		$helper = new DBSHelperForm();
		$helper->setInit($this->module)->setFieldsValue($fields);
		$helper->token = Tools::getAdminTokenLite(Tools::getValue('controller'));
		$helper->currentIndex = AdminController::$currentIndex.'&'. $this->queryStringKeyForTabs .'='.Tools::getValue($this->queryStringKeyForTabs);			
		return	$output . $helper->generateForm( $fields_form );
	}
		
	public function actionTab_fields_config()
	{
		$output = '';
		if (Tools::isSubmit('submitPSCart') )
		{
			if ( $this->module->setFieldsAddress() )
				$output .= $this->module->displayConfirmation($this->l('تنظیمات با موفقیت به روز شد !'));
			else
				$output .= $this->module->displayError($this->l('مشکلی در ثبت تغییرات وجود دارد ، لطفا مجدد سعی کنید ....'));
		}
		
		$fields_address = $this->module->getJsonOptions();
		$this->setSmartyAssign(
			array(
				'fields_address'	=> $fields_address,
			)
		);
		return $output.$this->module->display(_PS_MODULE_DIR_. $this->module->name,  'views/templates/admin/fields_config.tpl');
	}

	public function actionTab_virtual_config()
	{
		$output = '';
		$fields = array(
			'PSCA_TYPE_VIRTUAL'=>'',
			'PSCA_ALERT_VIRTUAL'=>'',
		);
		if (Tools::isSubmit('submit'.$this->module->name) )
		{
			foreach($fields as $key=>$field)
				Configuration::updateValue($key, Tools::getValue($key) );
			$output .= $this->module->displayConfirmation($this->l('تنظیمات با موفقیت به روز شد !'));
		}

		// Init Fields form array
		$fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('تنظیمات سبد خرید مجازی'),
			),
			'input' => array(
				array(
					'type' => 'switch',
					'label' => $this->l('امکان ثبت همزمان محصول فیزیکی و مجازی'),
					'name' => 'PSCA_TYPE_VIRTUAL',
					'default' => 1,
					'values' => $this->soption,
					'desc' => $this->l('با فعال کردن این تنظیم امکان ثبت سفارش همزمان محصول مجازی و فیزیکی در کنار یکدیگر را به کاربر خواهید داد. فیلد های ثبت اطلاعات آدرس و عضویت براساس تنظیمات محصول فیزیکی خواهد بود.'),
				),			
                array(
                    'type' => 'textarea',
                    'label' => $this->l('هشدار عدم امکان ثبت همزمان'),
                    'name' => 'PSCA_ALERT_VIRTUAL',
					'desc' => $this->l(''),
					'autoload_rte' => true
				),
			),
			'submit' => array(
				'title' => $this->l('Save'),
				'class' => 'btn btn-default pull-right'
			)
		);

		// Generate Module Settings Form With DBSCore\DBSHelperForm.
		$helper = new DBSHelperForm();
		$helper->setInit($this->module)->setFieldsValue($fields);
		$helper->token = Tools::getAdminTokenLite(Tools::getValue('controller'));
		$helper->currentIndex = AdminController::$currentIndex.'&'. $this->queryStringKeyForTabs .'='.Tools::getValue($this->queryStringKeyForTabs);			
		return	$output . $helper->generateForm( $fields_form );
	}

	/*
	|--------------------------------------------------------------------------
	| Action Methods
	|--------------------------------------------------------------------------
	*/
	/**
	 * Get Panel Cod 
	 */
	public function getPanelsCod()
	{
        if (!empty(DBSGlobal::$webServiceResponse['companay_partner'])) {
            $companay_partner = DBSGlobal::$webServiceResponse['companay_partner'];
            switch ($companay_partner){
                case 'Raga':
                    return [['name'=>'راگا','id'=>'Raga']];break;

                case 'WebSky':
                    return [['name'=>'آسمان وب','id'=>'WebSky']];break;

                case 'IranMC':
                    return [['name'=>'ایران مارکت سنتر','id'=>'IranMC']];break;

                case 'ParsPeik':
                    return [['name'=>'پارس پیک','id'=>'ParsPeik']];break;

                case 'ParsKasb':
                    return [['name'=>'پارس کسب','id'=>'ParsKasb']];break;

                case 'SafirCod':
                    return [['name'=>'سفیر','id'=>'SafirCod']];break;

                case 'Frotel':
                    return [['name'=>'فروتل','id'=>'Frotel']];break;

                case 'Logito':
                    return [['name'=>'لجیتو(فرتاک','id'=>'Logito']];break;
            }
        }

		return array(
            array('name'=>'آسمان وب','id'=>'WebSky'),
            array('name'=>'ایران مارکت سنتر','id'=>'IranMC'),
            array('name'=>'پارس پیک','id'=>'ParsPeik'),
            array('name'=>'پارس کسب','id'=>'ParsKasb'),
            array('name'=>'راگا','id'=>'Raga'),
            array('name'=>'سفیر','id'=>'SafirCod'),
            array('name'=>'فروتل','id'=>'Frotel'),
            array('name'=>'لجیتو(فرتاک)','id'=>'Logito')
		);
	}

	/**
	 * Get Skins Cart 
	 */
	public function getSkinsCart()
	{
		return array(
			0 => array('name'=>'پیش فرض','id'=>'default'),
			1 => array('name'=>'استایل یک','id'=>'book'),	
			2 => array('name'=>'استایل دو','id'=>'candy'),
			3 => array('name'=>'استایل سه','id'=>'grass'),
			4 => array('name'=>'استایل چهار','id'=>'happy'),
			5 => array('name'=>'استایل پنج','id'=>'heavy'),
			6 => array('name'=>'استایل شش','id'=>'noire'),
			7 => array('name'=>'استایل هفت','id'=>'passion'),
			8 => array('name'=>'استایل هشت','id'=>'snow'),
			9 => array('name'=>'استایل نه','id'=>'twilight')
		);
	}

	/**
	 * get Modules Payment
	 */
	public function getModulesPayment( $object = false, $pscart = false )
	{
        /* Get all modules then select only payment ones */
        $modules = Module::getModulesOnDisk();

		$result = array();
        foreach ($modules as $module) {
            if ($module->tab == 'payments_gateways' and $module->active and $module->name != $this->module->name ) {
                if(!$object)
                {
                    $result[] = array(
                        'name' => $module->displayName,
                        'id' => $module->name
                    );
                }
                else $result[] = $module;
			}

			if( $pscart and $module->name == $this->module->name ) {
                if(!$object)
                    $result[] = array(
                        'name' => $module->displayName,
                        'id' => $module->name
                    );
                else $result[] = $module;
            }
		}
		return $result;
	}

	/**
     * is Module Payment by name
     */
	public function isModulePayment( $moduleName )
	{
        /* Get all modules then select only payment ones */
        $modules = Module::getModulesOnDisk();
        foreach ($modules as $module)
            if ($module->tab == 'payments_gateways' and $module->active and $module->name == $moduleName )
				return true;

		return false;
	}

	/**
	 * Get banks payment dmtbanks
	 */
	public function getBanksPayment()
	{
		$result = array();
		if ( $this->isModulePayment('dmtbanks') )
		{
            $result[] = array(
                'name' 	=> $this->l('استفاده از چند درگاه بصورت همزمان'),
                'id'	=> 'payment',
            );

            require_once (_PS_MODULE_DIR_ .'dmtbanks/dmtbanks.php');
			$module = new DMTBanks();
			$banks = explode('_', Configuration::get($module->prefix . 'SORT_BANK'));
			foreach ($banks as $bank)
				if (Configuration::get($module->info[$bank]['plugin'] . 'ACTIVE') == 1)
				{
					$result[] = array(
						'name' 	=> Configuration::get($bank . '_ORDER_PAGE'),
						'id'	=> strtolower($bank),
					);				
				}
		}
		return $result;
	}

    /**
     * Get banks payment psf_prestapay
     */
    public function getGatesPayment()
    {
        $result = array();
        if ( $this->isModulePayment('psf_prestapay') )
        {
            $result[] = array(
                'name' 	=> $this->l('استفاده از چند درگاه بصورت همزمان'),
                'id'	=> 'list',
            );

            require_once (_PS_MODULE_DIR_ .'psf_prestapay/psf_prestapay.php');
            $module = new Psf_Prestapay();
            foreach ( $module->plugins() as $plugin ) {
                if ( is_array($plugin->category) && in_array('gateway',$plugin->category) &&
                     Configuration::get( 'PSFPAY_GATE_' . strtoupper($plugin->name).'_ACTIVE')==1) {

                    $result[] = array(
                        'name' 	=> $plugin->displayName,
                        'id'	=> $plugin->name,
                    );
                }
            }
        }
        return $result;
    }

	/**
	 * Get Order States
	 */
	public function getOrderStates($cod = false )
	{
		if( $cod )
			$result[] = array(
				'name' 	=> 'هیچ کدام :: وضعیت جدید ایجاد شود',
				'id'	=> 'newOrderState',
			);	

        $statuses = OrderState::getOrderStates((int)$this->context->language->id);
        foreach ($statuses as $status) {
			$result[] = array(
				'name' 	=> $status['name'],
				'id'	=> $status['id_order_state'],
			);			
        }
		return $result;
	}	

	/**
	 * Save Order States
	 */
	public function saveOrderState($key , $state = array() )
	{
		$orderState 			= new OrderState();
        $orderState->color 		= $state['options']['color'];
		$orderState->logable 	= $state['options']['logable'];
		$orderState->invoice 	= $state['options']['invoice'];
		$orderState->hidden 	= $state['options']['hidden'];
		$orderState->send_email = $state['options']['send_email'];
        $orderState->shipped 	= $state['options']['shipped'];
        $orderState->paid 		= $state['options']['paid'];
        $orderState->delivery 	= $state['options']['delivery'];
		
		$orderState->name 		= array();
        foreach (Language::getLanguages() as $language)
			$orderState->name[$language['id_lang']] = $state['options']['name'];		
			
		if ( $state['options']['send_email'])
			$orderState->template = 'order_changed';

        if ( $orderState->add() )
            Configuration::updateValue('PSCA_ORDER_STATE_'.$key, (int)$orderState->id);
	}	

	/**
	 * Check Config
	 */
	public function checkConfig()
	{
		$message = '';

		$cod_config = Configuration::get('PSCA_STATUS_COD');
		if( $cod_config and (!$this->module->nameCod or $this->module->nameCod == 'Online') )
		{
			$link = $this->context->link->getAdminLink('AdminPsCart').'&dbs_tab=cod_config';
			$message .= '<li><a href="'.$link.'">لطفا پنل پستی خود را انتخاب کرده و تنظیمات مربوطه را اعمال نمایید.</a></li>';	
			
		}
		elseif($cod_config)
		{
			$items = $this->module->cod->getItems();
			foreach($items as $key => $field)
			{
				if( !in_array( $key , array('PSCA_ID_STATE','PSCA_ID_CITY') ) )
				{
					$value = Configuration::get($key); 
					if( $field['required'] && (!$value or $value =='')  )
					{
						$link = $this->context->link->getAdminLink('AdminPsCart').'&dbs_tab=cod_config';
						$message .= '<li><a href="'.$link.'">لطفا تنظیمات پنل پستی ('.$this->module->nameCod.') را بررسی و تکمیل کنید.</a></li>';								
						break;
					}
				}
			}
			
			$orderStatesCod = $this->module->cod->getOrderStates();	
			foreach($orderStatesCod as $key => $item){
				$value = Configuration::get('PSCA_ORDER_STATE_'.$key);
				if( !$value or $value ==''  )
				{
					$link = $this->context->link->getAdminLink('AdminPsCart').'&dbs_tab=cod_config';
					$message .= '<li><a href="'.$link.'">لطفا وضعیت سفارشهای مربوط به پنل پستی را تنظیم و مشخص کنید.</a></li>';	
					break;
				}
			}			
		
			if( !$this->is_codCarrier()  )
			{
				$debug = Configuration::get('PSCA_DEBUG');
				if( $debug )
				{
					$link = $this->context->link->getAdminLink('AdminPsCart').'&dbs_tab=debug_config';
					$message .= '<li><a href="'.$link.'">حامل های پستی به درستی تنظیم نشده اند ، لطفاجهت تنظیم کلیک کنید.</a></li>';
				}
				else{
					$link = $this->context->link->getAdminLink('AdminPsCart').'&dbs_tab=module_config';
					$link2 = $this->context->link->getAdminLink('AdminPsCart').'&dbs_tab=debug_config';
					$message .= '<li> حامل های پستی به درستی تنظیم نشده اند ، ابتدا از طریق  بخش تنظیمات اصلی
					(<a href="'.$link.'"> اینجا </a>) حالت خطایابی را فعال کنید و سپس از طریق بخش تنظیمات پیشرفته 
					(<a href="'.$link2.'"> اینجا </a>) اقدام به تنظیم حامل های پستی کنید.</li>';
				}
			}
		}

        $city  = Configuration::get('PSCA_ID_CITY');
        $state = Configuration::get('PSCA_ID_STATE');
        if( !$city or !$state  )
        {
            $link = $this->context->link->getAdminLink('AdminPsCart').'&dbs_tab=carriersPayment_config';
            $message .= '<li><a href="'.$link.'">لطفا شهر و استان خود را انتخاب کنید.</a></li>';
        }
		
		$module_payment  = Configuration::get('PSCA_MODULE_PAYMENT');
		if(  $module_payment == 'empty' )
		{
			$link = $this->context->link->getAdminLink('AdminPsCart').'&dbs_tab=carriersPayment_config';
			$message .= '<li><a href="'.$link.'">ماژول پرداخت پیش فرض را انتخاب کنید.</a></li>';
		}

        $email = trim(Configuration::get('PSCA_EMAIL_GUEST'));
        if (!Customer::customerExists($email,false,false))
        {
            $link = $this->context->link->getAdminLink('AdminPsCart').'&dbs_tab=module_config';
            $message .= '<li><a href="'.$link.'">کاربری با ایمیل مهمان وجود ندارد.</a></li>';
        }
        else{
            $customer = new Customer();
            $customerGuest = $customer->getCustomersByEmail($email);
            if ( $customer->isBanned($customerGuest['0']['id_customer']) )
            {
                $link = $this->context->link->getAdminLink('AdminPsCart').'&dbs_tab=module_config';
                $message .= '<li><a href="'.$link.'">حساب کاربری مهمان بن شده است ، لطفا حساب کاربری دیگری را تنظیم کنید</a></li>';
            }
            else{
                $customer = new Customer();
                $customer->getByEmail($email,null,false);
                if ( !$customer->isGuest() )
                {
                    $customer->is_guest = 1;
                    $customer->active = 1;
                    $customer->cleanGroups();
                    $customer->addGroups(array(Configuration::get('PS_GUEST_GROUP'))); // add default customer group

                    if (!$customer->update()) {
                        $link = $this->context->link->getAdminLink('AdminPsCart').'&dbs_tab=module_config';
                        $message .= '<li><a href="'.$link.'">حساب کاربری مهمان باید بصورت مهمان باشد ، حساب کاربری انتخاب  شده توسط شما مشتری می باشد.</a></li>';
                    }

                }
                elseif(!$customer->active){
                    $customer->active = 1;
                    if (!$customer->update()) {
                        $link = $this->context->link->getAdminLink('AdminPsCart').'&dbs_tab=module_config';
                        $message .= '<li><a href="'.$link.'">حساب کاربری مهمان باید فعال باشد.</a></li>';
                    }
                }
            }
        }

		return ($message=='')?'':$this->module->showBootstrapAlert('<ul class="list-unstyled">'.$message.'</ul>', 'warning', 'عدم انتخاب موارد زیر باعث  مشکل در سبد خرید می شود.');
	}

	/**
	 * Change panel cod
	 */
	public function changePanel($type_panel)
	{
		$type_panel_two = Configuration::get('PSCA_TYPE_PANEL_COD');
		if($type_panel_two != $type_panel)
		{
			Configuration::updateValue('PSCA_ID_STATE',null);
			Configuration::updateValue('PSCA_ID_CITY',null);
			
			$orderStatesCod = $this->module->cod->getOrderStates();	
			foreach($orderStatesCod as $key => $item)
				Configuration::updateValue('PSCA_ORDER_STATE_'.$key,null);
						
		}
	}			
	
	/**
	 * Get Carriers
	 */
	public function getCarriers( $external = true , $cod = true )
	{
		if( !$this->carriers or !$this->carriers_external )
		{
			$this->carriers = Carrier::getCarriers($this->context->language->id, false, false, false, null, Carrier::ALL_CARRIERS);
			if( $cod )
				$this->carriers_external[] = array(
					'name' 	=> 'هیچ کدام :: حامل جدید ایجاد شود',
					'id_carrier'	=> 'newCarrier',
				);	

			foreach($this->carriers as $carrier )
				if( $carrier['shipping_external'] ) $this->carriers_external[] = $carrier;			
		}
		
		if( $external  )
			return $this->carriers_external;
		
		return $this->carriers;
	}	

	/**
	 * Isset Cod Carrier
	 */
	public function is_codCarrier( $external = true )
	{
		$carrier_pishtaz   = Configuration::get('PSCA_PISHTAZ_COD_CARRIER');
		if( !$this->is_carrier($carrier_pishtaz)) return false;
		
		$carrier_sefareshi = Configuration::get('PSCA_SEFARESHI_COD_CARRIER');
		if( !$this->is_carrier($carrier_sefareshi)) return false;
		
		return true;
	}

	/**
	 * Isset Carrier
	 */
	public function is_carrier( $carrier_id)
	{
		$carriers_external = $this->getCarriers();
		foreach($carriers_external as $carrier)
			if( $carrier_id == $carrier['id_carrier'] ) return true;
		
		return false;
	}

	/**
     * Carrier Restrictions
     */
    protected function renderCarrierRestrictions()
    {
        $typePayment = Configuration::get('PSCA_TYPE_PAYMENT');
        if ($typePayment == 'Merger') return;

        $this->payment_modules = $this->getModulesPayment(true,true);
        $display_restrictions = count($this->payment_modules)  ? true : false ;

        $carrierRestrictions = Configuration::get('PSCA_CARRIER_RESTRICTIONS');
        $values = Tools::jsonDecode($carrierRestrictions,true);

        $lists = array(
            array(
                'items' => Carrier::getCarriers($this->context->language->id, true, false, false, null, Carrier::ALL_CARRIERS),
                'title' => $this->l('محدودیت روش های پرداخت براساس حامل (روش ارسال)'),
                'desc' => $this->l('در این قسمت می توانید براساس روش ارسال انتخاب شده توسط کاربر ، روش پرداخت و ثبت سفارش را محدود کنید. بطور مثال روش ارسال با تیپاکس را محدود به پرداخت آنلاین سفارش کنید.'),
                'name_id' => 'carrier',
                'identifier' => 'id_carrier',
                'icon' => 'icon-carrier'
            )
        );

        $carrierPishtaz = (int) Configuration::get('PSCA_PISHTAZ_COD_CARRIER');
        $carrierSefareshi = (int) Configuration::get('PSCA_SEFARESHI_COD_CARRIER');

        foreach ($lists as $key_list => $list) {
            $list['check_list'] = array();
            foreach ($list['items'] as $key_item => $item) {
                $name_id = $list['name_id'];
                foreach ($this->payment_modules as $key_module => $module) {
                    if (isset($values[ $module->name ]) && in_array($item['id_'.$name_id], $values[ $module->name ] )) {
                        $list['items'][$key_item]['check_list'][$key_module] = 'checked';
                    } else {
                        $list['items'][$key_item]['check_list'][$key_module] = 'unchecked';
                    }

                    if (!isset($module->$name_id)) {
                        $module->$name_id = array();
                    }
                }
            }
            // update list
            $lists[$key_list] = $list;
        }
        $this->setSmartyAssign(
            array(
                'moduleNameCart' => $this->module->name,
                'carrierPishtaz' => $carrierPishtaz,
                'carrierSefareshi' => $carrierSefareshi,
                'display_restrictions' => $display_restrictions,
                'lists' => $lists,
                'payment_modules' => $this->payment_modules,
                'url_submit' => self::$currentIndex.'&token='.$this->token.'&dbs_tab=carriersPayment_config',
            )
        );
        return	$this->module->display(_PS_MODULE_DIR_. $this->module->name,  'views/templates/admin/carriers_payment.tpl');
    }

    /**
     * save Carrier Restrictions
     */
    protected function saveRestrictions( $type = 'carrier' )
    {
        $this->payment_modules = $this->getModulesPayment(true);
        // Fill the new restriction selection for active module.
        $values = array();
        foreach ($this->payment_modules as $module) {
            if ($module->active && isset($_POST[$module->name.'_'.$type.''])) {
                foreach ($_POST[$module->name.'_'.$type.''] as $selected) {
                    $values[$module->name][] = (int)$selected ;
                }
            }
        }
        $valuesJson = Tools::jsonEncode($values);
        Configuration::updateValue('PSCA_CARRIER_RESTRICTIONS', $valuesJson);
    }
}
