<?php
/**
 * Created by Vlasakh
 * Date: 06.04.2017
 */


class Addweb_AdvReservation_Block_Rentpledge extends Mage_Core_Block_Template
{
    private $productsPledges;
    private $currentOrder;


    public function __construct()
    {
        parent::__construct();
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        return $this;
    }



    /**
     * Pledge mails notification
     * @return array
     */
    public function getOrdersNoPlege()
    {
        $model = Mage::getModel('advreservation/pledge');
        return $model->notifyOrdersNoPlege();
    }



    /**
     * Get product info for pledge payment page
     * @return array
     */
    public function getProdPlege()
    {
        $this->currentOrder = $order = Mage::getModel('sales/order')->load(Mage::app()->getRequest()->getParams()['o']);

        $model = Mage::getModel('advreservation/pledge');
        $this->productsPledges = $result = $model->getOrderProductsPledge($order);

        return $result[Mage::app()->getRequest()->getParams()['g']];
    }



    function getSagePayPledgeFormData()
    {
        $model = Mage::getModel('advreservation/pledge');
        return $model->getSagePayPledgeFormData($this->currentOrder, $this->productsPledges[Mage::app()->getRequest()->getParams()['g']]);
    }



    /**
     * If pledge payment fails - show result for user
     * @return array
     */
    public function getFailResult()
    {
        $model = Mage::getModel('advreservation/pledge');
        $paymentData = $model->getPaymentResultInfo(Mage::app()->getRequest()->getParams()['crypt']);

//        Mage::getSingleton('core/session')->addError("Pledge payment fails 1111");

        return ['StatusDetail' => $paymentData['StatusDetail']];
    }


    /**
     * If pledge payment succeeded - show result for user and set DB
     * @return array
     */
    public function getSuccessResult()
    {
        $model = Mage::getModel('advreservation/pledge');
        $paymentData = $model->getPaymentResultInfo(Mage::app()->getRequest()->getParams()['crypt']);

        // set success payment into DB
        $products = [];
        if( $paymentData['Status'] == 'OK' )
        {
            //100000083-76-48-1495220596
            list($idOrder, $idProd) = $model->setPledgePayment($paymentData);

            $products = $model->getOrderProductsPledge($idOrder);

        } // endif

//        Mage::getSingleton('core/session')->addError("Pledge payment fails 1111");

        return $products[$idProd];
    }
}