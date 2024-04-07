<?php
if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_1_3_5($module)
{
    $value = Configuration::get('PSCA_FIELDS_ADDRESS');
    $values_fields_address = Tools::jsonDecode($value,true);

    $values = array_merge(
        $values_fields_address,
        array(
            'firstname' =>array(
                'enable'=> false,
                'required'=> false,
                'enable_virtual'=> false,
                'required_virtual'=> false,
                'position'=>'2'
            ),
            'lastname' =>array(
                'enable'=> false,
                'required'=> false,
                'enable_virtual'=> false,
                'required_virtual'=> false,
                'position'=>'2'
            ),
            'name_merged' =>array(
                'enable' => $values_fields_address['lastname']['enable'],
                'required' => $values_fields_address['lastname']['required'],
                'enable_virtual'=> $values_fields_address['lastname']['enable_virtual'],
                'required_virtual'=> $values_fields_address['lastname']['required_virtual'],
                'position'=>'2'
            ),
        )
    );
    Configuration::updateValue('PSCA_FIELDS_ADDRESS',Tools::jsonEncode($values));
    Configuration::updateValue('PSCA_PAYMENT_LINK','/payment');
    Configuration::updateValue('PSCA_ID_STATE_DEFAULT','0');
    Configuration::updateValue('PSCA_ID_CITY_DEFAULT','0');
    Configuration::updateValue('PSCA_HIDE_COD','0');
	return true;
}