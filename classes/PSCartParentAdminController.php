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
 
require_once( dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR .'DBSCore/DBSCore.php');
use DBSCore\V11Cart\DBSAdminController;

class PSCartParentAdminController extends DBSAdminController
{
	/**
	 * Set Toolbar Btn in Module ControlPanel.
	 */	
	public function initToolbar()
	{
		$baseLink 		 = $this->context->link->getAdminLink( $this->module->moduleTabClassName );

		$this->toolbar_btn['dbsit_module_config'] = array(
			'href' => $baseLink.'&dbs_tab=module_config',
			'desc' => $this->l('پیکربندی پرستاکارت'),
			'class' => 'icon-gears',
		);
		
		$this->toolbar_btn['dbsit_price_config'] = array(
			'short' => 'return customer mymodule account',
			'href' => $baseLink.'&dbs_tab=carriersPayment_config',
			'desc' => $this->l('روش پرداخت'),
			'class' => 'icon-AdminParentShipping',
		);
		
		$this->toolbar_btn['dbsit_show_info_config'] = array(
			'href' => $baseLink.'&dbs_tab=fields_config',
			'desc' => $this->l('فیلد های آدرس و ثبت نام'),
			'class' => 'icon-AdminParentCustomer',
		);
		
		$this->page_header_toolbar_btn = $this->toolbar_btn;
		return $this->toolbar_btn;
	}
	
}
