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
class PsCartOnline extends ObjectModel
{
	
	public function __construct()
	{
		$this->title = 'بدون پنل پستی';
		$this->nameCod = 'Online';
	}
	
	public function getInfo()
	{
		return array(
			'nameCod' =>'ONLINE',
			'titleCod'=>'آنلاین',
			'classCod' =>'Online',
			'description'=>'سبد خرید ساده و سریع سازگار با پنل واسطه ی آنلاین',
			'rahgiriUrl'=>'#',
			'count'=>'20'
		);
	}
	
	public function getItems()
	{
		return array(
			'PSCA_ID_STATE' =>array(
				'type'=>'selectState',
				'default' => '0',
				'label'=>'استان مبدا',
				'error'=>'لطفا استان و شهر مبدا را مشخص کنید',
				'required'=> true,
				'html'=>$this->get_states(),
				'htmlEdit'=>$this->get_states(false)
			),
			'PSCA_ID_CITY' =>array(
				'type'=>'selectCity',
				'default' => '0',
				'label'=>'شهر مبدا',
				'error'=>'لطفا استان و شهر مبدا را مشخص کنید',
				'required'=> true,
				'html'=>'<select id="id_city" class="id_city" name="PSCA_ID_CITY"><option value="0">لطفا استان خود را انتخاب کنید</option></select><script type="text/javascript" src="'._MODULE_DIR_.'psf_prestacart/views/js/city_online.js'.'"></script>',
				'htmlEdit'=>false
			),						
		);
	}	

	public function get_states($select = true) 
	{
		if($select){
			$html = '<select onchange="cityList(this.value);" name="PSCA_ID_STATE" id="id_state">';
				$html .= '<option value="0">لطفا استان خود را انتخاب کنید</option>';
				$html .= '<option value="1">تهران</option>';
				$html .= '<option value="2">گیلان</option>';
				$html .= '<option value="3">آذربایجان شرقی</option>';
				$html .= '<option value="4">خوزستان</option>';
				$html .= '<option value="5">فارس</option>';
				$html .= '<option value="6">اصفهان</option>';
				$html .= '<option value="7">خراسان رضوی</option>';
				$html .= '<option value="8">قزوین</option>';
				$html .= '<option value="9">سمنان</option>';
				$html .= '<option value="10">قم</option>';
				$html .= '<option value="11">مرکزی</option>';
				$html .= '<option value="12">زنجان</option>';
				$html .= '<option value="13">مازندران</option>';
				$html .= '<option value="14">گلستان</option>';
				$html .= '<option value="15">اردبیل</option>';
				$html .= '<option value="16">آذربایجان غربی</option>';
				$html .= '<option value="17">همدان</option>';
				$html .= '<option value="18">کردستان</option>';
				$html .= '<option value="19">کرمانشاه</option>';
				$html .= '<option value="20">لرستان</option>';
				$html .= '<option value="21">بوشهر</option>';
				$html .= '<option value="22">کرمان</option>';
				$html .= '<option value="23">هرمزگان</option>';
				$html .= '<option value="24">چهارمحال و بختیاری</option>';
				$html .= '<option value="25">یزد</option>';
				$html .= '<option value="26">سیستان و بلوچستان</option>';
				$html .= '<option value="27">ایلام</option>';
				$html .= '<option value="28">کهگلویه و بویراحمد</option>';
				$html .= '<option value="29">خراسان شمالی</option>';
				$html .= '<option value="30">خراسان جنوبی</option>';
				$html .= '<option value="31">البرز</option>';	
			$html .= '</select>';
		}else{
			return	array(
				'select'=>'<select onchange="cityList(this.value);" name="PSCA_ID_STATE" id="id_state">',
				'options'=>array(
                    '0'=>'لطفا استان خود را انتخاب کنید',
                    '1'=>'تهران',
                    '2'=>'گیلان',
                    '3'=>'آذربایجان شرقی',
                    '4'=>'خوزستان',
                    '5'=>'فارس',
                    '6'=>'اصفهان',
                    '7'=>'خراسان رضوی',
                    '8'=>'قزوین',
                    '9'=>'سمنان',
                    '10'=>'قم',
                    '11'=>'مرکزی',
                    '12'=>'زنجان',
                    '13'=>'مازندران',
                    '14'=>'گلستان',
                    '15'=>'اردبیل',
                    '16'=>'آذربایجان غربی',
                    '17'=>'همدان',
                    '18'=>'کردستان',
                    '19'=>'کرمانشاه',
                    '20'=>'لرستان',
                    '21'=>'بوشهر',
                    '22'=>'کرمان',
                    '23'=>'هرمزگان',
                    '24'=>'چهارمحال و بختیاری',
                    '25'=>'یزد',
                    '26'=>'سیستان و بلوچستان',
                    '27'=>'ایلام',
                    '28'=>'کهگلویه و بویراحمد',
                    '29'=>'خراسان شمالی',
                    '30'=>'خراسان جنوبی',
                    '31'=>'البرز',
				)
			);
		}			
		
		return $html;
	}
	
	public function isAjacent($origin_id_state=false,$id_state=false ) 
	{
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

	public function getOrderStates() 
	{
		return array();
	}
	
}


class PsCartCod extends PsCartOnline{
	public function __construct(){
		parent::__construct();
	}	
}