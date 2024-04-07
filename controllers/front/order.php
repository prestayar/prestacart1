<?php
/**
 * PrestaCart         Cart Module For Prestashop
 *
 * @website     PrestaYar.com
 * @copyright	(c) 2016 - PrestaYar Team
 * @author      Hashem Afkhami <hashem_afkhami@yahoo.com>
 * @since       18 Nov 2016
 */
if(!defined('_PS_VERSION_'))
	exit;

require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR .'DBSCore/DBSCore.php');
require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR .'classes/DBSOrder.php');
require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'classes/PSFOrderCore.php');
require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR .'classes/DBSChangeOrder.php');
require_once(_PS_ROOT_DIR_ .'/controllers/front/ParentOrderController.php');

class Psf_PrestacartOrderModuleFrontController extends ParentOrderController
{
	public function __construct()
	{
		parent::__construct();

		$this->display_column_left  = false;
		$this->display_column_right  = false;
		$this->php_self  = null;

		$this->DBSOrder = new DBSOrder();

        header('Vary:X-Requested-With');
	}		
	
    public function init()
    {
        parent::init();
		$this->action = Tools::getValue('action');
		if( $this->action == 'summery'  )
			Tools::redirect( $this->DBSOrder->getAction() );

		if( $this->action == false ) $this->action = 'summery';
		
		$this->ajax = $this->DBSOrder->IsAjaxRequest();
		$this->DBSOrder->ajax = $this->ajax;
		$this->ajaxPSCart = Configuration::get('PSCA_STATUS_AJAX');

        $urlPage = $_SERVER['REDIRECT_URL'];


        parse_str($_SERVER['QUERY_STRING'], $query_array);
        unset($query_array['ajax']);
        if (!empty($query_array)) {
            $query = '?' . http_build_query($query_array);
        } else {
            $query = '';
        }

        $urlPage .= $query;

		$this->context->smarty->assign(
			array(
				'ajaxPSCart' 	=> $this->ajaxPSCart,
				'ajax' 			=> $this->ajax,
                'urlPage'       => $urlPage
            )
		);

        if(!$this->nbProducts and $this->action != 'summery' and $this->action != 'status')
            Tools::redirect( $this->DBSOrder->getAction() );
	}

	public function initContent()
	{
		parent::initContent();

        if (!Tools::getValue('multi-shipping')) {
            $this->context->cart->setNoMultishipping();
        }

		$this->processAction($this->action);

		$this->errors = array_merge($this->errors, $this->DBSOrder->errors);
		if (count($this->errors))
		{
			if( $this->ajax )
			{
				$return = array(
					'hasError' => (bool)$this->errors,
					'errors' => $this->errors
				);
				$this->ajaxDie(Tools::jsonEncode($return));				
			}
			else{
				if( $this->action != $this->DBSOrder->action )
					$this->processAction($this->DBSOrder->action);
			}
		}

        $fields = array(
            'PSCA_ALERT_CART_FLAG',
            'PSCA_ALERT_CART_TEXT',
            'PSCA_ALERT_COLOR_BOX',
            'PSCA_ALERT_COLOR_BORDER',
            'PSCA_ALERT_COLOR_TEXT',
            'PSCA_CSS_CUSTOMIZE',
        );
        $this->context->smarty->assign(Configuration::getMultiple($fields));

	}
		
    public function processAction($action)
    {
		switch ($action) 
		{
		    case 'summery' :
				if ( $this->DBSOrder->IsPostRequest()  )
				{
                    $this->DBSOrder->SummeryCheck();
				    $submitAddDiscount = Tools::getValue('submitAddDiscount');
					if(!$submitAddDiscount)
					{
						if ( !count($this->DBSOrder->errors) )
							if( $this->context->customer->isLogged() )
								Tools::redirect( $this->DBSOrder->getAction('addresses') );
							else {
							    $params = array();
                                if (!empty(Tools::getValue('ajax'))) {
                                    $params['ajax'] = 1;
                                }
                                Tools::redirect( $this->DBSOrder->getAction('register',$params) );
                            }
					}
				}

				$this->DBSOrder->SummeryCart();
				$this->_assignSummaryInformations();
                $this->_assignWrappingAndTOS();
				if (!$this->nbProducts)
					$this->context->smarty->assign('empty', 1);
				break;

			case 'step2' :
				$this->DBSOrder->setStep2();
				break;

			case 'register' :
				if( $this->context->customer->isLogged() )
					Tools::redirect( $this->DBSOrder->getAction('addresses') );

				$this->DBSOrder->_checkCity();

				if ( $this->DBSOrder->IsPostRequest() )
				{
					$addressId = $this->DBSOrder->registerUser();
					if ( count($this->DBSOrder->errors) ) 
						$this->DBSOrder->setAction ('step2');
					else{
						$this->DBSOrder->setAddress($addressId);
						if ( count($this->DBSOrder->errors) ) 
							$this->DBSOrder->setAction ('step2');						
						else{
						    $this->DBSOrder->updateContext();
                            $this->_assignVirtual();
						    Tools::redirect( $this->DBSOrder->getAction('step3') );
                        }
					}
				}
				else {
                    $this->DBSOrder->setStep2();
                }
				// address back
                $this->context->smarty->assign(array( 'urlPageBack' => $this->DBSOrder->getAction()));
				break;

			case 'addresses' :
				if( !$this->context->customer->isLogged() )
					Tools::redirect( $this->DBSOrder->getAction('register',array('view'=>'login')) );
						
				if ( $this->DBSOrder->IsPostRequest() )
				{
					$this->DBSOrder->setAddress();
					if ( count($this->DBSOrder->errors) ) 
						$this->DBSOrder->setAction ('step2');
					else{
                        $this->_assignVirtual();
					    Tools::redirect( $this->DBSOrder->getAction('step3') );
                    }
				}
				else
					$this->DBSOrder->setStep2();
                // address back
                $this->context->smarty->assign(array( 'urlPageBack' => $this->DBSOrder->getAction()));
				break;

			case 'processAddress' :
				$this->DBSOrder->processAddress();			
				break;

			case 'step3' :
				$this->_assignCarrier();
				$this->_assignSummaryInformations();
				$this->DBSOrder->setStep3();
                $this->context->smarty->assign(array( 'urlPageBack' => $this->DBSOrder->getAction('register')));
				break;	
				
			case 'payment' :
                $txt_message = urldecode(Tools::getValue('message'));
                if($txt_message and $txt_message != '') $this->_updateMessage($txt_message);

			    $this->_assignPayment(true, $this->DBSOrder->ajax);
                $return = $this->DBSOrder->setPayment();
                if ($this->DBSOrder->ajax)
                    $this->ajaxDie(Tools::jsonEncode($return));
				break;				
				
            case 'updatePayments':
                $this->_assignPayment();
                break;

            case 'updateMessage':
                if (Tools::isSubmit('message')) {
                    $txt_message = urldecode(Tools::getValue('message'));
                    $this->_updateMessage($txt_message);
                    if (count($this->errors)) {
                        $this->ajaxDie('{"hasError" : true, "errors" : ["'.implode('\',\'', $this->errors).'"]}');
                    }
                    $this->ajaxDie(true);
                }
                break;

            case 'status':
                $ChangeOrder = new DBSChangeOrder();
                $update = (int) Tools::getValue('update',1);
                $ChangeOrder->init($update);
                die;
                break;
		}
		
		if( $this->DBSOrder->template )
			$this->setTemplate($this->DBSOrder->template);
    }

    /**
     * Virtual step
     */
    protected function _assignVirtual()
    {
        $virtual = !$this->DBSOrder->isPVCart();

        if( $virtual ){
            $txt_message = urldecode(Tools::getValue('message'));
            if($txt_message and $txt_message != '') $this->_updateMessage($txt_message);

            if ($this->context->cart->getOrderTotal() <= 0) {
                $link = $this->context->link->getModuleLink('psf_prestacart', 'validation' , array(), true);
                Tools::redirect( $link );
            }
            else{
                $return = $this->DBSOrder->setPaymentBank();
                if( $this->DBSOrder->ajax )
                    $this->ajaxDie(Tools::jsonEncode($return));
            }
        }
    }

    /**
     * Carrier step
     */
    protected function _assignCarrier()
    {
        $this->DBSOrder->updateContext();
        if (!isset($this->context->customer->id))
		{
            $this->errors[] = Tools::displayError('Fatal error: No customer');
            Tools::redirect( $this->DBSOrder->getAction() );
			return;
        }
        // Assign carrier
        parent::_assignCarrier();
        // Assign wrapping and TOS
        $this->_assignWrappingAndTOS();

		$vars = array(
			'use_taxes' => (int)Configuration::get('PS_TAX'),
			'priceDisplay' => Product::getTaxCalculationMethod((int)$this->context->cookie->id_customer),
			'cart'=>$this->context->cart
        );
		$this->context->smarty->assign($vars);

		$this->_assignCartRules();
    }

	/**
     * Carrier step
     */
    protected function _assignPayment($checkCGV = false,$ajax = false)
    {
        if (empty($ajax)) {
            $ajax = Tools::isSubmit('ajax');
        }

        $product = $this->context->cart->checkQuantities(true);
        if ((int)$id_product = $this->context->cart->checkProductsAccess()) {
            $this->errors[] = sprintf(Tools::displayError('An item in your cart is no longer available (%1s). You cannot proceed with your order.'), Product::getProductName((int)$id_product));
        }
        // If some products have disappear
        if (is_array($product)) {
            $this->errors[] = sprintf(Tools::displayError('An item (%1s) in your cart is no longer available in this quantity. You cannot proceed with your order until the quantity is adjusted.'), $product['name']);
        }

        if ($ajax && count($this->errors)){
            $this->ajaxDie(Tools::jsonEncode(array(
                'hasError'  => true,
                'errors' => $this->errors
            )));
        }

        if( !isset($_POST['recyclable']))  $_POST['recyclable'] = 0 ;
        if( !isset($_POST['gift']))  $_POST['gift'] = 0 ;
        if( !isset($_POST['cgv']))  $_POST['cgv'] = 0 ;

        $this->context->cookie->checkedTOS = $_POST['cgv'];
        if (!empty($checkCGV)) {
            if (!$this->context->cookie->checkedTOS && Configuration::get('PS_CONDITIONS')) {
                $this->errors[] = '<p class="warning">'.Tools::displayError('Please accept the Terms of Service.').'</p>';
            }
            if ($ajax && count($this->errors)){
                $this->ajaxDie(Tools::jsonEncode(array(
                    'hasError'  => true,
                    'errors' => $this->errors
                )));
            }
        }

        if ((Tools::isSubmit('delivery_option') || Tools::isSubmit('id_carrier')) && Tools::isSubmit('recyclable') && Tools::isSubmit('gift') && Tools::isSubmit('gift_message'))
		{
			if ($this->_processCarrier())
			{
                if (Tools::isSubmit('ajax'))
                {
                    $free_shipping = false;
                    foreach ($this->context->cart->getCartRules() as $rule) {
                        if ($rule['free_shipping'] && !$rule['carrier_restriction']) {
                            $free_shipping = true;
                            break;
                        }
                    }
                    $return = array_merge(
                        $this->getFormatedSummaryDetail(),
                        $this->DBSOrder->_setPaymentMethods(),
                        array('free_shipping'=>$free_shipping )
                    );
                    $this->ajaxDie(Tools::jsonEncode($return));
                }
			}
			else
				$this->errors[] = Tools::displayError('An error occurred while updating the cart.');

            if ($ajax && count($this->errors)){
                $this->ajaxDie(Tools::jsonEncode(array(
                    'hasError'  => true,
                    'errors' => $this->errors
                )));
            }
		}		

    }
	
	protected function _assignCartRules()
	{
		$delivery_option_list = $this->context->cart->getDeliveryOptionList();
        //$delivery_option = $this->context->cart->getDeliveryOption(null, false);{* #dbs# check version 3.1 *}
        $this->setDefaultCarrierSelection($delivery_option_list);

		$cart_rules = $this->context->cart->getCartRules();
		$discount = array();
		$discount[0] = 0;
		foreach ($cart_rules as $cart_rule)
		{
			if($cart_rule['carrier_restriction'] =='1')
			{
				$sql = '
				SELECT crca.*,c.*
				FROM '._DB_PREFIX_.'cart_rule_carrier crca
				LEFT JOIN '._DB_PREFIX_.'carrier c ON (c.id_reference = crca.id_carrier AND c.deleted = 0)
				WHERE crca.id_cart_rule = '.$cart_rule['id_cart_rule'];
				$result = Db::getInstance()->executeS($sql);
				foreach($result as $res){
					$discount[$res['id_carrier']] = isset($discount[$res['id_carrier']])? $discount[$res['id_carrier']]: 0;
					$discount[$res['id_carrier']] +=  $cart_rule['value_real'];
				}
			}
			else{
				$discount[0] += $cart_rule['value_real'];
			}
		}

        $delivery = array();
		foreach ($delivery_option_list as $id_address => &$carriers)
		{
			foreach($carriers as $key => &$carrier)
			{
				if( isset($total_price[(int)$key]) )
				{
					$carrier['total_price_without_tax'] = $total_price[(int)$key];
					foreach($carrier['carrier_list'] as $key => &$value)
						$carrier['price_without_tax'] = $price[(int)$key];
					
				}
				else{
					foreach($carrier['carrier_list'] as $key => &$value)
						$carrier['price_without_tax'] = $value['price_without_tax'];
					
				}
				
				if(isset($discount[(int)$key]) and $discount[(int)$key] != 0)
				{
					$carrier['discount'] = $discount[(int)$key]+$discount[0];
				}
				else{
					$carrier['discount'] = $discount[0];
				}
			}
			$delivery[$id_address] = $carriers;
		}
		$vars = array(		
			'delivery_option_list' => $delivery,
			'discount'=>$discount
		);
		$this->context->smarty->assign($vars);		
	}

    /**
     * Assigns module template for page content
     *
     * @param string $template Template filename
     * @throws PrestaShopException
     */	
    public function setTemplate($template)
    {
        if (!$path = $this->getTemplatePath($template)) {
            throw new PrestaShopException("Template '$template' not found");
        }
        $this->template = $path;
    }

    /**
     * Finds and returns module front template that take the highest precedence
     *
     * @param string $template Template filename
     * @return string|false
     */
    public function getTemplatePath($template)
    {
        $this->moduleName = 'psf_prestacart';
		if (Tools::file_exists_cache(_PS_THEME_DIR_.'modules/'.$this->moduleName.'/'.$template)) {
            return _PS_THEME_DIR_.'modules/'.$this->moduleName.'/'.$template;
        } elseif (Tools::file_exists_cache(_PS_THEME_DIR_.'modules/'.$this->moduleName.'/views/templates/front/'.$template)) {
            return _PS_THEME_DIR_.'modules/'.$this->moduleName.'/views/templates/front/'.$template;
        } elseif (Tools::file_exists_cache(_PS_MODULE_DIR_.$this->moduleName.'/views/templates/front/'.$template)) {
            return _PS_MODULE_DIR_.$this->moduleName.'/views/templates/front/'.$template;
        }

        return false;
    }

	public function displayAjax()
	{
		$page = $this->context->smarty->fetch( $this->template );
		$return = array(
			'page' => $page,
			'action' => $this->action,
			'hasError' => false
		);
		if( Tools::isSubmit('view') )
            $return['view'] = Tools::getValue('view');

		$this->ajaxDie(Tools::jsonEncode($return));		
	}

    protected function getFormatedSummaryDetail()
    {
        $result = array('summary' => $this->context->cart->getSummaryDetails(),
            'customizedDatas' => Product::getAllCustomizedDatas($this->context->cart->id, null, true));

        foreach ($result['summary']['products'] as $key => &$product)
        {
            $product['quantity_without_customization'] = $product['quantity'];
            if ($result['customizedDatas'])
            {
                if (isset($result['customizedDatas'][(int)$product['id_product']][(int)$product['id_product_attribute']]))
                    foreach ($result['customizedDatas'][(int)$product['id_product']][(int)$product['id_product_attribute']] as $addresses)
                        foreach ($addresses as $customization)
                            $product['quantity_without_customization'] -= (int)$customization['quantity'];
            }
        }

        if ($result['customizedDatas'])
            Product::addCustomizationPrice($result['summary']['products'], $result['customizedDatas']);
        return $result;
    }

    public function ajaxDieYar($value = null, $controller = null, $method = null)
    {
        $this->ajaxDie($value, $controller, $method);
    }
}