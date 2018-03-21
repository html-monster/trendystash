<?php
/**
 * Created by Wallinole
 * Date: 13.04.2016
 * Time: 5:41
 */

//class Addweb_AdvReservation_Model_Mysql4_Rent extends Mage_Core_Model_Mysql4_Abstract
class Addweb_AdvReservation_Model_Resource_Rent extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        $this->_init('advreservation/rent', 'id');
    }
}