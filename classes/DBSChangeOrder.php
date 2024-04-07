<?php
/**
 * PrestaCart      Cart Module For Prestashop
 *
 * @DBSCore        Compatible with DBSCore V11Cart
 * @website        PrestaYar.com
 * @copyright	   (c) 2017 - PrestaYar Team
 * @author         Hashem Afkhami <hashem_afkhami@yahoo.com>
 * @since          02 Jan 2017
 * http://domain.com/basket?action=status // check status orders
 * http://domain.com/basket?action=status&update=0 // check orders enserafi or barghasti , ...
 */

class DBSChangeOrder {
	
	public function __construct()
    {
        $cod_config = Configuration::get('PSCA_STATUS_COD');
        $panelCod   = Configuration::get('PSCA_TYPE_PANEL_COD');
        $file_patch = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'classes/codpanels/'.$panelCod.'.php';
        if($cod_config == '1' and file_exists($file_patch) )
        {
            require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'classes/DBSCodPanel.php');
            require_once($file_patch);
            $this->cod 		= new PsCartCod();
        }
        $this->orderCod = new PSFOrderCore();
        $this->optionsCod = $this->cod->getInfo();
	}

	public function init( $update = '1' )
    {
		$orderStatus = $this->cod->getOrderStates();
		$statusIds = array();
		foreach($orderStatus as $key => $item)
			if($item['update'] == $update)
				$statusIds[] = $key;

        $codType = Configuration::get('PSCA_TYPE_PANEL_COD');
		$items = $this->orderCod->getItems($this->optionsCod['count'],$statusIds,$codType);
        if( count($items) == 0 )
		{
			$this->orderCod->resetChange();
			$items = $this->orderCod->getItems($this->optionsCod['count'],$statusIds,$codType);
		}
		$items = $this->cod->GetListStatus($items);
		foreach($items as $key => $item){
			$this->order = New Order( (int)$item['id_order'] );
			if($this->is_ChangeOrder($item)){
				$this->setChangeOrder($item);
			}
		}
	}

	public function is_ChangeOrder($item)
    {
		if($item['result'])
		{
            // بررسی تغییر وضعیت
            $orderStateIdNew = (int) Configuration::get( 'PSCA_ORDER_STATE_'.$item['result']['state'] );
            if($orderStateIdNew and $orderStateIdNew != $this->order->current_state )
				return true;

			$this->orderCod->update(
				$item['id_order'],
				array(
					'date_change_state'=>date('Y-m-d H:i:s'),
					'is_change'=>'1',
					'id_status'=>$item['result']['state'],
				)
			);
		}
		return false;
	}

	public function setChangeOrder($item)
    {
		$orderStateIdNew = (int) Configuration::get( 'PSCA_ORDER_STATE_'.$item['result']['state'] );
		$orderStatus = $this->cod->getOrderStates();
		$orderStatusNew = $orderStatus[$item['result']['state']];
		
		if( $orderStatusNew )
		{
			$current_order_state = $this->order->getCurrentOrderState();
			if ($current_order_state->id != $orderStateIdNew){
				$history = new OrderHistory();
				$history->id_order = $this->order->id;
				$use_existings_payment = false;
				if (!$this->order->hasInvoice())$use_existings_payment = true;
				$history->changeIdOrderState((int)$orderStateIdNew, $this->order, $use_existings_payment);
				#$history->add();
				/*{#test#}*/
                if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING') && $order->shipping_number)
                    //$templateVars = array('{followup}' => str_replace('@', $order->shipping_number, $carrier->url));
                    $templateVars = array('{followup}' => str_replace('@', $order->shipping_number, ''));

                if ($history->addWithemail(true, $templateVars)){
                    if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'))
                        foreach ($this->order->getProducts() as $product)
                            if (StockAvailable::dependsOnStock($product['product_id']))
                                StockAvailable::synchronize($product['product_id'], (int)$product['id_shop']);
                }else
                    $this->errors[] = sprintf(Tools::displayError('Cannot change status for order #%d.'), $item['id_order']);
				/*{#test#}*/
				$array = array(
					'id_status'=>$item['result']['state'],
					'is_change'=>'1',
					'date_change_state'=>date('Y-m-d H:i:s'),
				);
				if($item['result']['cod_post']) $array['post_tracking_number']=$item['result']['cod_post'];
				if($item['result']['date']) $array['date_change_cod']=$item['result']['date'];
				if(isset($item['result']['ensraf'])) $array['ensraf']=$item['result']['ensraf'];
						
				$this->orderCod->update(
					$item['id_order'],
					$array
				);	
		
				return true;
			}			
		}
		return false;
	}
}

