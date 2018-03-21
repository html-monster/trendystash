<?php
/**
 * User: Vlasakh
 * Date: 06.04.2017
 */


class Addweb_AdvReservation_NewssubscribeController extends Mage_Core_Controller_Front_Action
{
    /**
     * Orders pledge need notification
     */
    public function successAction()
    {
//$s1 = 'Addweb_AdvReservation_SearchController='.var_export('Addweb_AdvReservation_SearchController', 1)."\n";
//0||$notpr||file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/var/file', "\n--------------------\n".date("H:i:s")."\n".$s1, FILE_APPEND);//FILE_APPEND
        $this->loadLayout();
        $this->renderLayout();
    }
}