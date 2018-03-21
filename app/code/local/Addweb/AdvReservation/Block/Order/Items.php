<?php
/**
 * Sales order view items block
 *
 * @author     Vlasakh
 */
class Addweb_AdvReservation_Block_Order_Items extends Mage_Sales_Block_Items_Abstract
{
//    function __construct()
//    {
//        parent::__construct();
//
////        if ($_GET['fail']) Mage::log("Pladge payment fails", LOG_ALERT);
////        Mage::getSingleton('core/session')->addError("Pladge payment fails");
//    }



    /**
     * Retrieve current order rent
     */
    public function getOrder()
    {
        $order = Mage::registry('current_order');

        // get products pledges
        $model = Mage::getModel('advreservation/pledge');
        $result = $model->getOrderProductsPledge($order);

        return [$order, $result];
    }



    function getSagePayPledgeFormData($order, $prodPledge)
    {
        $model = Mage::getModel('advreservation/pledge');
        return $model->getSagePayPledgeFormData($order, $prodPledge);
    }
}