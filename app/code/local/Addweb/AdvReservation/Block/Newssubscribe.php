<?php
/**
 * Created by Vlasakh
 * Date: 06.04.2017
 */


class Addweb_AdvReservation_Block_Newssubscribe extends Mage_Core_Block_Template
{

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
     * If pledge payment succeeded - show result for user and set DB
     * @return array
     */
    public function getSuccessResult()
    {
        return;
    }
}