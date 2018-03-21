<?php
/**
 * @author Vlasakh
 */


/**
 * Product View block
 *
 * @category Mage
 * @package  Mage_Catalog
 * @module   Catalog
 * @author   Magento Core Team <core@magentocommerce.com>
 */
class Addweb_AdvReservation_Block_Catalog_Product_View extends Mage_Catalog_Block_Product_View
//class Addweb_AdvReservation_Block_Catalog_Product_View extends Mage_Catalog_Block_Product_Abstract
{
    /**
     * Get product pledge sum
     */
    protected function getPledgePrice()
    {
        return $this->getProduct()->getData()['pledge'];
    }
}
