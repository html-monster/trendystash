<?php
/**
 * Created by Wallinole
 * Date: 14.04.2016
 * Time: 8:44
 */

/**
 * Заказчик отказался от этой страницы !!!!!!!!!!!!!!!!
 * @deprecated
 */
class Addweb_AdvReservation_Block_BytoRent extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();

        $userId = 0;
//        $customer = Mage::getSingleton('customer/session');
//        if( $customer->isLoggedIn() ) $userId = $customer->getId();

        $category = Mage::getModel('catalog/category');
        $cats = $category->getResource()->getAllChildren($category->load(33));
//        $tree = $category->getTreeModel();
//        $tree->load();
//        $ids = $tree->getCollection();
////            ->addFieldToFilter('parent_id', 33);
//        $ids = $ids->getAllIds();
//        if (true || $childs){
//            foreach ($all_child_categories as $cat){
////                $cat = Mage::getModel('catalog/category')->load($id);
//
//                $entity_id = $cat->getId();
//                $name = $cat->getName();
//                $url_key = $cat->getUrlKey();
//                $url_path = $cat->getUrlPath();
//            }
//        }



        $items = Mage::getModel('sales/order_item')->getCollection();
//        $items->addFieldToSelect('*')->distinct(true);
        $items->addFieldToSelect('item_id')->distinct(true)
            ->addFieldToSelect('order_id')
            ->addFieldToSelect('name')
            ->addFieldToSelect('product_id')
            ->addFieldToSelect('created_at');
        $items->getSelect()->join( array('sales_order'=>Mage::getSingleton('core/resource')->getTableName('sales/order')), 'main_table.order_id = sales_order.entity_id', array('sales_order.state', 'sales_order.base_grand_total', 'sales_order.total_item_count'));
        $items->getSelect()->join( array('t3'=> 'catalog_category_product'), 'main_table.product_id = t3.product_id', array());
        $items->getSelect()->where('customer_id=?', Mage::getSingleton('customer/session')->getCustomer()->getId());
        $items->getSelect()->where('t3.category_id IN(?)', $cats);
        $items->setOrder('created_at', 'desc');
//        echo $items->getSelect();// will print sql query
//        print_r($items->getData()); // will print order into array format

//        foreach ($items as $key => $val) {
//0||$notpr||print  "<pre> \$val :".$s1
//      .$s1 . var_export($val, 1)."\n"
//      ."</pre>";$notpr;
//        } // end foreach
//
////        require_once 'app/Mage.php';
////          Mage::app();
//
//        $orders = Mage::getResourceModel('sales/order_collection')
//            ->addFieldToSelect('*')
//            ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
//            ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
//            ->setOrder('created_at', 'desc')
//        ;
//
//        $this->setOrders($orders);
//
//        foreach ($orders as $order):
//
//            echo $order->getRealOrderId().'&nbsp;at&nbsp;'.$this->formatDate($order->getCreatedAtStoreDate()).'&nbsp;('.$order->formatPrice($order->getGrandTotal()).')';
//
//        endforeach;

        $this->setCollection($items);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
//        $pager->setAvailableLimit(array(10=>10, 20=>20, 'all'=>'all'));
        $pager->setAvailableLimit(array(10=>10, 20=>20));
        $pager->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }


    public function getCurrRents()
    {
        $rents = $this->getCollection();
        foreach ($rents as $key => $val)
        {
            $productsIds[] = $val->getProductId();
            $products[] = $val->getData();
        } // end foreach

//        $productsIds;

        $res = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('entity_id')
            ->addFieldToFilter('entity_id', array("in" => $productsIds));

        foreach ($res as $product)
        {
            $prod = Mage::getModel('catalog/product')->load($product->getId()); //Product ID
            $images[$product->getEntityId()] = [$this->helper('catalog/image')->init($prod, 'small_image')->resize(80) . ' ', $prod->getProductUrl()];
        }

        foreach ($products as $key => &$val)
        {
            $val['product'] = $images[$val['product_id']];
        } // end foreach

        return $products;
    }
}