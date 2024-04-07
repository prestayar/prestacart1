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
class PSFOrderCore{
	
	const TABLE ='dbs_order';
	const ID  	='id_dbs_order';
    
    public static function add($id_order,$cod_tracking_number, $panel ){
        
        $sql = "INSERT INTO " . _DB_PREFIX_ .self::TABLE. " SET id_order = '$id_order', cod_tracking_number = '$cod_tracking_number', panel = '$panel'";
        return Db::getInstance()->execute($sql);
    }
	
    public static function update($id_order,$options = array()){
		if( count($options) == 0) return false;
		$sql = " UPDATE `". _DB_PREFIX_ .self::TABLE."` SET ";
		$first = true;
		foreach($options as $key => $value){
			if($first){ 
				$sql .= "`". _DB_PREFIX_ .self::TABLE."`.`$key` ='$value'";
				$first = false;
			}else
				$sql .= ", `". _DB_PREFIX_ .self::TABLE."`.`$key` ='$value'";
				
			
		}
		$sql .= " WHERE `"._DB_PREFIX_.self::TABLE."`.`id_order` = '$id_order';";
		return Db::getInstance()->execute($sql);	
	}
	
	public static function getByIdOrder($id_order){
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT * FROM `'. _DB_PREFIX_ .self::TABLE.'` WHERE id_order ="'.pSQL($id_order).'"');
	}
	
	public static function getTrackingByIdOrder($id_order){
		$data = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT cod_tracking_number FROM `'. _DB_PREFIX_ .self::TABLE.'` WHERE id_order ="'.pSQL($id_order).'" ');
		return $data['0']['cod_tracking_number'];
	}	
		
	public static function getItems($count = 20,$statusIds=array(),$panel = null){
		if( count($statusIds) > 0 ){
			$in = implode(",", $statusIds);
			return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT * FROM `'. _DB_PREFIX_ .self::TABLE.'` WHERE is_change ="0" AND active ="1" AND `id_status` IN ('.$in.') AND `panel` = "'.$panel.'" LIMIT 0,'.$count.' ');
		}else
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT * FROM `'. _DB_PREFIX_ .self::TABLE.'` WHERE is_change ="0" AND active ="1"  AND `panel` = "'.$panel.'" LIMIT 0,'.$count.' ');
	}	    
	
	public static function resetChange(){
		return Db::getInstance()->execute("UPDATE `". _DB_PREFIX_ .self::TABLE."` SET `is_change`='0' WHERE 1");
	}
	
	public static function saveOrderInfo($ResNum,$totalAmont, $idCart, $idCustomer)
    {
        $time = date('Y-m-d');
        $sql = "INSERT INTO " . _DB_PREFIX_ .self::TABLE. " SET res_num = '" .$ResNum . "', total_amont = '$totalAmont', cart_id = '$idCart', customer_id = '$idCustomer' , date_start = $time";
        return Db::getInstance()->execute($sql);
    }
    public static function searchRefNum($refNum) 
	{
	   return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT * FROM `'. _DB_PREFIX_ .self::TABLE.'` WHERE ref_num ="'.pSQL($refNum).'"');
			
	}
    
     public static  function searchResNum($resNum) 
	{
	   return Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS("SELECT * FROM `" . _DB_PREFIX_ .self::TABLE. "` WHERE `res_num` = '$resNum'");
	}
    
    public static function saveBankInfo($payment,$resnum,$refnum,$description=false) {
		 
			$sql = " UPDATE " . _DB_PREFIX_ .self::TABLE. " SET ref_num = '" . $refnum . "' ,payment = '$payment'".(($description)?",description ='$description'":"")." WHERE res_num = '" . $resnum . "'";
			return Db::getInstance()->execute($sql);
	}
    
 }