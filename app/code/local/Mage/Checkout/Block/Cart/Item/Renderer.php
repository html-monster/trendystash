<?php

class Mage_Checkout_Block_Cart_Item_Renderer extends Mage_Core_Block_Template
{
    /** @var Mage_Checkout_Model_Session */
    protected $_checkoutSession;
    protected $_item;
    protected $_productUrl = null;
    protected $_productThumbnail = null;

    /**
     * Whether qty will be converted to number
     *
     * @var bool
     */
    protected $_strictQtyMode = true;

    /**
     * Check, whether product URL rendering should be ignored
     *
     * @var bool
     */
    protected $_ignoreProductUrl = false;

    /**
     * Set item for render
     *
     * @param   Mage_Sales_Model_Quote_Item $item
     * @return  Mage_Checkout_Block_Cart_Item_Renderer
     */
    public function setItem(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        $this->_item = $item;
        return $this;
    }

    /**
     * Get quote item
     *
     * @return Mage_Sales_Model_Quote_Item
     */
    public function getItem()
    {
        return $this->_item;
    }

    /**
     * Get item product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->getItem()->getProduct();
    }

    public function overrideProductThumbnail($productThumbnail)
    {
        $this->_productThumbnail = $productThumbnail;
        return $this;
    }

    /**
     * Get product thumbnail image
     *
     * @return Mage_Catalog_Model_Product_Image
     */
    public function getProductThumbnail()
    {
        if (!is_null($this->_productThumbnail)) {
            return $this->_productThumbnail;
        }
        return $this->helper('catalog/image')->init($this->getProduct(), 'thumbnail');
    }

    public function overrideProductUrl($productUrl)
    {
        $this->_productUrl = $productUrl;
        return $this;
    }

    /**
     * Check Product has URL
     *
     * @return bool
     */
    public function hasProductUrl()
    {
        if ($this->_ignoreProductUrl) {
            return false;
        }
        if ($this->_productUrl || $this->getItem()->getRedirectUrl()) {
            return true;
        }

        $product = $this->getProduct();
        $option  = $this->getItem()->getOptionByCode('product_type');
        if ($option) {
            $product = $option->getProduct();
        }
        if ($product->isVisibleInSiteVisibility()) {
            return true;
        }
        return false;
    }

    /**
     * Retrieve URL to item Product
     *
     * @return string
     */
    public function getProductUrl()
    {
        if (!is_null($this->_productUrl)) {
            return $this->_productUrl;
        }
        if ($this->getItem()->getRedirectUrl()) {
            return $this->getItem()->getRedirectUrl();
        }

        $product = $this->getProduct();
        $option  = $this->getItem()->getOptionByCode('product_type');
        if ($option) {
            $product = $option->getProduct();
        }

        return $product->getUrlModel()->getUrl($product);
    }

    /**
     * Get item product name
     *
     * @return string
     */
    public function getProductName()
    {
        if ($this->hasProductName()) {
            return $this->getData('product_name');
        }
        return $this->getProduct()->getName();
    }


    /**
     * Get item product option by name
     * @author Vlasakh
     * @return string
     */
    public function getProductOption($options, $name)
    {
        foreach ($options as $key => $val) if( $val['option_type'] == $name ) return $val;
        return false;
    }



    /**
     * Get product customize options
     * @author Vlasakh
     * @return array || false
     */
    public function getProductOptions()
    {
        /* @var $helper Mage_Catalog_Helper_Product_Configuration */
        $helper = Mage::helper('catalog/product_configuration');
        $customOptions = $helper->getCustomOptions($this->getItem());


        // get period in days
        $datetime1 = $datetime2 = null;

        foreach ($customOptions as $key => $val)
        {
            if( $val['option_type'] == 'from_date' ) $datetime1 = (new DateTime())->createFromFormat('d/m/Y', trim(explode(' ', $val['value'])[0]), new DateTimeZone("UTC"));
            if( $val['option_type'] == 'to_date' ) $datetime2 = (new DateTime())->createFromFormat('d/m/Y', trim(explode(' ', $val['value'])[0]), new DateTimeZone("UTC"));
        } // end foreach

        if( $datetime1 && $datetime2 )
        {
            $interval = $datetime1->diff($datetime2);
            $interval->d++;
            $customOptions[] = array (
                  'label' => 'Days',
                  'value' => $interval->d,
                  'print_value' => $interval->d.'d',
                  'option_id' => '101',
                  'option_type' => 'days',
                  'custom_view' => false,
                );
        } // endif

        return $customOptions;
    }

    /**
     * Get list of all otions for product
     *
     * @return array
     */
    public function getOptionList()
    {
        return $this->getProductOptions();
    }

    /**
     * Get item configure url
     *
     * @return string
     */
    public function getConfigureUrl()
    {
        return $this->getUrl(
            'checkout/cart/configure',
            array('id' => $this->getItem()->getId())
        );
    }

    /**
     * Get item delete url
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        if ($this->hasDeleteUrl()) {
            return $this->getData('delete_url');
        }

        return $this->getUrl(
            'checkout/cart/delete',
            array(
                'id'=>$this->getItem()->getId(),
                'form_key' => Mage::getSingleton('core/session')->getFormKey(),
                Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->helper('core/url')->getEncodedUrl()
            )
        );
    }

    /**
     * Get item ajax delete url
     *
     * @return string
     */
    public function getAjaxDeleteUrl()
    {
        return $this->getUrl(
            'checkout/cart/ajaxDelete',
            array(
                'id'=>$this->getItem()->getId(),
                Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->helper('core/url')->getEncodedUrl(),
                '_secure' => $this->_getApp()->getStore()->isCurrentlySecure(),
            )
        );
    }

    /**
     * Get item ajax update url
     *
     * @return string
     */
    public function getAjaxUpdateUrl()
    {
        return $this->getUrl(
            'checkout/cart/ajaxUpdate',
            array(
                'id'=>$this->getItem()->getId(),
                Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => $this->helper('core/url')->getEncodedUrl(),
                '_secure' => $this->_getApp()->getStore()->isCurrentlySecure(),
            )
        );
    }
    /**
     * Get quote item qty
     *
     * @return float|int|string
     */
    public function getQty()
    {
        if (!$this->_strictQtyMode && (string)$this->getItem()->getQty() == '') {
            return '';
        }
        return $this->getItem()->getQty() * 1;
    }

    /**
     * Check item is in stock
     *
     * @deprecated after 1.4.2.0-beta1
     * @return bool
     */
    public function getIsInStock()
    {
        if ($this->getItem()->getProduct()->isSaleable()) {
            if ($this->getItem()->getProduct()->getStockItem()->getQty() >= $this->getItem()->getQty()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get checkout session
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckoutSession()
    {
        if (null === $this->_checkoutSession) {
            $this->_checkoutSession = Mage::getSingleton('checkout/session');
        }
        return $this->_checkoutSession;
    }

    /**
     * Retrieve item messages
     * Return array with keys
     *
     * text => the message text
     * type => type of a message
     *
     * @return array
     */
    public function getMessages()
    {
        $messages = array();
        $quoteItem = $this->getItem();

        // Add basic messages occuring during this page load
        $baseMessages = $quoteItem->getMessage(false);
        if ($baseMessages) {
            foreach ($baseMessages as $message) {
                $messages[] = array(
                    'text' => $message,
                    'type' => $quoteItem->getHasError() ? 'error' : 'notice'
                );
            }
        }

        // Add messages saved previously in checkout session
        $checkoutSession = $this->getCheckoutSession();
        if ($checkoutSession) {
            /* @var $collection Mage_Core_Model_Message_Collection */
            $collection = $checkoutSession->getQuoteItemMessages($quoteItem->getId(), true);
            if ($collection) {
                $additionalMessages = $collection->getItems();
                foreach ($additionalMessages as $message) {
                    /* @var $message Mage_Core_Model_Message_Abstract */
                    $messages[] = array(
                        'text' => $message->getCode(),
                        'type' => ($message->getType() == Mage_Core_Model_Message::ERROR) ? 'error' : 'notice'
                    );
                }
            }
        }

        return $messages;
    }

    /**
     * Accept option value and return its formatted view
     *
     * @param mixed $optionValue
     * Method works well with these $optionValue format:
     *      1. String
     *      2. Indexed array e.g. array(val1, val2, ...)
     *      3. Associative array, containing additional option info, including option value, e.g.
     *          array
     *          (
     *              [label] => ...,
     *              [value] => ...,
     *              [print_value] => ...,
     *              [option_id] => ...,
     *              [option_type] => ...,
     *              [custom_view] =>...,
     *          )
     *
     * @return array
     */
    public function getFormatedOptionValue($optionValue)
    {
        /* @var $helper Mage_Catalog_Helper_Product_Configuration */
        $helper = Mage::helper('catalog/product_configuration');
        $params = array(
            'max_length' => 55,
            'cut_replacer' => ' <a href="#" class="dots" onclick="return false">...</a>'
        );
        return $helper->getFormattedOptionValue($optionValue, $params);
    }

    /**
     * Check whether Product is visible in site
     *
     * @return bool
     */
    public function isProductVisible()
    {
        return $this->getProduct()->isVisibleInSiteVisibility();
    }

    /**
     * Return product additional information block
     *
     * @return Mage_Core_Block_Abstract
     */
    public function getProductAdditionalInformationBlock()
    {
        return $this->getLayout()->getBlock('additional.product.info');
    }

    /**
     * Get html for MAP product enabled
     *
     * @param Mage_Sales_Model_Quote_Item $item
     * @return string
     */
    public function getMsrpHtml($item)
    {
        return $this->getLayout()->createBlock('catalog/product_price')
            ->setTemplate('catalog/product/price_msrp_item.phtml')
            ->setProduct($item->getProduct())
            ->toHtml();
    }

    /**
     * Set qty mode to be strict or not
     *
     * @param bool $strict
     * @return Mage_Checkout_Block_Cart_Item_Renderer
     */
    public function setQtyMode($strict)
    {
        $this->_strictQtyMode = $strict;
        return $this;
    }

    /**
     * Set ignore product URL rendering
     *
     * @param bool $ignore
     * @return Mage_Checkout_Block_Cart_Item_Renderer
     */
    public function setIgnoreProductUrl($ignore = true)
    {
        $this->_ignoreProductUrl = $ignore;
        return $this;
    }

    /**
     * Common code to be called by product renders of gift registry to create a block, which is be used to
     * generate html for mrsp price
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Mage_Catalog_Block_Product_Price
     */
    protected function _preparePriceBlock($product)
    {
        return $this->getLayout()
            ->createBlock('catalog/product_price')
            ->setTemplate('catalog/product/price.phtml')
            ->setIdSuffix($this->getIdSuffix())
            ->setProduct($product);
    }

    /**
     *  Common code to be called by product renders of gift registry to  generate final html block
     *
     * @param Mage_Catalog_Model_Product $product
     * @return string
     */
    protected function _getPriceContent($product)
    {
        return $this->getLayout()->createBlock('catalog/product_price')
            ->setTemplate('catalog/product/price_msrp.phtml')
            ->setProduct($product)
            ->toHtml();
    }

    /**
     * Retrieve block cache tags
     *
     * @return array
     */
    public function getCacheTags()
    {
        $tags = $this->getProduct()->getCacheIdTags();
        $tags = is_array($tags) ? $tags : array();

        return array_merge(parent::getCacheTags(), $tags);
    }

    /**
     * Returns true if user is going through checkout process now.
     *
     * @return bool
     */
    public function isOnCheckoutPage()
    {
        $module = $this->getRequest()->getModuleName();
        $controller = $this->getRequest()->getControllerName();
        return $module == 'checkout' && ($controller == 'onepage' || $controller == 'multishipping');
    }
}
