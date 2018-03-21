<?php
/**
 * Sales Order Email items default renderer
 *
 * @author     Vlasakh
 */
class Addweb_AdvReservation_Block_Some extends Mage_Core_Block_Template
{
    /**
     * Retrieve current order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getItem()->getOrder();
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
     * @author Vlasakh
     */
    public function getItemOptions()
    {
        $result = array();
        if ($options = $this->getItem()->getProductOptions()) {
            if (isset($options['options']))
            {
                // ---------------------------
                // proccess from_date and to_date options
                foreach ($options['options'] as $key => &$val)
                {
                    if( $val['option_type'] == 'from_date' )
                    {
                        list($val['value'],) = explode(" ", $val['value']); // trim time
                        list($val['print_value'],) = explode(" ", $val['print_value']);
                        list($val['option_value'],) = explode(" ", $val['option_value']);
                        $fromData = (new DateTime())->createFromFormat('d/m/Y', trim(explode(' ', $val['value'])[0]), new DateTimeZone("UTC"));
                    }

                    if( $val['option_type'] == 'to_date' )
                    {
                        list($val['value'],) = explode(" ", $val['value']);
                        list($val['print_value'],) = explode(" ", $val['print_value']);
                        list($val['option_value'],) = explode(" ", $val['option_value']);
                        $toDate = (new DateTime())->createFromFormat('d/m/Y', trim(explode(' ', $val['value'])[0]), new DateTimeZone("UTC"));
                    }
                } // end foreach

                if( $fromData && $toDate )
                {
                    $interval = $fromData->diff($toDate);
                    $interval->d++;
                    $options['options'][] = array (
                          'label' => 'Days',
                          'value' => $interval->d,
                          'print_value' => $interval->d.'d',
                          'option_id' => '101',
                          'option_type' => 'days',
                          'custom_view' => false,
                          'display' => false,
                        );
                } // endif
                // ---------------------------

                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }

//        $s1 = '$options='.var_export($options, 1)."\n";
//        0||$notpr||file_put_contents($_SERVER['DOCUMENT_ROOT'] . '/11/file', "\n--------------------\n".date("H:i:s")."\n".$s1, 0);//FILE_APPEND

        return $result;
    }

    public function getValueHtml($value)
    {
        if (is_array($value)) {
            return sprintf('%d', $value['qty']) . ' x ' . $this->escapeHtml($value['title']) . " "
                . $this->getItem()->getOrder()->formatPrice($value['price']);
        } else {
            return $this->escapeHtml($value);
        }
    }

    public function getSku($item)
    {
        if ($item->getProductOptionByCode('simple_sku'))
            return $item->getProductOptionByCode('simple_sku');
        else
            return $item->getSku();
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
}
