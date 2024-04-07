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
class PsCartParsKasb extends PsCodPanel
{
    public $username;
    public $password;

    public $nameCod = 'ParsKasb';
    public $titleCod = 'پنل واسطه پارس کسب';
    public $webServiceURL = 'http://parskasb.com/webservice/server.wsdl?';

    public function __construct()
    {
        $this->username = trim(Configuration::get('PSCA_PARSKASB_USERNAME'));
        $this->password = trim(Configuration::get('PSCA_PARSKASB_PASSWORD'));
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
            'rahgiriUrl'=>'http://rahgiri.safircod.ir/',
            'count'     =>'20'
        );
    }

    /**
     * get items setting panel cod
     * @return array
     */
	public function getItems(){
		return array(
            'PSCA_PARSKASB_USERNAME' =>array(
                'type'=>'text',
                'label'=>'نام کاربری',
                'error'=>'لطفا نام کاربری خود را وارد کنید',
                'required'=> true
            ),
            'PSCA_PARSKASB_PASSWORD' =>array(
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
		$errors = array();
        $pishtaz = 1;
        $sefareshi = 2;		
		// connect to webservice
		try{
			// pishtaz
			ini_set("soap.wsdl_cache_enabled", "0");
			$client = new SoapClient($this->webServiceURL, array('soap_version' => SOAP_1_2,'trace' => 1 ));
			$order = "<price>,".$totalprice.",</price> <weight>,".$weight.",</weight> <tedad>,1,</tedad> <pid></pid> 
					<o>".$id_state."</o> <s>".$id_city."</s> <u>".$this->username."</u> <ps>".$this->password."</ps> <type>".$pishtaz."</type>  "; 
			
			$Res = $client->__soapCall("Price", Array ($order)); 
			if($Res){
				$pishtazCarrier = (int) $Res;
			}else{
                $errors[]  = $this->getError(null, '102', $Res);
                return array(
                    'hasError' => !empty($errors),
                    'errors' => $errors
                );
			}
			// sefareshi
			$order = "<price>,".$totalprice.",</price> <weight>,".$weight.",</weight> <tedad>,1,</tedad> <pid></pid> 
					<o>".$id_state."</o> <s>".$id_city."</s> <u>".$this->username."</u> <ps>".$this->password."</ps> <type>".$sefareshi."</type>  "; 
			
			$Res = $client->__soapCall("Price", Array ($order)); 
			if($Res){
				$sefareshiCarrier = (int) $Res;
			}else{
                $errors[]  = $this->getError(null, '102', $Res);
                return array(
                    'hasError' => !empty($errors),
                    'errors' => $errors
                );
			}				
			
		}
		catch (SoapFault $e){
            return array(
                'hasError' => true,
                'errors' => array(
                    $this->getError(false, '100')
                )
            );
		}
        return array(
            'hasError'  => false,
            'sefareshi' => $sefareshiCarrier,
            'pishtaz'   => $pishtazCarrier
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
        if( $options['sendType'] == 'pishtaz' )
            $options['sendType'] ='1';
        else
            $options['sendType'] ='2';

		$options['phone'] = ($options['phone']!='')?$options['phone']:$options['mobile'];
		
		if(isset($options['cartAdmin'])) 
			$products = $this->_generateProductsAdmin($options['cartAdmin']);
		else
			$products = $this->_generateProducts($options['cart']);		
		
		try{
			ini_set("soap.wsdl_cache_enabled", "0");
			$client = new SoapClient($this->webServiceURL, array('soap_version' => SOAP_1_2,'trace' => 1 ));
			$order = "<n>{$options['lname']}</n>
						<f>,</f>
						<u>{$this->username}</u>
						<ps>{$this->password}</ps>
						<t>{$options['phone']}</t>
						<m>{$options['mobile']}</m>
						<e>{$options['email']}</e>
						<z>{$options['postcode']}</z>
						<a>{$options['address']}</a>
						<o>{$options['id_state']}</o>
						<s>{$options['id_city']}</s>
						<pname>,{$products['pname']}</pname>
						<price>,{$products['price']}</price>
						<weight>,{$products['weight']}</weight>
						<tedad>,{$products['tedad']}</tedad>
						<type>{$options['sendType']}</type>
						<card>0</card>
						";#		
			$Res = $client->__soapCall("Buy", Array ($order)); 	
			
			if((int)$Res<10)
			{
                $errors[]  = $this->getError($Res, '103');
                return array(
                    'hasError' => !empty($errors),
                    'errors' => $errors,
                    'message' => $this->getError($Res, '103', null,false)
                );
			}
			else{
                return array(
                    'hasError'  => !empty($errors),
                    'errors'    => $errors,
                    'rahgiriCod' => $Res
                );
			}						
												
		}
		catch (SoapFault $e){
            $errors[]  = $this->getError(false, '100', null);
            return array(
                'hasError' => !empty($errors),
                'errors' => $errors,
                'message' => $this->getError(false, '100', null, false)
            );
		}   
		return $return;
    }
    
	public function GetStatus($id_order_psk = null){
		if($id_order_psk){
			try{				
				ini_set("soap.wsdl_cache_enabled", "0");
				$client = new SoapClient($this->webServiceURL, array('soap_version' => SOAP_1_2,'trace' => 1 ));
				$order = "<shenase>{$id_order_psk}</shenase>
							<show>1</show>
							<rs>1</rs>
							";#		
				$state = $client->__soapCall("Status", Array ($order));

				if( $state == ',' or $state == '0,' or $state < 0  ){
					$result['result'] = false;
					$result['message'] = 'خطا در تعیین وضعیت :: ('. $state  .')';
				}else{
					$data = explode(',',$state);
					$result = array(
						'result'	=> true,
						'state' 	=> $data['0'],
						'cod_post' 	=> $data['1'],
					);
                    if( isset($data['2']) )
                        $result ['ensraf'] =  $data['2'];
				}
			}catch (SoapFault $e){
				$result['result'] = false;
				$result['message'] = 'خطا در اتصال به وب سرویس';
			}
			
			return $result;
		}
	}	
	public function GetListStatus($items){

		foreach($items as $key => $item){
			$result = $this->GetStatus($item['cod_tracking_number']);
			if($result){
				$res = $result;			
			}else{
				$res = array('result'=> false);				
			}
			$items[$key]['result']=$result ;
		}
		#echo '<pre>';var_dump($items);die;
		return $items;
		
		$data = "";
		foreach($items as $key => $item){
			$data .= $item['cod_tracking_number'].",";
		}
		$data = trim($data, ',');	
		try{				
				ini_set("soap.wsdl_cache_enabled", "0");
				$client = new SoapClient($this->client_url, array('soap_version' => SOAP_1_2,'trace' => 1 )); 
				$order = "<shenase>".$data."</shenase><show>1</show><rs>1</rs>";
				$state = $client->__soapCall("MultiStatus", Array ($order)); 					
				echo '<pre>';var_dump($order);die;
				
				if( $state == ',' or $state == '0,' or $state < 0  ){
					$result['result'] = false;
					$result['message'] = 'خطا در تعیین وضعیت :: ('. $state  .')';
				}else{
					$data = explode(',',$state);
					$result = array(
						'result'	=> true,
						'state' 	=> $data['0'],
						'cod_post' 	=> $data['1'],
						'ensraf' 	=> $data['2'],
					);
				}
		}catch (SoapFault $e){
				$result['result'] = false;
				$result['message'] = 'خطا در اتصال به وب سرویس';
		}
		return $result;
		
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
    private function _generateProducts($cart){
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
		$orders = array(
					'tedad'=>'',#',',
					'pname'=>'',#',',
					'weight'=>'',#',',
					'price'=>'',#',',
		);
        foreach ($products as $product){
            if($rial->id!=$currentCur->id)
				$price = (int)Tools::convertPriceFull((int)$product['price_wt'], $currentCur, $rial);
            else
				$price =(int)$product['price_wt'];

            if( !(int)$product['weight'] )
                $weight = (int)$weightDefault;
            else
                $weight = (int)$product['weight'];

				
            $orders['tedad'] .= $product['quantity'].',';
			$orders['pname'] .= $product['name'].',';
            $orders['weight'] .= $weight .',';
			$orders['price'] .= $price.',';
        }
		
        return $orders;
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
		$orders = array(
			'tedad'=>'',
			'pname'=>'',
			'weight'=>'',
			'price'=>'',
		);
        foreach ($products as $product){
            if($rial->id!=$currentCur->id)
				$price = (int)Tools::convertPriceFull((int)$product['product_price_wt'], $currentCur, $rial);
            else
				$price =(int)$product['product_price_wt'];
            if( !(int)$product['product_weight'] )
                $weight = (int)$weightDefault;
            else
                $weight = (int)$product['product_weight'];
				
            $orders['tedad']  .= $product['product_quantity'].',';
			$orders['pname']  .= $product['product_name'].',';
			$orders['weight'] .= $weight.',';
			$orders['price']  .= $price.',';
        }		
        return $orders ;
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
                case '1' : $error .= 'کد کاربری ویا رمزعبور صحیح نیست'; break;
                case '3' : $error .= 'شناسه شهر و استان اشتباه است.'; break;
                case '4' : $error .= 'وارد کردن نام و نام خانوادگی و آدرس  الزامی است.'; break;
                case '5' : $error .= 'وارد کردن تلفن و موبایل الزامی است.'; break;
                case '7' : $error .= 'وارد کردن وزن ، تعداد ، قیمت و نام محصول الزامی است.'; break;
                case '8' : $error .= 'نوع ارسال بدرستی وارد نشده است.'; break;
                case '9' :
                    $error .= 'شناسه سفارش معتبر نیست.';
                    break;
                default :
                    $error .= 'نامشخص';
                    break;
            }
            $error .= ' )';
        }
        return $error;
	}

	public function get_states($select = true) {
		if($select){
			$html = '<select name="PSCA_ID_STATE" class="text" onChange="cityList(this.value);" dir="rtl" id="id_state">';
			  $html .= '<option  value="0">لطفا استان خود را انتخاب کنید</option>';
			  $html .= '<option  value="41">آذربايجان شرقي</option>';
			  $html .= '<option  value="44">آذربايجان غربي</option>';
			  $html .= '<option  value="45">اردبيل</option>';
			  $html .= '<option  value="31">اصفهان</option>';
			  $html .= '<option  value="84">ايلام</option>';
			  $html .= '<option  value="77">بوشهر</option>';
			  $html .= '<option  value="26">البرز</option>';
			  $html .= '<option  value="21">تهران</option>';
			  $html .= '<option  value="38">چهارمحال بختياري</option>';
			  $html .= '<option  value="56">خراسان جنوبي</option>';
			  $html .= '<option  value="51">خراسان رضوي</option>';
			  $html .= '<option  value="58">خراسان شمالي</option>';
			  $html .= '<option  value="61">خوزستان</option>';
			  $html .= '<option  value="24">زنجان</option>';
			  $html .= '<option  value="23">سمنان</option>';
			  $html .= '<option  value="54">سيستان و بلوچستان</option>';
			  $html .= '<option  value="71">فارس</option>';
			  $html .= '<option  value="28">قزوين</option>';
			  $html .= '<option  value="25">قم</option>';
			  $html .= '<option  value="87">كردستان</option>';
			  $html .= '<option  value="34">كرمان</option>';
			  $html .= '<option  value="83">كرمانشاه</option>';
			  $html .= '<option  value="74">كهكيلويه و بويراحمد</option>';
			  $html .= '<option  value="17">گلستان</option>';
			  $html .= '<option  value="13">گيلان</option>';
			  $html .= '<option  value="66">لرستان</option>';
			  $html .= '<option  value="15">مازندران</option>';
			  $html .= '<option  value="86">مركزي</option>';
			  $html .= '<option  value="76">هرمزگان</option>';
			  $html .= '<option  value="81">همدان</option>';
			  $html .= '<option  value="35">يزد</option>';
			$html .= '</select>';
			return $html;
		}
		else{
			return	array(
				'select'=>'<select name="PSCA_ID_STATE" class="text" onChange="cityList(this.value);" dir="rtl" id="id_state">',
				'options'=>array(
                    '0'=>'لطفا استان خود را انتخاب کنید',
                    '41'=>'آذربايجان شرقي',
                    '44'=>'آذربايجان غربي',
                    '45'=>'اردبيل',
                    '31'=>'اصفهان',
                    '84'=>'ايلام',
                    '77'=>'بوشهر',
                    '26'=>'البرز',
                    '21'=>'تهران',
                    '38'=>'چهارمحال بختياري',
                    '56'=>'خراسان جنوبي',
                    '51'=>'خراسان رضوي',
                    '58'=>'خراسان شمالي',
                    '61'=>'خوزستان',
                    '24'=>'زنجان',
                    '23'=>'سمنان',
                    '54'=>'سيستان و بلوچستان',
                    '71'=>'فارس',
                    '28'=>'قزوين',
                    '25'=>'قم',
                    '87'=>'كردستان',
                    '34'=>'كرمان',
                    '83'=>'كرمانشاه',
                    '74'=>'كهكيلويه و بويراحمد',
                    '17'=>'گلستان',
                    '13'=>'گيلان',
                    '66'=>'لرستان',
                    '15'=>'مازندران',
                    '86'=>'مركزي',
                    '76'=>'هرمزگان',
                    '81'=>'همدان',
                    '35'=>'يزد'
				)
			);
		}
	}
	public function isAjacent($origin_id_state=false,$id_state=false ) {
		if(!$origin_id_state or !$id_state) return false;
		$ajacent = array(
			'21'=>array('23','15','26','25','86'),#'تهران'
			'13'=>array('15','28','24','45'),#'گیلان'
			'41'=>array('45','24','44'),#'آذربایجان شرقی'
			'61'=>array('84','38','74','66','77'),#'خوزستان'
			'71'=>array('77','76','34','35','31','74'),#'فارس'
			'31'=>array('35','71','23','25','86','66','74','38'),#'اصفهان'
			'51'=>array('58','56','23','35'),#'خراسان رضوی'
			'28'=>array('26','13','15','24','81','86'),#'قزوین'
			'23'=>array('58','51','17','25','21','15','31','35'),#'سمنان'
			'25'=>array('21','86','23','31'),#'قم'
			'86'=>array('21','26','28','81','66','31','25'),#'مرکزی'
			'24'=>array('13','41','28','45','44','81'.'87'),#'زنجان'
			'15'=>array('21','13','28','23','17','26'),#'مازندران'
			'17'=>array('15','23','58'),#'گلستان'
			'45'=>array('24','13','41'),#'اردبیل'
			'44'=>array('24','41','87'),#'آذربایجان غربی'
			'81'=>array('86','24','28','87','83','66'),#'همدان'
			'87'=>array('24','44','81','83'),#'کردستان'
			'83'=>array('81','87','84','66'),#'کرمانشاه'
			'66'=>array('86','81','61','31','83','84','38'),#'لرستان'
			'77'=>array('61','71','76','74'),#'بوشهر'
			'34'=>array('71','35','76','54','56'),#'کرمان'
			'76'=>array('77','34','71','54'),#'هرمزگان'
			'38'=>array('66','61','31','74'),#'چهارمحال و بختیاری'
			'35'=>array('34','71','31','51','23','56'),#'یزد'
			'54'=>array('34','76','56'),#'سیستان و بلوچستان'
			'84'=>array('83','66','61'),#'ایلام'
			'74'=>array('77','38','61','71','31'),#'کهگلویه و بویراحمد'
			'58'=>array('17','51','23'),#'خراسان شمالی'
			'56'=>array('34','35','54','51'),#'خراسان جنوبی'
			'26'=>array('21','28','86','15'),#'البرز'	
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
			'0'=>array(
				'title'=>'معلق',
				'update'=>'1',
				'options'=>array(
					'name'=>'معلق'.$panel,
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
			'1'=>array(
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
			'2'=>array(
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
			'3'=>array(
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
			'4'=>array(
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
			'5'=>array(
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
			'6'=>array(
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

class PsCartCod extends PsCartParsKasb{
	public function __construct(){
		parent::__construct();
	}	
}