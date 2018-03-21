<?php
/**
 * User: Wallinole
 * Date: 08.04.2016
 * Time: 21:40
 */


 class Addweb_AdvReservation_RentController extends Mage_Core_Controller_Front_Action
{
     /**
      * Current rent
      */
    public function currentAction()
    {
//        $rents = Mage::getModel('advreservation/rent')->getCollection();
//        $rents->addFieldToFilter('id_prod','2');
//        $rents = Mage::getResourceModel('advreservation/rent');
//        $rents = Mage::getResourceModel('advreservation/rent_collection');
//        $rents->setPageSize(2)->setCurPage(2);
//        foreach ($rents as $key => $val)
//        {
//            0||$notpr||print  "<pre> \$val :".$s1
//                  .$s1 . print_r($val, 1)."\n"
//                  ."</pre>";$notpr;
//        } // end foreach
//$s1 = 'currentAction='.var_export('currentAction', 1)."\n";
//0||$notpr||file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/var/file', "\n--------------------\n".date("H:i:s")."\n".$s1, 0);//FILE_APPEND

        $this->loadLayout();
        $this->renderLayout();
    }



     /**
      * Rent history
      */
    public function historyAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }



     /**
      * Rent future
      */
    public function futureAction()
    {
//$s1 = 'futureAction='.var_export('futureAction', 1)."\n";
//0||$notpr||file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/var/file', "\n--------------------\n".date("H:i:s")."\n".$s1, FILE_APPEND);//FILE_APPEND
        $this->loadLayout();
        $this->renderLayout();
    }



    public function addAction()
    {
        $blogpost = Mage::getModel('advreservation/rent');
        $blogpost->load(22);
        $blogpost->setStatus(10);
        $blogpost->save();
        echo 'post created';

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