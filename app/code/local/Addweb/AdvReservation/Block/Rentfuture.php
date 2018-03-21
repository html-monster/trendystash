<?php
/**
 * Created by Wallinole
 * Date: 17.04.2016
 */


class Addweb_AdvReservation_Block_RentFuture extends Mage_Core_Block_Template
{
    public function __construct()
    {
//$s1 = '$products='.var_export('RentFuture.php/constr', 1)."\n";
//0||$notpr||file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/var/file', "\n--------------------\n".date("H:i:s")."\n".$s1, 0);//FILE_APPEND
        parent::__construct();

        $userId = 0;
        $customer = Mage::getSingleton('customer/session');
        if( $customer->isLoggedIn() ) $userId = $customer->getId();

        $collection = Mage::getModel('advreservation/rent')
            ->getCollection()
            ->addFieldToFilter('id_user', $userId)
            ->addFieldToFilter('status', array("lt"=>'2'))
            ->addFieldToFilter('fdate', array("gt"=>date('Y-m-d H:i:s')))
            ->setOrder('id', 'DESC');
            ;
//        $collection->getSelect()->join( array('t2'=> 'catalog_product_entity'), 't2.entity_id = main_table.id_prod', array('t2.sku'));
//        $collection->getSelect()->join( array('t3'=> 'catalog_product_entity_media_gallery'), 't3.entity_id = t2.entity_id', array('t3.value', 't3.value_id'));
        $s1 = $collection->getSelect() . ' ';
//            ->addAttributeToSelect('*')
//            ->addAttributeToSelect('id_order')
//            ->addAttributeToSelect('id_prod')
//            ->addAttributeToSelect('tdate')
//            ->addAttributeToSelect('fdate')
            ;
        $this->setCollection($collection);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

$s1 = '$products='.var_export('RentFuture.php/_prepareLayout', 1)."\n";
0||$notpr||file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/var/file', "\n--------------------\n".date("H:i:s")."\n".$s1, 0);//FILE_APPEND
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
            $productsIds[] = $val->getIdProd();
            $products[] = $val->getData();
        } // end foreach

        $res = Mage::getModel('catalog/product')->getCollection()//->addAttributeToSelect('*');
            ->addFieldToFilter('entity_id', array("in" => $productsIds));
//        $collection->getSelect()->join( array('t2'=> 'catalog_product_entity'), 't2.entity_id = main_table.id_prod', array('t2.sku'));
//        $res->getSelect()->join( array('t3'=> 'catalog_product_entity_media_gallery'), 't3.entity_id = e.entity_id', array('t3.value', 't3.value_id'));
//        $s1 = $prod->getSelect() . ' ';

        foreach ($res as $product)
        {
            $prod = Mage::getModel('catalog/product')->load($product->getId()); //Product ID
            $images[$product->getEntityId()] = [$prod->getName(), $this->helper('catalog/image')->init($prod, 'small_image')->resize(80) . ' ', $prod->getProductUrl()];
        }

        foreach ($products as $key => &$val)
        {
            $val['product'] = $images[$val['id_prod']];
        } // end foreach

$s1 = '$products='.var_export($products, 1)."\n";
0||$notpr||file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/var/file', "\n--------------------\n".date("H:i:s")."\n".$s1, 0);//FILE_APPEND

        return $products;
    }
}