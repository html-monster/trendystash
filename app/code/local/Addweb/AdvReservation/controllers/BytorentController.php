<?php
/**
 * User: Wallinole
 * Date: 08.04.2016
 * Time: 21:40
 */


 class Addweb_AdvReservation_BytoRentController extends Mage_Core_Controller_Front_Action
{
     /**
      * Current rent
      */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }


    public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
        $loginUrl = Mage::helper('customer')->getLoginUrl();

        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }
}