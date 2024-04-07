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
require_once(_PS_ROOT_DIR_ .'/controllers/front/ParentOrderController.php');

class Psf_PrestacartValidationModuleFrontController extends ModuleFrontController
{
    public $display_column_left = false;
    public $display_column_right = false;

    public function __construct()
    {
        parent::__construct();

        $cod_config = Configuration::get('PSCA_STATUS_COD');
        $panelCod   = Configuration::get('PSCA_TYPE_PANEL_COD');
        $file_patch = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'classes/codpanels/'.$panelCod.'.php';
        if($cod_config == '1' and file_exists($file_patch) )
        {
            require_once(dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'classes/DBSCodPanel.php');
            require_once($file_patch);
            $this->cod 		= new PsCartCod();
        }
        $this->context =  Context::getContext();
    }

	/**
	 * @see FrontController::postProcess()
	 */
	public function postProcess()
	{
		$cart = $this->context->cart;
        $actionUrl  = $this->context->link->getModuleLink('psf_prestacart', 'order', array(), true );
		if (!$cart->id || $cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active)
		    Tools::redirect($actionUrl);

		$customer = new Customer($cart->id_customer);
		if (!Validate::isLoadedObject($customer))
			Tools::redirect($actionUrl);

		try {
            // virtual free order
            if ($this->context->cart->isVirtualCart()) {
                if ($this->context->cart->getOrderTotal() <= 0) {
                    $this->module->validateOrder($this->context->cart->id, Configuration::get('PS_OS_PAYMENT'), 0, Tools::displayError('Free order', false), null, array(), null, false, $this->context->cart->secure_key);
                } else
                    Tools::redirect($actionUrl);

                $rahgiriCod = $this->module->currentOrderReference;
            }
            else {
                if ($this->context->cart->getOrderTotal() <= 0) {
                    $this->module->validateOrder($this->context->cart->id, Configuration::get('PS_OS_PAYMENT'), 0, Tools::displayError('Free order', false), null, array(), null, false, $this->context->cart->secure_key);
                    $rahgiriCod = $this->module->currentOrderReference;
                } else {
                    // cach on delivery -> pscart or panel post
                    $id_carrier = $this->context->cart->id_carrier;
                    $id_carrier_pishtaz = (int)Configuration::get('PSCA_PISHTAZ_COD_CARRIER');
                    $id_carrier_sefareshi = (int)Configuration::get('PSCA_SEFARESHI_COD_CARRIER');

                    // for all status
                    $total = $this->context->cart->getOrderTotal(true, Cart::BOTH);
                    $customer = new Customer((int)$this->context->cart->id_customer);

                    // register order panel post
                    if ($id_carrier_pishtaz == $id_carrier or $id_carrier_sefareshi == $id_carrier)
                    {
                        $dbsCookie = new Cookie('PSCart');
                        $objAddress = new Address((int)$this->context->cart->id_address_delivery);
                        $sendType = ($id_carrier_pishtaz == $id_carrier) ? 'pishtaz' : 'sefareshi';

                        $old_message = Message::getMessageByCartId((int)$this->context->cart->id);
                        $message = (!empty($old_message['message'])) ? $old_message['message']:'';

                        $options = array(
                            'fname' => $objAddress->firstname,
                            'lname' => $objAddress->lastname,
                            'mobile' => $objAddress->phone_mobile,
                            'phone' => $objAddress->phone,
                            'address' => $objAddress->address1 . $objAddress->address2,
                            'email' => $this->context->customer->email,
                            'id_gender' => $this->context->customer->id_gender,
                            'id_state' => $dbsCookie->__get('PSCart_State'),
                            'id_city' => $dbsCookie->__get('PSCart_City'),
                            'sendType' => $sendType,
                            'postcode' => $objAddress->postcode,
                            'description' => $message ,//. $this->generatePropertProduct($this->context->cart->getProducts()),
                            'cart' => $this->context->cart,
                            'objAddress' => $objAddress
                        );

                        $cod_config = Configuration::get('PSCA_STATUS_COD');
                        if ($cod_config)
                            $result = $this->cod->registerOrder($options);
                        else {
                            $result = array(
                                'hasError' => true,
                                'errors' => array('پنل پستی غیرفعال می باشد.')
                            );
                        }

                        $paymentMethod = ($sendType == 'pishtaz') ? 'COD Pishtaz' : 'COD Sefareshi';
                        if ($result['hasError']) {

                            $panelCod   = Configuration::get('PSCA_TYPE_PANEL_COD');
                            if( $panelCod == 'Logito' or $panelCod == 'Raga'  )
                                $orderStateId = Configuration::get('PSCA_ORDER_STATE_200');
                            else
                                $orderStateId = Configuration::get('PSCA_ORDER_STATE_100');

                            $total = $this->context->cart->getOrderTotal(true, Cart::BOTH);

                            $this->module->validateOrder(
                                $cart->id,
                                $orderStateId,
                                $total,
                                $paymentMethod,
                                $result['message'],
                                array(),
                                false,
                                false,
                                $customer->secure_key
                            );
                            $rahgiriCod = $this->module->currentOrderReference;
                        }
                        else {
                            /** save to site */
                            $orderStateId = (int)Configuration::get('PSCA_ORDER_STATE_0');

                            // only frotel
                            if (!$orderStateId)
                                $orderStateId = (int)Configuration::get('PSCA_ORDER_STATE_1');

                            $this->module->validateOrder(
                                $cart->id,
                                $orderStateId,
                                $total,
                                $paymentMethod,
                                $result['rahgiriCod'],
                                array(),
                                false,
                                false,
                                $customer->secure_key
                            );

                            $codType = Configuration::get('PSCA_TYPE_PANEL_COD');

                            $orderCod = new PSFOrderCore();
                            $orderCod->add($this->module->currentOrder, $result['rahgiriCod'],$codType);

                            $rahgiriCod = $result['rahgiriCod'];
                        }
                    } else {
                        // register order by pscart - payk motory or ....
                        $orderState = Configuration::get('PSCA_ORDER_STATE');

                        $Carrier = new Carrier((int)($id_carrier));
                        $paymentMethod = $Carrier->name;

                        $this->module->validateOrder(
                            $cart->id,
                            $orderState,
                            $total,
                            $paymentMethod,
                            NULL,
                            array(),
                            false,
                            false,
                            $customer->secure_key
                        );
                        $rahgiriCod = $this->module->currentOrderReference;
                    }
                }
            }
        }
        catch (PrestaShopException $exception){
            $debug = Configuration::get('PSCA_DEBUG');
            if( $debug )
            {
                echo "<pre>";
                var_dump($exception);
                die;
            }
            die('Error register order #90');
        }
        $messageConfirmation = Configuration::get('PSCA_ALERT_ORDER_CONFIRMATION_MESSAGE');
        $rahgiriUrl = '';/* #dbs# check version 3.1 */

        $messageConfirmation = str_replace( "{rahgiriCod}" ,$rahgiriCod,$messageConfirmation);
        $messageConfirmation = str_replace( "{referenceOrder}" ,$this->module->currentOrderReference,$messageConfirmation);
        $messageConfirmation = str_replace( "{idOrder}" ,$this->module->currentOrder,$messageConfirmation);
        $messageConfirmation = str_replace( "{rahgiriUrl}" ,$rahgiriUrl,$messageConfirmation);
        $messageConfirmation = str_replace( "%7BrahgiriUrl%7D" ,$rahgiriUrl,$messageConfirmation);

        $order = new Order((int)$this->module->currentOrder);
        $products = $order->getProducts();

        $this->context->smarty->assign(
            array(
                'messageConfirmation' => $messageConfirmation,
                'id_order'			=> $this->module->currentOrder ,
                'products'  => $products,
                'PSCA_CSS_CUSTOMIZE' =>Configuration::get('PSCA_CSS_CUSTOMIZE'),
            )
        );
        $this->setTemplate('validation.tpl');

        $this->unsetCookies();

        if ( $this->IsAjaxRequest() )
            $this->displayAjax();
	}

    public function displayAjax()
    {
        $path = $this->getTemplatePath('validation.tpl');
        $page = $this->context->smarty->fetch( $path  );
        $return = array(
            'page' => $page,
            'hasError' => false,
            'urlPayment' => false
        );
        $this->ajaxDie(Tools::jsonEncode($return));
    }

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

    public function IsAjaxRequest()
    {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' )
            return true;
        return false;
    }

    /**
     * delete cookies
     */
    private function unsetCookies()
    {
        $dbsCookie = new Cookie('PSCart');
        $dbsCookie->__unset('Pishtaz_Carrier');
        $dbsCookie->__unset('Sefareshi_Carrier');
        $dbsCookie->__unset('PSCart_State');
        $dbsCookie->__unset('PSCart_City');
        $dbsCookie->__unset('PSCartCarrier');
    }
	
    /**
     * Generate propert products
     * @return string
     */
    private function generatePropertProduct( $products = array() )
    {
        if( !count($products) ) return '';
        $text = ' - ';
        foreach ($products as $product)
        {
            $productName = ( isset($product['name']) ) ? $product['name'] : $product['product_name'] ;
            $productAtributes = ( isset($product['attributes']) ) ? $product['attributes'] : '' ;
            $text .= $productName.'('.$productAtributes.')';
        }
        return $text;
    }
}
