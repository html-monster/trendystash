<?php
/**
 * Created by PhpStorm.
 * User: tianna
 * Date: 23.05.17
 * Time: 10:11
 */

class Addweb_AdvReservation_Adminhtml_HellobackController extends Mage_Adminhtml_Controller_Action
//class JR_CreateAdminController_Adminhtml_CustomController extends Mage_Adminhtml_Controller_Action
{
//    public function pledgenotifyemailAction()
    public function someAction()
    {
        $this->loadLayout()
//            ->_setActiveMenu('mycustomtab')
            ->_title($this->__('Index Action'));
        $this->renderLayout();
    }
}