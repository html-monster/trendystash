<?php

class Addweb_AdvReservation_Model_Observer
{
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
            $s1 = '$toDate='.var_export($toDate, 1)."\n";
            file_put_contents('D:\Xampp\htdocs\rentabag-magento\11\_file', "\n--------------------\n".date("H:i:s")."\n".$s1, 0 );//FILE_APPEND
            $s1 = '$fromDate='.var_export($fromDate, 1)."\n";
            file_put_contents('D:\Xampp\htdocs\rentabag-magento\11\_file', "\n--------------------\n".date("H:i:s")."\n".$s1, FILE_APPEND );//FILE_APPEND
            // -- sakharov 2016-03-03 -------------
           /* $bits = explode('-',explode(' ',$toDate)[0]);
            $toDate = $bits[0].'-'.$bits[2].'-'.$bits[1];
            $bits = explode('-',explode(' ',$fromDate)[0]);
            $fromDate = $bits[0].'-'.$bits[2].'-'.$bits[1];*/
            // -- ---------------------------------
            $seconds = strtotime($toDate) - strtotime($fromDate);
            $totalOfDays = ceil($seconds / 86400);
            if ($totalOfDays < 1) {
                $cstTotalDay = 1;
            } else {
                $cstTotalDay = $totalOfDays;
            }
            $customPrice = $arrMaxAvailability[$reserveType]['PRICE'];
            $roomPrices = $item->getProduct()->getPrice() + $customPrice;

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

            $totalOfDays = ceil($seconds / 86400);
            if ($totalOfDays < 1) {
                $cstTotalDay = 1;
            } else {
                $cstTotalDay = $totalOfDays;
            }
            $roomPrices = ($item->getProduct()->getPrice() + $customPrice) * $cstTotalDay;

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

            $roomPrices = $item->getProduct()->getPrice();
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
}