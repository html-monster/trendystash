<?php

class Addweb_AdvReservation_Model_Observer
{
    public $rangesData;
    public $isrentProduct;

    private $addedProductsIds;
    /*
        This function is used to calculate minimun quantity for the requested adults & childs and add it into cart and if
        user selects qty is higher than the minimum qty then selected qty added into cart
    */
    public function modifyCalulatedCustomOption(Varien_Event_Observer $observer)
    {
        //get Product QuoteItem product
        $item = $observer->getQuoteItem();
        // Ensure we have the parent item, if it has one
        $item = ($item->getParentItem() ? $item->getParentItem() : $item);

        // Load the custom price
        $arrInfo = $this->_getPriceByItem($item);
        $cartHelper = Mage::helper('checkout/cart');
        $items = $cartHelper->getCart()->getItems();
        $cntIndex = 0;
        $minCartItems = array();
        foreach ($items as $item) {
            $minCartItems[$cntIndex++]['qty'] = $arrInfo['reqQty'];
        }
        $price = $arrInfo['price'];
        $reqQty = $arrInfo['reqQty'];
        $stringReplace = $arrInfo['string'];

        // condition for calculated qty validation and get session value for shopping cart
        //Mage::getSingleton('core/session')->setMyValue($minCartItems);
        // Set the custom price
        $item->getCustomPrice($price);

        $item->setOriginalCustomPrice($price);
        //$item->setQty($reqQty);
        // Enable super mode on the product.
        $item->getProduct()->setIsSuperMode(true);
        $customArrData = $item->getQty();

        if ($customArrData >= $arrInfo['reqQty']) {
            //custom calculated quantity..
            $item->getQty();
        } else {
            $item->setQty($reqQty);
            //return false;
        }
    }

    // start code for product update function
    public function updatePriceCustomization(Varien_Event_Observer $observer)
    {
        $item = $observer->getQuoteItem();
        // Ensure we have the parent item, if it has one
        $item = ($item->getParentItem() ? $item->getParentItem() : $item);

        // Load the custom price
        $arrInfo = $this->_getPriceByItem($item);
        $cartHelper = Mage::helper('checkout/cart');
        $items = $cartHelper->getCart()->getItems();
        $cntIndex = 0;
        $minCartItems = array();
        foreach ($items as $item) {
            $minCartItems[$cntIndex++]['qty'] = $arrInfo['reqQty'];
        }
        $price = $arrInfo['price'];
        $reqQty = $arrInfo['reqQty'];
        $stringReplace = $arrInfo['string'];

        // Set the custom price
        $item->getCustomPrice($price);

        $item->setOriginalCustomPrice($price);
        //$item->setQty($reqQty);
        // Enable super mode on the product.
        $item->getProduct()->setIsSuperMode(true);


        // condition for calculated qty validation and get session value for shopping cart
        Mage::getSingleton('core/session')->setMyValue($minCartItems);

        $customArrData = $item->getQty();
        //echo $arrInfo['reqQty'];
        //exit;

        if ($customArrData >= $arrInfo['reqQty']) {
            //custom calculated quantity..
            $message = 'Your cart has been updated successfully!';
            Mage::getSingleton('core/session')->addSuccess($message);
            $item->getQty();
        } else {
            $message = "You cannot decrease the calculated quantity!";
            Mage::getSingleton('core/session')->addError($message);
            $item->setQty($reqQty); // calculated qty set.
        }
    }
    // End code for product update

    // function start for calculated type reserveType, adults, childs, start date, end date code
    protected function _getPriceByItem(Mage_Sales_Model_Quote_Item $item)
    {
        $product = $item->getProduct();
        $optionsReturn = array();
        $cart = Mage::getModel('checkout/cart')->getQuote();

        foreach ($cart->getAllItems() as $item) {
            $productId = $item->getProduct()->getId();

            $arrMaxAvailability = array();
            $_product = Mage::getModel('catalog/product')->load($productId);
            $options = $_product->getOptions();
            foreach ($options as $option) {
                $values = $option->getValues();
                foreach ($values as $value) {
                    switch ($value->getType()) {
                        default:
                            if (($value->getMaxAdults() != '') && ($value->getMaxChilds() != '')) {
                                $arrMaxAvailability[$value->getTitle()]['ADULTS'] = $value->getMaxAdults();
                                $arrMaxAvailability[$value->getTitle()]['CHILDS'] = $value->getMaxChilds();
                                $arrMaxAvailability[$value->getTitle()]['PRICE'] = $value->getPrice();
                                $arrMaxAvailability[$value->getTitle()]['PRINTVALUE'] = $value->getDefaultTitle();
                            }
                            break;
                    }
                }
            }
        }

        /*###################### Date Diffrence Start #########################*/
        $productOptionsDate = $product->getTypeInstance(true)->getOrderOptions($product);

        Mage::getSingleton('core/session')->setProductOptions($productOptionsDate);
        /* Each custom option loop */
        /* set session value for custom option array */


        foreach ($productOptionsDate['options'] as $_option) {
            // value store in custom variable
            switch ($_option['option_type']) {
                case 'to_date':
                    $toDate = $_option['option_value'];
                    break;
                case 'from_date':
                    $fromDate = $_option['option_value'];
                    break;
                case 'reserve_type':
                    $reserveType = $_option['value'];
                    break;
                case 'adults':
                    $adults = $_option['value'];
                    break;
                case 'childs':
                    $childs = $_option['value'];
                    break;
            }
        }
        //if ($toDate && $fromDate && $reserveType && $adults && $childs) { // this conditon for custom option product is available other goto to simple product
        if ($toDate && $fromDate) { // custom correction ==================================================================

            // -- sakharov 2016-03-03 -------------
            /*$bits = explode('-',explode(' ',$toDate)[0]);
            $toDate = $bits[0].'-'.$bits[2].'-'.$bits[1];
            $bits = explode('-',explode(' ',$fromDate)[0]);
            $fromDate = $bits[0].'-'.$bits[2].'-'.$bits[1];*/
            // -- ---------------------------------
            $seconds = strtotime($toDate) - strtotime($fromDate) + 86400;
            $totalOfDays = ceil($seconds / 86400);
            if ($totalOfDays < 1) {
                $cstTotalDay = 1;
            } else {
                $cstTotalDay = $totalOfDays;
            }
            $customPrice = $arrMaxAvailability[$reserveType]['PRICE'];
            // -- 16-03-29 -- wallinole --------------------------------------
            $priceSpec = $item->getProduct()->getSpecialPrice();
            $roomPrices = $priceSpec ?: $item->getProduct()->getPrice();
            // -- ------------------------------------------------------------
            $roomPrices = $roomPrices + $customPrice;
//            $roomPrices = $item->getProduct()->getPrice() + $customPrice;

            foreach ($productOptionsDate['options'] as $_option) {

                if ('reserve_type' === $_option['option_type'] && false) {
                    $optId = $_option['option_id'];
                    // Add replacement custom option with modified value
                    $additionalOptions = array(array(
                        'code' => 'reserve_type',
                        'label' => $_option['label'],
                        'value' => $_option['value'] . ' <p>' . $cstTotalDay . ' Days </p> <span>' . $roomPrices . ' / Day Price </span>',
                        'print_value' => $_option['print_value'] . 'For' . $cstTotalDay,
                    ));
                    $item->addOption(array(
                        'code' => 'additional_options',
                        'value' => serialize($additionalOptions),
                    ));

                    // Update info_buyRequest to reflect changes
                    if ($infoArr &&
                        isset($infoArr['options']) &&
                        isset($infoArr['options'][$optId])
                    ) {
                        // Remove real custom option
                        unset($infoArr['options'][$optId]);

                        // Add replacement additional option for reorder (see above)
                        $infoArr['additional_options'] = $additionalOptions;

                        $info->setValue(serialize($infoArr));
                        $item->addOption($info);
                    }

                    // Remove real custom option id from option_ids list
                    if ($optionIdsOption = $item->getProduct()->getCustomOption('option_ids')) {
                        $optionIds = explode(',', $optionIdsOption->getValue());
                        if (false !== ($idx = array_search($optId, $optionIds))) {
                            unset($optionIds[$idx]);
                            $optionIdsOption->setValue(implode(',', $optionIds));
                            $item->addOption($optionIdsOption);
                        }
                    }

                    // Remove real custom option
                    //$item->removeOption('option_' . $optId);
                }
            }

            /*$start_date = new DateTime($toDate);
            $since_start = $start_date->diff(new DateTime($fromDate));*/
            $seconds = strtotime($toDate) - strtotime($fromDate);


            $reqQty = 0;
            $remainAdults = 0;
            $moreChildAllow = 0;
            $maxAdults = $arrMaxAvailability[$reserveType]['ADULTS'];
            $maxChilds = $arrMaxAvailability[$reserveType]['CHILDS'];
            $customPrice = $arrMaxAvailability[$reserveType]['PRICE'];
            $maxAlowd = $maxAdults + $maxChilds;
            $printValue = $arrMaxAvailability[$reserveType]['PRINTVALUE'];
            while ($adults > 0) {
                if ($adults == 1) {
                    $remainAdults = $maxAdults - $adults;
                    $flagchildallow = true;
                }
                if ($childs > 0) {
                    if ($flagchildallow) {
                        $childs = $childs - $remainAdults;
                        $flagchildallow = false;
                    }
                    $childs = $childs - $maxChilds;
                }
                $reqQty++;
                $adults = $adults - $maxAdults;
                if ($adults < 0) {
                    $moreChildAllow = 0 - $adults;
                }
            }

            while ($childs > $moreChildAllow) {
                $childs = $childs - $maxChilds;
                if ($flagchildallow) {
                    $flagchildallow = false;
                    continue;
                }
                $reqQty++;
            }

            $totalOfDays = ceil($seconds / 86400) + 1;
            if ($totalOfDays < 1) {
                $cstTotalDay = 1;
            } else {
                $cstTotalDay = $totalOfDays;
            }
            // -- 16-03-29 -- wallinole --------------------------------------
            $priceSpec = $item->getProduct()->getSpecialPrice();
            $roomPrices = $priceSpec ?: $item->getProduct()->getPrice();
            // -- ------------------------------------------------------------
            $roomPrices = ($roomPrices + $customPrice) * $cstTotalDay;
//            $roomPrices = ($item->getProduct()->getPrice() + $customPrice) * $cstTotalDay;

            $customQty = $item->getQty();

            $optionsReturn = array(
                'price' => $roomPrices,
                'reqQty' => $reqQty,
                'string' => $string,
                'daysDiff' => $cstTotalDay,
                'customQty' => $customQty
            );
            return $optionsReturn;
        } else {
            $optionsReturn = array();

            // -- 16-03-29 -- wallinole --------------------------------------
            $priceSpec = $item->getProduct()->getSpecialPrice();
            $roomPrices = $priceSpec ?: $item->getProduct()->getPrice();
            // -- ------------------------------------------------------------
            $reqQty = $item->getQty();

            $optionsReturn = array(
                'price' => $roomPrices,
                'reqQty' => $reqQty,
            );
            return $optionsReturn;
        }
        /*###################### Date Diffrence End #########################*/

    }

    public function addProductAfter(Varien_Event_Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();
        //$product  = $item->getProduct();
        $afterEventOptionsReturn = array();
        $cart = Mage::getModel('checkout/cart')->getQuote();

        foreach ($cart->getAllItems() as $item) {
            $productId = $product->getId();

            $arrMaxAvailability = array();
            $_product = Mage::getModel('catalog/product')->load($productId);
            $options = $_product->getOptions();
            foreach ($options as $option) {
                $values = $option->getValues();
                foreach ($values as $value) {
                    switch ($value->getType()) {
                        default:
                            if (($value->getMaxAdults() != '') && ($value->getMaxChilds() != '')) {
                                $arrMaxAvailability[$value->getTitle()]['ADULTS'] = $value->getMaxAdults();
                                $arrMaxAvailability[$value->getTitle()]['CHILDS'] = $value->getMaxChilds();
                                $arrMaxAvailability[$value->getTitle()]['PRICE'] = $value->getPrice();
                                $arrMaxAvailability[$value->getTitle()]['PRINTVALUE'] = $value->getDefaultTitle();
                            }
                            break;
                    }
                }
            }
        }
        /*###################### Date Diffrence Start #########################*/
        //$productOptionsDate = $product->getTypeInstance(true)->getOrderOptions($product);
        $getProductOption = Mage::getSingleton('core/session')->getProductOptions();


        /* Each custom option loop */
        foreach ($getProductOption['options'] as $_option) {
            switch ($_option['option_type']) {
                case 'to_date':
                    $toDate = $_option['option_value'];
                    break;
                case 'from_date':
                    $fromDate = $_option['option_value'];
                    break;
                case 'reserve_type':
                    $reserveType = $_option['value'];
                    break;
                case 'adults':
                    $adults = $_option['value'];
                    break;
                case 'childs':
                    $childs = $_option['value'];
                    break;
            }
        }

        if ($toDate && $fromDate && $reserveType && $adults && $childs) { // this conditon for custom option product

            $seconds = strtotime($toDate) - strtotime($fromDate);
            $totalOfDays = ceil($seconds / 86400);
            if ($totalOfDays < 1) {
                $cstTotalDay = 1;
            } else {
                $cstTotalDay = $totalOfDays;
            }
            $customPrice = $arrMaxAvailability[$reserveType]['PRICE'];
            $roomPrices = $item->getProduct()->getPrice() + $customPrice;


            /*$start_date = new DateTime($toDate);
            $since_start = $start_date->diff(new DateTime($fromDate));*/
            $seconds = strtotime($toDate) - strtotime($fromDate);


            $reqQty = 0;
            $remainAdults = 0;
            $moreChildAllow = 0;
            $maxAdults = $arrMaxAvailability[$reserveType]['ADULTS'];
            $maxChilds = $arrMaxAvailability[$reserveType]['CHILDS'];
            $customPrice = $arrMaxAvailability[$reserveType]['PRICE'];
            $maxAlowd = $maxAdults + $maxChilds;
            $printValue = $arrMaxAvailability[$reserveType]['PRINTVALUE'];
            while ($adults > 0) {
                if ($adults == 1) {
                    $remainAdults = $maxAdults - $adults;
                    $flagchildallow = true;
                }
                if ($childs > 0) {
                    if ($flagchildallow) {
                        $childs = $childs - $remainAdults;
                        $flagchildallow = false;
                    }
                    $childs = $childs - $maxChilds;
                }
                $reqQty++;
                $adults = $adults - $maxAdults;
                if ($adults < 0) {
                    $moreChildAllow = 0 - $adults;
                }
            }

            while ($childs > $moreChildAllow) {
                $childs = $childs - $maxChilds;
                if ($flagchildallow) {
                    $flagchildallow = false;
                    continue;
                }
                $reqQty++;
            }

            $totalOfDays = ceil($seconds / 86400);
            if ($totalOfDays < 1) {
                $cstTotalDay = 1;
            } else {
                $cstTotalDay = $totalOfDays;
            }
            $roomPrices = ($item->getProduct()->getPrice() + $customPrice) * $cstTotalDay;

            $customQty = $item->getQty();

            $cartHelper = Mage::helper('checkout/cart');
            $items = $cartHelper->getCart()->getItems();
            $cntIndex = 0;
            $minCartItems = array();
            foreach ($items as $item) {
                $minCartItems[$cntIndex++]['qty'] = $reqQty;
            }
            // condition for calculated qty validation and get session value for shopping cart
            Mage::getSingleton('core/session')->setMyValue($minCartItems);

        } else {
            $afterEventOptionsReturn = array();

            $roomPrices = $item->getProduct()->getPrice();
            $reqQty = $item->getQty();

            $afterEventOptionsReturn = array(
                'price' => $roomPrices,
                'reqQty' => $reqQty,
            );
            return $afterEventOptionsReturn;
        }
        /*###################### Date Diffrence End #########################*/

    }



    /**
     * Add rent record after complete order
     */
    public function onSalesOrderSaveCommitAfter(Varien_Event_Observer $observer)
    {
//        $order = $observer->getOrder();
////        $invoice = $observer->getEvent()->getInvoice();
//        $invoice = $observer->getEvent();
//
//        $s1 = '$order='.var_export($order, 1)."\n";
//        $s1 .= 'getEvent='.var_export($invoice, 1)."\n";
//        $product = $observer->getEvent()->getProduct();
//        $productId = $product->getId();
        $cart = Mage::getModel('checkout/cart')->getQuote();
        $cart->getAllItems();

        $items = $cart = Mage::helper('checkout/cart')->getCart()->getQuote()->getAllItems();

        $orderID = $observer->getOrder()->getId();

        $orderData = array();
        foreach ($items as $item)
        {
            $productId = $item->getProduct()->getId();
            $_customOptions = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());

            /* Each custom option loop */
            foreach($_customOptions['options'] as $_option)
            {
//                echo $_option['label'] .'=>'. $_option['value']."<br/>";

                if( $_option['option_type'] == 'from_date' ) $orderData[$productId]['fdate'] = $_option['option_value'];
                elseif( $_option['option_type'] == 'to_date' ) $orderData[$productId]['tdate'] = $_option['option_value'];

                // Do your further logic here
            }
//            $productId = $product->getId();
//
//            $_product = Mage::getModel('catalog/product')->load($productId);
//            $option = $_product->getTypeInstance(true)->getOrderOptions($_item->getProduct());
//            $options = $_product->getOptions();
//            foreach ($options as $option) {
//                $values = $option->getValues();
//                foreach ($values as $value) {
//                    $type = $value->getType();
//                    $title = $value->getTitle();
//
//                }
//            }
        }

//        $getProductOption = Mage::getSingleton('core/session')->getProductOptions();

        // write to adv_rent
        foreach ($orderData as $key => $val)
        {
            $fdate = $val['fdate'];
            $tdate = $val['tdate'];

            if( $fdate && $tdate )
            {
                if( $_SESSION['order'][$orderID] )
                    if( in_array($key, $_SESSION['order'][$orderID]) ) continue;

                $write = Mage::getSingleton("core/resource")->getConnection("core_write");

                $userId = 0;
                $customer = Mage::getSingleton('customer/session');
                if( $customer->isLoggedIn() ) $userId = $customer->getId();


                $query = "insert into adv_rent "
                       . "(id_user, id_prod, id_order, fdate, tdate, status) values "
                       . "(:idus, :id_prod, :id_order, :fdate, :tdate, 0)";


                $_SESSION['order'][$orderID][] = $key;

                $binds = array(
                    'idus' => $userId,
                    'id_prod' => $key,
                    'id_order' => $orderID,
                    'fdate' => date('Y-m-d H:i:s', strtotime($fdate) + 1),
                    'tdate' => date('Y-m-d H:i:s', strtotime($tdate) + 86400 - 1),
                );
                $write->query($query, $binds);
            } // endif
        } // end foreach

//			<sales_order_save_commit_after>
//				<observers>
//					<sales_order_save_commit_after_handler>
//						<type>model</type>
//						<class>advreservation/observer</class>
//						<method>onSalesOrderSaveCommitAfter</method>
//					</sales_order_save_commit_after_handler>
//				</observers>
//			</sales_order_save_commit_after>
    }



    /**
     * After adding product to cart
     */
    public function onCheckoutCartProductAddAfter(Varien_Event_Observer $observer)
    {
        $quoteItem = $observer->getEvent()->getQuoteItem();
        $product = $observer->getEvent()->getProduct();
        $prodIDAdd = $product->getId();

        // for cart update event
        $customOptions = $quoteItem->getBuyRequest()->getOptions();

        // get the product rent dates
        $customOptions['options'] = $customOptions ?: unserialize($product->getCustomOptions()['info_buyRequest']->getData()['value']);
        foreach ($customOptions['options'] as $key => $val)
        {
            if( !isset($fdate) && isset($val['date_internal']) ) $fdate = strtotime($val['date_internal']);
            elseif( !isset($tdate) && isset($val['date_internal']) ) $tdate = strtotime($val['date_internal']);
        } // end foreach

//        $orderID = $observer->getOrder()->getId();

        // *** CHeck for this product in cart ***
        $items = $cart = Mage::helper('checkout/cart')->getCart()->getQuote()->getAllItems();
        $flag = 0;
        if( $fdate && $tdate && count($items) > 1 )
        {
            foreach ($items as $item)
            {
                $productId = $item->getProduct()->getId();
                if( $productId == $prodIDAdd ) { $flag++; }
    //            $productId = $product->getId();

    //            $_customOptions = $item->getProduct()->getTypeInstance(true)->getOrderOptions($item->getProduct());
                /* Each custom option loop */
    //            foreach($_customOptions['options'] as $_option)
    //            {
    ////                echo $_option['label'] .'=>'. $_option['value']."<br/>";
    //
    //                if( $_option['option_type'] == 'from_date' ) $orderData[$productId]['fdate'] = $_option['option_value'];
    //                elseif( $_option['option_type'] == 'to_date' ) $orderData[$productId]['tdate'] = $_option['option_value'];
    //
    //                // Do your further logic here
    //            }
            }
        } // endif

        if( $flag > 1 )
        {
            $quoteItem->getQuote()->removeItem($quoteItem->getId());
            Mage::throwException('You can buy only one product of this type.');
        } // endif



        // *** CHeck for entrance into rented ranges ***
        $resource = Mage::getSingleton('core/resource');
        $readConnection = $resource->getConnection('core_read');

        $prodID = $prodIDAdd;
        $query = "SELECT id, DATE_FORMAT(fdate, '%d-%m-%Y') fdate, DATE_FORMAT(tdate, '%d-%m-%Y') tdate FROM adv_rent WHERE id_prod = {$prodID} AND tdate >= NOW() AND status < 2 ORDER BY fdate";
        $results = $readConnection->fetchAll($query);


        $flag = 0;
        foreach ($results as $key => $val)
        {
            $val['fdate'] = strtotime($val['fdate']);
            $val['tdate'] = strtotime($val['tdate']);
            if( $fdate >= $val['fdate'] && $fdate <= $val['tdate']  ||
                $tdate >= $val['fdate'] && $tdate <= $val['tdate']
            ) { $flag = 1; break; }
        } // end foreach

        if( $flag > 0 )
        {
            $quoteItem->getQuote()->removeItem($quoteItem->getId());
            Mage::throwException('Selected rent range has already been rented.');
        } // endif


        // *** Check for tier price discount
        $tierPrices = $product->getFormatedTierPrice();
//        if( $tierPrices )
//        {
            $days = ($tdate - $fdate) / 86400 + 1;

            $price = $product->getSpecialPrice() ?: $product->getPrice();
            foreach ($tierPrices as $key => $val)
            {
                if( $val['price_qty'] <= $days )
                    if( $val['price'] < $price ) $price = $val['price'];
            } // end foreach

//            if( true || $price != $product->getPrice() )
//            {
        // change price always if update event
                $event = $observer->getEvent();
                $quote_item = $event->getQuoteItem();
                $quote_item->setOriginalCustomPrice($price * $days);
//            }
//        } // endif

            // stop add to cart
//            $productId = Mage::app()->getRequest()->getParam('product');
//            $product = Mage::getModel('catalog/product')->load($productId);
//            Mage::app()->getResponse()->setRedirect($product->getProductUrl());
//            Mage::getSingleton('checkout/session')->addError(Mage::helper('checkout')->__('Sorry, the price of this product has been updated. Please, add to cart after reviewing the updated price.'));
//            $request = Mage::app()->getRequest();
//            $action = $request->getActionName();
//            Mage::app()->getFrontController()->getAction()->setFlag($action, Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
//    //        $observer->getControllerAction()->setFlag($action, Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
    }



    /**
     * Get rented ranges of the rentable product
     */
    public function onCatalogProductLoadAfter($observer)
    {
        $product = $observer->getData('product');
//Mage::getModel('catalog/product')->load($product->getId())->()

        $options = $product->getProductOptionsCollection()->getItems();
        $flag = 0;
        foreach ($options as $key => $val)
        {
            if ( $val->getData()['type'] == 'from_date' ) $flag++;
            if ( $val->getData()['type'] == 'to_date' ) $flag++;
        } // end foreach


        // if product has rent fields get its rent ranges
        if( $flag > 1 )
        {
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');

            $prodID = $product->getId();
            $query = "SELECT id, DATE_FORMAT(fdate, '%d-%m-%Y') fdate, DATE_FORMAT(tdate, '%d-%m-%Y') tdate FROM adv_rent WHERE id_prod = {$prodID} AND tdate >= NOW() AND status < 2 ORDER BY fdate";
            $results = $readConnection->fetchAll($query);

            $rangesData = array();
            foreach ($results as $key => $val)
            {
                $rangesData[] = array(strtotime($val['fdate']), strtotime($val['tdate']),$val['fdate'],$val['tdate']);
            } // end foreach
//            $arrD[] = array(date('d-m-Y',1459717200), date('d-m-Y',1460062800));
//            $arrD[] = array(date('d-m-Y',1462568400), date('d-m-Y',1462827600));
//            $arrD[] = array(date('d-m-Y',1463000400), date('d-m-Y',1463173200));
//            $arrD[] = array(date('d-m-Y',1459717200000), date('d-m-Y',1460062800000));
//            $arrD[] = array(date('d-m-Y',1462568400000), date('d-m-Y',1462827600000));
//            $arrD[] = array(date('d-m-Y',1463000400000), date('d-m-Y',1463173200000));


            $this->rangesData = $rangesData;
            $this->isrentProduct = 1;
//            $model = Mage::getModel('advreservation/observer');
        } // endif
    }



    /**
     * Order change status
     */
    public function onSalesOrderSaveAfter(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getOrder();

        // Only trigger when an order enters processing state.
        // when product order cancaled
        if ($order->getState() == $order::STATE_CANCELED && $order->getOrigData('state') != $order::STATE_CANCELED)
        {
            $resource = Mage::getSingleton('core/resource');
            /** @var Magento_Db_Adapter_Pdo_Mysql */
            $readConnection = $resource->getConnection('core_write');

            $query = "UPDATE adv_rent SET status = 2 WHERE id_order = " . $order->getId();
            $results = $readConnection->query($query);


        // order complete
        } elseif ( $order->getState() == $order::STATE_COMPLETE && $order->getOrigData('state') != $order::STATE_COMPLETE )
        {
            $resource = Mage::getSingleton('core/resource');
            /** @var Magento_Db_Adapter_Pdo_Mysql */
            $readConnection = $resource->getConnection('core_write');

            $query = "UPDATE adv_rent SET status = 1 WHERE id_order = " . $order->getId();
            $results = $readConnection->query($query);
        }
    }



    public function onCmsControllerRouterMatchBefore($observer)
    {
//        if ($_GET['fail']) Mage::log("Pladge payment fails", LOG_ALERT);
//        Mage::getSingleton('core/session')->addError("Pladge payment fails");
//        pledgepay=&order={$order->getId()}&id={$prodPledge['id']}&fail
        if (Mage::app()->getRequest()->getParams()['pledgepay'])
            if (Mage::app()->getRequest()->getParams()['fail'])
            {
                Mage::getSingleton('core/session')->addError("Deposit payment fails");
            }
        return true;
    }


/*    public function onControllerActionLayoutLoadBefore($observer)
    {
        // redirect for after subcribe event
        if( Mage::getSingleton('customer/session')->getData()['wasSubscribe'] === true )
        {
            Mage::getSingleton('customer/session')->setData('wasSubscribe', false);

            Mage::app()->getFrontController()->getResponse()->setRedirect(Mage::getBaseUrl().'rent/newssubscribe/success');
        } // endif
    }

    public function onNewsletterSubscriberSaveCommitAfter($observer)
    {
//        $model = $observer->getObject();
//        if (!($model instanceof Mage_Newsletter_Model_Subscriber)) {
//            return;
//        }
//
//        // newsletter_subscriber table
//        if ($model->isSubscribed())
//        {
//
//        }
        Mage::getSingleton('customer/session')->setData('wasSubscribe', true);
    }*/
}