<?php
/**
 * User: Wallinole
 * Date: 21.04.2016
 */


class Addweb_AdvReservation_SearchController extends Mage_Core_Controller_Front_Action
{
     /**
      * Current rent
      */
    public function indexAction()
    {
//$s1 = 'Addweb_AdvReservation_SearchController='.var_export('Addweb_AdvReservation_SearchController', 1)."\n";
//0||$notpr||file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/var/file', "\n--------------------\n".date("H:i:s")."\n".$s1, FILE_APPEND);//FILE_APPEND
        $this->loadLayout();
        $this->renderLayout();
    }
}