<?php

/**
 *
 * 
 *  @module         MPForm
 *  @author         Frank Heyne, Dietrich Roland Pehlke, erpe
 *  @license        http://www.gnu.org/licenses/gpl.htm
 *  @platform       see info.php of this addon
 *  @license terms  see info.php of this addon
 *  @version        see info.php of this module
 *  
 *
 */

class mpform_order extends LEPTON_order {

// Clean ordering (should be called if a row in the middle has been deleted)
	function move_to($cf_value,$field_id,$position) {
		global $database;

		// Get current index
		$order = $this->get_position($field_id);
		if ($order === false) return false;
		if ($order == $position) return true;

		if($order < $position)
			$sql = "UPDATE `".$this->table ."` SET `".$this->order_field."` = `".$this->order_field."` - 1 ".
	 					" WHERE `".$this->common_field."` = '".$cf_value."' ".
	 					" AND `".$this->order_field."` > '".$order ."' AND `".$this->order_field."` <= '".$position."'";
		else
			$sql = "UPDATE `".$this->table ."` SET `".$this->order_field."` = `".$this->order_field."` + 1 ".
	 					" WHERE `".$this->common_field."` = '".$cf_value."' ".
	 					" AND `".$this->order_field."` >= '".$position ."' AND `".$this->order_field."` < '".$order."'";
	 					
		$database->query($sql);
		if($database->is_error()) {
			echo $sql."<br>".$database->get_error();
			return false;
		}
		$sql = "UPDATE `".$this->table ."` SET `".$this->order_field."` = '".$position."'".
				 	" WHERE `".$this->id_field."` = '".$field_id. "'";

		$database->query($sql);
		if($database->is_error()) {
			echo $sql."<br>".$database->get_error();
			return false;
		}
		return true;
	}
	
	function get_position($field_id) {
		global $database;

		// Get current index
		$query_order = "SELECT `".$this->order_field."` FROM `".$this->table."` WHERE `".$this->id_field."` = '$field_id'";
		$get_order = $database->query($query_order);
		if($database->is_error()) {
			echo $query_order."<br>".$database->get_error();
			return false;
		}		
		$fetch_order = $get_order->fetchRow();
		$order = $fetch_order[$this->order_field];
		
		return $order;
	}
}  // end of: class orderx extends order

?>