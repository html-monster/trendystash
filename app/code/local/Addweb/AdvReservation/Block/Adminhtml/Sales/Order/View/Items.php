<?php
/**
 * Adminhtml order items grid
 *
 * @author Vlasakh
 */
class Addweb_AdvReservation_Block_Adminhtml_Sales_Order_View_Items extends Mage_Adminhtml_Block_Sales_Items_Abstract
{
    public $rentData = "hello";


    /**
     * Retrieve required options from parent
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            Mage::throwException(Mage::helper('adminhtml')->__('Invalid parent block for this block'));
        }
        $this->setOrder($this->getParentBlock()->getOrder());
        parent::_beforeToHtml();
    }


    /**
     * Retrieve order items collection
     *
     * @return unknown
     */
    public function getItemsCollection()
    {
        return $this->getOrder()->getItemsCollection();
    }


    public function getItemPledge()
    {
        return "hello";
    }


    /**
     * Retrieve rendered item html content
     *
     * @param Varien_Object $item
     * @return string
     */
    public function getItemHtml(Varien_Object $item, $items)
    {
        if ($item->getOrderItem()) {
            $type = $item->getOrderItem()->getProductType();
        } else {
            $type = $item->getProductType();
        }

        // insert data in child block
        $renderer = $this->getItemRenderer($type);
        $renderer->setData('rent', $this->getOrderProductsPledge($items->getData()[0]["order_id"]));
        $renderer->setData('orderId', $items->getData()[0]["order_id"]);

//        $renderer->rent = $this->getOrderProductsPledge($smth->getData()[0]["order_id"]);
        return $renderer->setItem($item)
        //        return $this->getItemRenderer($type)->setItem($item)
            ->setCanEditQty($this->canEditQty())
            ->toHtml();
    }



    /**
     * Retrieve current order rent
     */
    public function getOrderProductsPledge($orderId)
    {
        // *** Get prod pledge payment info ***
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $query = "SELECT
                  id, mailletters, price, id_prod,
                  DATE_FORMAT(fdate, '%Y-%m-%d') fdate,
                  DATE_FORMAT(tdate, '%Y-%m-%d') tdate,
                  DATE_FORMAT(pldate, '%d %b %Y %H:%i') pldate
                FROM adv_rent
                WHERE id_order = {$orderId} ";
        $results = $readConnection->fetchAll($query);

        foreach ($results as $key => $val)
        {
            $rentInfo[$val['id_prod']] = $val;
        } // end foreach

        return $rentInfo;
    }
}
