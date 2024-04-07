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
class PsCartFrotel extends PsCodPanel
{
    public $api;
    public $id_state;
    public $id_city;

    public $titleCod = 'فروتل';
    public $nameCod = 'Frotel';
    public $webServiceURLs = array(
        'getPrices'     => 'http://webservice1.link/ws/v1/rest/order/getPrices.json',
        'registerOrder' => 'http://webservice1.link/ws/v1/rest/order/registerOrder.json',
        'tracking' => 'http://webservice1.link/ws/v1/rest/order/tracking.json',
    );

    public function __construct()
	{
		$this->api = trim(Configuration::get('PSCA_FROTEL_API'));
		$this->id_state = trim(Configuration::get('PSCA_ID_STATE'));
		$this->id_city  = trim(Configuration::get('PSCA_ID_CITY'));
	}

    /**
     * get info panel cod
     * @return array
     */
	public function getInfo()
	{
		return array(
			'nameCod'   => $this->nameCod,
			'titleCod'   => $this->titleCod,
			'rahgiriUrl'=>'http://www.frotrace.ir/',
			'count'     =>'100'
		);
	}

    /**
     * get items setting panel cod
     * @return array
     */
	public function getItems(){
		return array(
			'PSCA_FROTEL_API' =>array(
				'type'=>'text',
				'label'=>'کلید api',
				'error'=>'لطفا کلید api خود را وارد کنید',
				'required'=> true
			),
			'PSCA_ID_STATE' =>array(
				'type'=>'selectState',
				'label'=>'استان مبدا',
				'error'=>'لطفا استان و شهر مبدا را مشخص کنید',
				'required'=> true,
				'html'=>$this->get_states(),
				'htmlEdit'=>$this->get_states(false)
			),
			'PSCA_ID_CITY' =>array(
				'type'=>'selectCity',
				'label'=>'شهر مبدا',
				'error'=>'لطفا استان و شهر مبدا را مشخص کنید',
				'required'=> true,
				'html'=>'<select id="id_city" class="id_city" name="PSCA_ID_CITY"><option value="0">لطفا استان خود را انتخاب کنید...</option></select><script type="text/javascript" src="'._MODULE_DIR_.'psf_prestacart/views/js/city_'.strtolower($this->nameCod).'.js'.'"></script>',
				'htmlEdit'=>false
			)	
		);
	}		

    /**
     * Get price shipping cod
     * @param $totalprice :: total price products
     * @param $weight :: total weight products
     * @param $id_state :: state customer
     * @param $id_city :: city customer
     * @return array :: errors or price carriers pishtaz and sefareshi
     */
    public function getPostPrice( $totalprice, $weight, $id_state, $id_city ){
        $errors = array();
        try{
            // set weitgh default
            if ($weight == 0 or !$weight)
                $weight = Configuration::get('PSCA_WEIGHT_DEFAULT');

            // parmas post webserivce
            $wsQueryString = '?api=' . $this->api;
            $wsQueryString .= '&price='. $totalprice . '&weight=' . $weight . '&des_city=' . $id_city ;
            $wsQueryString .= '&send_type[0]=1&send_type[1]=2&buy_type[0]=1&buy_type[1]=2';

            // send request webservice
            $webServiceResponse = json_decode( PsCodPanel::getUrlContent( $this->webServiceURLs['getPrices'].$wsQueryString ), TRUE);

            // process result
            if( isset( $webServiceResponse['code']) )
            {
                if ( !(int) $webServiceResponse['code'] )
                {
                    $pricePosti = $webServiceResponse['result']['posti'];
                    $pricePishtazCarrier   = $pricePosti['1']['post'] + $pricePosti['1']['tax'] + $pricePosti['1']['frotel_service'];
                    $priceSefareshiCarrier = $pricePosti['2']['post'] + $pricePosti['2']['tax'] + $pricePosti['2']['frotel_service'];

                    return array(
                        'hasError' => !empty($errors),
                        'errors' => $errors,
                        'pishtaz' => $pricePishtazCarrier ,
                        'sefareshi' => $priceSefareshiCarrier
                    );
                }
                else{
                    $errors[]  = $this->getError($webServiceResponse['code'], '102', $webServiceResponse['message']);
                    return array(
                        'hasError' => !empty($errors),
                        'errors' => $errors
                    );
                }
            }
        }
        catch (Exception $e){
            return array(
                'hasError' => true,
                'errors' => array(
                    $this->getError(false, '100')
                )
            );
        }
        return array(
            'hasError' => true,
            'errors' => array(
                $this->getError(false, '100')
            )
        );
    }

    /**
     * Register order on cod
     * @param array $options
     * @return array
     */
    public function registerOrder( $options = array() )
    {
        $errors = array();

        try{
            if( $options['sendType'] == 'pishtaz' )
                $options['sendType'] ='1';
            else
                $options['sendType'] ='2';

            if(isset($options['cartAdmin']))
                $products = $this->_generateProductsAdmin($options['cartAdmin']);
            else
                $products = $this->_generateProducts($options['cart']);

            $options['id_gender'] = ($options['id_gender']=='2')?'0':'1';

            // parmas post webserivce
            $data = array(
                'api'       => $this->api,
                'name'      => $options['fname'] ,
                'family'    => $options['lname'],
                'phone'     => $options['phone'],
                'mobile'    => $options['mobile'],
                'gender'    => $options['id_gender'],
                'email'     => $options['email'],
                'address'   => $options['address'],
                'code_posti'=> $options['postcode'],
                'province'  => $options['id_state'],
                'city'      => $options['id_city'],
                'ip'        => $this->_getIP(),
                'pm'        => $options['description'],
                'send_type'  => $options['sendType'],
                'buy_type'  => 1,
                'free_send' => 0,
                'basket'    => $products
            );

            // send request webservice
            $webServiceResponse = json_decode( PsCodPanel::sendRequest( $this->webServiceURLs['registerOrder'] , $data ), TRUE);

            // process result
            if( isset( $webServiceResponse['code']) )
            {
                if ( !(int) $webServiceResponse['code'] )
                {
                    return array(
                        'hasError'  => !empty($errors),
                        'errors'    => $errors,
                        'rahgiriCod' => $webServiceResponse['result']['factor']['id']
                    );
                }
                else{
                    $errors[]  = $this->getError($webServiceResponse['code'], '103', $webServiceResponse['message']);
                    return array(
                        'hasError' => !empty($errors),
                        'errors' => $errors,
                        'message' => $this->getError($webServiceResponse['code'], '103', $webServiceResponse['message'],false)
                    );
                }
            }
        }
        catch (Exception $e){
            $errors[]  = $this->getError(false, '100', null);
            return array(
                'hasError' => !empty($errors),
                'errors' => $errors,
                'message' => $this->getError(false, '100', null, false)
            );
        }
        $errors[]  = $this->getError(false, '101', null);
        return array(
            'hasError' => !empty($errors),
            'errors' => $errors,
            'message' => $this->getError(false, '101', null, false)
        );
    }
    
	public function GetStatus($id_order_ft = null){
		if($id_order_ft){
			try{

                // parmas post webserivce
                $data = array(
                    'api'       => $this->api,
                    'factor'    => $id_order_ft
                );

                // send request webservice
                $webServiceResponse = json_decode( PsCodPanel::sendRequest( $this->webServiceURLs['tracking'] , $data ), TRUE);

                // process result
                if( isset( $webServiceResponse['code']) )
                {
                    if ( !(int) $webServiceResponse['code'] )
                    {
                        switch ($webServiceResponse['result']['order']['status']){
                            case 'معلق':
                                $status = '1';break;

                            case 'در حال آماده سازی':
                            case 'آماده ارسال':
                                $status = '2';break;

                            case 'پیش توزیعی':
                            case 'ارسال شده':
                                $status = '3';break;

                            case 'توزیع شده':
                                $status = '4';break;

                            case 'وصول شده':
                                $status = '5';break;

                            case 'پیش برگشتی':
                            case 'برگشتی':
                                $status = '6';break;

                            case 'انصرافی':
                                $status = '7';break;

                            default:
                                $status = '';
                        }
                        return array(
                            'result'    => true,
                            'date' 		=> $webServiceResponse['result']['order']['change_date'],
                            'state'     => $status,
                            'state_name'=> $webServiceResponse['result']['order']['status'],
                            'cod_post' 	=> $webServiceResponse['result']['order']['barcode']
                        );
                    }
                    else{
                        return array(
                            'result' => false,
                            'message' => $this->getError($webServiceResponse['code'], '104', $webServiceResponse['message'],false)
                        );
                    }
                }
				
			}
			catch (SoapFault $e){
				$result['result'] = false;
				$result['message'] = 'خطا در اتصال به وب سرویس';
			}
			return $result;
		}
	}	
	public function GetListStatus($items){

		$data = "";
		foreach($items as $key => $item){
			$data .= $item['cod_tracking_number'].";";
		}
		$data = trim($data, ';');
		
		try{
			$client = new SoapClient($this->client_url); 			
			#echo "<pre>";var_dump($data);
			$state = $client->FGetStatus (
					$data,
					$this->username,
					$this->password
			);
			$state = urldecode($state);
			$data = explode(';',$state);
			
			$index = 0;
			foreach($items as $key => $item){
				$state = $data[$index];
				if( !in_array($state,array(1,2,3,4,5,6,7))){
					$result = array(
						'result'	=> false,
						'state' 	=> 'خطا در تعیین وضعیت :: ('. $state  .')'
					);					
				}else{
					$result = array(
						'result'	=> true,
						'state' 	=> $state,
					);
				}
				$items[$key]['result']=$result ;
				$index++;
			}
			#echo '<pre>';var_dump($items);die;
			return $items;
		}
		catch (SoapFault $e){
			$result['result'] = false;
			$result['message'] = 'خطا در اتصال به وب سرویس';
		}
		return false;
	}
	
	private function _getIP(){
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
        {
             $ip = $_SERVER['HTTP_CLIENT_IP'];
        } 
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } 
        else 
        {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * Generate string for products order
     * @param $cart
     * @return string
     */
    private function _generateProducts( $cart )
    {
        $products = $cart->getProducts();

        $iso_rial = Currency::getIdByIsoCode('IRR');
        $rial = new Currency($iso_rial);
        $current_currency = new Currency($cart->id_currency);

		$cart_rules = $cart->getCartRules();
		foreach ($cart_rules as $cart_rule){
			if( $cart_rule['reduction_percent'] > 0)
			{
				if($cart_rule['reduction_product'] == '0'){
					foreach ($products as &$product) {
						$product['price_wt'] -= ($product['price_wt'])*($cart_rule['reduction_percent']/100);
					}
				}
				elseif($cart_rule['reduction_product'] > '0'){
					foreach ($products as &$product) {
						if($product['id_product'] == $cart_rule['reduction_product'])
							$product['price_wt'] -= ($product['price_wt'])*($cart_rule['reduction_percent']/100);
					}				
				}
			}
			elseif( $cart_rule['reduction_amount'] > 0){
				if($cart_rule['reduction_product'] == '0')
				{
					foreach ($products as &$product) 
						if( $product['price_wt'] > $cart_rule['reduction_amount'] )
							$product['price_wt'] -= $cart_rule['reduction_amount'];
				}
				elseif($cart_rule['reduction_product'] > '0'){
					foreach ($products as &$product) {
						if($product['id_product'] == $cart_rule['reduction_product'] 
							and $product['price_wt'] > $cart_rule['reduction_amount'])
							$product['price_wt'] -= $cart_rule['reduction_amount'];
					}				
				}			
			}
			elseif( $cart_rule['gift_product'] > 0){
				foreach ($products as &$product){
					if($product['id_product'] == $cart_rule['gift_product']){
						$product['price_wt'] = 0;
					}
				}
			}
		}

        $weightDefault = Configuration::get('PSCA_WEIGHT_DEFAULT');
        $basket = array();
        foreach ( $products as $product )
        {
            $data = array();
            $data ['pro_code'] = $product['id_product'];
            $data ['name'] = $product['name'];

            if( $rial->id != $current_currency->id )
                $data ['price'] = Tools::convertPriceFull((int)$product['price_wt'], $current_currency, $rial);
            else
                $data ['price'] = $product['price_wt'];

            $data ['count'] = $product['quantity'];

            if( !(int)$product['weight'] )
                $data ['weight'] = (int)$weightDefault;
            else
                $data ['weight'] = (int)$product['weight'];

            $basket[] = $data;
        }
        return $basket;
    }
	private function _generateProductsAdmin($options){
		$products = $options['products'];
		$Currrency = new Currency();
		$rial_id = $Currrency->getIdByIsoCode('IRR');
		$rial = new Currency($rial_id);
		$currentCur = new Currency($options['id_currency']);
		$cart_rules = $options['order']->getCartRules();			
		
		
		foreach ($cart_rules as $cartRule){
			$cart_rule = new CartRule($cartRule['id_cart_rule']);
			if( $cart_rule->reduction_percent > 0){
				if($cart_rule->reduction_product == '0'){
					foreach ($products as &$product) {
						$product['product_price_wt'] -= ($product['product_price_wt'])*($cart_rule->reduction_percent/100);
					}
				}elseif($cart_rule->reduction_product > '0'){
					foreach ($products as &$product) {
						if($product['id_product'] == $cart_rule->reduction_product)
							$product['product_price_wt'] -= ($product['product_price_wt'])*($cart_rule->reduction_percent/100);
					}				
				}
			}elseif( $cart_rule->reduction_amount > 0){
				if($cart_rule->reduction_product == '0'){
					foreach ($products as &$product) 
						if( $product['product_price_wt'] > $cart_rule->reduction_amount )
							$product['product_price_wt'] -= $cart_rule->eduction_amount;
				}elseif($cart_rule->reduction_product > '0'){
					foreach ($products as &$product) {
						if($product['id_product'] == $cart_rule->reduction_product 
							and $product['product_price_wt'] > $cart_rule->reduction_amount)
							$product['product_price_wt'] -= $cart_rule->reduction_amount;
					}				
				}			
			}elseif( $cart_rule->gift_product > 0){
				foreach ($products as &$product){
					if($product['id_product'] == $cart_rule->gift_product){
						$product['product_price_wt'] = 0;
					}
				}
			}
		
		}

        $weightDefault = Configuration::get('PSCA_WEIGHT_DEFAULT');
        $basket = array();
        foreach ( $products as $product )
        {
            $data = array();
            $data ['pro_code'] = $product['id_product'];
            $data ['name'] = $product['product_name'];

            if( $rial->id != $currentCur->id )
                $data ['price'] = Tools::convertPriceFull((int)$product['product_price_wt'], $currentCur, $rial);
            else
                $data ['price'] = $product['product_price_wt'];

            $data ['count'] = $product['product_quantity'];

            if( !(int)$product['weight'] )
                $data ['weight'] = (int)$weightDefault;
            else
                $data ['weight'] = (int)$product['product_weight'];

            $basket[] = $data;
        }
        return $basket;
    }
    		
	public function getError( $codeNumberError = null, $typeError = null, $messageResult = null, $debugCheck = true )
    {
        $error = parent::getError( $typeError, $debugCheck);
        if($debugCheck)
        {
            $debug = Configuration::get('PSCA_DEBUG');
            if ( !$debug ) return $error;
        }

        if($codeNumberError){
            $error .= ' ( خطا شماره #' . $codeNumberError.' » ';
            switch($codeNumberError){
                case '13' :
                    $error .= 'وزن محصولات نامعتبر است.';
                    break;

                default :
                    $error .= $messageResult;
            }
            $error .= ' )';
        }
        return $error;

		/*$err = false;
		switch($code){
			case 'Access Denied' :
			case 'Access+Denied' : $err = 'دسترسي وجود ندارد'; break;
			case 'empty' : $err = 'پارامترها درست وارد نشده اند'; break;
			case 'Data Error' : $err = 'اطلاعات ارسالي ناقص است'; break;
			case 'Province Error' : $err = 'شناسه استان اشتباه است'; break;
			case 'City Error' : $err = 'مشخصات شهر اشتباه است'; break;
			case 'Product Register Error' : $err = 'خطا در ثبت محصولات اين سفارش'; break;
			case 'Order Register Error' : $err = 'خطا در ثبت سفارش و مشخصات گيرنده'; break;
			case 'Create Faktor Error' : $err = 'خطا در ايجاد فاكتور جديد'; break;
			case 'Empty Product List Error' : $err = 'ليست محصولات اين سفارش خالي است و يا ساختار ليست محصولات براي ثبت سفارش صحيح نيست.'; break;		
			case 'not found' :
			case 'not+found' : $err = 'شماره فاکتور وجود ندارد'; break;
			case 'empty' : $err = 'شماره فاکتور وارد نشده است.'; break;			
		}		
		return $err;*/
	}

    /**
     * get states panel cod
     * @param bool $select
     * @return array|string
     */
	public function get_states($select = true) {
		if($select)
		{
			$html = '<select name="PSCA_ID_STATE" class="text" onChange="cityList(this.value);" dir="rtl" id="id_state">';
			  $html .= '<option  value="0">لطفا استان خود را انتخاب کنید</option>';
			  $html .= '<option  value="3">آذربايجان شرقي</option>';
			  $html .= '<option  value="16">آذربايجان غربي</option>';
			  $html .= '<option  value="15">اردبيل</option>';
			  $html .= '<option  value="6">اصفهان</option>';
              $html .= '<option  value="31">البرز</option>';
			  $html .= '<option  value="27">ايلام</option>';
			  $html .= '<option  value="21">بوشهر</option>';
			  $html .= '<option  value="1">تهران</option>';
			  $html .= '<option  value="24">چهارمحال بختياري</option>';
			  $html .= '<option  value="30">خراسان جنوبي</option>';
			  $html .= '<option  value="7">خراسان رضوي</option>';
			  $html .= '<option  value="29">خراسان شمالي</option>';
			  $html .= '<option  value="4">خوزستان</option>';
			  $html .= '<option  value="12">زنجان</option>';
			  $html .= '<option  value="9">سمنان</option>';
			  $html .= '<option  value="26">سيستان و بلوچستان</option>';
			  $html .= '<option  value="5">فارس</option>';
			  $html .= '<option  value="8">قزوين</option>';
			  $html .= '<option  value="10">قم</option>';
			  $html .= '<option  value="18">كردستان</option>';
			  $html .= '<option  value="22">كرمان</option>';
			  $html .= '<option  value="19">كرمانشاه</option>';
			  $html .= '<option  value="28">كهكيلويه و بويراحمد</option>';
			  $html .= '<option  value="14">گلستان</option>';
			  $html .= '<option  value="2">گيلان</option>';
			  $html .= '<option  value="20">لرستان</option>';
			  $html .= '<option  value="13">مازندران</option>';
			  $html .= '<option  value="11">مركزي</option>';
			  $html .= '<option  value="23">هرمزگان</option>';
			  $html .= '<option  value="17">همدان</option>';
			  $html .= '<option  value="25">يزد</option>';
			$html .= '</select>';
			return $html;
		}
		else{
			return	array(
				'select'=>'<select name="PSCA_ID_STATE" class="text" onChange="cityList(this.value);" dir="rtl" id="id_state">',
				'options'=>array(
                    '0'=>'لطفا استان خود را انتخاب کنید',
                    '3'=>'آذربایجان شرقی',
                    '16'=>'آذربایجان غربی',
                    '15'=>'اردبیل',
                    '6'=>'اصفهان',
                    '31'=>'البرز',
                    '27'=>'ایلام',
                    '21'=>'بوشهر',
                    '1'=>'تهران',
                    '24'=>'چهارمحال و بختیاری',
                    '30'=>'خراسان جنوبی',
                    '7'=>'خراسان رضوی',
                    '29'=>'خراسان شمالی',
                    '4'=>'خوزستان',
                    '12'=>'زنجان',
                    '9'=>'سمنان',
                    '26'=>'سیستان و بلوچستان',
                    '5'=>'فارس',
                    '8'=>'قزوین',
                    '10'=>'قم',
                    '18'=>'کردستان',
                    '22'=>'کرمان',
                    '19'=>'کرمانشاه',
                    '28'=>'کهگلویه و بویراحمد',
                    '14'=>'گلستان',
                    '2'=>'گیلان',
                    '20'=>'لرستان',
                    '13'=>'مازندران',
                    '11'=>'مرکزی',
                    '23'=>'هرمزگان',
                    '17'=>'همدان',
                    '25'=>'یزد',
                )
			);
		}
	}

	public function isAjacent($origin_id_state=false,$id_state=false ) {
		if(!$origin_id_state or !$id_state) return false;
		$ajacent = array(
            '1'=>array('9','13','31','10','11'),#'تهران'
            '2'=>array('13','8','12','15'),#'گیلان'
            '3'=>array('15','12','16'),#'آذربایجان شرقی'
            '4'=>array('27','24','28','20','21'),#'خوزستان'
            '5'=>array('21','23','22','25','6','28'),#'فارس'
            '6'=>array('25','5','9','10','11','20','28','24'),#'اصفهان'
            '7'=>array('29','30','9','25'),#'خراسان رضوی'
            '8'=>array('31','2','13','12','17','11'),#'قزوین'
            '9'=>array('29','7','14','10','1','13','6','25'),#'سمنان'
            '10'=>array('1','11','9','6'),#'قم'
            '11'=>array('1','31','8','17','20','6','10'),#'مرکزی'
            '12'=>array('2','3','8','15','16','17'.'18'),#'زنجان'
            '13'=>array('1','2','8','9','14','31'),#'مازندران'
            '14'=>array('13','9','29'),#'گلستان'
            '15'=>array('12','2','3'),#'اردبیل'
            '16'=>array('12','3','18'),#'آذربایجان غربی'
            '17'=>array('11','12','8','18','19','20'),#'همدان'
            '18'=>array('12','16','17','19'),#'کردستان'
            '19'=>array('17','18','27','20'),#'کرمانشاه'
            '20'=>array('11','17','4','6','19','27','24'),#'لرستان'
            '21'=>array('4','5','23','28'),#'بوشهر'
            '22'=>array('5','25','23','26','30'),#'کرمان'
            '23'=>array('21','22','5','26'),#'هرمزگان'
            '24'=>array('20','4','6','28'),#'چهارمحال و بختیاری'
            '25'=>array('22','5','6','7','9','30'),#'یزد'
            '26'=>array('22','23','30'),#'سیستان و بلوچستان'
            '27'=>array('19','20','4'),#'ایلام'
            '28'=>array('21','24','4','5','6'),#'کهگلویه و بویراحمد'
            '29'=>array('14','7','9'),#'خراسان شمالی'
            '30'=>array('22','25','26','7'),#'خراسان جنوبی'
            '31'=>array('1','8','11','13'),#'البرز'
		);
		if( in_array($id_state,$ajacent[$origin_id_state]) ) return true;
		return false;
	
	}	
	
	public function getOrderStates() {
		$panel = '';
		$states = array(
			/*'0'=>array(
				'title'=>'سفارش جدید',
				'icon'=>'',
				'color'=>'',
				'logable'=>true, // معتبر دانستن سفارشات وابسته
				'invoice'=>true, // اجازه به به مشتری برای دانلود و نمایش نسخه PDF صورتحساب.
				'hidden'=>false, //مخفی کردن این آمار برای مشتری
				'send_email'=>false, // ارسال رایانامه به مشتری وقتی وضعیت سفارش تغییر می‌یابد.
				'shipped'=>false,//تنظیم وضعیت سفارش به حمل شده.
				'paid'=>false,//تنظیم وضعیت سفارش به پرداخت شده.
				'delivery'=>false,//نمایش pdf تحویل
			),*/
			'100'=>array(
				'title'=>'معلق',
				'description'=>'سفارشاتی که در پنل پستی ثبت نشده است.',
				'update'=>'0',
				'options'=>array(
					'name'=>'معلق',
					'icon'=>'',
					'color'=>'#8F0621',
					'logable'=>true,
					'invoice'=>true,
					'hidden'=>false,
					'send_email'=>false,
					'shipped'=>false,
					'paid'=>false,
					'delivery'=>false,
				)
			),
			'1'=>array(
				'title'=>'معلق پستی',
				'update'=>'1',
				'options'=>array(
					'name'=>'معلق پستی '.$panel,
					'icon'=>'',
					'color'=>'#4169E1',
					'logable'=>true,
					'invoice'=>true,
					'hidden'=>false,
					'send_email'=>false,
					'shipped'=>false,
					'paid'=>false,
					'delivery'=>false,
				)
			),			
			'2'=>array(
				'title'=>'آماده به ارسال',
				'update'=>'1',
				'options'=>array(
					'name'=>'آماده به ارسال'.$panel,
					'icon'=>'',
					'color'=>'#FF8C00',
					'logable'=>true,
					'invoice'=>true,
					'hidden'=>false,
					'send_email'=>false,
					'shipped'=>false,
					'paid'=>false,
					'delivery'=>false,
				)
			),
			'3'=>array(
				'title'=>'ارسال شده',
				'update'=>'1',
				'options'=>array(
					'name'=>'ارسال شده'.$panel,
					'icon'=>'',
					'color'=>'#8A2BE2',
					'logable'=>true,
					'invoice'=>true,
					'hidden'=>false,
					'send_email'=>true,
					'shipped'=>true,
					'paid'=>false,
					'delivery'=>false,
				)
			),	
			'4'=>array(
				'title'=>'توزیع شده',
				'update'=>'1',
				'options'=>array(
					'name'=>'توزیع شده'.$panel,				
					'icon'=>'',
					'color'=>'#108510',
					'logable'=>true,
					'invoice'=>true,
					'hidden'=>false,
					'send_email'=>false,
					'shipped'=>true,
					'paid'=>true,
					'delivery'=>true
				)
			),
			'5'=>array(
				'title'=>'وصول شده',
				'update'=>'0',
				'options'=>array(
					'name'=>'وصول شده'.$panel,				
					'icon'=>'',
					'color'=>'#32CD32',
					'logable'=>true,
					'invoice'=>true,
					'hidden'=>false,
					'send_email'=>false,
					'shipped'=>true,
					'paid'=>true,
					'delivery'=>true
				)
			),
			'6'=>array(
				'title'=>'برگشتی',
				'update'=>'1',
				'options'=>array(
					'name'=>'برگشتی'.$panel,				
					'icon'=>'',
					'color'=>'#FF8C00',
					'logable'=>false,
					'invoice'=>false,
					'hidden'=>false,
					'send_email'=>false,
					'shipped'=>true,
					'paid'=>false,
					'delivery'=>false
				)
			),	
			'7'=>array(
				'title'=>'انصرافی',
				'update'=>'0',
				'options'=>array(
					'name'=>'انصرافی'.$panel,				
					'icon'=>'',
					'color'=>'#DC143C',					
					'logable'=>false,
					'invoice'=>false,
					'hidden'=>false,
					'send_email'=>false,
					'shipped'=>false,
					'paid'=>false,
					'delivery'=>false
				)
			)	
		);
		return $states;
	}
}
class PsCartCod extends PsCartFrotel{
	public function __construct(){
		parent::__construct();
	}
}