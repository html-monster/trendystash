<?php
/**
 * Created by Wallinole
 * Date: 21.04.2016
 */


class Addweb_AdvReservation_Block_RentSearch extends Mage_Core_Block_Template
{
    private $from;
    private $to;


    public function __construct()
    {
        parent::__construct();

//$s1 = 'Addweb_AdvReservation_Block_RentSearch='.var_export('Addweb_AdvReservation_Block_RentSearch', 1)."\n";
//0||$notpr||file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/var/file', "\n--------------------\n".date("H:i:s")."\n".$s1, FILE_APPEND*0);//FILE_APPEND

        $daterange = $this->getRequest()->getParams('daterange');
        list($from, $to) = explode('-', $daterange['daterange']);
        list($p1,$p2,$p3) = explode('/', $from);
        $from = strtotime(sprintf('%s.%s.%s', $p1,$p2,$p3));
        $this->from = $from;
        $from -=  86400;

        list($p1,$p2,$p3) = explode('/', $to);
        $to = strtotime(sprintf('%s.%s.%s', $p1,$p2,$p3));
        $this->to = $to;
        $to += 86400;

        $collection = Mage::getModel('advreservation/rent')
            ->getCollection()
            ->addFieldToFilter('status', array("lt"=>'2'));
        $collection->getSelect()->where(sprintf("fdate BETWEEN '%1\$s' AND '%2\$s' OR tdate BETWEEN '%1\$s' AND '%2\$s' OR fdate < '%1\$s' AND tdate > '%2\$s'", date("Y-m-d", $from), date("Y-m-d", $to)))
//            ->addFieldToFilter('fdate', array('from'=>date("Y-m-d", $from),'to'=>date("Y-m-d", $to)))
//            ->addFieldToFilter('tdate', array('from'=>date("Y-m-d", $from),'to'=>date("Y-m-d", $to)))
//            ->setOrder('id', 'DESC');
            ;
//        $collection->getSelect();

        foreach ($collection as $key => $val)
        {
            $ids[$val['id_prod']] = $val['id_prod'];
        } // end foreach


        // get product categories
        $category = Mage::getModel('catalog/category');
        $cats = $category->getResource()->getAllChildren($category->load(16));


        $items = Mage::getModel('catalog/product')->getCollection();
//        $items->addFieldToSelect('*')->distinct(true);
        $items//->addFieldToSelect('entity_id')->distinct(true)
            ->distinct(true)
            ->addFieldToFilter('entity_id', array('nin' => $ids));
//        $items->setOrder('created_at', 'desc');
//        echo $items->getSelect();// will print sql query

        $items->getSelect()->join( array('t2'=> 'catalog_category_product'), 'e.entity_id = t2.product_id', array());
        $items->getSelect()->where('t2.category_id IN(?)', $cats);

        $this->setCollection($items);
    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'custom.pager');
//        $pager->setAvailableLimit(array(10=>10, 20=>20, 'all'=>'all'));
        $pager->setAvailableLimit(array(12=>12, 21=>21));
        $pager->setCollection($this->getCollection());
        $this->setChild('pager', $pager);
        $this->getCollection()->load();
        return $this;
    }

    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }


    public function getSearchResults()
    {
        $collection = $this->getCollection();
        foreach ($collection as $key => $val)
        {
            $productsIds[] = $val->getId();
            $products[] = $val->getData();
        } // end foreach

//        $productsIds;

        $res = Mage::getModel('catalog/product')->getCollection()
            ->addAttributeToSelect('entity_id')
            ->addFieldToFilter('entity_id', array("in" => $productsIds));

        foreach ($res as $product)
        {
            $prod = Mage::getModel('catalog/product')->load($product->getId()); //Product ID
            $price = explode('.', $prod->getPrice())[0] . "." . substr(explode('.', $prod->getPrice())[1], 0, 2);
            $images[$product->getEntityId()] = [$prod->getName(), $this->helper('catalog/image')->init($prod, 'small_image')->resize(210) . ' ', $prod->getProductUrl(), $price];
        }

        foreach ($products as $key => &$val)
        {
            $val['product'] = $images[$val['entity_id']];
        } // end foreach

        return $products;
    }


    public function getRange()
    {
        return [$this->from, $this->to];
    }
}