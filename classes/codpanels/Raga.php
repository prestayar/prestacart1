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
class PsCartRaga extends PsCodPanel
{
    public $nameCod = 'Raga';
    public $titleCod = 'راگا';
    public $webServiceURL = 'http://msrv.cod.ir/cod.svc?wsdl';

    public function __construct()
    {
        $this->credential = array(
            'Username'=> trim(Configuration::get('PSCA_RAGA_USERNAME')),
            'Password'=> trim(Configuration::get('PSCA_RAGA_PASSWORD')),
        );
	}

    /**
     * get info panel cod
     * @return array
     */
	public function getInfo()
	{
		return array(
			'nameCod'   => $this->nameCod,
			'titleCod'  => $this->titleCod,
			'rahgiriUrl'=> '',/*#psf#*/
			'count'     => '20'
		);
	}

    /**
     * get items setting panel cod
     * @return array
     */
	public function getItems(){
		return array(
            'PSCA_RAGA_USERNAME' =>array(
                'type'=>'text',
                'label'=>'نام کاربری',
                'error'=>'لطفا نام کاربری خود را وارد کنید',
                'required'=> true
            ),
            'PSCA_RAGA_PASSWORD' =>array(
                'type'=>'password',
                'label'=>'کلمه عبور',
                'error'=>'لطفا کلمه عبور خود را وارد کنید',
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
    public function getPostPrice( $totalprice, $weight, $id_state, $id_city )
    {
        $errors 	= array();
        try{
            $client = new SoapClient($this->webServiceURL);

            $requestData = array(
                "credential" => $this->credential,
                "calculation" => array(
                    "CityId" => $id_city,
                    "SendTypeId" => "1",// sefareshi method
                    "PaymentTypeId" => "1",// cod payment
                    "TotalPrice" => $totalprice,
                    "Weight" =>  strval( $weight ),
                    "ExactPrice" => true
                )
            );
            $response = $client->__soapCall("CalculatePrice", array("CalculatePrice" => $requestData));
        }
        catch (SoapFault $e){
            return array(
                'hasError' => true,
                'errors' => array(
                    $this->getError(false, '100')
                )
            );
        }

        if( $response->CalculatePriceResult->ResultCode >= 0   )
        {
            $priceSefareshiCarrier =(int) (
                $response->CalculatePriceResult->PostPrice+
                $response->CalculatePriceResult->ServicePrice+
                $response->CalculatePriceResult->TaxPrice);
        }
        else{
            $errors[]  = $this->getError($response->CalculatePriceResult->ResultCode, '102',$response->CalculatePriceResult->ResultMessage);
            return array(
                'hasError' => !empty($errors),
                'errors' => $errors
            );
        }

        try{
            $requestData = array(
                "credential" => $this->credential,
                "calculation" => array(
                    "CityId" => $id_city,
                    "SendTypeId" => "2",// pishtaz method
                    "PaymentTypeId" => "1",// cod payment
                    "TotalPrice" => $totalprice,
                    "Weight" =>  strval( $weight ),
                    "ExactPrice" => true
                )
            );
            $response = $client->__soapCall("CalculatePrice", array("CalculatePrice" => $requestData));

        }
        catch (SoapFault $e){
            return array(
                'hasError' => true,
                'errors' => array(
                    $this->getError(false, '100')
                )
            );
        }

        if( $response->CalculatePriceResult->ResultCode >= 0   )
        {
            $pricePishtazCarrier =(int) (
                $response->CalculatePriceResult->PostPrice+
                $response->CalculatePriceResult->ServicePrice+
                $response->CalculatePriceResult->TaxPrice);
        }
        else{
            $errors[]  = $this->getError($response->CalculatePriceResult->ResultCode, '102',$response->CalculatePriceResult->ResultMessage);
            return array(
                'hasError' => !empty($errors),
                'errors' => $errors
            );
        }

        return array(
            'hasError' => !empty($errors),
            'errors' => $errors,
            'pishtaz' => $pricePishtazCarrier ,
            'sefareshi' => $priceSefareshiCarrier
        );
    }

    /**
     * Register order on cod
     * @param array $options
     * @return array
     */
    public function registerOrder($options = array())
    {
        $errors = array();
        try{
            $options['sendType'] = ( $options['sendType'] == 'pishtaz' ) ? '2'  : '1';
            $options['gender'] = ( $options['id_gender'] == '2' ) ? 'Female'  : 'Male';

            if( isset($options['cartAdmin']) )
                $products = $this->_generateProductsAdmin($options['cartAdmin']);
            else
                $products = $this->_generateProducts($options['cart']);

            // parmas post webserivce
            $requestData = array(
                "credential" => $this->credential,
                "order" => array(
                    "Address" => $options['address'],
                    "Description" => $options['description'],
                    "DestinationCityId" => $options['id_city'],
                    "Email" => $options['email'],
                    "FirstName" => $options['fname'],
                    "Gender" => $options['gender'] ,
                    "IpAddress" => $this->_getIP(),
                    "LastName" => $options['lname'],
                    "MarketPartnerId" => null,
                    "Message" => $options['description'],
                    "Mobile" => $options['mobile'],
                    "PaymentTypeId" => "1",// cod payment
                    "PostalCode" => $options['postcode'],
                    "SendTypeId" => $options['sendType'],
                    "Telephone" => $options['mobile'],
                    "OrderDetails" => $products,
                )
            );

            // send request webservice
            $client = new SoapClient($this->webServiceURL);
            $response = $client->__soapCall("CreateOrder", array("CreateOrder" => $requestData));

            // process result
            if( isset( $response->CreateOrderResult->ResultCode) )
            {
                if ($response->CreateOrderResult->ResultCode >= 0)
                {
                    return array(
                        'hasError' => !empty($errors),
                        'errors' => $errors,
                        'rahgiriCod' => $response->CreateOrderResult->OrderId
                    );
                }
                else {
                    $errors[] = $this->getError($response->CreateOrderResult->ResultCode, '103',$response->CreateOrderResult->ResultMessage);
                    return array(
                        'hasError' => !empty($errors),
                        'errors' => $errors,
                        'message' => $this->getError($response->CreateOrderResult->ResultCode, '103', $response->CreateOrderResult->ResultMessage, false)
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

    public function GetStatus($id_order_raga = null)
    {
        $result = array();
        if($id_order_raga)
        {
            try{
                // parmas post webserivce
                $requestData = array(
                    "credential" => $this->credential,
                    "status" => array(
                        "OrderId" => (float) $id_order_raga
                    )
                );

                // send request webservice
                $client = new SoapClient($this->webServiceURL);
                $response = $client->__soapCall("GetStatus", array("GetStatus" => $requestData));

                if( isset( $response->GetStatusResult->ResultCode) )
                {
                    if ($response->GetStatusResult->ResultCode >= 0)
                    {
                        return array(
                            'result'    => true,
                            'state'     => $response->GetStatusResult->OrderStatusId,
                            'state_name'=> $response->GetStatusResult->OrderStatusTitle,
                            'cod_post' 	=> $response->GetStatusResult->ShippingCode
                        );
                    }
                    else{
                        return array(
                            'result' => false,
                            'message' => $this->getError($response->GetStatusResult->ResultCode, '104', $response->GetStatusResult->ResultMessage,false)
                        );
                    }
                }
            }
            catch (SoapFault $e){
                $result['result'] = false;
                $result['message'] = 'خطا در اتصال به وب سرویس';
            }
        }
        return $result;
    }
    public function GetListStatus($items)
    {
        foreach($items as $key => $item){
            $result = $this->GetStatus($item['cod_tracking_number']);
            $items[$key]['result'] = $result ;
        }
        return $items;
    }

    private function _getIP()
    {
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
        $Currrency = new Currency();
        $rial_id = $Currrency->getIdByIsoCode('IRR');
        $rial = new Currency($rial_id);
        $currentCur = new Currency($cart->id_currency);
        $cart_rules = $cart->getCartRules();
        foreach ($cart_rules as $cart_rule){
            if( $cart_rule['reduction_percent'] > 0){
                if($cart_rule['reduction_product'] == '0'){
                    foreach ($products as &$product) {
                        $product['price_wt'] -= ($product['price_wt'])*($cart_rule['reduction_percent']/100);
                    }
                }elseif($cart_rule['reduction_product'] > '0'){
                    foreach ($products as &$product) {
                        if($product['id_product'] == $cart_rule['reduction_product'])
                            $product['price_wt'] -= ($product['price_wt'])*($cart_rule['reduction_percent']/100);
                    }
                }
            }elseif( $cart_rule['reduction_amount'] > 0){
                if($cart_rule['reduction_product'] == '0'){
                    foreach ($products as &$product)
                        if( $product['price_wt'] > $cart_rule['reduction_amount'] )
                            $product['price_wt'] -= $cart_rule['reduction_amount'];
                }elseif($cart_rule['reduction_product'] > '0'){
                    foreach ($products as &$product) {
                        if($product['id_product'] == $cart_rule['reduction_product']
                            and $product['price_wt'] > $cart_rule['reduction_amount'])
                            $product['price_wt'] -= $cart_rule['reduction_amount'];
                    }
                }
            }elseif( $cart_rule['gift_product'] > 0){
                foreach ($products as &$product){
                    if($product['id_product'] == $cart_rule['gift_product']){
                        $product['price_wt'] = 0;
                    }
                }
            }

        }

        $weightDefault = Configuration::get('PSCA_WEIGHT_DEFAULT');
        $orders = array();
        foreach ($products as $product){
            if($rial->id!=$currentCur->id)
                $price = (int)Tools::convertPriceFull((int)$product['price_wt'], $currentCur, $rial);
            else
                $price =(int)$product['price_wt'];

            if( !(int)$product['weight'] )
                $weight = (int)$weightDefault;
            else
                $weight = (int)$product['weight'];

            $orders[] = array(
                "ProductId" => $product['id_product'],
                "Amount" => $product['quantity'],
                "Title" => $product['name'],
                "Price" => $price,
                "Weight" => $weight,
            );
        }
        return $orders;
    }
    private function _generateProductsAdmin( $options )
    {
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
        $orders = array();
        foreach ($products as $product){
            if($rial->id!=$currentCur->id)
                $price = (int)Tools::convertPriceFull((int)$product['product_price_wt'], $currentCur, $rial);
            else
                $price =(int)$product['product_price_wt'];

            if( !(int)$product['product_weight'] )
                $weight = (int)$weightDefault;
            else
                $weight = (int)$product['product_weight'];

            $orders[] = array(
                "ProductId" => $product['id_product'],
                "Amount" => $product['product_quantity'],
                "Title" => $product['product_name'],
                "Price" => $price,
                "Weight" => $weight,
            );
        }
        return $orders;

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
			'200'=>array(
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
				'title'=>'تحت بررسی',
				'update'=>'1',
				'options'=>array(
					'name'=>'تحت بررسی'.$panel,
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
                'title'=>'اشتباه در آماده به ارسال',
                'update'=>'1',
                'options'=>array(
                    'name'=>'اشتباه در آماده به ارسال'.$panel,
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
            '4'=>array(
                'title'=>'عدم حضور مدیرفروشگاه',
                'update'=>'1',
                'options'=>array(
                    'name'=>'عدم حضور مدیرفروشگاه'.$panel,
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
			'5'=>array(
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
            '6'=>array(
                'title'=>'عدم قبول',
                'update'=>'0',
                'options'=>array(
                    'name'=>'عدم قبول'.$panel,
                    'icon'=>'',
                    'color'=>'#4169E1',
                    'logable'=>true,
                    'invoice'=>true,
                    'hidden'=>true,
                    'send_email'=>false,
                    'shipped'=>true,
                    'paid'=>false,
                    'delivery'=>true
                )
            ),
			'7'=>array(
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
            '8'=>array(
                'title'=>'باجه معطله',
                'update'=>'1',
                'options'=>array(
                    'name'=>'باجه معطله'.$panel,
                    'icon'=>'',
                    'color'=>'#FF69B4',
                    'logable'=>true,
                    'invoice'=>true,
                    'hidden'=>false,
                    'send_email'=>false,
                    'shipped'=>true,
                    'paid'=>false,
                    'delivery'=>true,
                )
            ),
            '9'=>array(
                'title'=>'غیرقابل توزیع',
                'update'=>'1',
                'options'=>array(
                    'name'=>'غیرقابل توزیع'.$panel,
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
            '10'=>array(
                'title'=>'پیش برگشتی',
                'update'=>'1',
                'options'=>array(
                    'name'=>'پیش برگشتی'.$panel,
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
            '11'=>array(
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
            '70'=>array(
				'title'=>'تایید شده مدیرمالی',
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
            '71'=>array(
                'title'=>'تسویه شده مدیرمالی',
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
			'100'=>array(
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
class PsCartCod extends PsCartRaga{
	public function __construct(){
		parent::__construct();
	}
}