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
class PsCartParsPeik extends PsCodPanel
{
    public $username;
    public $password;
    public $shopID;

    public $nameCod = 'ParsPeik';
    public $titleCod = 'پنل واسطه پارس پیک';
    public $webServiceURL = 'http://p24.ir/ws/pws.php?wsdl';

    public function __construct()
    {
        $this->username = trim(Configuration::get('PSCA_PARSPEIK_USERNAME'));
        $this->password = trim(Configuration::get('PSCA_PARSPEIK_PASSWORD'));
        $this->shopID = trim(Configuration::get('PSCA_PARSPEIK_SHOP'));

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
            'rahgiriUrl'=>'',
            'count'     =>'20'
        );
    }

    /**
     * get items setting panel cod
     * @return array
     */
	public function getItems(){
		return array(
            'PSCA_PARSPEIK_USERNAME' =>array(
                'type'=>'text',
                'label'=>'کد همکاری',
                'error'=>'لطفا  کد همکاری خود را وارد کنید',
                'required'=> true
            ),
            'PSCA_PARSPEIK_SHOP' =>array(
                'type'=>'text',
                'label'=>'کد فروشگاه',
                'error'=>'لطفا  کد فروشگاه خود را وارد کنید',
                'required'=> true
            ),
            'PSCA_PARSPEIK_PASSWORD' =>array(
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
            ),
					
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
		try{
			$client = new SoapClient($this->webServiceURL);
            $result = $client->delivery_price($weight , $totalprice , $id_state , $id_city  , $this->username , $this->shopID , $this->password);
			
            if(!$result or strlen($result->post_sef)=='3')
			{
			    $codeError = ( !$result ) ? null:$result->post_sef;
                $errors[]  = $this->getError($codeError, '102');
                return array(
                    'hasError' => !empty($errors),
                    'errors' => $errors
                );
			}
			else{
                $pricePishtazCarrier = (int) (int)$result->post_pish + ($result->kala_khadamat_pish - $totalprice);
                $priceSefareshiCarrier = (int) (int)$result->post_sef + ($result->kala_khadamat_sef - $totalprice);
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
    public function registerOrder( $options = array() )
    {
		$errors = array();
        if( $options['sendType'] == 'pishtaz' )
            $options['sendType'] ='1';
        else
            $options['sendType'] ='0';

		if(isset($options['cartAdmin'])) 
			$products = $this->_generateProductsAdmin($options['cartAdmin']);
		else
			$products = $this->_generateProducts($options['cart']);	
		
		try{
			$client = new SoapClient($this->webServiceURL);
            if( !$options['postcode'] or strpos($options['postcode'], "2") or strpos($options['postcode'], "0") ) $options['postcode'] ='1111111111';
            $result = $client->order_register(
                $products,
                $options['lname'],
                $options['email'],
                $options['mobile'],
                $options['mobile'],
                $options['id_state'],
                $options['id_city'],
                $options['postcode'],
                $options['address'],
                $options['sendType'],
                $options['description'],
                $this->_getIP(),
                $this->username,
                $this->shopID,
                $this->password
			);

			if(!$result or strlen($result)!='27')
			{
                $errors[]  = $this->getError($result , '103');
                return array(
                    'hasError' => !empty($errors),
                    'errors' => $errors,
                    'message' => $this->getError($result, '103', null, false)
                );
			}
			else{
                return array(
                    'hasError'  => !empty($errors),
                    'errors'    => $errors,
                    'rahgiriCod' => $result
                );
			}										
		}catch (SoapFault $e){
            $errors[]  = $this->getError(false, '100', null);
            return array(
                'hasError' => !empty($errors),
                'errors' => $errors,
                'message' => $this->getError(false, '100', null, false)
            );
		}
    }
    
	public function GetStatus($id_order_psPeik = null){
		return false;
	}	
	public function GetListStatus($items){
		foreach($items as $key => $item){
			$result = $this->GetStatus($item['cod_tracking_number']);
			if($result){
				$res = $result;			
			}else{
				$res = array('result' 	=> false);				
			}
			$items[$key]['result']=$result ;
		}
		return $items;
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
		$orders = array();
        foreach ($products as $product){
            if($rial->id!=$currentCur->id)
				$price = (int)Tools::convertPriceFull((int)$product['price_wt'], $currentCur, $rial).';';
            else
				$price = (int)$product['price_wt'].';';

            if( !(int)$product['weight'] )
                $weight = (int)$weightDefault;
            else
                $weight = (int)$product['weight'];


			$orders[] = array(
                'id' => $product['id_product'] ,
                'prd_name' => $product['name'] ,
                'weight' => $weight ,
                'price' => $price,
                'quantity' => (int)$product['quantity']
            );
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
		$orders = array();
        foreach ($products as $product){
            if($rial->id!=$currentCur->id)
				$price = (int)Tools::convertPriceFull((int)$product['product_price_wt'], $currentCur, $rial).';';
            else
				$price = (int)$product['product_price_wt'].';';

            if( !(int)$product['weight'] )
                $weight = (int)$weightDefault;
            else
                $weight = (int)$product['product_weight'];

			$orders[] = array(
                'id' => $product['id_product'] ,
                'prd_name' => $product['product_name'] ,
                'weight' => $weight ,
                'price' => $price,
                'quantity' => (int)$product['product_quantity']
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
                case 101 : $error .= 'عدم احراز هویت فروشگاه'; break;

                ## Add Products
                case 201 : $error .= 'کالایی با کد ذکر شده وجود ندارد - محصولات ارسال نشده است'; break;
                case 202 : $error .= 'محصول غیر فعال است - قیمت و یا وزن کمتر از یک است و یا نام محصول خالی است'; break;
                case 203 : $error .= 'محصول برای این فروشگاه غیر قابل فروش است'; break;
                case 301 : $error .= 'کد سفارش موقت در پارامتر های ورودی وجود ندارد'; break;
                case 225 : $error .= 'پارامتر های تخفیف درست تعریف نشده است'; break;

                ## Change Quantity
                case 402 : $error .= 'تعداد محصول کمتر از یک وارد شده است'; break;

                ## Temp Register
                case 601 : $error .= 'کد استان یا شهرستان خالی یا مساوی صفر است و یا کد شهرستان با استان هماهنگ نیست'; break;
                case 602 : $error .= 'سفارشی با این کد سفارش موقت به ثبت نرسیده است'; break;
                case 603 : $error .= 'نام و نام خانوادگی وارد نشده است'; break;
                case 604 : $error .= 'ایمیل نامعتبر است'; break;
                case 605 : $error .= 'شماره تلفن نامعتبر است'; break;
                case 606 : $error .= 'موبایل نامعتبر است'; break;
                case 607 : $error .= 'کد پستی نامعتبر است'; break;
                case 608 : $error .= 'آدرس خالی است'; break;
                case 609 : $error .= 'نوع ارسال صحیح وارد نشده است'; break;
                case 611 : $error .= 'آی پی وارد نشده است'; break;

                ## Add Products To Shop
                case 701 : $error .= 'قیمت کالا کمتر از یک ریال است'; break;
                case 702 : $error .= 'وزن کالا کمتر از یک گرم است'; break;
                case 703 : $error .= 'طول نام بزرگتر از 50 کاراکتر است'; break;

                ## Remove/Modify Products From Shop
                case 704 : $error .= 'کالایی با کد ذکر شده وجود ندارد'; break;

                default :
                    $error .= $messageResult;
            }
            $error .= ' )';
        }
        return $error;

    }

	public function get_states($select = true)
    {
		if($select)
		{
			$html = '<select name="PSCA_ID_STATE" id="id_state" onchange="cityList(this.value);">';
				$html .= '<option value="0">لطفا استان خود را انتخاب کنید...</option>';
				$html .= '<option value="1700">آذربایجان شرقی</option>';
				$html .= '<option value="1800">آذربایجان غربی</option>';
				$html .= '<option value="2000">اردبیل</option>';
				$html .= '<option value="2100">اصفهان</option>';
				$html .= '<option value="3100">البرز</option>';
				$html .= '<option value="1900">ایلام</option>';
				$html .= '<option value="2200">بوشهر</option>';
				$html .= '<option value="2300">تهران</option>';
				$html .= '<option value="1100">چهارمحال بختیاری</option>';
				$html .= '<option value="2600">خراسان جنوبی</option>';
				$html .= '<option value="2500">خراسان رضوی</option>';
				$html .= '<option value="2700">خراسان شمالی</option>';
				$html .= '<option value="2400">خوزستان</option>';
				$html .= '<option value="2800">زنجان</option>';
				$html .= '<option value="2900">سمنان</option>';
				$html .= '<option value="3000">سیستان و بلوچستان</option>';
				$html .= '<option value="100">فارس</option>';
				$html .= '<option value="300">قزوین</option>';
				$html .= '<option value="200">قم</option>';
				$html .= '<option value="700">كردستان</option>';
				$html .= '<option value="500">كرمان</option>';
				$html .= '<option value="600">كرمانشاه</option>';
				$html .= '<option value="400">كهكیلویه و بویر احمد</option>';
				$html .= '<option value="1500">گلستان</option>';
				$html .= '<option value="1600">گیلان</option>';
				$html .= '<option value="800">لرستان</option>';
				$html .= '<option value="900">مازندران</option>';
				$html .= '<option value="1000">مركزی</option>';
				$html .= '<option value="1300">هرمزگان</option>';
				$html .= '<option value="1200">همدان</option>';
				$html .= '<option value="1400">یزد</option>';
			$html .= '</select>';
			return $html;
		}
		else{
			return	array(
				'select'=>'<select onchange="'."cityList(this.value);".'" id="id_state" name="PSCA_ID_STATE">',
				'options'=>array(
							'0'=>'لطفا استان خود را انتخاب کنید...',
							'1700'=>'آذربایجان شرقی',
							'1800'=>'آذربایجان غربی',
							'2000'=>'اردبیل',
							'2100'=>'اصفهان',
							'3100'=>'البرز',
							'1900'=>'ایلام',
							'2200'=>'بوشهر',
							'2300'=>'تهران',
							'1100'=>'چهارمحال بختیاری',
							'2600'=>'خراسان جنوبی',
							'2500'=>'خراسان رضوی',
							'2700'=>'خراسان شمالی',
							'2400'=>'خوزستان',
							'2800'=>'زنجان',
							'2900'=>'سمنان',
							'3000'=>'سیستان و بلوچستان',
							'100'=>'فارس',
							'300'=>'قزوین',
							'200'=>'قم',
							'700'=>'كردستان',
							'500'=>'كرمان',
							'600'=>'كرمانشاه',
							'400'=>'كهكیلویه و بویر احمد',
							'1500'=>'گلستان',
							'1600'=>'گیلان',
							'800'=>'لرستان',
							'900'=>'مازندران',
							'1000'=>'مركزی',
							'1300'=>'هرمزگان',
							'1200'=>'همدان',
							'1400'=>'یزد',
				)
			);	
		}
	}

	public function isAjacent($origin_id_state=false,$id_state=false ) {
		if(!$origin_id_state or !$id_state) return false;
		$ajacent = array(
			'2300'=>array('2900','900','3100','200','1000'),#'تهران'
			'1600'=>array('900','300','2800','2000'),#'گیلان'
			'1700'=>array('2000','2800','1800'),#'آذربایجان شرقی'
			'2400'=>array('1900','1100','400','800','2200'),#'خوزستان'
			'100'=>array('2200','1300','500','1400','2100','400'),#'فارس'
			'2100'=>array('1400','100','2900','200','1000','800','400','1100'),#'اصفهان'
			'2500'=>array('2700','2600','2900','1400'),#'خراسان رضوی'
			'300'=>array('3100','1600','900','2800','1200','1000'),#'قزوین'
			'2900'=>array('2700','2500','1500','200','2300','900','2100','1400'),#'سمنان'
			'200'=>array('2300','1000','2900','2100'),#'قم'
			'1000'=>array('2300','3100','300','1200','800','2100','200'),#'مرکزی'
			'2800'=>array('1600','1700','300','2000','1800','1200'.'700'),#'زنجان'
			'900'=>array('2300','1600','300','2900','1500','3100'),#'مازندران'
			'1500'=>array('900','2900','2700'),#'گلستان'
			'2000'=>array('2800','1600','1700'),#'اردبیل'
			'1800'=>array('2800','1700','700'),#'آذربایجان غربی'
			'1200'=>array('1000','2800','300','700','600','800'),#'همدان'
			'700'=>array('2800','1800','1200','600'),#'کردستان'
			'600'=>array('1200','700','1900','800'),#'کرمانشاه'
			'800'=>array('1000','1200','2400','2100','600','1900','1100'),#'لرستان'
			'2200'=>array('2400','100','1300','400'),#'بوشهر'
			'500'=>array('100','1400','1300','3000','2600'),#'کرمان'
			'1300'=>array('2200','500','100','3000'),#'هرمزگان'
			'1100'=>array('800','2400','2100','400'),#'چهارمحال و بختیاری'
			'1400'=>array('500','100','2100','2500','2900','2600'),#'یزد'
			'3000'=>array('500','1300','2600'),#'سیستان و بلوچستان'
			'1900'=>array('600','800','2400'),#'ایلام'
			'400'=>array('2200','1100','2400','100','2100'),#'کهگلویه و بویراحمد'
			'2700'=>array('1500','2500','2900'),#'خراسان شمالی'
			'2600'=>array('500','1400','3000','2500'),#'خراسان جنوبی'
			'3100'=>array('2300','300','1000','900'),#'البرز'	
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
			),
			'94'=>array(
				'title'=>'غير قابل توزيع',
				'update'=>'0',
				'options'=>array(
					'name'=>'غير قابل توزيع'.$panel,				
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
			'96'=>array(
				'title'=>'عدم قبول در مبداء',
				'update'=>'0',
				'options'=>array(
					'name'=>'عدم قبول در مبداء'.$panel,				
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
			'97'=>array(
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
			)		
		);
		return $states;
	}
}
class PsCartCod extends PsCartParsPeik{
	public function __construct(){
		parent::__construct();
	}	
}