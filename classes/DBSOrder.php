<?php
/**
 * PrestaCart         Cart Module For Prestashop
 *
 * @class       DBSOrder
 * @website     PrestaYar.com
 * @copyright	(c) 2016 - PrestaYar Team
 * @author      Hashem Afkhami <hashem_afkhami@yahoo.com>
 * @since       18 Nov 2016
 */
if(!defined('_PS_VERSION_')) exit;

use DBSCore\V11Cart\DBSGlobal;

require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'psf_prestacart.php');

class DBSOrder  extends Psf_PrestaCart
{
	public $errors = array();
	public $action;
    public $newCookie;

	public function __construct()
	{
		parent::__construct();
		$this->ajax = false;
		$this->action 	= 'summery';
		$this->template = false;
	}

    /**
     * Step 1 - Show summery cart
     */
	public function SummeryCart()
	{
	    //setDeliveryOption
		$this->context->cart->setDeliveryOption();
		$this->context->cart->update();

		$typeCart 	    = $this->isPVCart();
		$actionUrl      = $this->getAction();

        $pscartCookie   = new Cookie('PSCart');
        $stateDefault    = $pscartCookie->__get('PSCart_State');
        $cityDefault    = $pscartCookie->__get('PSCart_City');

        if ( !$stateDefault && Configuration::get('PSCA_ID_STATE_DEFAULT') ) {
            $stateDefault = Configuration::get('PSCA_ID_STATE') ;

            if ( Configuration::get('PSCA_ID_CITY_DEFAULT') ) {
                $cityDefault = Configuration::get('PSCA_ID_CITY') ;
            }
        }

		$fields = array(
            'PSCA_ALERT_FLAG_TOP',
            'PSCA_ALERT_TEXT_TOP',
            'PSCA_ALERT_CART_FLAG',
            'PSCA_ALERT_CART_TEXT',
            'PSCA_ALERT_VIRTUAL',
        );
		$tpl_vars = array_merge(
            array(
                'city'          => $cityDefault,
                'state'         => $stateDefault,
                'typeCart' 		=> $typeCart,
                'actionUrl' 	=> $actionUrl,
                'StatesOptions'	=> $this->cod->get_states(),
                'tpldir' 		=> _PS_MODULE_DIR_ . $this->name . '/views/templates/front',
                'jsCity' 		=> _MODULE_DIR_ . $this->name . '/views/js/city_' . strtolower($this->nameCod) . '.js',
                'panelCodeIndex' => in_array($this->nameCod,array('Frotel','IranMC','ParsKasb','ParsPeik','Raga')) ? false : true,
            ),
            Configuration::getMultiple($fields)
        );
		$this->context->smarty->assign($tpl_vars);

		$this->template = 'shopping-cart.tpl';
	}

    /**
     * Process Step 1 - Checks
     */
	public function SummeryCheck()
	{
		// Check minimal amount
        global $cookie;
 		$currency = Currency::getCurrency((int)$cookie->id_currency);
		$minimal_purchase = Tools::convertPrice((float)Configuration::get('PS_PURCHASE_MINIMUM'), $currency);
		if ( $this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS) < $minimal_purchase )
		{
			$this->errors[] = sprintf(
                Tools::displayError('A minimum purchase total of %1s (tax excl.) is required to validate your order, current purchase total is %2s (tax excl.).'),
                Tools::displayPrice($minimal_purchase, $currency), Tools::displayPrice($this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS), $currency)
            ) ;
			return;
		}

        $product = $this->context->cart->checkQuantities(true);
        if ((int)$id_product = $this->context->cart->checkProductsAccess()) {
            $this->errors[] = sprintf(Tools::displayError('An item in your cart is no longer available (%1s). You cannot proceed with your order.'), Product::getProductName((int)$id_product));
            return;
        }
        // If some products have disappear
        if (is_array($product)) {

            $products_stock = [];
            foreach ($this->context->cart->getProducts() as $product) {
                if (!$this->context->cart->allow_seperated_package &&
                    !$product['allow_oosp'] &&
                    StockAvailable::dependsOnStock($product['id_product']) &&
                    $product['advanced_stock_management'] &&
                    (bool)Context::getContext()->customer->isLogged() &&
                    ($delivery = $this->context->cart->getDeliveryOption())
                    && !empty($delivery)) {
                    $product['stock_quantity'] = StockManager::getStockByCarrier((int)$product['id_product'], (int)$product['id_product_attribute'], $delivery);
                }
                if (!$product['active'] || !$product['available_for_order']
                    || (!$product['allow_oosp'] && $product['stock_quantity'] < $product['cart_quantity'])) {
                    $products_stock[] =  $product;
                }
            }

            $error_message = Tools::displayError('محصولات زیر با تعداد انتخاب شده موجود نمی باشد ، تا زمانی که تعداد آن تنظیم نشود، شما نمی‌توانید سفارش را تکمیل نمایید. ');
            foreach ($products_stock as $product) {
                $info_product = ' '. $product['name'].' / '.$product['attributes'].' ';
                if (!empty($product['stock_quantity'])){
                    $info_product .= ' / تعداد موجود ' . $product['stock_quantity'] . ' عدد ';
                }

                $error_message .= '<br>' . $info_product;
            }


           // $this->errors[] = sprintf(Tools::displayError('An item (%1s) in your cart is no longer available in this quantity. You cannot proceed with your order until the quantity is adjusted.'), $info_product);
            $this->errors[] = $error_message;
            return;
        }

		$typeCart 	  = $this->isPVCart();
		$typeVirtual  = Configuration::get('PSCA_TYPE_VIRTUAL');
		if ( $typeCart >= 2 and !$typeVirtual )
		{
			$this->errors[] = 'شما یک محصول فایل در سبد خرید خود دارید. لطفاً پس از حذف محصول فایل از سبد خرید، ابتدا سفارش محصول فیزیکی را ثبت نموده سپس محصول فایل را بصورت مجزا خریداری نمایید. سپاسگزاریم.' ;
			return;
		}

        $this->newCookie = new Cookie('PSCart');
		if ( $typeCart  )
		{
			$this->getPriceCarriers();
			$state = Tools::getValue('PSCA_ID_STATE');
			$city  = Tools::getValue('PSCA_ID_CITY');
			$name_city   = Tools::getValue('city');
			$name_state  = Tools::getValue('state');
			$this->newCookie->__set('PSCart_City', $city);
			$this->newCookie->__set('PSCart_State', $state);
			$this->newCookie->__set('PSCart_Name_City', $name_state.' - '. $name_city);

			$this->newCookie->__set('PSCart_Virtual', false);
		}
		else $this->newCookie->__set('PSCart_Virtual', true);
    }

    /**
     * Step 2 - Show address or show register and login
     */
    public function setStep2()
	{
	    // is logged users
		if( $this->context->customer->isLogged() )
		{
			$addresses = $this->context->customer->getAddresses($this->context->language->id);
			if( !count($addresses) )
				Tools::redirect( $this->getAction('processAddress') );
			else{
				$this->context->smarty->assign(
					array(
						'addresses'=>$addresses
					)
				);

				$this->template 	= 'addresses.tpl';
				$actionUrl 			= $this->getAction('addresses');
				$this->context->smarty->assign(array('actionUrlProcess' => $this->getAction('processAddress') ));

				$view = Tools::getValue('view');

				if (isset($view) and $view) {
                    $id_address = $view;
                }
				elseif (!empty($this->context->cart->id_address_delivery)) {
                    $id_address = $this->context->cart->id_address_delivery;
                }

				if (!empty($id_address)) {
                    $this->context->smarty->assign(array('id_address' => $id_address));
                }
			}
		}
		// not logged => register or login
		else{
			$this->template  = 'register.tpl';
			$actionUrl 		 = $this->getAction('register');

			$this->context->smarty->assign(
				array(
                    'login' => ( Tools::getValue('view')=='login' ) ? true : false,
				    'actionUrlLogin' => $this->getAction('login'),
					'actionUrlAddresses' => $this->getAction('addresses')
                )
			);
		}

		$typeCart 	= $this->isPVCart();
		$fields_address = $this->getJsonOptions();

        $fields = array(
            'PSCA_ALERT_LOGIN_FLAG',
            'PSCA_ALERT_LOGIN_TEXT',
            'PSCA_ALERT_GUEST_FLAG',
            'PSCA_ALERT_GUEST_TEXT',
            'PSCA_ALERT_CART_FLAG',
            'PSCA_ALERT_CART_TEXT',
            'PSCA_ALERT_CART_POSITION',
            'PSCA_ALERT_CART_STEP3_FLAG',
            'PSCA_ALERT_CART_STEP3_TEXT',
            'PSCA_TAB_ADDRESS',
        );
        $tpl_vars = array_merge(
            array(
                'PSCA_FIELDS_ADDRESS'	=> $fields_address,
                'typeCart' 				=> $typeCart,
                'actionUrl' 			=> $actionUrl,
                'HOOK_CREATE_ACCOUNT_FORM' => Hook::exec('displayCustomerAccountForm'),
                'HOOK_CREATE_ACCOUNT_TOP'  => Hook::exec('displayCustomerAccountFormTop')
            ),
            Configuration::getMultiple($fields)
        );
        $this->context->smarty->assign($tpl_vars);
	}

    /**
     * Process Step 2 register user or save address guest user
     * @return bool|void
     */
    public function registerUser()
	{
		$result  = $this->getEmail();
		$isEmail = $result['isEmail'];
		$email   = $result['email'];
        $lastNameCustomer   = $result['lastNameCustomer'];

        // validate fields register user
		$this->isFields();
		if ( $this->errors ) return;

		$customer = new Customer();
		if($isEmail)
		{
		    // set guest user without login and redirect user to login
			$customer->getByEmail($email,null,false);
			if( !$customer->isGuest() )
                Tools::redirect( $this->getAction('register',array('view'=>'login','email'=>$email)) );

			$addresses = $customer->getAddresses($this->context->language->id);
			$count = count($addresses)+1;
            $_POST['alias'] = 'آدرس ' .$count ;
		}
		else{
		    // set password for new user
			if( !isset($_POST['passwd']) or $_POST['passwd'] == '' )
				$_POST['passwd'] = $this->randomPassword();
			$passwd = $_POST['passwd'];

            $_POST['alias'] = 'آدرس 1';

			// Preparing customer
			$this->errors = array_unique(array_merge($this->errors, $customer->validateController()));
			$this->errors = array_merge($this->errors, $customer->validateFieldsRequiredDatabase());
		}

		// Preparing address
		$newCookie = new Cookie('PSCart');
		$_POST['city'] = $newCookie->__get('PSCart_Name_City');

		$virtual = ( !Tools::getValue('typeCart') ) ? true : false ;
		if( $virtual ) $_POST['city'] = 'ندارد';

		$_POST['id_country'] =  Country::getByIso('IR');

		$_POST['id_state'] = $this->getStateAddress();

		// set address long to fieds address1 and address2
		$_POST['address1'] = str_replace('_','-',$_POST['address1']);
		if( mb_strlen($_POST['address1']) > 128 )
		{
			$_POST['address2'] = mb_substr($_POST['address1'],128,mb_strlen($_POST['address1'])-128);
			$_POST['address1'] = mb_substr($_POST['address1'],0,128);
		}

		$address = new Address();
		$address->validateController();

		if( !$virtual )
		{
            /*چک کردن کدپستی براساس تنظیمات کشور - این مورد بررسی شود که امکان پذیر است یا خیر*/
            /* #dbs# check version 3.1 */
            $country = new Country($address->id_country);
			// Check country
			if (!($country = new Country($address->id_country)) || !Validate::isLoadedObject($country))
				throw new PrestaShopException('Country cannot be loaded with address->id_country');

			if (!$country->active)
				$this->errors[] = $this->l('This country is not active.','dbsorder');
		}

		// Check the requires fields which are settings in the BO
		$this->errors = array_merge($this->errors, $address->validateFieldsRequiredDatabase());

		// Don't continue this process if we have errors !
		if ($this->errors) return;

		$isCustomerAdd = false;
		if(!$isEmail)
		{
			// if registration type is in one step, we save the address
            $regGuest = Configuration::get('PSCA_REGISTER_GUEST');
            $customer->is_guest =  ( $regGuest )  ? 1 : 0;
			$customer->active = 1;
            $customer->lastname =  $lastNameCustomer;
			if( !$customer->add() )
				$this->errors[] = Tools::displayError('An error occurred while creating your account.');
			else
			{
                if (!$customer->is_guest) {
                    if (!$this->sendConfirmationMail($customer)) {
                        //$this->errors[] = Tools::displayError('The email cannot be sent.');
                    }
                }
				$isCustomerAdd = true;
			}
		}

		if ($isEmail or $isCustomerAdd)
		{
			$address->id_customer = (int)$customer->id;
			if (!$address->add())
				$this->errors[] = Tools::displayError('An error occurred while creating your address.');
			else{
			    if( $isCustomerAdd )
                    Hook::exec('actionCustomerAccountAdd', array(
                        '_POST' => $_POST,
                        'newCustomer' => $customer
                    ));

				$id_cart = $this->context->cookie->id_cart;
				$ClassCart = new Cart($id_cart);
				$this->context->cart  = $ClassCart;
				$this->context->customer = $customer;

				$this->context->cart->id_customer = (int)$customer->id;
				$this->context->cart->secure_key = $customer->secure_key;
				$this->context->cart->save();
				return $address->id;
			}
            if( $isCustomerAdd )
                Hook::exec('actionCustomerAccountAdd', array(
                    '_POST' => $_POST,
                    'newCustomer' => $customer
                ));
		}
		return false;
	}

	/**
     * Process Step 2 for save address user logged
     */
	public function processAddress()
	{
		if( !$this->context->customer->isLogged() )
			Tools::redirect( $this->getAction('register',array('view'=>'login')) );

		$id_address = (int)Tools::getValue('id_address', 0);
        if ($id_address)
		{
            $address = new Address($id_address);

			if($this->context->customer->id != $address->id_customer)
				Tools::redirect( $this->getAction('addresses') );

			$address->address1 .= $address->address2;
			$this->context->smarty->assign(
				array(
					'address' 	 => $address ,
					'id_address' => $id_address
				)
			);
		} else {
            $params = array(
                'firstname' => $this->context->customer->firstname,
                'lastname' => $this->context->customer->lastname
            );

            $mobile = $this->getMobileCustomer($this->context->customer->id, false);
            if (!empty($mobile)) {
                $params['phone_mobile'] = $mobile;
            }

            $this->context->smarty->assign(
                array(
                    'defaultValues'	=> $params
                )
            );
        }

		$this->template  = 'address.tpl';
		$this->action    = 'processAddress';
		$actionUrl 		 = $this->getAction('processAddress');

		$typeCart 	= $this->isPVCart();
		$fields_address = $this->getJsonOptions();

        $addresses = $this->context->customer->getAddresses($this->context->language->id);

		$this->context->smarty->assign(
			array(
				'PSCA_FIELDS_ADDRESS'	=> $fields_address,
				'typeCart' 				=> $typeCart,
				'actionUrl' 			=> $actionUrl,
                'is_address'            => (bool) count($addresses)
			)
		);

		if ($this->IsPostRequest()) {
			$this->isFields(true);
			if ( $this->errors ) return;

			$virtual = ( !Tools::getValue('typeCart') ) ? true : false;

            // add address
			if (!$id_address) {
				$newCookie = new Cookie('PSCart');
				$_POST['city'] = $newCookie->__get('PSCart_Name_City');
				if($virtual ) $_POST['city'] = 'ندارد';
				$_POST['id_country'] =  Country::getByIso('IR');

				$i = count($this->context->customer->getAddresses($this->context->language->id))+1 ;
				$_POST['alias'] = 'آدرس '.$i;

				$_POST['id_state'] = $this->getStateAddress();

				$address = new Address();
				$address->id_customer = (int)$this->context->customer->id;
			}

            if ($virtual && empty($address->city)) {
                $_POST['city'] = 'ندارد';
            }

            if ( !isset($_POST['firstname']) and $_POST['firstname'] == '' )
                $_POST['firstname'] = '-';

            if ( isset($_POST['name_merged']) ){
                $_POST['lastname']  = $_POST['name_merged'];
                $_POST['firstname'] = '-';
            }

            // set address long to fieds address1 and address2
			$_POST['address1'] = str_replace('_','-',$_POST['address1']);
			if( mb_strlen($_POST['address1']) > 128 )
			{
				$_POST['address2'] = mb_substr($_POST['address1'],128,mb_strlen($_POST['address1'])-128);
				$_POST['address1'] = mb_substr($_POST['address1'],0,128);
			}

			$this->errors = array_merge($this->errors, $address->validateController());
            $this->errors = array_merge($this->errors, $address->validateFieldsRequiredDatabase());

            if ($id_address) {
                foreach ($fields_address as $field => $fields_address) {
                    if ($virtual) {
                        $required = 'required_virtual';
                    } else {
                        $required = 'required';
                    }

                    if (!$fields_address['data'][$required] && !Tools::getValue($field)) {
                        $address->{$field} = '';
                    }
                }
            }

			if ($this->errors) return;

			if (!$virtual)
			{
				$country = new Country($address->id_country);
				// Check country
				if (!($country = new Country($address->id_country)) || !Validate::isLoadedObject($country))
					throw new PrestaShopException('Country cannot be loaded with address->id_country');

				if (!$country->active)
					$this->errors[] = $this->l('This country is not active.','dbsorder');
			}

			if( !$address->save() )
				$this->errors[] = Tools::displayError('An error occurred while creating your address.');
			else{
				// Update cart address
				$this->context->cart->autosetProductAddress();

				$this->context->cart->id_address_invoice  = (int)$address->id;
				$this->context->cart->id_address_delivery = (int)$address->id;
				$this->context->cart->update();
			}

			if ( $this->errors ) return;
			Tools::redirect( $this->getAction('addresses',array('view'=>$address->id)) );
		}


	}

	/**
	 * Step 2 - set address to cart when select address user logged or after register user guest
	 */
	public function setAddress( $addressId = null )
	{
		if(!$addressId){
			$addressId = (int)Tools::getValue('id_address_delivery');
		}
		if (!Customer::customerHasAddress($this->context->customer->id, $addressId))
			$this->errors[] = Tools::displayError('Invalid address');
		else
		{
			$this->context->cart->id_address_delivery = $addressId;
			$this->context->cart->id_address_invoice  = $this->context->cart->id_address_delivery;

            $this->context->cart->autosetProductAddress();

			CartRule::autoRemoveFromCart($this->context);
			CartRule::autoAddToCart($this->context);

			if (!$this->context->cart->update())
				$this->errors[] = Tools::displayError('An error occurred while updating your cart.', !Tools::getValue('ajax'));

            $address = new Address($addressId);
            $newCookie = new Cookie('PSCart');
            $address->city = $newCookie->__get('PSCart_Name_City');
            $address->id_state = $this->getStateAddress();

            $this->errors = array_merge($this->errors, $address->validateFieldsRequiredDatabase());

            if (!empty($address->dni) && !Validate::isDniLite($address->dni)) {
                $this->errors[] = Tools::displayError('The identification number is incorrect or has already been used.');
            }

            if (empty($this->errors)) {
                if (!$address->update()) {
                    $this->errors[] = 'خطا در انتخاب آدرس';
                }

                if (!$this->context->cart->isMultiAddressDelivery()) {
                    $this->context->cart->setNoMultishipping();
                }
            }
        }
	}

    /**
     * Step 3 - Show summery carriers
     */
    public function setStep3()
	{
        $product = $this->context->cart->checkQuantities(true);
        if ((int)$id_product = $this->context->cart->checkProductsAccess()) {
            $this->errors[] = sprintf(Tools::displayError('An item in your cart is no longer available (%1s). You cannot proceed with your order.'), Product::getProductName((int)$id_product));
            return;
        }
        // If some products have disappear
        if (is_array($product)) {
            $this->errors[] = sprintf(Tools::displayError('An item (%1s) in your cart is no longer available in this quantity. You cannot proceed with your order until the quantity is adjusted.'), $product['name']);
            return;
        }

		// Add checking for all addresses
		$address_without_carriers = $this->context->cart->getDeliveryAddressesWithoutCarriers();
		if (count($address_without_carriers) && !$this->context->cart->isVirtualCart())
		{
			if (count($address_without_carriers) > 1)
				$this->errors[] = sprintf(Tools::displayError('There are no carriers that deliver to some addresses you selected.', !Tools::getValue('ajax')));
			elseif ($this->context->cart->isMultiAddressDelivery())
				$this->errors[] = sprintf(Tools::displayError('There are no carriers that deliver to one of the address you selected.', !Tools::getValue('ajax')));
			/* این خطا در بخش نمایش با بررسی تعداد حامل انجام میشود. - کامنت باقی بماند */
			//else
				//$this->errors[] = sprintf(Tools::displayError('There are no carriers that deliver to the address you selected.', !Tools::getValue('ajax')));
		}

        $this->_setPaymentMethods();

		$this->template  = 'carrier.tpl';
		$this->action    = 'step3';
		$actionUrl 		 = $this->getAction('payment');
		$actionUrlAjaxPayments	 = $this->getAction('updatePayments');
		$actionUrlAjaxMessages	 = $this->getAction('updateMessage');

        $this->newCookie = new Cookie('PSCart');

        $fields = array(
            'PSCA_ALERT_CART_STEP3_FLAG',
            'PSCA_ALERT_CART_STEP3_TEXT',
            'PSCA_BOX_MESSAGE_ORDER',
            'PSCA_SEFARESHI_COD_CARRIER',
            'PSCA_PISHTAZ_COD_CARRIER',
        );
        $tpl_vars = array_merge(
            array(
                'hideCOD'               => $this->newCookie->__get('PSCart_Hide_Cod'),// use version 2.0
                'actionUrl' 			=> $actionUrl,
                'actionUrlAjaxPayments' => $actionUrlAjaxPayments,
                'actionUrlAjaxMessages' => $actionUrlAjaxMessages,
            ),
            Configuration::getMultiple($fields)
        );
        $this->context->smarty->assign($tpl_vars);
	}

    /**
     * Process Step 3 - status merger - redirect module payment
     */
	public function setPayment()
	{
        $product = $this->context->cart->checkQuantities(true);
        if ((int)$id_product = $this->context->cart->checkProductsAccess()) {
            $this->errors[] = sprintf(Tools::displayError('An item in your cart is no longer available (%1s). You cannot proceed with your order.'), Product::getProductName((int)$id_product));
        }
        // If some products have disappear
        if (is_array($product)) {
            $this->errors[] = sprintf(Tools::displayError('An item (%1s) in your cart is no longer available in this quantity. You cannot proceed with your order until the quantity is adjusted.'), $product['name']);
        }

        if (count($this->errors)){
            return array(
                'errors' => $this->errors,
                'hasError' => true
            );
        }

		$delivery_option = Tools::getValue('delivery_option');
        $carreir_id = $this->getIdCarrierFromDeliveryOption($delivery_option);
        if (empty($carreir_id)) {
            $carreir_id = $this->getIdCarrierFromDeliveryOption($this->context->cart->getDeliveryOption());
        }

        if (empty($carreir_id)) {
            $this->errors[] = Tools::displayError('خطا در شناسایی روش ارسال ،صفحه را مجدد بارگذاری نمایید..');

            return array(
                'errors' => $this->errors,
                'hasError' => true
            );
        }

		$online_carrier = explode(',',Configuration::get('PSCA_ONLINE_CARRIER'));

		if (in_array($carreir_id, $online_carrier))
		{
            return $this->setPaymentBank();
		}
		else{

            $log_message = sprintf(
                $this->l('Redirect Validation PrestaCart - Carreir %s - ONLINE %s - delivery_option %s'),
                $carreir_id,
                Configuration::get('PSCA_ONLINE_CARRIER'),
                $delivery_option
            );
            PrestaShopLogger::addLog($log_message, 1, null, 'Cart', (int)$this->context->cart->id);

			$link = $this->context->link->getModuleLink('psf_prestacart', 'validation', array(), true);
			Tools::redirect( $link );
		}
	}

    /**
     * set Payment bank
     * @return array
     */
    public function setPaymentBank()
    {
        $module_payment = Configuration::get('PSCA_MODULE_PAYMENT');
        $data = array();
        if ( $module_payment == 'dmtbanks') {

            $bank_payment = Configuration::get('PSCA_MODULE_PAYMENT_BANK');
            if( $bank_payment != 'payment' )
                $data = array( 'bank' => $bank_payment );

        }

        if ( $module_payment == 'psf_prestapay') {
            $gate = Configuration::get('PSCA_MODULE_PAYMENT_GATE');
            $data = array( 'gate' => $gate );
        }

        if( $module_payment == 'paymentLink')
            $link = Configuration::get('PSCA_PAYMENT_LINK');
        else
            $link = $this->context->link->getModuleLink($module_payment, 'payment' , $data, true );

        if( $this->ajax ){
            return array(
                'urlPayment' => $link ,
                'page' => '',
                'hasError' => false
            );
        }
        else
            Tools::redirect( $link );
    }

	/**
     * get address action
	 */
	public function getAction( $action = 'summery' , $data = array() )
	{
		if($action == 'summery' and !count($data) )
			return $this->context->link->getModuleLink('psf_prestacart', 'order', array(), true );

		$data = array_merge(
			array(
				'action'=>$action
			),
			$data
		);
		return $this->context->link->getModuleLink('psf_prestacart', 'order', $data, true);
	}

	/**
     * set address action
     */
	public function setAction( $action = 'summery' )
	{
		$this->action = $action;
	}

	/**
     * is Virtual Cart
     * @return int
	 * return 0 or 1 or 2 or 3
     * 0 => Virtual
     * 1 => Physical
     * 2 => Virtual and Physical => true
     * 3 => Virtual and Physical => false
     *
     */
	public function isPVCart()
    {
		if( $this->context->cart->isVirtualCart()  )
			return 0;

		$products = $this->context->cart->getProducts();
        if (!count($products))
            return 1;

		$physical = false;
		$virtual  = false;
		foreach( $products as $product )
			if( $product['is_virtual'] ) $virtual = true ;
			else $physical = true ;

		if( $virtual and $physical )
		{
			$type = Configuration::get('PSCA_TYPE_VIRTUAL');
			if($type) return 2;
			return 3;
		}
        return 1;
    }

    /**
     * Process email customer
     * @return array
     */
	public function getEmail()
	{
		$fields_address = $this->getJsonOptions();
		$virtual 		= ( !Tools::getValue('typeCart') ) ? true : false ;
		$enable_email  	= ( $virtual ) ? $fields_address['email_create']['data']['enable_virtual']   : $fields_address['email_create']['data']['enable'] ;
		$require_email 	= ( $virtual ) ? $fields_address['email_create']['data']['required_virtual'] : $fields_address['email_create']['data']['required'] ;

		$isEmail = false;
		$_POST['email'] = $email = Tools::getValue('email_create');

        if ( !isset($_POST['firstname']) or $_POST['firstname'] == '' ) {
            $_POST['firstname'] = '-';
            $firstName = '';
        }
        else $firstName = $_POST['firstname'];

        if ( isset($_POST['name_merged']) ){
            $_POST['lastname'] = $_POST['name_merged'];
            $_POST['firstname'] = '-';
            $firstName = '';
        }

        $lastNameCustomer = $_POST['lastname'] ;

        $mobileEmail = false;
        if ( Configuration::get('PSCA_IS_MOBILE_EMAIL') ) {
            $phone_mobile = $this->convertNumber($_POST['phone_mobile']);
            if ($phone_mobile != '' && preg_match('/^(((\+|00)98)|0)?9[01239]\d{8}$/', $phone_mobile)) {
                $domain = str_replace("www.","", $_SERVER['HTTP_HOST']);
                $mobileEmail = $phone_mobile.'@'.$domain;
            }
        }

		if( empty($email) )
		{
			if($enable_email and $require_email and !$email)
			{
				$this->errors[] = $this->l('Email is required.','dbsorder');
			}
			else{

                if ($mobileEmail) {
                    $_POST['email'] = $email = $mobileEmail;
                } else {
                    $_POST['email'] = $email = trim(Configuration::get('PSCA_EMAIL_GUEST'));
                    $lastNameCustomer = 'مشتری مهمان';
                    if( empty($email) )
                        $email ='email@email.com';
                }

				if( Customer::customerExists($email,false,false) )
					$isEmail = true;

                $customer = new Customer();
                if($isEmail)
                {
                    $customer->getByEmail($email,null,false);
                    if ( !$customer->isGuest() )
                    {
                        $customer->is_guest = 1;
                        $customer->cleanGroups();
                        $customer->addGroups(array(Configuration::get('PS_GUEST_GROUP'))); // add default customer group

                        if (!$customer->update())
                            $this->errors[] = $this->l('امکان ثبت سفارش بدون ایمیل وجود ندارد.','dbsorder');
                    }
                }
			}
		}
		else{
			if (!Validate::isEmail($email) )
				$this->errors[] = $this->l('Invalid email address.','dbsorder');
			elseif (Customer::customerExists($email,false,false))
				$isEmail = true;
		}
		return array(
			'isEmail'=>$isEmail,
			'email'=>$email,
			'lastNameCustomer'=>$lastNameCustomer
		);
	}

    /**
     * create random password
     * @return string
     */
	private function randomPassword()
	{
		$alphabet = "0123456789";
		$pass = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < 8; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass); //turn the array into a string
	}

    /**
     * Get Price Carriers Cod
     */
    private function getPriceCarriers()
    {
        $pishtazCarrier = $sefareshiCarrier =  0;

        $statusCod  = Configuration::get('PSCA_STATUS_COD');
        if ( $statusCod ) {

            // get total price => Producs + DISCOUNTS - WITHOUT_SHIPPING
            $rial_currency = new Currency((int)Currency::getIdByIsoCode('IRR'));
            $totalprice = $this->context->cart->getOrderTotal(true,Cart::BOTH_WITHOUT_SHIPPING);
            if ( $rial_currency->id != $this->context->currency->id)
                $totalprice = number_format(Tools::convertPriceFull($totalprice, $this->context->currency, $rial_currency), 0, '', '');
            else
                $totalprice = number_format($totalprice, 0, '', '');

            // get weight
            $weight = $this->context->cart->getTotalWeight();
            if ($weight == 0 or !$weight)
            {
                $weightDefault = Configuration::get('PSCA_WEIGHT_DEFAULT');
                if( !$weightDefault  )
                {
                    $this->errors[] = $this->getError(50);// wieght of products is not valid please contact admin
                    return;
                }
                $weight = $weightDefault;
            }

            $this->newCookie = new Cookie('PSCart');

            $result = $this->cod->getPostPrice($totalprice , $weight , Tools::getValue('PSCA_ID_STATE'), Tools::getValue('PSCA_ID_CITY') );
            if( $result['hasError'] )
            {
                $error_cod = Configuration::get('PSCA_HIDE_COD');
                if ( $error_cod )
                    $this->newCookie->__set('PSCart_Hide_Cod', true);
                else
                    $this->errors = $result['errors'];
            }
            else{
                $pishtazCarrier = $result['pishtaz'];
                $sefareshiCarrier = $result['sefareshi'];
            }
        }

        $this->newCookie->__set('Pishtaz_Carrier', $pishtazCarrier );
        $this->newCookie->__set('Sefareshi_Carrier', $sefareshiCarrier );
    }

    /**
     * Validate fields for register or save address
     *
     * params
     * @address true (address) or false (register)
     */
    private function isFields( $address = false )
    {
        $fields_address = $this->getJsonOptions();
        $virtual 		= ( !$this->isPVCart() ) ? true : false ;
        foreach($fields_address as $key => $values)
        {
            if( $address and $values['view'] == 'user' ) continue;
            if( $key == 'email_create' ) continue;

            $enable  = ( $virtual ) ? $values['data']['enable_virtual']   : $values['data']['enable'];
            $require = ( $virtual ) ? $values['data']['required_virtual'] : $values['data']['required'];

            if($enable and $require and !Tools::getValue($key) )
                $this->errors[] = $values['required'];
            elseif($key == 'address1')
            {
                if( !$enable or ($enable and Tools::getValue($key) == '') )
                    $_POST[$key] = '-';

                $_POST['address1'] = str_replace('+', '-', Tools::getValue($key));
                $_POST['address1'] = str_replace('_', '-', Tools::getValue($key));

            } else if ( $key == 'lastname' ) {
                $enable_name_merged  = ( $virtual ) ? $fields_address['name_merged']['data']['enable_virtual']   : $fields_address['name_merged']['data']['enable'];
                if( (!$enable and !$enable_name_merged) or ($enable and Tools::getValue($key) == '') )
                    $_POST[$key] = '-';
            }

            if ($key =='phone_mobile') {
                $phone_mobile = $_POST['phone_mobile'] = $this->convertNumber(Tools::getValue($key));
                if ($phone_mobile != ''){
                    if(!preg_match('/^(((\+|00)98)|0)?9[01239]\d{8}$/', $phone_mobile ))
                        $this->errors[] = 'لطفا شماره همراه خود را بصورت صحیح وارد کنید.';
                }
            }

            if ($key =='phone') {
                $_POST['phone'] = $this->convertNumber(Tools::getValue($key));
            }

            if ($key =='postcode') {
                $postcode = $_POST['postcode'] = $this->convertNumber(Tools::getValue($key));
                if ($postcode != '') {
                    //if(!preg_match('/^[1-9]\d{4}[\s\-]?[1-9]\d{4}$/', $postcode ))
                    if(!preg_match('/^\d{10}$/', $postcode ))
                        $this->errors[] = 'لطفا کدپستی را بصورت صحیح وارد کنید.';
                }
            }

            if ($key == 'dni') {
                $dni = $_POST['dni'] = $this->convertNumber(Tools::getValue($key));
                if ($dni != '') {
                    if( (!Tools::getValue('dni') || !Validate::isDniLite(Tools::getValue('dni'))) )
                        $this->errors[] = Tools::displayError('The identification number is incorrect or has already been used.');
                }
            }
        }
    }

    /**
     * Set Payment Methods By Carriers
     */
    public function _setPaymentMethods()
    {
        $typePayment = Configuration::get('PSCA_TYPE_PAYMENT');
        $output = '';
        if( $typePayment == 'Separate' )
        {
            $HOOK_PAYMENT = $this->_getPaymentMethods();
            if( is_array($HOOK_PAYMENT ) )
            {
                if (!$this->context->cookie->checkedTOS && Configuration::get('PS_CONDITIONS'))
                    $output = '<p class="warning">'.Tools::displayError('Please accept the Terms of Service.').'</p>';
                else {
                    $carrierRestrictions = Configuration::get('PSCA_CARRIER_RESTRICTIONS');
                    $carrierRestrictions = Tools::jsonDecode($carrierRestrictions,true);

                    $delivery_option = $this->context->cart->getDeliveryOption(null, false);
                    foreach ($delivery_option as $item) $idCarrierDefault = $item;
                    $idCarrierDefault = trim($idCarrierDefault,',');

                    $modulesPayment = array();
                    if( count($carrierRestrictions) ){
                        foreach ($carrierRestrictions as $key => $paymentCarriers)
                        {
                            foreach ($paymentCarriers as $item )
                                if( $item == $idCarrierDefault)
                                {
                                    $modulesPayment[] = $key;
                                    break;
                                }
                        }
                    }


                    $carrierPishtaz = (int) Configuration::get('PSCA_PISHTAZ_COD_CARRIER');
                    $carrierSefareshi = (int) Configuration::get('PSCA_SEFARESHI_COD_CARRIER');
                    if( $idCarrierDefault == $carrierPishtaz or $idCarrierDefault == $carrierSefareshi   )
                        $modulesPayment[] = $this->name;

                    $orderTotal = $this->context->cart->getOrderTotal();
                    foreach ($HOOK_PAYMENT as $key => $item) {
                        if ($orderTotal <= 0) {
                            if ($key == 'psf_prestacart')
                                $output .= $item;
                        } else {
                            if (in_array($key, $modulesPayment))
                                $output .= $item;
                        }
                    }
                }
            }
            else
                $this->errors[] = $HOOK_PAYMENT ;
        }

        if ( $output == '' ) {
            $output = '<div class="alert alert-danger">هیچ روش پرداختی برای این روش ارسال تعریف نشده است.</div>';
        }
        $tpl_vars = array(
            'typePayment' => $typePayment,
            'HOOK_PAYMENT' => $output,
            'hasError' => false
        );
        $this->context->smarty->assign($tpl_vars);
        return $tpl_vars;
    }

    /**
     * Get Payment Methods
     */
    private function _getPaymentMethods()
    {
        if ($this->context->cart->OrderExists())
            return '<p class="warning">'.Tools::displayError('Error: This order has already been validated.').'</p>';

        if (!$this->context->cart->id_customer || !Customer::customerIdExistsStatic($this->context->cart->id_customer) || Customer::isBanned($this->context->cart->id_customer))
            return '<p class="warning">'.Tools::displayError('Error: No customer.').'</p>';

        $address_delivery = new Address($this->context->cart->id_address_delivery);
        $address_invoice = ($this->context->cart->id_address_delivery == $this->context->cart->id_address_invoice ? $address_delivery : new Address($this->context->cart->id_address_invoice));
        if (!$this->context->cart->id_address_delivery || !$this->context->cart->id_address_invoice || !Validate::isLoadedObject($address_delivery) || !Validate::isLoadedObject($address_invoice) || $address_invoice->deleted || $address_delivery->deleted)
            return '<p class="warning">'.Tools::displayError('Error: Please select an address.').'</p>';
        if (count($this->context->cart->getDeliveryOptionList()) == 0 && !$this->context->cart->isVirtualCart())
        {
            if ($this->context->cart->isMultiAddressDelivery())
                return '<p class="warning">'.Tools::displayError('Error: None of your chosen carriers deliver to some of the addresses you have selected.').'</p>';
            else
                return '<p class="warning">'.Tools::displayError('Error: None of your chosen carriers deliver to the address you have selected.').'</p>';
        }
        if (!$this->context->cart->getDeliveryOption(null, false) && !$this->context->cart->isVirtualCart())
            return '<p class="warning">'.Tools::displayError('Error: Please choose a carrier.').'</p>';
        if (!$this->context->cart->id_currency)
            return '<p class="warning">'.Tools::displayError('Error: No currency has been selected.').'</p>';

        /*  شرایط استفاده از خدما مورد بررسی قرار گیرد. */
        /* #dbs# check version 3.1 */
        //if (!$this->context->cookie->checkedTOS && Configuration::get('PS_CONDITIONS'))
            //return '<p class="warning">'.Tools::displayError('Please accept the Terms of Service.').'</p>';

        /* If some products have disappear */
        if (is_array($product = $this->context->cart->checkQuantities(true)))
            return '<p class="warning">'.sprintf(Tools::displayError('An item (%s) in your cart is no longer available in this quantity. You cannot proceed with your order until the quantity is adjusted.'), $product['name']).'</p>';

        if ((int)$id_product = $this->context->cart->checkProductsAccess())
            return '<p class="warning">'.sprintf(Tools::displayError('An item in your cart is no longer available (%s). You cannot proceed with your order.'), Product::getProductName((int)$id_product)).'</p>';

        /* Check minimal amount */
        $currency = Currency::getCurrency((int)$this->context->cart->id_currency);

        $minimal_purchase = Tools::convertPrice((float)Configuration::get('PS_PURCHASE_MINIMUM'), $currency);
        if ($this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS) < $minimal_purchase)
            return '<p class="warning">'.sprintf(
                    Tools::displayError('A minimum purchase total of %1s (tax excl.) is required to validate your order, current purchase total is %2s (tax excl.).'),
                    Tools::displayPrice($minimal_purchase, $currency), Tools::displayPrice($this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS), $currency)
                ).'</p>';

        /* Bypass payment step if total is 0 */
        //if ($this->context->cart->getOrderTotal() <= 0)
            //return '<p class="center"><button class="button btn btn-default button-medium" name="confirmOrder" id="confirmOrder" onclick="confirmFreeOrder();" type="submit"> <span>'.Tools::displayError('I confirm my order.').'</span></button></p>';

        $return = Hook::exec('displayPayment',array(),null,true);
        if (!$return)
            return '<p class="warning">'.Tools::displayError('No payment method is available for use at this time. ').'</p>';
        return $return;
    }

    private function getIdCarrierFromDeliveryOption($delivery_option)
    {
        $delivery_option_list = $this->context->cart->getDeliveryOptionList();
        foreach ($delivery_option as $key => $value)
            if (isset($delivery_option_list[$key]) && isset($delivery_option_list[$key][$value]))
                if (count($delivery_option_list[$key][$value]['carrier_list']) == 1)
                    return current(array_keys($delivery_option_list[$key][$value]['carrier_list']));

        return 0;
    }

    /**
     * check city for step 2
     */
    public function _checkCity()
    {
        $typeCart  = $this->isPVCart();
        if ( $typeCart )
        {
            $newCookie = new Cookie('PSCart');
            $city = $newCookie->__get('PSCart_City');
            if ( !$city or $city == '' )
                Tools::redirect( $this->getAction() );
        }
        return true;
    }

    /**
     * sendConfirmationMail
     * @param Customer $customer
     * @return bool
     */
    protected function sendConfirmationMail(Customer $customer)
    {
        if (!Configuration::get('PS_CUSTOMER_CREATION_EMAIL')) {
            return true;
        }

        return Mail::Send(
            $this->context->language->id,
            'account',
            Mail::l('Welcome!'),
            array(
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{email}' => $customer->email,
                '{passwd}' => Tools::getValue('passwd')),
            $customer->email,
            $customer->firstname.' '.$customer->lastname
        );

    }
}
