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
if( !defined('_PS_VERSION_'))
	exit;

require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'classes/PrestaCartParent.php');

class Psf_PrestaCart extends PsCartParent {

    // --

    /**
     * field Address
     */
    public function makeFieldsAddress()
    {
        return array(
            'id_gender' =>array(
                'type'=>'radio', // radio , text , date ,checkbox ,email ,
                'title'=>$this->l('جنسیت'),
                'view'=>'user', // user , address
                'virtual'=>'1', // 1 or 0
                'label'=>$this->l('عنوان','prestacartparent'),
                'required'=>$this->l('عنوان  موردنیاز است.','prestacartparent'),
                'value'=>array(
                    '1'=>$this->l('مرد','prestacartparent'),
                    '2'=>$this->l('زن','prestacartparent')
                ),
                //'placeholder'=>'',
                'data'=>array(
                    'enable'=>'0', // 1 or 0
                    'required'=>'0', // 1 or 0
                    'enable_virtual'=>'0', // 1 or 0
                    'required_virtual'=>'0', // 1 or 0
                    'position'=>'1',
                    //'default'=>'1'
                    //'checked'=>'1',
                )
            ),
            'firstname' =>array(
                'type'=>'text',
                'title'=>$this->l('نام'),
                'view'=>'address',
                'virtual'=>'1',
                'label'=>$this->l('نام','prestacartparent'),
                'required'=>$this->l('نام الزامی می باشد.','prestacartparent'),
                'placeholder'=>$this->l('لطفاً به زبان پارسی تایپ نمایید','prestacartparent'),
                'data'=>array(
                    'enable'=>'0',
                    'required'=>'0',
                    'enable_virtual'=>'0',
                    'required_virtual'=>'0',
                    'position'=>'2'
                )
            ),
            'lastname' =>array(
                'type'=>'text',
                'title'=>$this->l('نام خانوادگی'),
                'view'=>'address',
                'virtual'=>'1',
                'label'=>$this->l('نام خانوادگی','prestacartparent'),
                'required'=>$this->l('نام خانوادگی الزامی می باشد.','prestacartparent'),
                'placeholder'=>$this->l('لطفاً به زبان پارسی تایپ نمایید','prestacartparent'),
                'data'=>array(
                    'enable'=>'0',
                    'required'=>'0',
                    'enable_virtual'=>'0',
                    'required_virtual'=>'0',
                    'position'=>'2'
                )
            ),
            'name_merged' =>array(
                'type'=>'text',
                'title'=>$this->l('نام و نام خانوادگی'),
                'view'=>'address',
                'virtual'=>'1',
                'label'=>$this->l('نام و نام خانوادگی','prestacartparent'),
                'required'=>$this->l('نام و نام خانوادگی الزامی می باشد.','prestacartparent'),
                'placeholder'=>$this->l('لطفاً به زبان پارسی تایپ نمایید','prestacartparent'),
                'data'=>array(
                    'enable'=>'1',
                    'required'=>'1',
                    'enable_virtual'=>'1',
                    'required_virtual'=>'1',
                    'position'=>'2'
                )
            ),
            'email_create' =>array(
                'type'=>'email',
                'title'=>$this->l('ایمیل'),
                'view'=>'user',
                'virtual'=>'1',
                'label'=>$this->l('آدرس ایمیل : ','prestacartparent'),
                'required'=>$this->l('ایمیل الزامی می باشد.','prestacartparent'),
                'placeholder'=>$this->l('example@site.com','prestacartparent'),
                'class'=>'ltr',
                'data'=>array(
                    'enable'=>'1',
                    'required'=>'0',
                    'enable_virtual'=>'1',
                    'required_virtual'=>'1',
                    'position'=>'3'
                )
            ),
            'passwd' =>array(
                'type'=>'password',
                'title'=>$this->l('رمز عبور'),
                'view'=>'user',
                'virtual'=>'1',
                'label'=>$this->l('رمز عبور : ','prestacartparent'),
                'required'=>$this->l('رمز عبور خود را وارد کنید.','prestacartparent'),
                'placeholder'=>'',
                'class'=>'ltr',
                'data'=>array(
                    'enable'=>'0',
                    'required'=>'0',
                    'enable_virtual'=>'0',
                    'required_virtual'=>'0',
                    'position'=>'4'
                )
            ),
            'address1' =>array(
                'type'=>'text',
                'title'=>$this->l('آدرس'),
                'view'=>'address',
                'virtual'=>'0',
                'label'=>$this->l('آدرس به همراه شماره پلاک','prestacartparent'),
                'required'=>$this->l('آدرس  موردنیاز است.','prestacartparent'),
                'placeholder'=>$this->l('لطفاً به زبان پارسی تایپ نمایید','prestacartparent'),
                'data'=>array(
                    'enable'=>'1',
                    'required'=>'1',
                    'enable_virtual'=>'0',
                    'required_virtual'=>'0',
                    'position'=>'5'
                )
            ),
            'postcode' =>array(
                'type'=>'text',
                'title'=>$this->l('کد پستی'),
                'view'=>'address',
                'virtual'=>'0',
                'label'=>$this->l('کد پستی','prestacartparent'),
                'required'=>$this->l('کد پستی  موردنیاز است.','prestacartparent'),
                'placeholder'=>$this->l('لطفاً کدپستی را با دقت و صحیح وارد نمایید','prestacartparent'),
                'class'=>'toltr',
                'data'=>array(
                    'enable'=>'1',
                    'required'=>'1',
                    'enable_virtual'=>'0',
                    'required_virtual'=>'0',
                    'position'=>'6'
                )
            ),
            'phone' =>array(
                'type'=>'text',
                'title'=>$this->l('تلفن ثابت'),
                'view'=>'address',
                'virtual'=>'1',
                'label'=>$this->l('تلفن ثابت','prestacartparent'),
                'required'=>$this->l('تلفن ثابت  موردنیاز است.','prestacartparent'),
                'placeholder'=>$this->l('02112345678','prestacartparent'),
                'class'=>'ltr',
                'data'=>array(
                    'enable'=>'0',
                    'required'=>'0',
                    'enable_virtual'=>'0',
                    'required_virtual'=>'0',
                    'position'=>'7'
                )
            ),
            'phone_mobile' =>array(
                'type'=>'text',
                'title'=>$this->l('تلفن همراه'),
                'view'=>'address',
                'virtual'=>'1',
                'label'=>$this->l('شماره همراه (جهت ارسال کد رهگیری)','prestacartparent'),
                'required'=>$this->l('تلفن همراه  موردنیاز است.','prestacartparent'),
                'placeholder'=>$this->l('09111234567','prestacartparent'),
                'class'=>'ltr',
                'data'=>array(
                    'enable'=>'1',
                    'required'=>'1',
                    'enable_virtual'=>'1',
                    'required_virtual'=>'1',
                    'position'=>'8'
                )
            ),
            'dni' => array(
                'type'		=> 'text',
                'title'		=> $this->l('کد ملی'),
                'view'		=> 'address',
                'virtual'	=> '1',
                'label'		=> $this->l('کد ملی','prestacartparent'),
                'required'	=> $this->l('کد ملی موردنیاز است.','prestacartparent'),
                'placeholder' => '',
                'class'		=> 'ltr',
                'data'		=> array(
                    'enable'=>'0',
                    'required'=>'0',
                    'enable_virtual'=>'0',
                    'required_virtual'=>'0',
                    'position'=>'9'
                )
            ),
            'newsletter' =>array(
                'type'=>'checkbox',
                'title'=>$this->l('خبرنامه'),
                'view'=>'user',
                'virtual'=>'1',
                'label'=>$this->l('در خبرنامه ما ثبت نام کنید.','prestacartparent'),
                'required'=>$this->l('عضویت در خبرنامه الزامی می باشد.','prestacartparent'),
                'value'=>'1',
                'data'=>array(
                    'enable'=>'0',
                    'required'=>'0',
                    'enable_virtual'=>'0',
                    'required_virtual'=>'0',
                    'position'=>'10',
                    'checked'=>'0'
                )
            ),
            'optin' =>array(
                'type'=>'checkbox',
                'title'=>$this->l('پیشنهادات ویژه'),
                'view'=>'user',
                'virtual'=>'1',
                'label'=>$this->l('دریافت پیشنهادات ویژه.','prestacartparent'),
                'required'=>$this->l('انتخاب دریافت پیشنهادات الزامی می باشد.','prestacartparent'),
                'value'=>'1',
                'data'=>array(
                    'enable'=>'0',
                    'required'=>'0',
                    'enable_virtual'=>'0',
                    'required_virtual'=>'0',
                    'position'=>'11',
                    'checked'=>'0'
                )
            )
        );
    }
}