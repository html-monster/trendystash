<?php
/**
 * User: Vlasakh
 * Date: 23.05.2017
 */


 class Addweb_AdvReservation_Adminhtml_AjaxactionsController extends Mage_Adminhtml_Controller_Action
{
     /**
      * Current rent
      */
    public function pledgenotifyemailAction()
    {
        $aa = "here";


//        $this->loadLayout();
//        $this->renderLayout();

        0||$notpr||print  "<pre> \$_POST :".$s1
              .$s1 . print_r($_POST, 1)."\n"
              ."</pre>";$notpr;
    }


/*    public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
        $loginUrl = Mage::helper('customer')->getLoginUrl();

        if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
    }*/
}