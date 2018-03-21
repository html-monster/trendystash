<?php
/**
 * Created by Wallinole.
 * Date: 13.04.2016
 * Time: 8:32
 */

//class Addweb_AdvReservation_Model_Mysql4_Rent_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract {
class Addweb_AdvReservation_Model_Resource_Rent_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract {
    protected function _construct()
    {
            $this->_init('advreservation/rent');
    }
}